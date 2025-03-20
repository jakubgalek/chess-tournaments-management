<?php
session_start();

if (!isset($_SESSION["logged"])) {
    header('Location: ../index.php');
    exit();
}

include("../scripts/connect.php");

$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$polaczenie->set_charset("utf8");

if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą danych: " . $polaczenie->connect_error);
}

if (!isset($_SESSION['Role']) || $_SESSION['Role'] == 'player') {
    header("Location: account_statistics.php");
    exit();
}

function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function updatePlayerTitles($polaczenie) {
    // Pobierz identyfikator użytkownika i najwyższy osiągnięty ranking
    $query = "
        SELECT UserID, MAX(Ranking) AS MaxRanking
        FROM ranking_history
        GROUP BY UserID
    ";
    $result = $polaczenie->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userID = $row['UserID'];
            $maxRanking = $row['MaxRanking'];
            $title = null;

            if ($maxRanking >= 2500) {
                $title = "Arcymistrz";
            } elseif ($maxRanking >= 2400) {
                $title = "Mistrz Międzynarodowy";
            } elseif ($maxRanking >= 2300) {
                $title = "Mistrz FIDE";
            } elseif ($maxRanking >= 2200) {
                $title = "Kandydat na Mistrza";
            } elseif ($maxRanking < 2200) {
                $title = "Brak tytułu";
            }

            if ($title) {
                $updateStmt = $polaczenie->prepare("UPDATE users SET Title = ? WHERE UserID = ?");
                $updateStmt->bind_param("si", $title, $userID);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }
    } else {
        echo "Nie znaleziono historii rankingu zawodników.";
    }
}

updatePlayerTitles($polaczenie);

function finalize_tournament_results($tournament_id, $polaczenie) {
    // Pobierz dane graczy i ich zdobyte punkty
    $results = $polaczenie->query("
    SELECT 
        tr.UserID, 
        u.Title, 
        u.FirstName, 
        u.LastName, 
        COALESCE(SUM(CASE 
            WHEN rr.UserID = tr.UserID THEN rr.Points 
            WHEN rr.OpponentID = tr.UserID THEN 1 - rr.Points
            ELSE 0 
        END), 0) AS TotalPoints
    FROM tournament_registrations tr
    JOIN users u ON tr.UserID = u.UserID
    LEFT JOIN round_results rr ON rr.RoundID IN (
        SELECT r.RoundID 
        FROM rounds r 
        WHERE r.TournamentID = $tournament_id
    )
    WHERE tr.TournamentID = $tournament_id
    GROUP BY tr.UserID
    ORDER BY TotalPoints DESC, u.LastName ASC, u.FirstName ASC
");

    if ($results->num_rows > 0) {
        $position = 1; // Pozycja gracza w turnieju
        while ($row = $results->fetch_assoc()) {
            $user_id = $row['UserID'];
            $title = $row['Title'];
            $first_name = $row['FirstName'];
            $last_name = $row['LastName'];
            $points = $row['TotalPoints'];

            // Wstaw wyniki do tabeli `tournament_results`
            $stmt = $polaczenie->prepare("
                INSERT INTO tournament_results 
                (TournamentID, UserID, Title, LastName, FirstName, Points, Position) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iisssdi", $tournament_id, $user_id, $title, $last_name, $first_name, $points, $position);
            $stmt->execute();

            $position++; // Zwiększ index dla kolejnego gracza
        }

        echo "Wyniki turnieju zostały zapisane w tabeli `tournament_results`.";
    } else {
        echo "Brak wyników do zapisania.";
    }
}


function update_rankings($round_id, $polaczenie) {
    // Pobierz wyniki rundy
    $round_results = $polaczenie->query("
        SELECT rr.UserID, rr.OpponentID, rr.Result, u.Ranking AS UserRanking, o.Ranking AS OpponentRanking
        FROM round_results rr
        JOIN users u ON rr.UserID = u.UserID
        JOIN users o ON rr.OpponentID = o.UserID
        WHERE rr.RoundID = $round_id
    ");

    if ($round_results->num_rows > 0) {
        while ($row = $round_results->fetch_assoc()) {
            $user_id = $row['UserID'];
            $opponent_id = $row['OpponentID'];
            $result = $row['Result']; // win, draw, loss
            $current_user_ranking = $row['UserRanking'];
            $current_opponent_ranking = $row['OpponentRanking'];

            // Ustal zmianę rankingu dla gracza na podstawie wyniku
            if ($result === 'win') {
                $user_ranking_change = 10;
                $opponent_ranking_change = -10;
            } elseif ($result === 'loss') {
                $user_ranking_change = -10;
                $opponent_ranking_change = 10;
            } else { // draw
                $user_ranking_change = 0;
                $opponent_ranking_change = 0;
            }

            // Oblicz nowe rankingi
            $new_user_ranking = $current_user_ranking + $user_ranking_change;
            $new_opponent_ranking = $current_opponent_ranking + $opponent_ranking_change;

            // Zaktualizuj ranking gracza w tabeli `users`
            $stmt = $polaczenie->prepare("UPDATE users SET Ranking = ? WHERE UserID = ?");
            $stmt->bind_param("di", $new_user_ranking, $user_id);
            $stmt->execute();

            // Dodaj wpis do historii rankingu gracza
            $stmt = $polaczenie->prepare("INSERT INTO ranking_history (UserID, Ranking, ChangeDate) VALUES (?, ?, NOW())");
            $stmt->bind_param("id", $user_id, $new_user_ranking);
            $stmt->execute();

            // Zaktualizuj ranking przeciwnika w tabeli `users`
            $stmt = $polaczenie->prepare("UPDATE users SET Ranking = ? WHERE UserID = ?");
            $stmt->bind_param("di", $new_opponent_ranking, $opponent_id);
            $stmt->execute();

            // Dodaj wpis do historii rankingu przeciwnika
            $stmt = $polaczenie->prepare("INSERT INTO ranking_history (UserID, Ranking, ChangeDate) VALUES (?, ?, NOW())");
            $stmt->bind_param("id", $opponent_id, $new_opponent_ranking);
            $stmt->execute();
        }

        echo "Rankingi graczy i ich przeciwników zostały zaktualizowane dla rundy $round_id.";
    } else {
        echo "Brak wyników do aktualizacji rankingów.";
    }
}

$tournaments_result = $polaczenie->query("SELECT TournamentID, TournamentName FROM tournaments");

// Obsługa wyboru turnieju w celu możliwości dodania wyników rundy
$players_result = null;
$next_round_id = null;
$next_round_number = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tournament'])) {
    $tournament_name = $_POST['tournament_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $game_tempo = $_POST['game_tempo'];
    $arbiter = $_POST['arbiter'];
    $organizer = $_POST['organizer'];
    $total_rounds = $_POST['total_rounds'];
    $completed_rounds = 0;
    $system = $_POST['system'];

    // Tworzenie nowego turnieju
    $stmt = $polaczenie->prepare("INSERT INTO tournaments (TournamentName, StartDate, EndDate, Location, GameTempo, Arbiter, Organizer, CompletedRounds, TotalRounds, System) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssiis", $tournament_name, $start_date, $end_date, $location, $game_tempo, $arbiter, $organizer, $completed_rounds, $total_rounds, $system);
    $stmt->execute();

    $tournament_id = $polaczenie->insert_id; // Pobierz ID nowo utworzonego turnieju

    // Automatyczne generowanie rund
    $stmt = $polaczenie->prepare("INSERT INTO rounds (TournamentID, RoundNumber, Date) VALUES (?, ?, ?)");
    for ($i = 1; $i <= $total_rounds; $i++) {
        $stmt->bind_param("iis", $tournament_id, $i, $start_date);
        $stmt->execute();
    }

    echo "Turniej został utworzony i wszystkie rundy zostały wygenerowane!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tournament_id'])) {
    $tournament_id = intval($_POST['tournament_id']);

      // Pobierz dane turnieju
      $tournament_check = $polaczenie->query("
      SELECT StartDate, EndDate 
      FROM tournaments 
      WHERE TournamentID = $tournament_id
  ")->fetch_assoc();

  $current_time = time();
  $start_time = strtotime($tournament_check['StartDate']);
  $end_time = strtotime($tournament_check['EndDate']);

  // Sprawdź, czy turniej jeszcze się nie rozpoczął
  if ($start_time > $current_time) {
      echo "Turniej jeszcze się nie rozpoczął. Możliwość dodawania wyników będzie dostępna po rozpoczęciu!";
  } 
  else {
    // Fetch the smallest RoundID with no results for the selected tournament
    $round_result = $polaczenie->query("
        SELECT r.RoundID, r.RoundNumber 
        FROM rounds r 
        LEFT JOIN round_results rr ON r.RoundID = rr.RoundID
        WHERE r.TournamentID = $tournament_id 
        GROUP BY r.RoundID 
        HAVING COUNT(rr.RoundID) = 0 
        ORDER BY r.RoundNumber ASC 
        LIMIT 1
    ");

    if ($round_result->num_rows > 0) {
        $round_data = $round_result->fetch_assoc();
        $next_round_id = $round_data['RoundID'];
        $next_round_number = $round_data['RoundNumber'];
    } else {
        // Sprawdź, czy można utworzyć nową rundę
        $tournament_data = $polaczenie->query("
            SELECT TotalRounds, CompletedRounds 
            FROM tournaments 
            WHERE TournamentID = $tournament_id
        ")->fetch_assoc();

            if ($tournament_data['CompletedRounds'] < $tournament_data['TotalRounds']) {
                // Utwórz rundę
                $new_round_number = $tournament_data['CompletedRounds'] + 1;
                $stmt = $polaczenie->prepare("INSERT INTO rounds (TournamentID, RoundNumber, Date) VALUES (?, ?, NOW())");
                $stmt->bind_param("ii", $tournament_id, $new_round_number);

                if ($stmt->execute()) {
                    $next_round_id = $polaczenie->insert_id;
                    $next_round_number = $new_round_number;

                    $polaczenie->query("
                        UPDATE tournaments 
                        SET CompletedRounds = CompletedRounds + 1 
                        WHERE TournamentID = $tournament_id
                    ");

                    echo "Utworzono nową rundę: RoundID=$next_round_id, RoundNumber=$next_round_number.";
                } else {
                    die("Błąd tworzenia nowej rundy: " . $stmt->error);
                }
            } else {
                echo "Wszystkie rundy w tym turnieju zostały zakończone!";
            }
        }

    // Pobierz graczy zarejestrowanych do wybranego turnieju z elementu <option> w HTML   
    $players_result = $polaczenie->query("
        SELECT u.UserID, u.FirstName, u.LastName 
        FROM tournament_registrations tr
        JOIN users u ON tr.UserID = u.UserID
        WHERE tr.TournamentID = $tournament_id
    ");
}
}

// Obsługa dodawania wyników rund z formularza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_results'])) {
    if (!isset($_POST['round_id']) || empty($_POST['round_id'])) {
        echo "Błąd: Nie można dodać wyników, brak aktywnej rundy!";
        exit();
    }

    $round_id = intval($_POST['round_id']);
    if (isset($_POST['match_results']) && !empty($_POST['match_results'])) {
        $stmt = $polaczenie->prepare("
            INSERT INTO round_results (RoundID, UserID, OpponentID, Result, Points) 
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($_POST['match_results'] as $match) {
            $user_id = intval($match['user_id']);
            $opponent_id = intval($match['opponent_id']);
            $result = validate_input($match['result']);
            $points = $result === 'win' ? 1 : ($result === 'draw' ? 0.5 : 0);

            $stmt->bind_param("iiisd", $round_id, $user_id, $opponent_id, $result, $points);
            $stmt->execute();
        }

        // Sprawdź, czy wszystkie mecze w tej rundzie mają wyniki
        $round_complete_check = $polaczenie->query("
            SELECT COUNT(*) AS FinishedMatches 
            FROM round_results rr 
            WHERE rr.RoundID = $round_id
        ");

        $total_matches_in_round = $polaczenie->query("
            SELECT COUNT(*) AS TotalMatches 
            FROM tournament_registrations 
            WHERE TournamentID = (SELECT TournamentID FROM rounds WHERE RoundID = $round_id)
        ")->fetch_assoc()['TotalMatches'] / 2;

        if ($round_complete_check->fetch_assoc()['FinishedMatches'] >= $total_matches_in_round) {
            // Oznacz rundę jako ukończoną, aktualizując CompletedRounds
            $polaczenie->query("
                UPDATE tournaments t 
                INNER JOIN rounds r ON t.TournamentID = r.TournamentID 
                SET t.CompletedRounds = t.CompletedRounds + 1 
                WHERE r.RoundID = $round_id
            ");

            echo "Runda została ukończona! CompletedRounds zaktualizowane.";

            // Aktualizacja rankingów
            update_rankings($round_id, $polaczenie);

            // Sprawdź, czy turniej został zakończony
            $tournament_data = $polaczenie->query("
                SELECT TournamentID, CompletedRounds, TotalRounds 
                FROM tournaments 
                WHERE TournamentID = (SELECT TournamentID FROM rounds WHERE RoundID = $round_id)
            ")->fetch_assoc();

            if ($tournament_data['CompletedRounds'] == $tournament_data['TotalRounds']) {
                // Finalizuj wyniki turnieju
                $tournament_id = intval($tournament_data['TournamentID']);
                finalize_tournament_results($tournament_id, $polaczenie);
            }
        } else {
            echo "Wyniki zostały dodane, ale runda wciąż niekompletna.";
        }
    } else {
        echo "Brak wyników do dodania!";
    }
}


?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <title>Zarządzanie - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
</head>
<body>
<?php include('../include/header.php'); ?>
<div class="main-container">
<?php include('../include/account_side_menu.php'); ?>
<div class="table-container">

    <div class="tournament_container">
        <form method="post" style="margin-right:100px">
            <h2>Tworzenie turnieju</h2>
            <input type="hidden" name="create_tournament" value="1">
            <p><strong>Nazwa turnieju:</strong><br> <input class="search-input" type="text" name="tournament_name" required><br>
            <p><strong>Data rozpoczęcia:</strong><br> <input class="search-input" type="date" name="start_date" required><br>
            <p><strong>Data zakończenia:</strong><br> <input class="search-input" type="date" name="end_date" required><br>
            <p><strong>Lokalizacja:</strong><br> <input class="search-input" type="text" name="location" required><br>
            <p><strong>Tempo gry:</strong><br> <input class="search-input" type="text" name="game_tempo" required><br>
            <p><strong>Arbiter:</strong><br> <input class="search-input" type="text" name="arbiter" required><br>
            <p><strong>Organizator:</strong><br> <input class="search-input" type="text" name="organizer" required><br>
            <p><strong>Liczba rund:</strong><br> <input class="search-input" type="number" name="total_rounds"  min="1" required><br>
            <p><strong>System:</strong><br> 
            <select class="search-input" name="system">
                <option value="Losowy">Losowy</option>
            </select><br />
            <button class="search-button" type="submit">Utwórz turniej</button>
        </form>
        <!-- Formularz wyboru turnieju -->
        <div class="rounds_container">
        <form method="post">
            <h2>Dodawanie wyników</h2>
            <p><strong>Wybierz turniej:</strong>
            <select class="search-input" name="tournament_id" onchange="this.form.submit()">
                <?php while ($row = $tournaments_result->fetch_assoc()) : ?>
                    <option value="<?= $row['TournamentID'] ?>" <?= (isset($tournament_id) && $tournament_id == $row['TournamentID']) ? 'selected' : '' ?>>
                        <?= $row['TournamentName'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

<!-- Formularz wyników rundy -->
<?php if (isset($next_round_id) && $next_round_id): ?>
    <form method="post">
        <input type="hidden" name="add_results" value="1">
        <input type="hidden" name="round_id" value="<?php echo $next_round_id; ?>">
        <p><strong>Numer rundy:</strong><input class="search-input" type="number" name="round_number" value="<?php echo $next_round_number; ?>" readonly><br>
        <h4>Wyniki:</h4>
        <div id="match_results">
            <?php if ($players_result && $players_result->num_rows > 1): 
                $players = [];
                while ($player = $players_result->fetch_assoc()) {
                    $players[] = $player;
                }

                // Sprawdź, czy liczba graczy jest nieparzysta
                if (count($players) % 2 !== 0) {
                    // Usuń ostatniego gracza z turnieju
                    $removed_player = array_pop($players);
                    $polaczenie->query("
                        DELETE FROM tournament_registrations 
                        WHERE TournamentID = $tournament_id 
                        AND UserID = {$removed_player['UserID']}
                    ");
                    echo "<p style='color: red;'>Gracz {$removed_player['FirstName']} {$removed_player['LastName']} został usunięty z turnieju z powodu nieparzystej liczby uczestników.</p>";
                }

                // Podziel graczy na 2 grupy
                shuffle($players); // Losowe przemieszanie graczy
                $midpoint = count($players) / 2;
                $group1 = array_slice($players, 0, $midpoint);
                $group2 = array_slice($players, $midpoint);

                // Dopasowanie graczy losowo między grupami
                while (!empty($group1) && !empty($group2)) {
                    $player1 = array_pop($group1);
                    $player2 = array_splice($group2, array_rand($group2), 1)[0]; // Losowy gracz z grupy 2
                    ?>
                    <div>
                        <input type="hidden" name="match_results[<?php echo $player1['UserID']; ?>][user_id]" value="<?php echo $player1['UserID']; ?>">
                        <input type="hidden" name="match_results[<?php echo $player1['UserID']; ?>][opponent_id]" value="<?php echo $player2['UserID']; ?>">
                        <label><?php echo $player1['FirstName'] . ' ' . $player1['LastName']; ?>
                        
                        <select name="match_results[<?php echo $player1['UserID']; ?>][result]" required>
                            <option value="win"> 1:0 </option>
                            <option value="loss"> 0:1 </option>
                            <option value="draw"> 1/2:1/2 </option>
                        </select>
                        
                        <?php echo $player2['FirstName'] . ' ' . $player2['LastName']; ?></label>
                    </div>
                <?php }
            endif; ?>
        </div>
        <button type="submit" class="search-button">Dodaj wyniki</button>
    </form>
<?php endif; ?>
    </div>
</div>
</div>
</div>

</body>
<style>
.side-menu #account_manage_tournament {
    border: 1px solid #8f8f8f;
}
</style>
</html>

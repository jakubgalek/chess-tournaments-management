<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Lista turnejów - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
    <link rel="stylesheet" href="../css/list_tournaments.css">
    <script>
    function toggleParticipants(tournamentId, button) {
        const participants = document.getElementById('participants-' + tournamentId);
        const isVisible = participants.style.display === 'block';
        
        participants.style.display = isVisible ? 'none' : 'block';
        button.innerText = isVisible ? 'Pokaż uczestników' : 'Ukryj uczestników';
    }

    function toggleResults(tournamentId, button) {
        const results = document.getElementById('results-' + tournamentId);
        const isVisible = results.style.display === 'block';
        
        results.style.display = isVisible ? 'none' : 'block';
        button.innerText = isVisible ? 'Pokaż wyniki' : 'Ukryj wyniki';
    }
</script>
</head>
<body>

<?php include('../include/header.php'); ?>

<div class="form-container">
    <h3 class="section-title">Lista turniejów</h3>

    <?php
session_start();
include("../scripts/connect.php");
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$polaczenie->set_charset("utf8");

if ($polaczenie->connect_error) {
    die("Błąd połączenia: " . $polaczenie->connect_error);
}

// Sprawdzenie, czy użytkownik dołączył do turnieju
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tournament_id'])) {
    $userId = isset($_SESSION['UserID']) ?  $_SESSION['UserID'] : null;
    $tournamentId = intval($_POST['tournament_id']);
    $action = $_POST['action'];

    if ($action == 'join') {
        if (isset($_SESSION["logged"]) && $_SESSION["logged"] === true) {
            // Dodaj zapis do tabeli tournament_registrations
            $stmt = $polaczenie->prepare("INSERT INTO tournament_registrations (UserID, TournamentID) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $tournamentId);

            if ($stmt->execute()) {
                echo "<p class='info'>Zostałeś zapisany na turniej!</p>";

                // Aktualizuj liczbę graczy w tabeli tournaments
                $updateStmt = $polaczenie->prepare("UPDATE tournaments SET PlayerCount = (SELECT COUNT(*) FROM tournament_registrations WHERE TournamentID = ?) WHERE TournamentID = ?");
                $updateStmt->bind_param("ii", $tournamentId, $tournamentId);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                echo "<p class='error'>Błąd zapisu: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='info'><a style='text-decoration: underline;' href='panel_login.php'>Zaloguj się</a>, aby dołączyć do turnieju.</p>";
        }
    } elseif ($action == 'leave') {
        if (isset($_SESSION["logged"]) && $_SESSION["logged"] === true) {
            // Usuń zapis z tabeli tournament_registrations
            $stmt = $polaczenie->prepare("DELETE FROM tournament_registrations WHERE UserID = ? AND TournamentID = ?");
            $stmt->bind_param("ii", $userId, $tournamentId);

            if ($stmt->execute()) {
                echo "<p class='info'>Opuściłeś turniej!</p>";

                // Aktualizuj liczbę graczy w tabeli tournaments
                $updateStmt = $polaczenie->prepare("UPDATE tournaments SET PlayerCount = (SELECT COUNT(*) FROM tournament_registrations WHERE TournamentID = ?) WHERE TournamentID = ?");
                $updateStmt->bind_param("ii", $tournamentId, $tournamentId);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                echo "<p class='error'>Błąd opuszczania: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='info'>Musisz być zalogowany, aby opuścić turniej.</p>";
        }
    }
}

// Pobierz listę turniejów
$sql = "SELECT * FROM tournaments ORDER BY StartDate DESC";
$wynik = $polaczenie->query($sql);

// Ustawienie lokalizacji na polski aby miesiące wyświetlały się po polsku
setlocale(LC_TIME, 'pl_PL.UTF-8');

$currentMonth = "";
if ($wynik->num_rows > 0) {
    while ($row = $wynik->fetch_assoc()) {
        $startDate = new DateTime($row['StartDate']);
        $month = strftime('%B %Y', strtotime($row['StartDate'])); // Pobieramy miesiąc i rok

        // Jeśli zmienia się miesiąc, wyświetlamy nowy nagłówek
        if ($month !== $currentMonth) {
            if ($currentMonth !== "") {
                echo "</div>"; // Zamykamy poprzednią sekcję miesięczną
            }
            echo "<h3 class='month-header'>" . $month . "</h3>";
            echo "<div class='month-section'>";
            $currentMonth = $month;
        }

        echo "<div class='tournament-card'>";
        echo "<h3>{$row['TournamentName']}</h3>";
        echo "<p><strong>Data rozpoczęcia:</strong> {$row['StartDate']}</p>";
        echo "<p><strong>Lokalizacja:</strong> {$row['Location']}</p>";
        echo "<p><strong>Tempo gry:</strong> {$row['GameTempo']}</p>";
        echo "<p><strong>Organizator:</strong> {$row['Organizer']}</p>";

        // Zliczanie liczby graczy i rund z tabeli tournament_registrations
        $tournamentId = $row['TournamentID'];
        $countPlayersSql = "SELECT PlayerCount, CompletedRounds, TotalRounds FROM tournaments WHERE TournamentID = ?";
        $stmt = $polaczenie->prepare($countPlayersSql);
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $PlayerCount = $data['PlayerCount'];
        $CompletedRounds = $data['CompletedRounds'];
        $TotalRounds = $data['TotalRounds'];

        echo "<p><strong>Liczba graczy:</strong> {$PlayerCount}</p>";
        echo "<p><strong>Runda:</strong> {$CompletedRounds}/{$TotalRounds}</p>";


        // Sprawdzamy, czy użytkownik jest zalogowany
        if (!isset($_SESSION["logged"])) {
            echo "<p class='info'> <a style='text-decoration:underline;' href='panel_login.php'>Zaloguj się</a>, aby dołączyć do turnieju.</p>";
        } else {
            // Jeżeli użytkownik jest zalogowany, sprawdzamy, czy może dołączyć lub opuścić turniej
            $userId =  $_SESSION['UserID'];
            $tournamentID = $row['TournamentID'];
            $startDate = new DateTime($row['StartDate']);
            $currentDate = new DateTime();

            // Zapytanie, czy użytkownik jest już zapisany do turnieju
            $checkParticipantSql = "SELECT * FROM tournament_registrations WHERE TournamentID = ? AND UserID = ?";
            $stmt = $polaczenie->prepare($checkParticipantSql);
            $stmt->bind_param("ii", $tournamentId, $userId);
            $stmt->execute();
            $participantResult = $stmt->get_result();
            $isParticipant = $participantResult->num_rows > 0;

            if ($startDate > $currentDate) {
                if ($isParticipant) {
                    // Jeśli użytkownik jest już zapisany, wyświetlamy przycisk 'Opuść turniej'
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='tournament_id' value='{$tournamentID}'>";
                    echo "<input type='hidden' name='action' value='leave'>";
                    echo "<input type='submit' value='Opuść turniej' class='join-button' style='background-color: #711515;'>";
                    echo "</form>";
                } else {
                    // Jeśli turniej jeszcze się nie rozpoczął i użytkownik nie jest zapisany, przycisk 'Dołącz'
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='tournament_id' value='{$tournamentID}'>";
                    echo "<input type='hidden' name='action' value='join'>";
                    echo "<input type='submit' value='Dołącz do turnieju' class='join-button'>";
                    echo "</form>";
                }
            } else {
                // Jeśli turniej już się rozpoczął, zmieniamy styl przycisku na nieaktywny
                echo "<input type='button' value='Turniej już rozpoczęty' class='join-button disabled' disabled>";
            }

            $stmt->close();
        }

        // Przycisk do pokazania uczestników
        echo "<button onclick='toggleParticipants({$row['TournamentID']}, this)'>Pokaż uczestników</button>";
        echo "<div id='participants-{$row['TournamentID']}' class='participants' style='display:none;'>";

        // Zapytanie o uczestników
        $participantsSql = "SELECT 
                                u.Title, u.LastName, u.FirstName, u.Ranking, u.Club, u.Birthday 
                             FROM 
                                users u
                             JOIN 
                                tournament_registrations tr ON u.UserID = tr.UserID
                             WHERE 
                                tr.TournamentID = ?";
        $stmt = $polaczenie->prepare($participantsSql);
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Wyświetlanie uczestników
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Nr</th><th>Tytuł</th><th>Nazwisko Imię</th><th>Ranking</th><th>Klub</th><th>Data ur.</th></tr>";
            $counter = 1;
            while ($participant = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$counter}</td>"; // Wyświetla numer od 1
                echo "<td>{$participant['Title']}</td>";
                echo "<td>{$participant['LastName']}, {$participant['FirstName']}</td>";
                echo "<td>{$participant['Ranking']}</td>";
                echo "<td>{$participant['Club']}</td>";
                echo "<td>" . date("Y", strtotime($participant['Birthday'])) . "</td>";
                echo "</tr>";
                $counter++;
            }
            echo "</table>";
        } else {
            echo "Brak uczestników.";
        }
        echo "</div>";

        // Przycisk do pokazania wyników
        echo "<button onclick='toggleResults({$row['TournamentID']}, this)'>Pokaż wyniki</button>";
        echo "<div id='results-{$row['TournamentID']}' class='results' style='display:none;'>";

        // Zapytanie o wyniki turnieju
        $resultsSql = "
        SELECT tr.Position, tr.UserID, tr.Points, u.LastName, u.FirstName, u.Ranking, u.Club, u.Title
        FROM tournament_results tr
        JOIN users u ON tr.UserID = u.UserID
        WHERE tr.TournamentID = ?";

        $stmt = $polaczenie->prepare($resultsSql);
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Wyświetlanie wyników
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Miejsce</th><th>Tytuł</th><th>Nazwisko Imię</th><th>Ranking</th><th>Klub</th><th>Punkty</th></tr>";
            while ($resultRow = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$resultRow['Position']}</td>";
                echo "<td>{$resultRow['Title']}</td>";
                echo "<td>{$resultRow['LastName']}, {$resultRow['FirstName']}</td>";
                echo "<td>{$resultRow['Ranking']}</td>";
                echo "<td>{$resultRow['Club']}</td>";
                echo "<td>{$resultRow['Points']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Brak wyników.</p>";
        }
        echo "</div>";

        echo "</div>";
    }
} else {
    echo "<p>Brak turniejów.</p>";
}

$polaczenie->close();
?>

</div>

</div>

<?php include('../include/footer.php'); ?>

</body>
</html>

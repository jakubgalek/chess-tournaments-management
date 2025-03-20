<?php
session_start();
include("../scripts/connect.php");

$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$polaczenie->set_charset("utf8");

if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą danych: " . $polaczenie->connect_error);
}

// Parametry sortowania z wartościami domyślnymi
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'Ranking';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';
$allowedSortColumns = ['LastName', 'Club', 'Ranking', 'Birthday'];

// Sprawdź, czy kolumna sortująca jest dozwolona
$sort = in_array($sort, $allowedSortColumns) ? $sort : 'Ranking';
$nextOrder = $order === 'asc' ? 'desc' : 'asc';

// Obsługa wyszukiwania z wartością domyślną
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$searchParam = "%" . $searchQuery . "%";

// Zapytanie SQL z sortowaniem i wyszukiwaniem
$sql = "SELECT UserID, FirstName, LastName, Title, Ranking, Club, Birthday, ProfilePicture 
        FROM users 
        WHERE (FirstName LIKE ? OR LastName LIKE ? OR Club LIKE ?)
        ORDER BY $sort $order";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Rejestr członków - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/main_mobile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php include('../include/header.php'); ?>

<div class="content-container">
    <h3 class="section-title">Rejestr członków</h3>
    
    <div class="cont">
        <div class="search-sort-container">
            <!-- Formularz wyszukiwania -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Wyszukaj zawodnika..." value="<?php echo htmlspecialchars($searchQuery); ?>" class="search-input">
                <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
            </form>

            <!-- Formularz sortowania -->
            <form method="GET" action="" class="sort-form">
                <select name="sort" id="sort" class="search-input">
                    <option value="LastName" <?php if ($sort == 'LastName') echo 'selected'; ?>>Nazwisko</option>
                    <option value="Club" <?php if ($sort == 'Club') echo 'selected'; ?>>Klub</option>
                    <option value="Ranking" <?php if ($sort == 'Ranking') echo 'selected'; ?>>Ranking</option>
                    <option value="Birthday" <?php if ($sort == 'Birthday') echo 'selected'; ?>>Data urodzenia</option>
                </select>
                <select name="order" class="search-input">
                    <option value="asc" <?php if ($order == 'asc') echo 'selected'; ?>>Rosnąco</option>
                    <option value="desc" <?php if ($order == 'desc') echo 'selected'; ?>>Malejąco</option>
                </select>
                <button type="submit" class="search-button"><i class="fa fa-sort" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>

    <?php
    if ($result && $result->num_rows > 0) {
        echo "<div class='players-container'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='player-card'>";
            echo "<img src='" . htmlspecialchars($row['ProfilePicture']) . "' alt='Zdjęcie zawodnika' class='player-image'>";
            echo "<div class='player-info'>";
            echo "<p><strong>Imię:</strong> " . htmlspecialchars($row['FirstName']) . "</p>";
            echo "<p><strong>Nazwisko:</strong> " . htmlspecialchars($row['LastName']) . "</p>";
            echo "<p><strong>Tytuł:</strong> " . htmlspecialchars($row['Title']) . "</p>";
            echo "<p><strong>Ranking:</strong> " . htmlspecialchars($row['Ranking']) . "</p>";
            echo "<p><strong>Klub:</strong> " . htmlspecialchars($row['Club']) . "</p>";
            echo "<p><strong>Rok urodzenia:</strong> " . date('Y', strtotime($row['Birthday'])) . "</p>";
            echo "<p><strong>Identyfikator:</strong> " . htmlspecialchars($row['UserID']) . "</p>";
            echo "</div></div>";
        }
        echo "</div>";
    } else {
        echo "<p>Brak danych o zawodnikach.</p>";
    }
    $polaczenie->close();
    ?>
</div>

<?php include('../include/footer.php'); ?>

</body>
</html>

<?php
session_start();

if (!isset($_SESSION["logged"])) {
    header('Location: ../index.php');
    exit();
}

include("../scripts/connect.php");

// Inicjalizacja połączenia z bazą danych
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$polaczenie->set_charset("utf8");

if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą danych: " . $polaczenie->connect_error);
}

// Pobranie ID zalogowanego użytkownika z sesji
$userID = $_SESSION['UserID'];

// Zapytanie o historię rankingu dla zalogowanego gracza
$sql = "SELECT Ranking, ChangeDate FROM ranking_history WHERE UserID = ? ORDER BY HistoryID ASC";
$stmt = $polaczenie->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// Przygotowanie danych do wykresu liniowego
$rankings = [];
$dates = [];

while ($row = $result->fetch_assoc()) {
    $rankings[] = $row['Ranking'];
    $dates[] = date('d-m-Y', strtotime($row['ChangeDate']));
}

$stmt->close();

// Zapytanie o liczbę wygranych, przegranych i remisów
$sqlResults = "SELECT 
    SUM(CASE 
        WHEN (UserID = ? AND Result = 'win') OR (OpponentID = ? AND Result = 'loss') THEN 1 
        ELSE 0 
    END) AS wins,
    SUM(CASE 
        WHEN (UserID = ? AND Result = 'loss') OR (OpponentID = ? AND Result = 'win') THEN 1 
        ELSE 0 
    END) AS losses,
    SUM(CASE 
        WHEN Result = 'draw' THEN 1 
        ELSE 0 
    END) AS draws
FROM round_results
WHERE UserID = ? OR OpponentID = ?";

$stmtResults = $polaczenie->prepare($sqlResults);
$stmtResults->bind_param("iiiiii", $userID, $userID, $userID, $userID, $userID, $userID);
$stmtResults->execute();
$resultResults = $stmtResults->get_result();

$resultsData = $resultResults->fetch_assoc();
$wins = $resultsData['wins'] ?? 0;
$losses = $resultsData['losses'] ?? 0;
$draws = $resultsData['draws'] ?? 0;

$stmtResults->close();

// Obliczenie zmiany rankingu
$rankingChange = isset($rankings) && count($rankings) > 1 ? $rankings[count($rankings) - 1] - $rankings[count($rankings) - 2] : 0;
$rankingChange = $rankingChange >= 0 ? "+$rankingChange" : "$rankingChange"; // Formatowanie zmiany rankingu

// Zapytanie o przeciwników
$sqlOpponents = "SELECT DISTINCT 
            CASE 
                WHEN UserID = ? THEN OpponentID 
                ELSE UserID 
            END AS OpponentID 
        FROM round_results 
        WHERE UserID = ? OR OpponentID = ?";

$stmtOpponents = $polaczenie->prepare($sqlOpponents);
$stmtOpponents->bind_param("iii", $userID, $userID, $userID);
$stmtOpponents->execute();
$resultOpponents = $stmtOpponents->get_result();

$opponents = [];
while ($row = $resultOpponents->fetch_assoc()) {
    $opponents[] = $row['OpponentID'];
}
$stmtOpponents->close();

// Jeśli jest żądanie przeciwnika, zwróć dane
if (isset($_GET['opponentID'])) {
    $opponentID = intval($_GET['opponentID']);

// Pobranie liczby wygranych, przegranych i remisów dla aktualnego przeciwnika
$sqlHeadToHead = "SELECT 
        SUM(CASE 
            WHEN (UserID = ? AND OpponentID = ? AND Result = 'win') OR (UserID = ? AND OpponentID = ? AND Result = 'loss') THEN 1 
            ELSE 0 
        END) AS wins,
        SUM(CASE 
            WHEN (UserID = ? AND OpponentID = ? AND Result = 'loss') OR (UserID = ? AND OpponentID = ? AND Result = 'win') THEN 1 
            ELSE 0 
        END) AS losses,
        SUM(CASE 
            WHEN (UserID = ? AND OpponentID = ? AND Result = 'draw') OR (UserID = ? AND OpponentID = ? AND Result = 'draw') THEN 1 
            ELSE 0 
        END) AS draws
    FROM round_results
    WHERE (UserID = ? AND OpponentID = ?) OR (UserID = ? AND OpponentID = ?)";

$stmtHeadToHead = $polaczenie->prepare($sqlHeadToHead);
$stmtHeadToHead->bind_param("iiiiiiiiiiiiiiii", $userID, $opponentID, $opponentID, $userID, $userID, $opponentID, $opponentID, $userID, $userID, $opponentID, $opponentID, $userID, $userID, $opponentID, $opponentID, $userID);
$stmtHeadToHead->execute();
$resultHeadToHead = $stmtHeadToHead->get_result();

$data = $resultHeadToHead->fetch_assoc();
echo json_encode([
    'wins' => $data['wins'] ?? 0,
    'losses' => $data['losses'] ?? 0,
    'draws' => $data['draws'] ?? 0
]);

$stmtHeadToHead->close();
$polaczenie->close();
exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Statystyki - TurniejeSzachowe</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/account.css">
        <link rel="stylesheet" href="../css/main_mobile.css">
    <script src="../lib/chart.js"></script>
</head>
<body>
<?php include('../include/header.php'); ?>

<div class="main-container">
    <?php include('../include/account_side_menu.php'); ?>

    <div class="table-container">
        <div class="chart-container">
    <div class="wide-chart">
        <h3>Historia rankingu</h3>
        <canvas id="rankingChart"></canvas>
    </div>
    
    <div class="side-by-side-charts">
        <div class="pie-chart">
            <h3>Bilans partii z przeciwnikiem</h3>
            <canvas id="headToHeadChart"></canvas>
            
            <h5>Wybierz przeciwnika</h5>
            <select class="form-text-input" id="opponentSelect" onchange="updateOpponentData()">
                <?php
                // Pobranie listy przeciwników po imieniu i nazwisku
                $sqlOpponents = "SELECT DISTINCT OpponentID, FirstName, LastName FROM round_results 
                                 JOIN users ON round_results.OpponentID = users.UserID 
                                 WHERE round_results.UserID = ?";
                $stmtOpponents = $polaczenie->prepare($sqlOpponents);
                $stmtOpponents->bind_param("i", $userID);
                $stmtOpponents->execute();
                $resultOpponents = $stmtOpponents->get_result();

                while ($row = $resultOpponents->fetch_assoc()) {
                    echo '<option value="' . $row['OpponentID'] . '">' . $row['FirstName'] . ' ' . $row['LastName'] . '</option>';
                }

                $stmtOpponents->close();
                ?>
            </select>
        </div>

        <div class="pie-chart">
            <h3>Bilans wszystkich partii</h3>
            <canvas id="resultsPieChart"></canvas>
        </div>  
</div>

        </div>
    </div>

    <script>
    // Przekazanie danych z PHP do JavaScript
    const labels = <?php echo json_encode($dates); ?>;
    const data = <?php echo json_encode($rankings); ?>;
    const wins = <?php echo $wins; ?>;
    const losses = <?php echo $losses; ?>;
    const draws = <?php echo $draws; ?>;
    const currentRanking = data[data.length - 1];
    const rankingChange = '<?php echo $rankingChange; ?>';

    // Inicjalizacja wykresu liniowego dla historii rankingu
    const ctx = document.getElementById('rankingChart').getContext('2d');
    const rankingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: `Ranking: ${currentRanking} (${rankingChange})`,
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 4,
                fill: false,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    enabled: false
                }
            },
            animation: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Data',
                        font: {
                            size: 14
                        }
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Ranking',
                        font: {
                            size: 14
                        }
                    },
                    beginAtZero: false,
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Dane do wykresu kołowego liczby wygranych, przegranych i remisów
    const pieData = {
        labels: [
            `Wygrane: ${wins} (${((wins / (wins + losses + draws)) * 100).toFixed(2)}%)`,
            `Przegrane: ${losses} (${((losses / (wins + losses + draws)) * 100).toFixed(2)}%)`,
            `Remisy: ${draws} (${((draws / (wins + losses + draws)) * 100).toFixed(2)}%)`
        ],
        datasets: [{
            data: [wins, losses, draws],
            backgroundColor: ['#60cf4d', '#FF5A47', 'gray'],
            hoverOffset: 4
        }]
    };

    // Inicjalizacja wykresu kołowego dla wyników ogólnych
    const pieCtx = document.getElementById('resultsPieChart').getContext('2d');
    const resultsPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    enabled: true
                }
            },
            animation: false
        }
    });

// Zmienna do przechowywania instancji wykresu
let headToHeadChart;

function updateOpponentData() {
    const opponentID = document.getElementById('opponentSelect').value;
    if (opponentID) {
        fetch(`?opponentID=${opponentID}`)
            .then(response => response.json())
            .then(data => {
                if (headToHeadChart) headToHeadChart.destroy();

                const ctx = document.getElementById('headToHeadChart').getContext('2d');
                headToHeadChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Wygrane', 'Przegrane', 'Remisy'],
                        datasets: [{
                            data: [data.wins, data.losses, data.draws],
                            backgroundColor: ['#60cf4d', '#FF5A47', 'gray']
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12 
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        animation: false
                    },
                });
            })
            .catch(err => console.error('Error:', err));
    }
}

updateOpponentData();
</script>

<style>
.side-menu #account_statistics {
    border: 1px solid #8f8f8f;
}
</style>
</body>
</html>

<?php
session_start();

$password = 'steen';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Ongeldig wachtwoord';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Database connection
$host = 'localhost';
$db   = 'Afwas';
$user = 'root';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Handle score reset
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_scores'])) {
        $pdo->query('UPDATE score SET score = 0, updated = "", date = 0');
        // Optional: Clear the log as well
        // $pdo->query('TRUNCATE TABLE updatelog');
        header('Location: admin.php');
        exit;
    }

    $logStmt = $pdo->query('SELECT * FROM updatelog ORDER BY date DESC');
    $logs = $logStmt->fetchAll();

    $scoreStmt = $pdo->query('SELECT naam, score FROM score ORDER BY id');
    $scoresData = $scoreStmt->fetchAll();
    $chartLabels = json_encode(array_column($scoresData, 'naam'));
    $chartScores = json_encode(array_column($scoresData, 'score'));
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-4 md:p-8">
        <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
            <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
                <h1 class="text-2xl font-bold text-center mb-6">Admin Login</h1>
                <form method="post">
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-bold mb-2">Wachtwoord:</label>
                        <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <?php if ($error): ?>
                        <p class="text-red-500 text-xs italic mb-4"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Inloggen
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="text-right mb-6">
                <a href="?logout=true" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    Uitloggen
                </a>
            </div>
            <h1 class="text-3xl font-bold text-center mb-8">Welkom, Admin</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Score Overzicht</h2>
                    <canvas id="scoreChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Activiteitenlogboek</h2>
                    <div class="overflow-y-auto h-96">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white sticky top-0">
                                <tr>
                                    <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Naam</th>
                                    <th class="w-1/2 text-left py-3 px-4 uppercase font-semibold text-sm">Datum en Tijd</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="text-left py-3 px-4 border-b border-gray-200"><?php echo htmlspecialchars($log['naam']); ?></td>
                                        <td class="text-left py-3 px-4 border-b border-gray-200"><?php echo htmlspecialchars($log['date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Beheer</h2>
                <form method="post" onsubmit="return confirm('Weet je zeker dat je alle scores wilt resetten? Deze actie kan niet ongedaan worden gemaakt.');">
                    <input type="hidden" name="reset_scores" value="1">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                        Reset Alle Scores
                    </button>
                </form>
            </div>

            <script>
                const ctx = document.getElementById('scoreChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo $chartLabels; ?>,
                        datasets: [{
                            label: 'Score',
                            data: <?php echo $chartScores; ?>,
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            </script>

        <?php endif; ?>
    </div>

</body>
</html>

<?php

// Set the locale to Dutch for date formatting
setlocale(LC_TIME, 'nl_NL.UTF-8', 'Dutch');

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
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

// Fetch all people from the database to dynamically handle submissions
$stmt = $pdo->query('SELECT * FROM score ORDER BY id');
$people = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($people as $person) {
        if (isset($_POST[$person['naam']])) {
            $score = $person['score'] + 1;
            $newdate = strftime('%A %d %B %Y %H:%M:%S');
            $newdbdate = time();

            $updateStmt = $pdo->prepare("UPDATE `score` SET `score` = ?, `updated` = ?, `date` = ? WHERE `id` = ?");
            $updateStmt->execute([$score, $newdate, $newdbdate, $person['id']]);

            $logStmt = $pdo->prepare("INSERT INTO `updatelog` VALUES (?, ?)");
            $logStmt->execute([$person['naam'], $newdate]);

            header("Location: main.php");
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afwassen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center mb-8">Afwas Tracker</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($people as $person): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <h2 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($person['naam']); ?></h2>
                    <p class="text-5xl font-extrabold text-indigo-600 mb-4"><?php echo $person['score']; ?></p>
                    <div class="text-gray-800 font-medium">
                        <?php
                            echo "Heeft " . $person['score'] . " keer afgewast.";
                            if (!empty($person['updated'])) {
                                echo "<br>Laatst afgewassen op: " . $person['updated'];
                            }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" class="mt-8 flex justify-center flex-wrap gap-4">
            <?php foreach ($people as $person): ?>
                <button type="submit" name="<?php echo htmlspecialchars($person['naam']); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                    <?php echo htmlspecialchars($person['naam']); ?> heeft afgewassen
                </button>
            <?php endforeach; ?>
        </form>

        <div class="text-center mt-8 text-gray-500">
            <p><?php echo strftime('%A %d %B %Y %H:%M:%S'); ?></p>
        </div>
    </div>

</body>
</html>

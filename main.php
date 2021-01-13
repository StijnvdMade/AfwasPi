<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score</title>
    <style>
    input {
        font-family: "Open Sans", Sans-serif;
    font-weight: 900;
    font-size: 16px;
    background-color: #FFFFFF;
    border-style: solid;
    border-width: 3px 3px 3px 3px;
    border-color: #000000;
    padding: 10px 30px 16px 30px;
    cursor: pointer;
    border-radius: 4px;
    }
    body {
        font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #333;
    }
    </style>
</head>
<body>

<?php

$host = 'localhost:3306';
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
// get number of id's
$amount = 0;
$stmt = $pdo->query('SELECT * FROM score;');
while ($row = $stmt->fetch())
{
    $amount++;
}
//  Load score from database
$data = $pdo->query('SELECT * FROM score')->fetch(PDO::FETCH_ASSOC);
for ($i=1; $i < $amount+1; $i++) { 
    $data = $pdo->query('SELECT * FROM score WHERE id=' . $i)->fetch(PDO::FETCH_ASSOC);
    $id = $data['id'];
    $naam = $data['naam'];
    $score = $data['score'];
    $updated = $data['updated'];
    $dbdate = $data['date'];
    echo($naam . " heeft " . $score . " keer afgewast. Laatst afgewassen op " . $updated . " voor het laatst afgewast </br></br></br></br>");
    // $pcdate = strtotime($dbdate);
    // // echo($dbdate . PHP_EOL);
    // $newtime = time();
    // // echo($newtime);
    // if(strtotime($pcdate+60) > $newtime) {
    //     echo(strtotime($pcdate+60) . $newtime);
    //     echo 'yes<br/>';
    // } else {
    //     echo 'no<br/>';
    //     echo(strtotime($pcdate+60) . $newtime);
    // }
    
    
    
    // $dateFromDatabase = strtotime($dbdate);
    // $dateTwelveHoursAgo = strtotime("-12 hours");
    // echo($dateFromDatabase);
    // if ($dateFromDatabase >= $dateTwelveHoursAgo) {
    //     echo '<style type="text/css">
    //     form {
    //         display: none;
    //     }
    //     </style>';
    // }
}

if(isset($_POST['Stijn'])) { 
    echo "Stijn heeft afgewassen"; 
    $data = $pdo->query('SELECT * FROM score WHERE id=1')->fetch(PDO::FETCH_ASSOC);
    $score = $data['score'];
    $score1 = $score+1;
    $newdate = date('l jS \of F Y H:i:s');
    $newdbdate = time();
    $pdo->query("UPDATE `score` SET `score` = " . $score1 .  " WHERE `score`.`id` = 1;");
    $pdo->query("UPDATE `score` SET `updated` = '" . $newdate . "'WHERE `score`.`id` = 1;");
    $pdo->query("UPDATE `score` SET `date` = '" . $newdbdate . "'WHERE `score`.`id` = 1;");
    $sql = "INSERT INTO `updatelog` VALUES (? , ?);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["Stijn", $newdate]);
    header("Location: main.php");
    exit();
} 
if(isset($_POST['Koen'])) { 
    echo "Koen heeft afgewassen"; 
    $data = $pdo->query('SELECT * FROM score WHERE id=2')->fetch(PDO::FETCH_ASSOC);
    $score = $data['score'];
    $score1 = $score+1;
    $newdate = date('l jS \of F Y H:i:s');
    $newdbdate = time();
    $pdo->query("UPDATE `score` SET `score` = " . $score1 .  " WHERE `score`.`id` = 2;");
    $pdo->query("UPDATE `score` SET `updated` = '" . $newdate . "'WHERE `score`.`id` = 2;");
    $pdo->query("UPDATE `score` SET `date` = '" . $newdbdate . "'WHERE `score`.`id` = 2;");
    $sql = "INSERT INTO `updatelog` VALUES (? , ?);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["Koen", $newdate]);
    header("Location: main.php");
    exit();
} 
if(isset($_POST['Jenny'])) { 
    echo "Jenny heeft afgewassen"; 
    $data = $pdo->query('SELECT * FROM score WHERE id=3')->fetch(PDO::FETCH_ASSOC);
    $score = $data['score'];
    $score1 = $score+1;
    $newdate = date('l jS \of F Y H:i:s');
    $newdbdate = time();
    $pdo->query("UPDATE `score` SET `score` = " . $score1 .  " WHERE `score`.`id` = 3;");
    $pdo->query("UPDATE `score` SET `updated` = '" . $newdate . "'WHERE `score`.`id` = 3;");
    $pdo->query("UPDATE `score` SET `date` = '" . $newdbdate . "'WHERE `score`.`id` = 3;");
    $sql = "INSERT INTO `updatelog` VALUES (? , ?);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["Jenny", $newdate]);
    header("Location: main.php");
    exit();
} 
echo date('l jS \of F Y H:i:s');




?>
<form method="post"> 
    <input type="submit" name="Stijn"
        value="Stijn"/> 
        
    <input type="submit" name="Koen"
            value="Koen"/> 
    <input type="submit" name="Jenny"
            value="Jenny"/> 
</form> 
</body>
</html>

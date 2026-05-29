
<?php

$host = 'localhost';
$dbname = 'egliselystaberna_data';
$username = 'egliselystaberna_user';
$password = ']8H(ZHJ907CS0ijo';

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die('Erreur DB : ' . $e->getMessage());
}
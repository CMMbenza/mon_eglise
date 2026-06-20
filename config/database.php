
<?php

$host = 'localhost';
$dbname = 'egliselystaberna_data';
$username = 'egliselystaberna_user';
$password = 'u3E,E10w9Ke7%$h5';

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
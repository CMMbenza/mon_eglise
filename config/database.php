
<?php

$host = 'localhost';
$dbname = 'yebananotechgrou_mon_eglise';
$username = 'yebananotechgrou_mon_eglise';
$password = 'a%GFtQbFYqd4ixjg';

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
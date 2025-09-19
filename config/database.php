<?php
$host = 'mysql5027.site4now.net';
$dbname = 'db_9b86be_arpusof';
$username = '9b86be_arpusof';
$password = 'arpusoft@123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
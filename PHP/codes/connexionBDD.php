<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation_salle";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname", $username, $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

} catch (PDOException $e) {
    echo "connection failed: " . $e->getMessage();   
}

?>
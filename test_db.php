<?php
try {
    require_once 'config/database.php';
    echo "SUCCESS: Conexiunea la baza de date funcționează!<br>";
    echo "Nume BD: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "User: " . DB_USER . "<br>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
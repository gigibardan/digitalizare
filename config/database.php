<?php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'numele_tau_evaluation_system'); // Schimbă cu numele real
define('DB_USER', 'numele_tau_eval_user'); // Schimbă cu numele real
define('DB_PASS', 'parola_ta'); // Schimbă cu parola reală

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Eroare la conectarea la baza de date. Contactați administratorul.");
}
?>
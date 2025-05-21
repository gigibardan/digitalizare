<?php
// Includem sistemul de autentificare
require_once 'includes/auth.php';

// Înregistrăm deconectarea
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    
    $log_dir = __DIR__ . '/logs';
    
    // Verificăm dacă directorul există
    if (file_exists($log_dir)) {
        // Creăm fișierul de log
        $log_file = $log_dir . '/login_log.txt';
        
        // Informații pentru log
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Creăm înregistrarea în log
        $log_entry = "[LOGOUT] $timestamp | User: $username | IP: $ip\n";
        
        // Scriem în fișierul de log
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}

// Ștergem sesiunea
session_unset();
session_destroy();

// Redirecționăm la pagina principală
header('Location: /index.html');
exit();
?>
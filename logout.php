<?php
// Includem sistemul de autentificare și configurația
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Înregistrăm deconectarea
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    
    // Folosim funcția de logging din config.php care include numele complet
    logEvent('LOGOUT', $username);
}

// Ștergem sesiunea complet
session_unset();
session_destroy();

// Ștergem și cookie-ul de sesiune pentru securitate maximă
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirecționăm la pagina principală
header('Location: /index.html');
exit();
?>
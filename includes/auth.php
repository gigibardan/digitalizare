<?php
// Inițializăm sesiunea
session_start();
require_once 'config.php';

// Funcție pentru verificarea autentificării
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['login_time']);
}

// Funcție pentru verificarea expirării sesiunii
function isSessionValid() {
    global $session_timeout;
    if (!isLoggedIn()) return false;
    
    $current_time = time();
    $session_time = $_SESSION['login_time'];
    
    // Verificăm dacă sesiunea a expirat
    if ($current_time - $session_time > $session_timeout) {
        // Sesiunea a expirat, deconectăm utilizatorul
        session_unset();
        session_destroy();
        return false;
    }
    
    // Actualizăm timpul de sesiune
    $_SESSION['login_time'] = $current_time;
    return true;
}

// Funcție pentru verificarea dacă o pagină este publică
function isPublicPage($page) {
    global $public_pages;
    $page = basename($page);
    return in_array($page, $public_pages);
}

// Funcție pentru verificarea paginii curente
function checkAccess() {
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Dacă pagina este publică, permitem accesul
    if (isPublicPage($current_page)) {
        return true;
    }
    
    // Verificăm dacă utilizatorul este autentificat
    if (isSessionValid()) {
        return true;
    }
    
    // Utilizatorul nu este autentificat, redirecționăm la pagina restricționată
    header('Location: /restricted.php');
    exit();
}
?>
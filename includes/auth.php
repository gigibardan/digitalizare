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
    
    // Normalizăm calea, eliminând / de la început
    $page = ltrim($page, '/');
    
    // Verificăm dacă pagina este în lista paginilor publice
    return in_array($page, $public_pages);
}

// Adaugă această funcție în auth.php
function isAdmin() {
    // Verifică dacă utilizatorul este logat
    if (!isSessionValid()) {
        return false;
    }
    
    // Lista utilizatorilor admin
    $admin_users = ['admin', 'user1']; // Adaugă aici utilizatorii care ar trebui să aibă acces de admin
    
    // Verifică dacă utilizatorul curent este în lista de admini
    return in_array($_SESSION['user'], $admin_users);
}
?>
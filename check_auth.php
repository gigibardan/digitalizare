<?php
// Includ sistemul de autentificare
require_once 'includes/auth.php';

// Setăm header-ul pentru JSON și prevenim cache-ul
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

// Verificăm dacă funcția isAdmin există, dacă nu, o definim
if (!function_exists('isAdmin')) {
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
}

// Returnăm starea autentificării și informații despre admin
if (isSessionValid()) {
    echo json_encode([
        'authenticated' => true,
        'username' => $_SESSION['user'],
        'isAdmin' => isAdmin()
    ]);
} else {
    echo json_encode([
        'authenticated' => false,
        'isAdmin' => false
    ]);
}
exit;
?>
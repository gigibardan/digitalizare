<?php
// Includ sistemul de autentificare
require_once 'includes/auth.php';

// Setăm header-ul pentru JSON și prevenim cache-ul
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

// Returnăm starea autentificării
echo json_encode(['authenticated' => isSessionValid()]);
exit;
?>
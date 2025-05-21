<?php
// Includem sistemul de autentificare
require_once 'includes/auth.php';

// Ștergem sesiunea
session_unset();
session_destroy();

// Redirecționăm la pagina principală
header('Location: /index.html');
exit();
?>
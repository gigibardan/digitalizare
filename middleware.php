<?php
// Includem sistemul de autentificare
require_once 'includes/auth.php';

// Obținem calea cerută
$requested_path = $_SERVER['REQUEST_URI'];

// Extragem numele fișierului din cale
$file_path = parse_url($requested_path, PHP_URL_PATH);
$file_name = basename($file_path);

// Verificăm dacă este o pagină publică
if (!isPublicPage($file_name) && !isPublicPage($file_path)) {
    // Verificăm dacă utilizatorul este autentificat
    if (!isSessionValid()) {
        // Redirecționare la pagina de acces restricționat
        header('Location: /restricted.php');
        exit();
    }
}

// Dacă ajungem aici, permitem accesul la pagina solicitată
// Și continuăm cu procesarea normală
?>
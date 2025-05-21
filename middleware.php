<?php
// Includem sistemul de autentificare
require_once 'includes/auth.php';

// Obținem calea cerută
$requested_path = $_SERVER['REQUEST_URI'];

// Extragem numele fișierului din cale
$file_path = parse_url($requested_path, PHP_URL_PATH);
$file_name = basename($file_path);
$full_path = ltrim($file_path, '/'); // Normalizăm calea pentru a o compara cu $public_pages

// Verificăm dacă este o pagină publică
if (!in_array($full_path, $public_pages)) {
    // Verificăm dacă utilizatorul este autentificat
    if (!isSessionValid()) {
        // Redirecționare la pagina de acces restricționat
        header('Location: /restricted.php');
        exit();
    }
}

// Servim conținutul paginii originale
$requested_file = $_SERVER['DOCUMENT_ROOT'] . $file_path;

// Dacă fișierul există, îl servim
if (file_exists($requested_file)) {
    // Determinăm tipul conținutului
    $extension = pathinfo($requested_file, PATHINFO_EXTENSION);
    
    switch ($extension) {
        case 'html':
            header('Content-Type: text/html');
            break;
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        // Adăugă alte tipuri după necesitate
    }
    
    // Output conținutul fișierului
    readfile($requested_file);
    exit();
} else {
    // Fișierul nu există
    header("HTTP/1.0 404 Not Found");
    echo "Pagina solicitată nu a fost găsită.";
    exit();
}
?>
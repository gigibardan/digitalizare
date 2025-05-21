<?php
// Definim utilizatorii și parolele (parolele trebuie să fie hash-uri)
$users = [
    'user1' => password_hash('parola1', PASSWORD_DEFAULT),
    'user2' => password_hash('parola2', PASSWORD_DEFAULT),
    // Adaugă mai mulți utilizatori după nevoie
];

// Configurări pentru sesiune
$session_timeout = 7200; // 2 ore în secunde

// Adresa de email pentru contact
$contact_email = 'office@techminds-academy.ro';

// Pagina index (care nu va fi protejată)
$public_pages = ['index.html', 'index.php', 'login.php', 'restricted.php'];
?>
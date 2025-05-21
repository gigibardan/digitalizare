<?php
// Definim utilizatorii și parolele (parolele trebuie să fie hash-uri)
$users = [
    'admin' => password_hash('techminds', PASSWORD_DEFAULT),
    'user1' => password_hash('parola1', PASSWORD_DEFAULT),
    'user2' => password_hash('parola2', PASSWORD_DEFAULT),
    'user3' => password_hash('parola3', PASSWORD_DEFAULT),
    // Adaugă mai mulți utilizatori după nevoie
];

// Configurări pentru sesiune
$session_timeout = 7200; // 2 ore în secunde

// Adresa de email pentru contact
$contact_email = 'office@techminds-academy.ro';

// Definim paginile publice (care nu vor fi protejate)
$public_pages = [
    'index.html',
    'index.php',
    'login.php',
    'restricted.php',
    'check_auth.php',
    // Doar pagina index din fiecare modul (dacă dorești să fie accesibile)
    //'modules/module1/index.html',
    //'modules/module2/index.html',
   // 'modules/module3/index.html',
    //'modules/module4/index.html',
   // 'modules/module5/index.html',
   // 'modules/module6/index.html'
];
?>
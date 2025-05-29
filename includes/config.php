<?php
// Definim utilizatorii și parolele (parolele trebuie să fie hash-uri)
$users = [
    // Administrator
    'admin' => password_hash('techminds', PASSWORD_DEFAULT),
    
    // Utilizatori ȘCOALA GIMNAZIALĂ SMARDIOASA
    // Toți utilizatorii au parola: scoalasmardioasa
    'slavu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Slavu Lorelai Mihaela
    'petre.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Petre Elena  
    'cristea.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),    // Cristea Olimpia
    'zahariea.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),   // Zahariea Cristina
    'ionescu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),    // Ionescu Ileana
    'raportaru.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),  // Raportaru Aurelian
    'paun.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),       // Păun Roxana Elena
    'bratu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Bratu Angelica Mihaela
    'urucu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Urucu Angelica Mădălina
    'dociu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Dociu Mihaela Simona
    'dragomirescu.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT), // Dragomirescu Alina
    'lazarica.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),   // Lăzărică Liliana
    'joinel.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),     // Joinel Mihaela
    'avram.smardioasa' => password_hash('scoalasmardioasa', PASSWORD_DEFAULT),      // Avram Roxana Madalina
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
    //'modules/module3/index.html',
    //'modules/module4/index.html',
    //'modules/module5/index.html',
    //'modules/module6/index.html'
];

/* 
LISTĂ UTILIZATORI ȘCOALA GIMNAZIALĂ SMARDIOASA:
================================================
Username: slavu.smardioasa      | Nume: Slavu Lorelai Mihaela     | Email: lory.slavu@gmail.com
Username: petre.smardioasa      | Nume: Petre Elena               | Email: elis_2u@yahoo.com
Username: cristea.smardioasa    | Nume: Cristea Olimpia           | Email: cristea.olimpia1964@yahoo.com
Username: zahariea.smardioasa   | Nume: Zahariea Cristina         | Email: cristina.zahariea@yahoo.com
Username: ionescu.smardioasa    | Nume: Ionescu Ileana            | Email: ionescuileana1981@yahoo.com
Username: raportaru.smardioasa  | Nume: Raportaru Aurelian        | Email: aurelianraportaru@yahoo.com
Username: paun.smardioasa       | Nume: Păun Roxana Elena         | Email: roxanapaun33@gmail.com
Username: bratu.smardioasa      | Nume: Bratu Angelica Mihaela    | Email: bratumihaelaangelica@yahoo.com
Username: urucu.smardioasa      | Nume: Urucu Angelica Mădălina   | Email: urucu.madalina@gmail.com
Username: dociu.smardioasa      | Nume: Dociu Mihaela Simona      | Email: dociumihaelasimona@yahoo.com
Username: dragomirescu.smardioasa | Nume: Dragomirescu Alina      | Email: alinutad76@yahoo.com
Username: lazarica.smardioasa   | Nume: Lăzărică Liliana          | Email: lazaricaliliana134@gmail.com
Username: joinel.smardioasa     | Nume: Joinel Mihaela            | Email: joinel_mihaela@yahoo.com
Username: avram.smardioasa      | Nume: Avram Roxana Madalina     | Email: avram_roxana2009@yahoo.com

PAROLA COMUNĂ PENTRU TOȚI: scoalasmardioasa
*/
?>
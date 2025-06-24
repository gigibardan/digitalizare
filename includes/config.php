<?php
// Definim utilizatorii și parolele (parolele trebuie să fie hash-uri)
$users = [
    // Administrator
    'admin' => password_hash('techminds', PASSWORD_DEFAULT),
    
    // ȘCOALA GIMNAZIALĂ SMARDIOASA
    // Toți utilizatorii au parola: scoalasmardioasa
    'gigitest' => password_hash('gigitest', PASSWORD_DEFAULT),      // Gigi Test
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
    
    // ȘCOALA PROFESIONALĂ COZMEȘTI
    // Toți utilizatorii au parola: scoalacozmesti
    'chescu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Cheșcu Mioara
    'baltag.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Baltag Adina
    'guralivu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),       // Guralivu Daniela
    'agavriloaiei.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),   // Agavriloaiei Pompilia
    'tanase.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Tănase Gheorghe
    'piriiala.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),       // Pîrîială Constantin
    'iacob.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),          // Iacob Cristina
    'rusu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),           // Rusu Claudiu
    'piriialaim.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),     // Pîrîială Mihaela
    'ifrim.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),          // Ifrim Adina-Cosmina
    'galita.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Găliță Mihai-Florin
    'chiriac.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),        // Chiriac Ecaterina
    'ungureanu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),      // Ungureanu Valentina
    'andrii.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Andrii Doru Emil
    'samoila.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),        // Samoilă Ștefania
    'puscasu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),        // Pușcașu Gabriela
    'verdes.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),         // Verdeș-Enculescu Mirela-Elena
    'lupu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),           // Lupu Silviu
    'pascariu.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),       // Pascariu-Iliescu Mihaela
    'policiuc.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),       // Policiuc Andreea
    'aniculaesei.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),    // Aniculaesei Patricia-Maria
    
    // ȘCOALA GIMNAZIALĂ PETREȘTI
    // Toți utilizatorii au parola: scoalapetresti
    'muresanu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),       // Mureșanu T. Marius (Director)
    'tatuc.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Tătuc V. Gabriela
    'copilu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Copilu I. Ion-Florin
    'oancea.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Oancea-Anghelescu N. Monica
    'spinu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Spînu V. Liliana
    'popescu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Popescu C. Maria-Amalia
    'dita.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),           // Diţă I. Nicoleta-Manuela
    'craciun.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Crăciun I. Cecilia-Oana
    'topor.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Topor M. Elena
    'spinuc.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Spînu C. Ancuța-Elena
    'calin.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Călin G. Constantin
    'rotaru.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Rotaru D. Mihaela-Carmen
    'hodorogea.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),      // Hodorogea I. Ana-Maria
    'stoian.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Stoian L. Marius
    'nicolae.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Nicolae P. Marioara
    'blendea.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Blendea G. Valentina-Florentina
];

// MAPAREA NUMELOR COMPLETE PENTRU LOGURI
$user_full_names = [
    'admin' => 'Administrator',
    'gigitest' => 'Gigi Test',
    
    // ȘCOALA GIMNAZIALĂ SMARDIOASA
    'slavu.smardioasa' => 'Slavu Lorelai Mihaela',
    'petre.smardioasa' => 'Petre Elena',
    'cristea.smardioasa' => 'Cristea Olimpia',
    'zahariea.smardioasa' => 'Zahariea Cristina',
    'ionescu.smardioasa' => 'Ionescu Ileana',
    'raportaru.smardioasa' => 'Raportaru Aurelian',
    'paun.smardioasa' => 'Păun Roxana Elena',
    'bratu.smardioasa' => 'Bratu Angelica Mihaela',
    'urucu.smardioasa' => 'Urucu Angelica Mădălina',
    'dociu.smardioasa' => 'Dociu Mihaela Simona',
    'dragomirescu.smardioasa' => 'Dragomirescu Alina',
    'lazarica.smardioasa' => 'Lăzărică Liliana',
    'joinel.smardioasa' => 'Joinel Mihaela',
    'avram.smardioasa' => 'Avram Roxana Madalina',
    
    // ȘCOALA PROFESIONALĂ COZMEȘTI
    'chescu.cozmesti' => 'Cheșcu Mioara',
    'baltag.cozmesti' => 'Baltag Adina',
    'guralivu.cozmesti' => 'Guralivu Daniela',
    'agavriloaiei.cozmesti' => 'Agavriloaiei Pompilia',
    'tanase.cozmesti' => 'Tănase Gheorghe',
    'piriiala.cozmesti' => 'Pîrîială Constantin',
    'iacob.cozmesti' => 'Iacob Cristina',
    'rusu.cozmesti' => 'Rusu Claudiu',
    'piriialaim.cozmesti' => 'Pîrîială Mihaela',
    'ifrim.cozmesti' => 'Ifrim Adina-Cosmina',
    'galita.cozmesti' => 'Găliță Mihai-Florin',
    'chiriac.cozmesti' => 'Chiriac Ecaterina',
    'ungureanu.cozmesti' => 'Ungureanu Valentina',
    'andrii.cozmesti' => 'Andrii Doru Emil',
    'samoila.cozmesti' => 'Samoilă Ștefania',
    'puscasu.cozmesti' => 'Pușcașu Gabriela',
    'verdes.cozmesti' => 'Verdeș-Enculescu Mirela-Elena',
    'lupu.cozmesti' => 'Lupu Silviu',
    'pascariu.cozmesti' => 'Pascariu-Iliescu Mihaela',
    'policiuc.cozmesti' => 'Policiuc Andreea',
    'aniculaesei.cozmesti' => 'Aniculaesei Patricia-Maria',
    
    // ȘCOALA GIMNAZIALĂ PETREȘTI
    'muresanu.petresti' => 'Mureșanu T. Marius',
    'tatuc.petresti' => 'Tătuc V. Gabriela',
    'copilu.petresti' => 'Copilu I. Ion-Florin',
    'oancea.petresti' => 'Oancea-Anghelescu N. Monica',
    'spinu.petresti' => 'Spînu V. Liliana',
    'popescu.petresti' => 'Popescu C. Maria-Amalia',
    'dita.petresti' => 'Diţă I. Nicoleta-Manuela',
    'craciun.petresti' => 'Crăciun I. Cecilia-Oana',
    'topor.petresti' => 'Topor M. Elena',
    'spinuc.petresti' => 'Spînu C. Ancuța-Elena',
    'calin.petresti' => 'Călin G. Constantin',
    'rotaru.petresti' => 'Rotaru D. Mihaela-Carmen',
    'hodorogea.petresti' => 'Hodorogea I. Ana-Maria',
    'stoian.petresti' => 'Stoian L. Marius',
    'nicolae.petresti' => 'Nicolae P. Marioara',
    'blendea.petresti' => 'Blendea G. Valentina-Florentina',
];

// Funcție pentru obținerea numelui complet
function getUserFullName($username) {
    global $user_full_names;
    return isset($user_full_names[$username]) ? $user_full_names[$username] : $username;
}

// Funcție pentru logarea evenimentelor cu nume complet
function logEvent($type, $username, $ip = null, $user_agent = null) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    
    // DEBUG - verifică calea
    error_log("LOG EVENT - File path: " . $log_file);
    error_log("LOG EVENT - File exists: " . (file_exists($log_file) ? 'YES' : 'NO'));
    error_log("LOG EVENT - Directory exists: " . (is_dir(dirname($log_file)) ? 'YES' : 'NO'));
    error_log("LOG EVENT - File writable: " . (is_writable($log_file) ? 'YES' : 'NO'));
    
    // Creăm directorul logs dacă nu există
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
        error_log("LOG EVENT - Created directory: " . $log_dir);
    }
    
    // Obținem IP-ul și user agent-ul dacă nu sunt furnizate
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Necunoscut';
    }
    if ($user_agent === null) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Necunoscut';
    }
    
    // Obținem numele complet
    $full_name = getUserFullName($username);
    
    // Formatăm mesajul de log cu numele complet
    $timestamp = date('Y-m-d H:i:s');
    
    // Formatăm diferit în funcție de tip
    if ($type === 'FAILED') {
        $log_message = "[{$type}] {$timestamp} | Attempted User: {$username} ({$full_name}) | IP: {$ip} | Agent: {$user_agent}";
    } else {
        $log_message = "[{$type}] {$timestamp} | User: {$username} ({$full_name}) | IP: {$ip} | Agent: {$user_agent}";
    }
    
    // Încearcă să scrie în fișier
    $result = file_put_contents($log_file, $log_message . "\n", FILE_APPEND | LOCK_EX);
    
    if ($result === false) {
        error_log("LOG EVENT - FAILED to write to file!");
        error_log("LOG EVENT - Message was: " . $log_message);
    } else {
        error_log("LOG EVENT - Successfully wrote " . $result . " bytes");
    }
}

// Funcții pentru gestionarea logurilor (folosite în admin_logs.php)
function clearAllLogs() {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (file_exists($log_file)) {
        // Creăm backup înainte de ștergere
        $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_login_log.txt';
        copy($log_file, $backup_file);
        
        // Golim fișierul
        file_put_contents($log_file, '');
        return true;
    }
    return false;
}

function clearLogsByType($type) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Filtrăm logurile (păstrăm cele care NU sunt de tipul specificat)
    $filtered_logs = array_filter($logs, function($log) use ($type) {
        return !preg_match('/\[' . preg_quote($type, '/') . '\]/', $log);
    });
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_' . strtolower($type) . '_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

function clearOldLogs($days = 30) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Data limită
    $limit_date = date('Y-m-d', strtotime("-{$days} days"));
    
    // Filtrăm logurile (păstrăm doar cele mai noi)
    $filtered_logs = array_filter($logs, function($log) use ($limit_date) {
        // Extragem data din log
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\] (\d{4}-\d{2}-\d{2})/', $log, $matches)) {
            return $matches[2] >= $limit_date;
        }
        return true; // Păstrăm logurile cu format necunoscut
    });
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_old_logs_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

function getLogStats() {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return ['total' => 0, 'SUCCESS' => 0, 'FAILED' => 0, 'LOGOUT' => 0, 'size' => 0];
    }
    
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $stats = ['total' => count($logs), 'SUCCESS' => 0, 'FAILED' => 0, 'LOGOUT' => 0];
    
    foreach ($logs as $log) {
        if (preg_match('/\[(SUCCESS|FAILED|LOGOUT)\]/', $log, $matches)) {
            $stats[$matches[1]]++;
        }
    }
    
    // Mărimea fișierului
    $stats['size'] = filesize($log_file);
    
    return $stats;
}

// Adaugă această funcție în config.php
function clearSelectedLogs($selected_indices) {
    $log_file = __DIR__ . '/logs/login_log.txt';
    if (!file_exists($log_file)) {
        return false;
    }
    
    // Citim toate logurile
    $logs = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Convertim indicii la integers pentru comparație sigură
    $selected_indices = array_map('intval', $selected_indices);
    
    // Filtrăm logurile (păstrăm cele care NU sunt selectate)
    $filtered_logs = [];
    foreach ($logs as $index => $log) {
        if (!in_array($index, $selected_indices)) {
            $filtered_logs[] = $log;
        }
    }
    
    // Creăm backup
    $backup_file = __DIR__ . '/logs/backup_' . date('Y-m-d_H-i-s') . '_selected_removed.txt';
    copy($log_file, $backup_file);
    
    // Scriem logurile filtrate înapoi
    file_put_contents($log_file, implode("\n", $filtered_logs) . (empty($filtered_logs) ? '' : "\n"));
    
    return true;
}

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
    'admin_logs.php',
    // Doar pagina index din fiecare modul (dacă dorești să fie accesibile)
    //'modules/module1/index.html',
    //'modules/module2/index.html',
    //'modules/module3/index.html',
    //'modules/module4/index.html',
    //'modules/module5/index.html',
    //'modules/module6/index.html'
];

/* 
LISTĂ UTILIZATORI - TOTAL: 52 utilizatori (1 admin + 1 test + 14 Smardioasa + 21 Cozmești + 16 Petrești)
==========================================================================================================

ȘCOALA GIMNAZIALĂ SMARDIOASA (14 utilizatori):
================================================
Username: slavu.smardioasa      | Nume: Slavu Lorelai Mihaela     
Username: petre.smardioasa      | Nume: Petre Elena               
Username: cristea.smardioasa    | Nume: Cristea Olimpia           
Username: zahariea.smardioasa   | Nume: Zahariea Cristina         
Username: ionescu.smardioasa    | Nume: Ionescu Ileana            
Username: raportaru.smardioasa  | Nume: Raportaru Aurelian        
Username: paun.smardioasa       | Nume: Păun Roxana Elena         
Username: bratu.smardioasa      | Nume: Bratu Angelica Mihaela    
Username: urucu.smardioasa      | Nume: Urucu Angelica Mădălina   
Username: dociu.smardioasa      | Nume: Dociu Mihaela Simona      
Username: dragomirescu.smardioasa | Nume: Dragomirescu Alina      
Username: lazarica.smardioasa   | Nume: Lăzărică Liliana          
Username: joinel.smardioasa     | Nume: Joinel Mihaela            
Username: avram.smardioasa      | Nume: Avram Roxana Madalina     
PAROLA COMUNĂ: scoalasmardioasa

ȘCOALA PROFESIONALĂ COZMEȘTI (21 utilizatori):
===============================================
Username: chescu.cozmesti       | Nume: Cheșcu Mioara             
Username: baltag.cozmesti       | Nume: Baltag Adina              
Username: guralivu.cozmesti     | Nume: Guralivu Daniela          
Username: agavriloaiei.cozmesti | Nume: Agavriloaiei Pompilia     
Username: tanase.cozmesti       | Nume: Tănase Gheorghe           
Username: piriiala.cozmesti     | Nume: Pîrîială Constantin       
Username: iacob.cozmesti        | Nume: Iacob Cristina            
Username: rusu.cozmesti         | Nume: Rusu Claudiu              
Username: piriialaim.cozmesti   | Nume: Pîrîială Mihaela          
Username: ifrim.cozmesti        | Nume: Ifrim Adina-Cosmina       
Username: galita.cozmesti       | Nume: Găliță Mihai-Florin       
Username: chiriac.cozmesti      | Nume: Chiriac Ecaterina         
Username: ungureanu.cozmesti    | Nume: Ungureanu Valentina       
Username: andrii.cozmesti       | Nume: Andrii Doru Emil          
Username: samoila.cozmesti      | Nume: Samoilă Ștefania          
Username: puscasu.cozmesti      | Nume: Pușcașu Gabriela          
Username: verdes.cozmesti       | Nume: Verdeș-Enculescu Mirela-Elena 
Username: lupu.cozmesti         | Nume: Lupu Silviu               
Username: pascariu.cozmesti     | Nume: Pascariu-Iliescu Mihaela  
Username: policiuc.cozmesti     | Nume: Policiuc Andreea          
Username: aniculaesei.cozmesti  | Nume: Aniculaesei Patricia-Maria 
PAROLA COMUNĂ: scoalacozmesti

ȘCOALA GIMNAZIALĂ PETREȘTI (16 utilizatori):
=============================================
Username: muresanu.petresti     | Nume: Mureșanu T. Marius (Director)
Username: tatuc.petresti        | Nume: Tătuc V. Gabriela (CHI, FIZ)
Username: copilu.petresti       | Nume: Copilu I. Ion-Florin (MAT)
Username: oancea.petresti       | Nume: Oancea-Anghelescu N. Monica (MAT)
Username: spinu.petresti        | Nume: Spînu V. Liliana (LRO)
Username: popescu.petresti      | Nume: Popescu C. Maria-Amalia (LRO)
Username: dita.petresti         | Nume: Diţă I. Nicoleta-Manuela (LRO)
Username: craciun.petresti      | Nume: Crăciun I. Cecilia-Oana (LEN)
Username: topor.petresti        | Nume: Topor M. Elena (IST, EDS, EDF)
Username: spinuc.petresti       | Nume: Spînu C. Ancuța-Elena (EDF)
Username: calin.petresti        | Nume: Călin G. Constantin (EDR)
Username: rotaru.petresti       | Nume: Rotaru D. Mihaela-Carmen (FIZ, CHI)
Username: hodorogea.petresti    | Nume: Hodorogea I. Ana-Maria (GEO)
Username: stoian.petresti       | Nume: Stoian L. Marius (EDF)
Username: nicolae.petresti      | Nume: Nicolae P. Marioara (LFR, LEN)
Username: blendea.petresti      | Nume: Blendea G. Valentina-Florentina (LRO)
PAROLA COMUNĂ: scoalapetresti
*/
?>
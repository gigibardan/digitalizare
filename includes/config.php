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
    'carp.cozmesti' => password_hash('scoalacozmesti', PASSWORD_DEFAULT),           // Carp Mihaela Mirela

    // ȘCOALA GIMNAZIALĂ PERISORU
    // Toți utilizatorii au parola: scoalaperisoru
    'muresanu.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),       // Mureșanu T. Marius (Director)
    'tatuc.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),          // Tătuc V. Gabriela
    'copilu.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),         // Copilu I. Ion-Florin
    'oancea.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),         // Oancea-Anghelescu N. Monica
    'spinu.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),          // Spînu V. Liliana
    'popescu.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),        // Popescu C. Maria-Amalia
    'dita.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),           // Diţă I. Nicoleta-Manuela
    'craciun.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),        // Crăciun I. Cecilia-Oana
    'topor.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),          // Topor M. Elena
    'spinuc.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),         // Spînu C. Ancuța-Elena
    'calin.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),          // Călin G. Constantin
    'rotaru.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),         // Rotaru D. Mihaela-Carmen
    'hodorogea.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),      // Hodorogea I. Ana-Maria
    'stoian.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),         // Stoian L. Marius
    'nicolae.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),        // Nicolae P. Marioara
    'blendea.perisoru' => password_hash('scoalaperisoru', PASSWORD_DEFAULT),        // Blendea G. Valentina-Florentina
    
    // ȘCOALA GIMNAZIALĂ PETREȘTI
    // Toți utilizatorii au parola: scoalapetresti
    'diaconu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Diaconu Nicușor (Director)
    'dumitrescu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),     // Dumitrescu Mariana (Director adjunct)
    'ilie.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),           // Ilie Alin Florin
    'staicu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Staicu Adriana
    'andrei.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Andrei Cristina Luciana
    'rosu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),           // Roșu Eugen
    'serban.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Șerban Geanina Liliana
    'stan.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),           // Stan Daniel Florin
    'zarnescu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),       // Zărnescu Nela
    'banica.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Bănică George Silvian
    'schmidt.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),        // Schmidt Victor Marius
    'ene.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),            // Ene Florentina Cristina
    'soare.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Soare Viorica
    'tudor.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),          // Tudor Dumitra Daniela
    'alexandru.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),      // Alexandru Vasilica
    'neblea.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Neblea Florin
    'ioneci.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Ioneci Nicoleta
    'stoica.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Stoica Adeluța
    'constantinescu.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT), // Constantinescu Nicoleta
    'bumbes.petresti' => password_hash('scoalapetresti', PASSWORD_DEFAULT),         // Bumbeș Iliuță
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
    'carp.cozmesti' => 'Carp Mihaela Mirela',
    
    // ȘCOALA GIMNAZIALĂ PERISORU
    'muresanu.perisoru' => 'Mureșanu T. Marius',
    'tatuc.perisoru' => 'Tătuc V. Gabriela',
    'copilu.perisoru' => 'Copilu I. Ion-Florin',
    'oancea.perisoru' => 'Oancea-Anghelescu N. Monica',
    'spinu.perisoru' => 'Spînu V. Liliana',
    'popescu.perisoru' => 'Popescu C. Maria-Amalia',
    'dita.perisoru' => 'Diţă I. Nicoleta-Manuela',
    'craciun.perisoru' => 'Crăciun I. Cecilia-Oana',
    'topor.perisoru' => 'Topor M. Elena',
    'spinuc.perisoru' => 'Spînu C. Ancuța-Elena',
    'calin.perisoru' => 'Călin G. Constantin',
    'rotaru.perisoru' => 'Rotaru D. Mihaela-Carmen',
    'hodorogea.perisoru' => 'Hodorogea I. Ana-Maria',
    'stoian.perisoru' => 'Stoian L. Marius',
    'nicolae.perisoru' => 'Nicolae P. Marioara',
    'blendea.perisoru' => 'Blendea G. Valentina-Florentina',
    
    // ȘCOALA GIMNAZIALĂ PETREȘTI
    'diaconu.petresti' => 'Diaconu Nicușor',
    'dumitrescu.petresti' => 'Dumitrescu Mariana',
    'ilie.petresti' => 'Ilie Alin Florin',
    'staicu.petresti' => 'Staicu Adriana',
    'andrei.petresti' => 'Andrei Cristina Luciana',
    'rosu.petresti' => 'Roșu Eugen',
    'serban.petresti' => 'Șerban Geanina Liliana',
    'stan.petresti' => 'Stan Daniel Florin',
    'zarnescu.petresti' => 'Zărnescu Nela',
    'banica.petresti' => 'Bănică George Silvian',
    'schmidt.petresti' => 'Schmidt Victor Marius',
    'ene.petresti' => 'Ene Florentina Cristina',
    'soare.petresti' => 'Soare Viorica',
    'tudor.petresti' => 'Tudor Dumitra Daniela',
    'alexandru.petresti' => 'Alexandru Vasilica',
    'neblea.petresti' => 'Neblea Florin',
    'ioneci.petresti' => 'Ioneci Nicoleta',
    'stoica.petresti' => 'Stoica Adeluța',
    'constantinescu.petresti' => 'Constantinescu Nicoleta',
    'bumbes.petresti' => 'Bumbeș Iliuță',
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
LISTĂ UTILIZATORI - TOTAL: 73 utilizatori (1 admin + 1 test + 14 Smardioasa + 22 Cozmești + 16 Perisoru + 20 Petrești)
========================================================================================================================

ȘCOALA GIMNAZIALĂ SMARDIOASA (14 utilizatori): PAROLA: scoalasmardioasa
ȘCOALA PROFESIONALĂ COZMEȘTI (22 utilizatori): PAROLA: scoalacozmesti
ȘCOALA GIMNAZIALĂ PERISORU (16 utilizatori): PAROLA: scoalaperisoru
ȘCOALA GIMNAZIALĂ PETREȘTI (20 utilizatori): PAROLA: scoalapetresti
*/
?>
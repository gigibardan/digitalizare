<?php
// ATENȚIE: Rulează acest fișier O SINGURĂ DATĂ pentru a genera hash-urile
// generate_hashes.php

echo "Generând hash-urile...<br>";

$users_plain = [
    // Administrator
    'admin' => 'techminds',
    
    // ȘCOALA GIMNAZIALĂ SMARDIOASA
    'gigitest' => 'gigitest',
    'slavu.smardioasa' => 'scoalasmardioasa',
    'petre.smardioasa' => 'scoalasmardioasa',
    'cristea.smardioasa' => 'scoalasmardioasa',
    'zahariea.smardioasa' => 'scoalasmardioasa',
    'ionescu.smardioasa' => 'scoalasmardioasa',
    'raportaru.smardioasa' => 'scoalasmardioasa',
    'paun.smardioasa' => 'scoalasmardioasa',
    'bratu.smardioasa' => 'scoalasmardioasa',
    'urucu.smardioasa' => 'scoalasmardioasa',
    'dociu.smardioasa' => 'scoalasmardioasa',
    'dragomirescu.smardioasa' => 'scoalasmardioasa',
    'lazarica.smardioasa' => 'scoalasmardioasa',
    'joinel.smardioasa' => 'scoalasmardioasa',
    'avram.smardioasa' => 'scoalasmardioasa',

    // ȘCOALA PROFESIONALĂ COZMEȘTI
    'chescu.cozmesti' => 'scoalacozmesti',
    'baltag.cozmesti' => 'scoalacozmesti',
    'guralivu.cozmesti' => 'scoalacozmesti',
    'agavriloaiei.cozmesti' => 'scoalacozmesti',
    'tanase.cozmesti' => 'scoalacozmesti',
    'piriiala.cozmesti' => 'scoalacozmesti',
    'iacob.cozmesti' => 'scoalacozmesti',
    'rusu.cozmesti' => 'scoalacozmesti',
    'piriialaim.cozmesti' => 'scoalacozmesti',
    'ifrim.cozmesti' => 'scoalacozmesti',
    'galita.cozmesti' => 'scoalacozmesti',
    'chiriac.cozmesti' => 'scoalacozmesti',
    'ungureanu.cozmesti' => 'scoalacozmesti',
    'andrii.cozmesti' => 'scoalacozmesti',
    'samoila.cozmesti' => 'scoalacozmesti',
    'puscasu.cozmesti' => 'scoalacozmesti',
    'verdes.cozmesti' => 'scoalacozmesti',
    'lupu.cozmesti' => 'scoalacozmesti',
    'pascariu.cozmesti' => 'scoalacozmesti',
    'policiuc.cozmesti' => 'scoalacozmesti',
    'aniculaesei.cozmesti' => 'scoalacozmesti',
    'carp.cozmesti' => 'scoalacozmesti',

    // ȘCOALA GIMNAZIALĂ PERISORU
    'muresanu.perisoru' => 'scoalaperisoru',
    'tatuc.perisoru' => 'scoalaperisoru',
    'copilu.perisoru' => 'scoalaperisoru',
    'oancea.perisoru' => 'scoalaperisoru',
    'spinu.perisoru' => 'scoalaperisoru',
    'popescu.perisoru' => 'scoalaperisoru',
    'dita.perisoru' => 'scoalaperisoru',
    'craciun.perisoru' => 'scoalaperisoru',
    'topor.perisoru' => 'scoalaperisoru',
    'spinuc.perisoru' => 'scoalaperisoru',
    'calin.perisoru' => 'scoalaperisoru',
    'rotaru.perisoru' => 'scoalaperisoru',
    'hodorogea.perisoru' => 'scoalaperisoru',
    'stoian.perisoru' => 'scoalaperisoru',
    'nicolae.perisoru' => 'scoalaperisoru',
    'blendea.perisoru' => 'scoalaperisoru',

    // ȘCOALA GIMNAZIALĂ PETREȘTI
    'diaconu.petresti' => 'scoalapetresti',
    'dumitrescu.petresti' => 'scoalapetresti',
    'ilie.petresti' => 'scoalapetresti',
    'staicu.petresti' => 'scoalapetresti',
    'andrei.petresti' => 'scoalapetresti',
    'rosu.petresti' => 'scoalapetresti',
    'serban.petresti' => 'scoalapetresti',
    'stan.petresti' => 'scoalapetresti',
    'zarnescu.petresti' => 'scoalapetresti',
    'banica.petresti' => 'scoalapetresti',
    'schmidt.petresti' => 'scoalapetresti',
    'ene.petresti' => 'scoalapetresti',
    'soare.petresti' => 'scoalapetresti',
    'tudor.petresti' => 'scoalapetresti',
    'alexandru.petresti' => 'scoalapetresti',
    'neblea.petresti' => 'scoalapetresti',
    'ioneci.petresti' => 'scoalapetresti',
    'stoica.petresti' => 'scoalapetresti',
    'constantinescu.petresti' => 'scoalapetresti',
    'bumbes.petresti' => 'scoalapetresti',

    // LICEUL CU PROGRAM SPORTIV ROMAN
    'agache.roman' => 'liceulroman',
    'andrei.roman' => 'liceulroman',
    'bumbu.roman' => 'liceulroman',
    'ciobanu.roman' => 'liceulroman',
    'ciobanuc.roman' => 'liceulroman',
    'chiriac.roman' => 'liceulroman',
    'costandache.roman' => 'liceulroman',
    'creanga.roman' => 'liceulroman',
    'dascalescu.roman' => 'liceulroman',
    'didi.roman' => 'liceulroman',
    'enea.roman' => 'liceulroman',
    'gaina.roman' => 'liceulroman',
    'hanganu.roman' => 'liceulroman',
    'huci.roman' => 'liceulroman',
    'lazar.roman' => 'liceulroman',
    'lazarv.roman' => 'liceulroman',
    'lungu.roman' => 'liceulroman',
    'lupu.roman' => 'liceulroman',
    'lupusoru.roman' => 'liceulroman',
    'minut.roman' => 'liceulroman',
    'murariu.roman' => 'liceulroman',
    'pascal.roman' => 'liceulroman',
    'patrauceanu.roman' => 'liceulroman',
    'petcu.roman' => 'liceulroman',
    'pintea.roman' => 'liceulroman',
    'pislaru.roman' => 'liceulroman',
    'simionescu.roman' => 'liceulroman',
    'tamba.roman' => 'liceulroman',
];

$users_hashed = [];
$count = 0;
$total = count($users_plain);

foreach ($users_plain as $username => $password) {
    $count++;
    echo "[$count/$total] Hashing: $username...<br>";
    flush(); // Afișează progresul în timp real
    
    $users_hashed[$username] = password_hash($password, PASSWORD_DEFAULT);
}

echo "<br><strong>Generând fișierul users_final.php...</strong><br>";

// Generează conținutul fișierului
$content = "<?php\n";
$content .= "// Hash-uri pre-generate pentru utilizatori\n";
$content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n\n";
$content .= '$users = ' . var_export($users_hashed, true) . ";\n";
$content .= "?>";

// Salvează fișierul
if (file_put_contents('users_final.php', $content)) {
    echo "<strong style='color: green;'>✓ Succes! Fișierul users_final.php a fost creat.</strong><br>";
    echo "<br><strong>PASUL URMĂTOR:</strong><br>";
    echo "1. Copiază users_final.php pe server<br>";
    echo "2. Modifică config.php să folosească require_once 'users_final.php';<br>";
    echo "3. Șterge toate password_hash()-urile din config.php<br>";
    echo "4. Testează login-ul<br>";
    echo "5. Șterge acest fișier (generate_hashes.php)<br>";
} else {
    echo "<strong style='color: red;'>✗ Eroare la salvarea fișierului!</strong><br>";
}
// ADAUGĂ ACESTE LINII în generate_hashes.php:

echo "<br><strong>Generând fișierul user_names.php...</strong><br>";

// Aici ai nevoie să adaugi mapping-ul numelor complete
// Copiază din config.php original secțiunea $user_full_names

$user_full_names = [
    'admin' => 'Administrator',
    'gigitest' => 'Gigi Test',
    // ... restul mapării din config.php original
];

$names_content = "<?php\n";
$names_content .= "// Maparea numelor complete\n";
$names_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n\n";
$names_content .= '$user_full_names = ' . var_export($user_full_names, true) . ";\n";
$names_content .= "?>";

if (file_put_contents('user_names.php', $names_content)) {
    echo "<strong style='color: green;'>✓ Succes! Fișierul user_names.php a fost creat.</strong><br>";
} else {
    echo "<strong style='color: red;'>✗ Eroare la salvarea user_names.php!</strong><br>";
}
?>
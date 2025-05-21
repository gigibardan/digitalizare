<?php
// Includem fișierele necesare
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Afișăm utilizatorii configurați
echo "<h2>Utilizatori configurați:</h2>";
echo "<pre>";
print_r($users);
echo "</pre>";

// Testăm autentificarea pentru fiecare utilizator
echo "<h2>Test autentificare:</h2>";
echo "<ul>";

// Parolele pentru test
$test_passwords = [
    'admin' => 'techminds',
    'user1' => 'parola1',
    'user2' => 'parola2',
    'user3' => 'parola3',
];

foreach ($users as $username => $hash) {
    $password = $test_passwords[$username] ?? 'parola_necunoscuta';
    $result = password_verify($password, $hash) ? "SUCCES" : "EȘEC";
    echo "<li>Utilizator: $username, Parolă: $password - Rezultat: $result</li>";
}
echo "</ul>";

// Regenerăm hash-uri noi pentru a fi copiate în config.php
echo "<h2>Hash-uri noi pentru config.php:</h2>";
echo "<pre>\$users = [<br>";
foreach ($test_passwords as $username => $password) {
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    echo "    '$username' => '$new_hash',<br>";
}
echo "];</pre>";
?>
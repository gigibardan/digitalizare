<?php
// add_users_quick.php - Pentru adăugarea rapidă a mai multor utilizatori
// FOLOSEȘTE ACEST SCRIPT când ai mulți utilizatori de adăugat dintr-odată

// PROTECȚIE - doar pentru admin
session_start();
require_once 'auth.php';
if (!isLoggedIn() || !isAdmin()) {
    die('Acces interzis!');
}

echo "<h1>🚀 Adăugare Rapidă Utilizatori</h1>";

// Exemplu: Adaugă utilizatori dintr-un array
if (isset($_POST['add_bulk'])) {
    // MODIFICĂ AICI cu utilizatorii noi
    $new_users = [
        // Exemplu pentru o școală nouă
        'director.nouascoala' => [
            'password' => 'parolanouascoala',
            'full_name' => 'Director Nou Școala',
            'school' => 'nouascoala'
        ],
        'profesor1.nouascoala' => [
            'password' => 'parolanouascoala',
            'full_name' => 'Profesor Unu',
            'school' => 'nouascoala'
        ],
        'profesor2.nouascoala' => [
            'password' => 'parolanouascoala',
            'full_name' => 'Profesor Doi',
            'school' => 'nouascoala'
        ],
        // Adaugă aici mai mulți utilizatori...
    ];
    
    // Încarcă utilizatorii existenți
    $users_data_file = 'users_data.json';
    if (file_exists($users_data_file)) {
        $existing_users = json_decode(file_get_contents($users_data_file), true);
    } else {
        $existing_users = [];
    }
    
    $added_count = 0;
    $skipped_count = 0;
    
    foreach ($new_users as $username => $data) {
        if (isset($existing_users[$username])) {
            echo "⚠️ SKIP: $username (există deja)<br>";
            $skipped_count++;
        } else {
            $existing_users[$username] = [
                'password' => $data['password'],
                'full_name' => $data['full_name'],
                'school' => $data['school'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            echo "✅ ADDED: $username ({$data['full_name']})<br>";
            $added_count++;
        }
    }
    
    // Salvează datele actualizate
    if (file_put_contents($users_data_file, json_encode($existing_users, JSON_PRETTY_PRINT))) {
        echo "<br><strong>📊 Rezultat: $added_count adăugați, $skipped_count omisi</strong><br>";
        
        // Regenerează automat fișierul cu hash-uri
        if (regenerateHashFile($existing_users)) {
            echo "✅ Fișierul users_final.php a fost regenerat!<br>";
        } else {
            echo "❌ Eroare la regenerarea fișierului!<br>";
        }
    } else {
        echo "❌ Eroare la salvarea datelor!<br>";
    }
}

function regenerateHashFile($users_data) {
    // Generează hash-urile
    $users_hashed = [];
    $user_full_names = [];
    
    echo "<br>🔄 Generând hash-uri...<br>";
    foreach ($users_data as $username => $data) {
        $users_hashed[$username] = password_hash($data['password'], PASSWORD_DEFAULT);
        $user_full_names[$username] = $data['full_name'];
        echo ".";
    }
    
    // Generează conținutul fișierelor
    $users_content = "<?php\n";
    $users_content .= "// Hash-uri pre-generate pentru utilizatori\n";
    $users_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n";
    $users_content .= "// Total utilizatori: " . count($users_hashed) . "\n\n";
    $users_content .= '$users = ' . var_export($users_hashed, true) . ";\n";
    $users_content .= "?>";
    
    $names_content = "<?php\n";
    $names_content .= "// Maparea numelor complete\n";
    $names_content .= '$user_full_names = ' . var_export($user_full_names, true) . ";\n";
    $names_content .= "?>";
    
    $success1 = file_put_contents('users_final.php', $users_content);
    $success2 = file_put_contents('user_names.php', $names_content);
    
    return $success1 !== false && $success2 !== false;
}
?>

<form method="POST">
    <p><strong>ATENȚIE:</strong> Acest script va adăuga utilizatorii definiți în cod.</p>
    <p>Verifică lista din cod înainte de a continua!</p>
    <button type="submit" name="add_bulk" onclick="return confirm('Ești sigur că vrei să adaugi utilizatorii?')">
        ➕ Adaugă Utilizatorii în Bulk
    </button>
</form>

<p><a href="user_manager.php">← Înapoi la Manager Utilizatori</a></p>
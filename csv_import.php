<?php
// csv_import.php - Import utilizatori din CSV
session_start();
require_once 'includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    die('Acces interzis!');
}

$message = '';
$error = '';

if ($_POST['action'] ?? '' === 'import_csv') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $csv_content = file_get_contents($_FILES['csv_file']['tmp_name']);
        $lines = str_getcsv($csv_content, "\n");
        
        // Încarcă utilizatorii existenți
        $users_data_file = 'users_data.json';
        $existing_users = [];
        if (file_exists($users_data_file)) {
            $existing_users = json_decode(file_get_contents($users_data_file), true) ?? [];
        }
        
        $added_count = 0;
        $skipped_count = 0;
        $errors = [];
        
        foreach ($lines as $index => $line) {
            if ($index === 0) continue; // Skip header
            
            $data = str_getcsv($line);
            if (count($data) < 4) continue;
            
            $username = trim($data[0]);
            $password = trim($data[1]);
            $full_name = trim($data[2]);
            $school = trim($data[3]);
            
            if (empty($username) || empty($password) || empty($full_name)) {
                $errors[] = "Linia " . ($index + 1) . ": Date incomplete";
                continue;
            }
            
            if (isset($existing_users[$username])) {
                $skipped_count++;
                continue;
            }
            
            $existing_users[$username] = [
                'password' => $password,
                'full_name' => $full_name,
                'school' => $school,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $added_count++;
        }
        
        // Salvează datele
        if (file_put_contents($users_data_file, json_encode($existing_users, JSON_PRETTY_PRINT))) {
            // Regenerează hash-urile
            if (regenerateFromData($existing_users)) {
                $message = "✅ Import complet! $added_count adăugați, $skipped_count omisi.";
                if (!empty($errors)) {
                    $message .= "<br>⚠️ Erori: " . implode('<br>', $errors);
                }
            } else {
                $error = "Eroare la regenerarea hash-urilor!";
            }
        } else {
            $error = "Eroare la salvarea datelor!";
        }
    } else {
        $error = "Eroare la upload-ul fișierului!";
    }
}

function regenerateUsersFile() {
    $users_data_file = 'users_data.json';
    
    // 1. Încarcă utilizatorii existenți din users_final.php (cei 102)
    $existing_users = [];
    if (file_exists('includes/users_final.php')) {
        // Citește fișierul ca string și extrage array-ul
        $content = file_get_contents('includes/users_final.php');
        
        // Evaluează conținutul pentru a obține array-ul $users
        eval(str_replace('<?php', '', $content));
        $existing_users = $users ?? [];
        
        echo "<p>DEBUG: Utilizatori existenți din users_final.php: " . count($existing_users) . "</p>";
    }
    
    // 2. Încarcă numele existente din user_names.php
    $existing_names = [];
    if (file_exists('includes/user_names.php')) {
        $names_content = file_get_contents('includes/user_names.php');
        eval(str_replace('<?php', '', $names_content));
        $existing_names = $user_full_names ?? [];
        
        echo "<p>DEBUG: Nume existente din user_names.php: " . count($existing_names) . "</p>";
    }
    
    // 3. Încarcă utilizatorii NOI din users_data.json
    $new_users_data = [];
    if (file_exists($users_data_file)) {
        $new_users_data = json_decode(file_get_contents($users_data_file), true) ?? [];
        echo "<p>DEBUG: Utilizatori noi din JSON: " . count($new_users_data) . "</p>";
    }
    
    // 4. COMBINĂ: păstrează utilizatorii vechi + adaugă cei noi
    $all_users_hash = $existing_users; // ÎNCEPE cu toți cei 102
    $all_names = $existing_names;      // ÎNCEPE cu toate numele
    
    $added_count = 0;
    
    // 5. Adaugă doar utilizatorii NOI care nu existau
    foreach ($new_users_data as $username => $data) {
        if (!isset($all_users_hash[$username])) {
            // Utilizator nou - generează hash
            $all_users_hash[$username] = password_hash($data['password'], PASSWORD_DEFAULT);
            $all_names[$username] = $data['full_name'];
            $added_count++;
            echo "<p>DEBUG: Adăugat utilizator nou: $username</p>";
        } else {
            echo "<p>DEBUG: Utilizator existent păstrat: $username</p>";
        }
    }
    
    echo "<p>DEBUG: Total utilizatori finali: " . count($all_users_hash) . "</p>";
    echo "<p>DEBUG: Utilizatori adăugați: $added_count</p>";
    
    // 6. Salvează fișierele cu toți utilizatorii (vechi + noi)
    $users_content = "<?php\n";
    $users_content .= "// Hash-uri pre-generate pentru utilizatori\n";
    $users_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n";
    $users_content .= "// Total utilizatori: " . count($all_users_hash) . "\n\n";
    $users_content .= '$users = ' . var_export($all_users_hash, true) . ";\n";
    $users_content .= "?>";
    
    $names_content = "<?php\n";
    $names_content .= "// Maparea numelor complete\n";
    $names_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n\n";
    $names_content .= '$user_full_names = ' . var_export($all_names, true) . ";\n";
    $names_content .= "?>";
    
    $success1 = file_put_contents('includes/users_final.php', $users_content);
    $success2 = file_put_contents('includes/user_names.php', $names_content);
    
    echo "<p>DEBUG: Salvare users_final.php: " . ($success1 ? 'SUCCES (' . number_format($success1) . ' bytes)' : 'EROARE') . "</p>";
    echo "<p>DEBUG: Salvare user_names.php: " . ($success2 ? 'SUCCES (' . number_format($success2) . ' bytes)' : 'EROARE') . "</p>";
    
    return $success1 !== false && $success2 !== false;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Import CSV Utilizatori</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        input[type="file"], button { padding: 10px; margin: 10px 0; }
        button { background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .example { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>📊 Import Utilizatori din CSV</h1>
    
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="container">
        <h2>📁 Upload fișier CSV</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import_csv">
            <input type="file" name="csv_file" accept=".csv" required>
            <br>
            <button type="submit">📥 Import Utilizatori</button>
        </form>
    </div>
    
    <div class="container">
        <h2>📋 Format CSV necesar</h2>
        <div class="example">
            <p><strong>Exemplu de fișier CSV:</strong></p>
            <table>
                <tr>
                    <th>username</th>
                    <th>password</th>
                    <th>full_name</th>
                    <th>school</th>
                </tr>
                <tr>
                    <td>popescu.scoalanou</td>
                    <td>parolascoala</td>
                    <td>Popescu Ion Marian</td>
                    <td>scoalanou</td>
                </tr>
                <tr>
                    <td>ionescu.scoalanou</td>
                    <td>parolascoala</td>
                    <td>Ionescu Maria Elena</td>
                    <td>scoalanou</td>
                </tr>
            </table>
            
            <p><strong>Instrucțiuni:</strong></p>
            <ul>
                <li>Prima linie trebuie să fie header-ul (username,password,full_name,school)</li>
                <li>Separatorul trebuie să fie virgula (,)</li>
                <li>Salvează din Excel ca "CSV (Comma delimited)"</li>
                <li>Username-urile duplicat vor fi omise</li>
            </ul>
        </div>
    </div>
    
    <div class="container">
        <h2>🔗 Link-uri utile</h2>
        <a href="user_manager.php"><button type="button">👥 User Manager</button></a>
        <a href="admin_logs.php"><button type="button">📊 Loguri</button></a>
        <a href="dashboard.php"><button type="button">🏠 Dashboard</button></a>
    </div>
</body>
</html>
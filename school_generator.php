<?php
// school_generator.php - Generator rapid pentru o școală întreagă
session_start();
require_once 'includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    die('Acces interzis!');
}

$message = '';
$error = '';

if ($_POST['action'] ?? '' === 'generate_school') {
    $school_name = trim($_POST['school_name'] ?? '');
    $school_code = trim($_POST['school_code'] ?? '');
    $school_password = trim($_POST['school_password'] ?? '');
    $teachers_list = trim($_POST['teachers_list'] ?? '');
    
    if (empty($school_name) || empty($school_code) || empty($school_password) || empty($teachers_list)) {
        $error = 'Toate câmpurile sunt obligatorii!';
    } else {
        // Procesează lista de profesori
        $teachers = array_filter(array_map('trim', explode("\n", $teachers_list)));
        
        // Încarcă utilizatorii existenți
        $users_data_file = 'users_data.json';
        $existing_users = [];
        if (file_exists($users_data_file)) {
            $existing_users = json_decode(file_get_contents($users_data_file), true) ?? [];
        }
        
        $added_count = 0;
        $skipped_count = 0;
        
        foreach ($teachers as $teacher_name) {
            // Generează username din nume
            $username = generateUsername($teacher_name, $school_code);
            
            if (isset($existing_users[$username])) {
                $skipped_count++;
                continue;
            }
            
            $existing_users[$username] = [
                'password' => $school_password,
                'full_name' => $teacher_name,
                'school' => $school_code,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $added_count++;
        }
        
        // Salvează datele
        if (file_put_contents($users_data_file, json_encode($existing_users, JSON_PRETTY_PRINT))) {
            // Regenerează hash-urile
            if (regenerateFromData($existing_users)) {
                $message = "✅ Școala '$school_name' a fost adăugată! $added_count profesori adăugați, $skipped_count omisi.";
            } else {
                $error = "Eroare la regenerarea hash-urilor!";
            }
        } else {
            $error = "Eroare la salvarea datelor!";
        }
    }
}

function generateUsername($full_name, $school_code) {
    // Extrage primul cuvânt (numele de familie)
    $parts = explode(' ', trim($full_name));
    $surname = strtolower($parts[0]);
    
    // Elimină diacriticele
    $surname = str_replace(
        ['ă', 'â', 'î', 'ș', 'ț', 'Ă', 'Â', 'Î', 'Ș', 'Ț'],
        ['a', 'a', 'i', 's', 't', 'a', 'a', 'i', 's', 't'],
        $surname
    );
    
    return $surname . '.' . strtolower($school_code);
}

function regenerateFromData($users_data) {
    // Încarcă utilizatorii existenți din users_final.php
    $existing_users = [];
    if (file_exists('includes/users_final.php')) {
        $content = file_get_contents('includes/users_final.php');
        eval(str_replace('<?php', '', $content));
        $existing_users = $users ?? [];
    }
    
    $existing_names = [];
    if (file_exists('includes/user_names.php')) {
        $names_content = file_get_contents('includes/user_names.php');
        eval(str_replace('<?php', '', $names_content));
        $existing_names = $user_full_names ?? [];
    }
    
    // Combină: păstrează vechi + adaugă noi
    $all_users_hash = $existing_users;
    $all_names = $existing_names;
    
    foreach ($users_data as $username => $data) {
        if (!isset($all_users_hash[$username])) {
            $all_users_hash[$username] = password_hash($data['password'], PASSWORD_DEFAULT);
            $all_names[$username] = $data['full_name'];
        }
    }
    
    // Salvează fișierele
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
    
    return $success1 !== false && $success2 !== false;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Generator Școală</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .example { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🏫 Generator Școală Completă</h1>
    
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="container">
        <h2>➕ Adaugă Școală Nouă</h2>
        <form method="POST">
            <input type="hidden" name="action" value="generate_school">
            
            <div class="form-group">
                <label for="school_name">Numele Școlii:</label>
                <input type="text" id="school_name" name="school_name" required 
                       placeholder="ex: Școala Gimnazială Noua">
            </div>
            
            <div class="form-group">
                <label for="school_code">Codul Școlii (pentru username):</label>
                <input type="text" id="school_code" name="school_code" required 
                       placeholder="ex: noua (va deveni popescu.noua)">
            </div>
            
            <div class="form-group">
                <label for="school_password">Parola comună pentru școală:</label>
                <input type="text" id="school_password" name="school_password" required 
                       placeholder="ex: scoalanoua2025">
            </div>
            
            <div class="form-group">
                <label for="teachers_list">Lista Profesorilor (câte un nume pe linie):</label>
                <textarea id="teachers_list" name="teachers_list" rows="10" required 
                          placeholder="Popescu Ion Marian&#10;Ionescu Maria Elena&#10;Georgescu Ana Cristina&#10;..."></textarea>
            </div>
            
            <button type="submit">🏫 Generează Școala</button>
        </form>
    </div>
    
    <div class="container">
        <h2>📋 Cum funcționează</h2>
        <div class="example">
            <p><strong>Exemplu:</strong></p>
            <ul>
                <li><strong>Școala:</strong> "Școala Gimnazială Noua"</li>
                <li><strong>Cod:</strong> "noua"</li>
                <li><strong>Parola:</strong> "scoalanoua2025"</li>
                <li><strong>Profesor:</strong> "Popescu Ion Marian"</li>
                <li><strong>Username generat:</strong> "popescu.noua"</li>
                <li><strong>Parola:</strong> "scoalanoua2025"</li>
            </ul>
            
            <p><strong>Avantaje:</strong></p>
            <ul>
                <li>✅ Adaugi 20+ profesori în 30 secunde</li>
                <li>✅ Username-uri consistente</li>
                <li>✅ O singură parolă pe școală</li>
                <li>✅ Regenerare automată hash-uri</li>
            </ul>
        </div>
    </div>
    
    <div class="container">
        <a href="csv_import.php"><button type="button">📊 Import CSV</button></a>
        <a href="user_manager.php"><button type="button">👥 User Manager</button></a>
        <a href="dashboard.php"><button type="button">🏠 Dashboard</button></a>
    </div>
</body>
</html>
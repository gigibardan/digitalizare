<?php
// user_manager.php - Pentru adăugarea/gestionarea utilizatorilor
// PROTEJAT - doar pentru admin!

session_start();
require_once 'includes/auth.php';

// Verifică dacă utilizatorul este admin
if (!isLoggedIn() || !isAdmin()) {
    die('Acces interzis! Doar administratorii pot accesa această pagină.');
}

// Procesează formularul
$message = '';
$error = '';

if ($_POST['action'] ?? '' === 'add_user') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $school = trim($_POST['school'] ?? '');
    
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = 'Toate câmpurile sunt obligatorii!';
    } else {
        if (addNewUser($username, $password, $full_name, $school)) {
            $message = "Utilizatorul '$username' a fost adăugat cu succes!";
        } else {
            $error = 'Eroare la adăugarea utilizatorului!';
        }
    }
}

if ($_POST['action'] ?? '' === 'regenerate_all') {
    if (regenerateUsersFile()) {
        $message = 'Fișierul users_final.php a fost regenerat cu succes!';
    } else {
        $error = 'Eroare la regenerarea fișierului!';
    }
}

// Funcția pentru adăugarea unui utilizator nou
function addNewUser($username, $password, $full_name, $school = '') {
    // Verifică dacă utilizatorul există deja
    $users_data_file = 'users_data.json';
    
    if (file_exists($users_data_file)) {
        $users_data = json_decode(file_get_contents($users_data_file), true);
    } else {
        $users_data = [];
    }
    
    // Verifică dacă username-ul există deja
    if (isset($users_data[$username])) {
        return false; // Utilizatorul există deja
    }
    
    // Adaugă utilizatorul nou
    $users_data[$username] = [
        'password' => $password, // Parola în text clar (va fi hash-uită)
        'full_name' => $full_name,
        'school' => $school,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Salvează datele
    if (file_put_contents($users_data_file, json_encode($users_data, JSON_PRETTY_PRINT))) {
        // Regenerează automat fișierul cu hash-uri
        return regenerateUsersFile();
    }
    
    return false;
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

// Încarcă utilizatorii existenți pentru afișare
$current_users = [];

// Încarcă din users_final.php
if (file_exists('includes/users_final.php')) {
    include 'includes/users_final.php';
    $all_users = $users ?? [];
    
    // Încarcă numele
    if (file_exists('includes/user_names.php')) {
        include 'includes/user_names.php';
        $all_names = $user_full_names ?? [];
    }
    
    foreach ($all_users as $username => $hash) {
        $current_users[$username] = [
            'full_name' => $all_names[$username] ?? $username,
            'school' => (strpos($username, '.') !== false) ? explode('.', $username)[1] : 'unknown',
            'created_at' => '2025-01-01 00:00:00'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Utilizatori</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a8b; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .school-badge { background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔧 Manager Utilizatori</h1>
    
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Formular pentru adăugarea utilizatorilor -->
    <div class="container">
        <h2>➕ Adaugă Utilizator Nou</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_user">
            
            <div class="form-group">
                <label for="username">Username (ex: popescu.scoala):</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Format: nume.scoala">
            </div>
            
            <div class="form-group">
                <label for="password">Parola:</label>
                <input type="text" id="password" name="password" required 
                       placeholder="ex: scoalasmardioasa">
            </div>
            
            <div class="form-group">
                <label for="full_name">Nume Complet:</label>
                <input type="text" id="full_name" name="full_name" required 
                       placeholder="ex: Popescu Ion Marian">
            </div>
            
            <div class="form-group">
                <label for="school">Școala (opțional):</label>
                <select id="school" name="school">
                    <option value="">Selectează școala...</option>
                    <option value="smardioasa">Școala Gimnazială Smardioasa</option>
                    <option value="cozmesti">Școala Profesională Cozmești</option>
                    <option value="perisoru">Școala Gimnazială Perisoru</option>
                    <option value="petresti">Școala Gimnazială Petrești</option>
                    <option value="roman">Liceul cu Program Sportiv Roman</option>
                    <option value="alta">Altă școală</option>
                </select>
            </div>
            
            <button type="submit">➕ Adaugă Utilizator</button>
        </form>
    </div>
    
    <!-- Butoane de management -->
    <div class="container">
        <h2>🔄 Management Sistem</h2>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="regenerate_all">
            <button type="submit" onclick="return confirm('Ești sigur că vrei să regenerezi fișierul cu hash-uri?')">
                🔄 Regenerează Fișierul Hash-uri
            </button>
        </form>
        
        <a href="admin_logs.php" style="margin-left: 10px;">
            <button type="button">📊 Vezi Loguri</button>
        </a>
        
        <a href="dashboard.php" style="margin-left: 10px;">
            <button type="button">🏠 Înapoi la Dashboard</button>
        </a>
    </div>
    
    <!-- Lista utilizatorilor existenți -->
    <div class="container">
        <h2>👥 Utilizatori Existenți (<?= count($current_users) ?>)</h2>
        
        <?php if (empty($current_users)): ?>
            <p>Nu există utilizatori în sistem.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nume Complet</th>
                        <th>Școala</th>
                        <th>Data Creării</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current_users as $username => $data): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($username) ?></strong></td>
                            <td><?= htmlspecialchars($data['full_name']) ?></td>
                            <td>
                                <?php if (!empty($data['school'])): ?>
                                    <span class="school-badge"><?= htmlspecialchars($data['school']) ?></span>
                                <?php else: ?>
                                    <em>Nespecificat</em>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($data['created_at'] ?? 'Necunoscut') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
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
            $error = 'Eroare la adăugarea utilizatorului sau utilizatorul există deja!';
        }
    }
}

if ($_POST['action'] ?? '' === 'delete_user') {
    $username_to_delete = trim($_POST['username_to_delete'] ?? '');
    
    if (!empty($username_to_delete)) {
        if ($username_to_delete === 'admin') {
            $error = 'Nu poți șterge contul de administrator!';
        } else {
            if (deleteUser($username_to_delete)) {
                $message = "Utilizatorul '$username_to_delete' a fost șters cu succes!";
            } else {
                $error = 'Eroare la ștergerea utilizatorului!';
            }
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
    // Verifică dacă utilizatorul există deja în users_final.php
    if (file_exists('includes/users_final.php')) {
        include 'includes/users_final.php';
        if (isset($users[$username])) {
            return false; // Utilizatorul există deja în sistemul principal
        }
    }
    
    // Verifică dacă utilizatorul există deja în users_data.json
    $users_data_file = 'users_data.json';
    
    if (file_exists($users_data_file)) {
        $users_data = json_decode(file_get_contents($users_data_file), true);
    } else {
        $users_data = [];
    }
    
    // Verifică dacă username-ul există deja în JSON
    if (isset($users_data[$username])) {
        return false; // Utilizatorul există deja
    }
    
    // Adaugă utilizatorul nou
    $users_data[$username] = [
        'password' => $password,
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

// Funcția pentru ștergerea unui utilizator
function deleteUser($username) {
    // Șterge din users_data.json
    $users_data_file = 'users_data.json';
    $deleted_from_json = false;
    
    if (file_exists($users_data_file)) {
        $users_data = json_decode(file_get_contents($users_data_file), true) ?? [];
        if (isset($users_data[$username])) {
            unset($users_data[$username]);
            file_put_contents($users_data_file, json_encode($users_data, JSON_PRETTY_PRINT));
            $deleted_from_json = true;
        }
    }
    
    // Șterge din users_final.php și user_names.php
    $deleted_from_main = false;
    
    if (file_exists('includes/users_final.php')) {
        $content = file_get_contents('includes/users_final.php');
        eval(str_replace('<?php', '', $content));
        $existing_users = $users ?? [];
        
        if (isset($existing_users[$username])) {
            unset($existing_users[$username]);
            $deleted_from_main = true;
            
            // Încarcă și numele
            $existing_names = [];
            if (file_exists('includes/user_names.php')) {
                $names_content = file_get_contents('includes/user_names.php');
                eval(str_replace('<?php', '', $names_content));
                $existing_names = $user_full_names ?? [];
                unset($existing_names[$username]);
            }
            
            // Salvează fișierele actualizate
            $users_content = "<?php\n";
            $users_content .= "// Hash-uri pre-generate pentru utilizatori\n";
            $users_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n";
            $users_content .= "// Total utilizatori: " . count($existing_users) . "\n\n";
            $users_content .= '$users = ' . var_export($existing_users, true) . ";\n";
            $users_content .= "?>";
            
            $names_content = "<?php\n";
            $names_content .= "// Maparea numelor complete\n";
            $names_content .= "// Generat pe: " . date('Y-m-d H:i:s') . "\n\n";
            $names_content .= '$user_full_names = ' . var_export($existing_names, true) . ";\n";
            $names_content .= "?>";
            
            file_put_contents('includes/users_final.php', $users_content);
            file_put_contents('includes/user_names.php', $names_content);
        }
    }
    
    return $deleted_from_json || $deleted_from_main;
}

function regenerateUsersFile() {
    $users_data_file = 'users_data.json';
    
    // Încarcă utilizatorii existenți din users_final.php
    $existing_users = [];
    if (file_exists('includes/users_final.php')) {
        $content = file_get_contents('includes/users_final.php');
        eval(str_replace('<?php', '', $content));
        $existing_users = $users ?? [];
    }
    
    // Încarcă numele existente din user_names.php
    $existing_names = [];
    if (file_exists('includes/user_names.php')) {
        $names_content = file_get_contents('includes/user_names.php');
        eval(str_replace('<?php', '', $names_content));
        $existing_names = $user_full_names ?? [];
    }
    
    // Încarcă utilizatorii NOI din users_data.json
    $new_users_data = [];
    if (file_exists($users_data_file)) {
        $new_users_data = json_decode(file_get_contents($users_data_file), true) ?? [];
    }
    
    // COMBINĂ: păstrează utilizatorii vechi + adaugă cei noi
    $all_users_hash = $existing_users;
    $all_names = $existing_names;
    
    $added_count = 0;
    
    // Adaugă doar utilizatorii NOI care nu existau
    foreach ($new_users_data as $username => $data) {
        if (!isset($all_users_hash[$username])) {
            $all_users_hash[$username] = password_hash($data['password'], PASSWORD_DEFAULT);
            $all_names[$username] = $data['full_name'];
            $added_count++;
        }
    }
    
    // Salvează fișierele cu toți utilizatorii (vechi + noi)
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

// Încarcă utilizatorii existenți pentru afișare
$current_users = [];
$available_schools = ['smardioasa', 'cozmesti', 'perisoru', 'petresti', 'roman']; // Pentru dropdown

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
        $school = 'unknown';
        if (strpos($username, '.') !== false) {
            $school = explode('.', $username)[1];
            if (!in_array($school, $available_schools)) {
                $available_schools[] = $school; // Adaugă școala nouă în dropdown
            }
        }
        
        $current_users[$username] = [
            'full_name' => $all_names[$username] ?? $username,
            'school' => $school,
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
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .school-badge { background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
        .actions { white-space: nowrap; }
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
                <label for="school">Școala:</label>
                <select id="school" name="school">
                    <option value="">Selectează școala...</option>
                    <?php foreach ($available_schools as $school): ?>
                        <option value="<?= htmlspecialchars($school) ?>">
                            <?= ucfirst(htmlspecialchars($school)) ?>
                        </option>
                    <?php endforeach; ?>
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
        
        <a href="school_generator.php" style="margin-left: 10px;">
            <button type="button">🏫 Generator Școli</button>
        </a>
        
        <a href="csv_import.php" style="margin-left: 10px;">
            <button type="button">📊 Import CSV</button>
        </a>
        
        <a href="admin_logs.php" style="margin-left: 10px;">
            <button type="button">📋 Vezi Loguri</button>
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
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current_users as $username => $data): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($username) ?></strong></td>
                            <td><?= htmlspecialchars($data['full_name']) ?></td>
                            <td>
                                <span class="school-badge"><?= htmlspecialchars($data['school']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($data['created_at'] ?? 'Necunoscut') ?></td>
                            <td class="actions">
                                <?php if ($username !== 'admin'): ?>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Ești sigur că vrei să ștergi utilizatorul <?= htmlspecialchars($username) ?>?')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="username_to_delete" value="<?= htmlspecialchars($username) ?>">
                                        <button type="submit" class="btn-danger" style="padding: 4px 8px; font-size: 12px;">
                                            🗑️ Șterge
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <em style="color: #666;">Protejat</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
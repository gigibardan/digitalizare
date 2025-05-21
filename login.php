<?php
require_once 'includes/auth.php';

// Verificăm dacă utilizatorul este deja autentificat
if (isSessionValid()) {
    // Redirecționăm la pagina principală
    header('Location: /index.html');
    exit();
}

$error = '';

// Procesăm formularul de login dacă a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Verificăm credențialele
    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        // Autentificare reușită, setăm sesiunea
        $_SESSION['user'] = $username;
        $_SESSION['login_time'] = time();
        
        // Redirecționăm la pagina principală
        header('Location: /index.html');
        exit();
    } else {
        $error = 'Nume de utilizator sau parolă incorecte!';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare | TechMinds Academy</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn-login {
            display: inline-block;
            background-color: #1e88e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .error-message {
            color: #e53935;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="login-container">
        <h2>Autentificare</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Nume de utilizator:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Parolă:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Autentificare</button>
        </form>
    </div>
    
    <!-- Footer placeholder -->
    <div id="footer-placeholder"></div>
    
    <script src="/js/main.js"></script>
</body>
</html>
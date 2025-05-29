<?php
// Debug - elimină după ce rezolvi problema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// IMPORTANT: Includem config.php PRIMUL pentru că auth.php depinde de el
require_once 'config.php';
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
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Verificăm credențialele
        if (isset($users[$username]) && password_verify($password, $users[$username])) {
            // Autentificare reușită, setăm sesiunea
            $_SESSION['user'] = $username;
            $_SESSION['login_time'] = time();
            
            // Logăm evenimentul cu numele complet (folosim funcția din config.php)
            logEvent('SUCCESS', $username);
            
            // Redirecționăm la pagina principală
            header('Location: /index.html');
            exit();
        } else {
            // Autentificare eșuată
            $error = 'Nume de utilizator sau parolă incorecte!';
            
            // Logăm tentativa eșuată cu numele complet
            logEvent('FAILED', $username);
        }
    } else {
        $error = 'Vă rugăm să completați toate câmpurile!';
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
            max-width: 420px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 16px;
            padding: 5px;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #1e88e5;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-field input {
            padding-right: 50px;
        }
        
        .btn-login {
            width: 100%;
            background-color: #1e88e5;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-login:hover {
            background-color: #1565c0;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #1e88e5;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #1565c0;
            text-decoration: underline;
        }
        
        .icon-input {
            position: relative;
        }
        
        .icon-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 1;
        }
        
        .icon-input input {
            padding-left: 45px;
        }
        
        .login-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border-left: 4px solid #1e88e5;
            font-size: 14px;
            text-align: center;
        }
        
        .login-info p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-lock"></i> Autentificare</h2>
            <p>Digitalizare și Tehnici Moderne de Învățare cu Tabla Interactivă</p>
        </div>
        
        <!-- Informații generale -->
        <div class="login-info">
            <p><i class="fas fa-info-circle"></i> Introduceți credențialele primite de la administrator</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Nume de utilizator:
                </label>
                <div class="icon-input">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required autocomplete="username" 
                           placeholder="Introduceți numele de utilizator">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key"></i> Parolă:
                </label>
                <div class="password-field">
                    <div class="icon-input">
                        <i class="fas fa-key"></i>
                        <input type="password" id="password" name="password" 
                               required autocomplete="current-password" 
                               placeholder="Introduceți parola">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Autentificare
            </button>
        </form>
        
        <a href="/index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Înapoi la pagina principală
        </a>
    </div>
    
    <!-- Footer placeholder -->
    <div id="footer-placeholder"></div>
    
    <script src="/js/main.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Focus pe primul câmp la încărcarea paginii
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>
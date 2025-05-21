<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acces restricționat | TechMinds Academy</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .restricted-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .icon-lock {
            font-size: 60px;
            color: #e53935;
            margin-bottom: 20px;
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
            text-decoration: none;
            margin-top: 20px;
        }
        
        .contact-info {
            margin-top: 30px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header placeholder -->
    <div id="header-placeholder"></div>
    
    <div class="restricted-container">
        <div class="icon-lock">
            <i class="fas fa-lock"></i>
        </div>
        
        <h2>Acces restricționat</h2>
        
        <p>Conținutul pe care încerci să-l accesezi este disponibil doar pentru utilizatorii autentificați.</p>
        
        <a href="/login.php" class="btn-login">Autentificare</a>
        
        <div class="contact-info">
            <p>Dacă ai uitat parola sau ai nevoie de acces, te rugăm să ne contactezi la <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></p>
        </div>
    </div>
    
    <!-- Footer placeholder -->
    <div id="footer-placeholder"></div>
    
    <script src="/js/main.js"></script>
</body>
</html>
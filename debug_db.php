<?php
// Testează conexiunea pas cu pas

echo "<h3>Debug conectare baza de date</h3>";

// Pas 1: Verifică constantele
echo "<h4>1. Verifică constantele:</h4>";
require_once 'config/database.php';

echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : 'GOALĂ!') . "<br><br>";

// Pas 2: Testează conexiunea cu detalii
echo "<h4>2. Test conexiune cu eroare detaliată:</h4>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    echo "DSN: $dsn<br>";
    
    $pdo_test = new PDO($dsn, DB_USER, DB_PASS);
    $pdo_test->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<span style='color: green;'>SUCCESS: Conexiune reușită!</span><br>";
    
    // Pas 3: Verifică tabelele
    echo "<h4>3. Verifică tabelele:</h4>";
    $stmt = $pdo_test->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<span style='color: orange;'>Nu există tabele în baza de date.</span><br>";
    } else {
        echo "Tabele găsite: " . implode(', ', $tables) . "<br>";
        
        if (in_array('intrebari', $tables)) {
            $stmt = $pdo_test->query("SELECT COUNT(*) FROM intrebari");
            $count = $stmt->fetchColumn();
            echo "Întrebări în tabelă: $count<br>";
        }
    }
    
} catch(PDOException $e) {
    echo "<span style='color: red;'>ERROR: " . $e->getMessage() . "</span><br>";
    echo "Error Code: " . $e->getCode() . "<br>";
}
?>
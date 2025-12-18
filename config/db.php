<?php
/**
 * BOONCHUAY GYM - DATABASE CONNECTION
 * PDO MySQL Connection Configuration
 * 
 * This file provides a singleton database connection using PDO.
 * It should be included at the top of any PHP file that needs database access.
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

// Database credentials - UPDATE THESE FOR YOUR ENVIRONMENT
define('DB_HOST', 'localhost');        // Database host (usually 'localhost' for WAMP)
define('DB_NAME', 'boonchuay_gym');    // Database name
define('DB_USER', 'root');             // Database username (default 'root' for WAMP)
define('DB_PASS', 'aa105f81');                 // Database password (default empty for WAMP)
define('DB_CHARSET', 'utf8mb4');       // Character set

// ============================================
// PDO CONNECTION
// ============================================

try {
    // Create DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    // PDO options for better security and error handling
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES => false,                   // Use real prepared statements
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET // Set charset
    ];

    // Create PDO instance (database connection)
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Connection successful - you can uncomment the line below for debugging
    // echo "Database connection successful!";

} catch (PDOException $e) {
    // Connection failed - log error and show user-friendly message

    // Log the actual error (in production, write to error log file)
    error_log("Database Connection Error: " . $e->getMessage());

    // Show user-friendly error message (customize as needed)
    die("Error de conexión a la base de datos. Por favor, contacta al administrador del sistema.");

    // For development/debugging, you can uncomment this to see the actual error:
    // die("Database Connection Error: " . $e->getMessage());
}

/**
 * USAGE EXAMPLES:
 * 
 * 1. Simple SELECT query:
 * ---
 * require_once 'config/db.php';
 * $stmt = $pdo->query("SELECT * FROM disciplinas");
 * $disciplinas = $stmt->fetchAll();
 * 
 * 2. Prepared statement with parameters (RECOMMENDED for security):
 * ---
 * require_once 'config/db.php';
 * $stmt = $pdo->prepare("SELECT * FROM disciplinas WHERE id = :id");
 * $stmt->execute(['id' => $disciplina_id]);
 * $disciplina = $stmt->fetch();
 * 
 * 3. INSERT with prepared statement:
 * ---
 * require_once 'config/db.php';
 * $stmt = $pdo->prepare("INSERT INTO leads (nombre, email, telefono, mensaje, origen) 
 *                        VALUES (:nombre, :email, :telefono, :mensaje, :origen)");
 * $stmt->execute([
 *     'nombre' => $nombre,
 *     'email' => $email,
 *     'telefono' => $telefono,
 *     'mensaje' => $mensaje,
 *     'origen' => 'web'
 * ]);
 * 
 * 4. Get last inserted ID:
 * ---
 * $lastId = $pdo->lastInsertId();
 */

?>
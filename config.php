<?php
// Configuração da Base de Dados
define('ENVIRONMENT', 'hostinger'); 

if (ENVIRONMENT === 'local') {
    
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');      
    define('DB_PASS', '');         
    define('DB_NAME', 'u506280443_rafperDB'); 
    define('DB_PORT', 3306);
    
    
    define('SITE_URL', 'http://localhost/seu_projeto/'); 

} else {
    define('DB_HOST', 'localhost'); 
    define('DB_USER', 'u506280443_rafperdbUser');
    define('DB_PASS', '2&0>Kd=~GsP');
    define('DB_NAME', 'u506280443_rafperDB');
    define('DB_PORT', 3306);

    // Configurações gerais produção
    define('SITE_URL', 'http://antrob.eu/');
}

define('PROJECT_PATH', dirname(__FILE__));

// Criar conexão usando PDO
try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
} catch (PDOException $e) {
    die("Erro ao conectar à base de dados: " . $e->getMessage());
}
?>

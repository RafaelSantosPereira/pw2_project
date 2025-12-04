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

// Criar conexão usando MySQLi
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Verificar conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    // Definir charset para UTF-8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Erro ao conectar à base de dados: " . $e->getMessage());
}
?>

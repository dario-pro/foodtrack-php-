<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'sistema_alimentacion_db';

// configuracion adiccional del PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,     // Mostrar errores como excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de obtención por defecto
    PDO::ATTR_EMULATE_PREPARES => false,             // Usar prepared statements reales
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" // Configurar UTF-8
];
try {
    // Crear una nueva conexión PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, $options);
    // Establecer el modo de error a excepción
} catch (PDOException $e) {
    // encaso de error, mostrar un mensaje
    die("Error de conexión a la base de datos: " . $e->getMessage());
    
}
//funcion para ejecutar consultas seguras
function ejecutarConsulta($sql, $params = [])
{
    global $conn;
    if(!$conn){
        throw new Exception("Conexión a la base de datos no inicializada (conn es null)");
    }
    try {
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // Manejo de errores
        throw new Exception("Error al ejecutar la consulta: " . $e->getMessage());
    } 
}
function cerrarConexion(){
    global $conn;
    $conn = null; // Cerrar la conexión PDO
}
?>
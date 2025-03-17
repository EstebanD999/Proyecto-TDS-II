<?php
require_once "conexion.php"; // Asegúrate de que `$pdo` está definido antes de usarlo

if (!isset($pdo)) {
    die(json_encode(["error" => "Error en la conexión a la base de datos"]));
}

// Consulta para obtener la lista de usuarios
$sql = "SELECT 
    Usuario_Id, 
    nombre, 
    email, 
    Es_Admin
FROM usuarios 
ORDER BY nombre;"; // Asegúrate de que la tabla se llama "usuarios"

try {
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve un array asociativo

    // Formatear la respuesta para que sea fácil de usar en el frontend
    echo json_encode(["success" => true, "usuarios" => $usuarios]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Error al obtener los usuarios: " . $e->getMessage()]);
}
?>
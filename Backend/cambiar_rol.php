<?php
header('Content-Type: application/json'); // Indicamos que la respuesta será en formato JSON

// Incluimos el archivo de conexión a la base de datos
require_once 'conexion.php'; // Asegúrate de que la ruta sea correcta

if (!isset($pdo)) {
    die(json_encode(["success" => false, "error" => "Error en la conexión a la base de datos"]));
}

// Obtenemos los datos enviados por POST
$usuarioId = $_POST['Usuario_Id'] ?? null;
$esAdmin = $_POST['Es_Admin'] ?? null;

// Validamos que los datos no estén vacíos
if (!$usuarioId || $esAdmin === null) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

// Convertimos el valor de `es_admin` a entero (1 o 0)
$esAdmin = intval($esAdmin);

// Depuración: Verificar los datos recibidos
error_log("Datos recibidos - Usuario_Id: $usuarioId, Es_Admin: $esAdmin");

// Actualizamos el campo `Es_Admin` en la base de datos
try {
    $query = "UPDATE usuarios SET Es_Admin = :Es_Admin WHERE Usuario_Id = :Usuario_Id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':Es_Admin' => $esAdmin,
        ':Usuario_Id' => $usuarioId
    ]);

    // Depuración: Verificar el número de filas afectadas
    $rowCount = $stmt->rowCount();
    error_log("Filas afectadas: $rowCount");

    // Verificamos si se actualizó correctamente
    if ($rowCount > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "No se encontró el usuario o no se realizaron cambios"]);
    }
} catch (PDOException $e) {
    // Depuración: Verificar el error de la consulta
    error_log("Error en la consulta: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Error al actualizar el rol: " . $e->getMessage()]);
}
?>
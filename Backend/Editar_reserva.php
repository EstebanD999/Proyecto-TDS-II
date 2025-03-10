<?php
header("Content-Type: application/json"); // Asegura que la respuesta sea JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conexion.php"; // Asegúrate de que $pdo está definido.

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $horarioId = $_POST["Horario_Id"] ?? null;
    $horaInicio = $_POST["Hora_Inicio"] ?? null;
    $horaFin = $_POST["Hora_Fin"] ?? null;

    if (!$horarioId || !$horaInicio || !$horaFin) {
        $response["error"] = "Datos incompletos.";
        echo json_encode($response);
        exit;
    }

    // Verificar si la conexión a la base de datos está activa
    if (!$pdo) {
        $response["error"] = "Error de conexión a la base de datos.";
        echo json_encode($response);
        exit;
    }

    try {
        // Preparar la consulta SQL
        $sql = "UPDATE horarios SET Hora_Inicio = ?, Hora_Fin = ? WHERE Horario_Id = ?";
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([$horaInicio, $horaFin, $horarioId]);

        // Verificar si se actualizó algún registro
        if ($stmt->rowCount() > 0) {
            $response["success"] = true;
        } else {
            $response["error"] = "No se encontró el registro para actualizar.";
        }
    } catch (PDOException $e) {
        $response["error"] = "Error al actualizar: " . $e->getMessage();
    }
} else {
    $response["error"] = "Método no permitido.";
}

echo json_encode($response);
?>
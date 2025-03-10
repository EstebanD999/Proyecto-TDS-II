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
        // Obtener el ID de la sala asociada al horario que se está editando
        $stmt = $pdo->prepare("SELECT Sala_Id FROM reservas WHERE Horario_Id = ?");
        $stmt->execute([$horarioId]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            $response["error"] = "No se encontró la reserva asociada al horario.";
            echo json_encode($response);
            exit;
        }

        $sala_id = $reserva['Sala_Id'];

        // Verificar si ya existe una reserva en la misma sala con la misma hora de inicio o fin
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM reservas 
            JOIN horarios ON reservas.Horario_Id = horarios.Horario_Id 
            WHERE reservas.Sala_Id = ? 
            AND (horarios.Hora_Inicio = ? OR horarios.Hora_Fin = ?)
            AND reservas.Horario_Id != ?
        ");
        $stmt->execute([$sala_id, $horaInicio, $horaFin, $horarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $response["error"] = "Ya existe una reserva en la misma sala con la misma hora de inicio o fin.";
            echo json_encode($response);
            exit;
        }

        // Preparar la consulta SQL para actualizar el horario
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
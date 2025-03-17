<?php
header("Content-Type: application/json"); // Asegura que la respuesta sea JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conexion.php"; // Asegúrate de que $pdo está definido.

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $horarioId = $_POST["Horario_Id"] ?? null;
    $fechaReserva = $_POST["Fecha_Reserva"] ?? null;
    $horaInicio = $_POST["Hora_Inicio"] ?? null;
    $horaFin = $_POST["Hora_Fin"] ?? null;
    $videoBeam = $_POST["video_beam"] ?? 0;
    $microfono = $_POST["microfono"] ?? 0;
    $portatil = $_POST["portatil"] ?? 0;
    $cables = $_POST["cables_adicionales"] ?? 0;

    if (!$horarioId || !$fechaReserva) {
        $response["error"] = "Datos incompletos.";
        echo json_encode($response);
        exit;
    }

    $fechaActual = date("Y-m-d");
    if ($fechaReserva < $fechaActual) {
        $response["error"] = "No puedes reservar una sala en una fecha anterior al día actual.";
        echo json_encode($response);
        exit;
    }

    if (!$pdo) {
        $response["error"] = "Error de conexión a la base de datos.";
        echo json_encode($response);
        exit;
    }

    try {
        // Obtener la información actual de la reserva
        $stmt = $pdo->prepare("
            SELECT r.Sala_Id, h.Hora_Inicio, h.Hora_Fin 
            FROM reservas r
            JOIN horarios h ON r.Horario_Id = h.Horario_Id
            WHERE r.Horario_Id = ?
        ");
        $stmt->execute([$horarioId]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            $response["error"] = "No se encontró la reserva.";
            echo json_encode($response);
            exit;
        }

        $sala_id = $reserva['Sala_Id'];
        $horaInicioActual = $reserva['Hora_Inicio'];
        $horaFinActual = $reserva['Hora_Fin'];

        // Si la hora ha cambiado, verificar disponibilidad y actualizar
        if ($horaInicio !== $horaInicioActual || $horaFin !== $horaFinActual) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM reservas 
                JOIN horarios ON reservas.Horario_Id = horarios.Horario_Id 
                WHERE reservas.Sala_Id = ? 
                AND reservas.Fecha_Reserva = ?
                AND (
                    (horarios.Hora_Inicio < ? AND horarios.Hora_Fin > ?) OR
                    (horarios.Hora_Inicio < ? AND horarios.Hora_Fin > ?) OR
                    (horarios.Hora_Inicio >= ? AND horarios.Hora_Fin <= ?)
                )
                AND reservas.Horario_Id != ?
            ");
            $stmt->execute([$sala_id, $fechaReserva, $horaFin, $horaInicio, $horaInicio, $horaFin, $horaInicio, $horaFin, $horarioId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $response["error"] = "Ya existe una reserva en la misma sala con la misma fecha y hora.";
                echo json_encode($response);
                exit;
            }

            // Actualizar la hora solo si ha cambiado
            $stmt = $pdo->prepare("UPDATE horarios SET Hora_Inicio = ?, Hora_Fin = ? WHERE Horario_Id = ?");
            $stmt->execute([$horaInicio, $horaFin, $horarioId]);
        }

        // Actualizar los dispositivos en la tabla `reservas`
        $stmt = $pdo->prepare("
            UPDATE reservas 
            SET Fecha_Reserva = ?, video_beam = ?, microfono = ?, portatil = ?, cables_adicionales = ? 
            WHERE Horario_Id = ?
        ");
        $stmt->execute([$fechaReserva, $videoBeam, $microfono, $portatil, $cables, $horarioId]);

        $response["success"] = true;
    } catch (PDOException $e) {
        $response["error"] = "Error al actualizar: " . $e->getMessage();
    }
} else {
    $response["error"] = "Método no permitido.";
}

echo json_encode($response);
?>
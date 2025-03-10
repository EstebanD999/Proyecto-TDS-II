<?php
include('conexion.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['sala_id'], $data['Hora_Inicio'], $data['Hora_Fin']) || empty($data['sala_id'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos o sala no seleccionada"]);
    exit;
}

$sala_id = $data['sala_id'] ?? null;
$Hora_Inicio = $data['Hora_Inicio'];
$Hora_Fin = $data['Hora_Fin'];

error_log("ID de la sala recibido: " . print_r($sala_id, true)); // Debug sin romper JSON

try {
    // Verifica la conexión con la base de datos
    if (!isset($pdo)) {
        throw new Exception("Error en la conexión a la base de datos");
    }

    // ✅ Verificar si la sala existe en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM salas WHERE Sala_Id = ?");
    $stmt->execute([$sala_id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(["success" => false, "error" => "La sala seleccionada no existe"]);
        exit;
    }

    // ✅ Insertar el horario en la tabla horarios
    $stmt = $pdo->prepare("INSERT INTO horarios (Hora_Inicio, Hora_Fin) VALUES (?, ?)");
    $stmt->execute([$Hora_Inicio, $Hora_Fin]);

    // Obtener el ID del horario insertado
    $horario_id = $pdo->lastInsertId();

    // ✅ Insertar la reserva en la tabla reservas
    $stmt = $pdo->prepare("INSERT INTO reservas (Usuario_Id, Sala_Id, Horario_Id) VALUES (?, ?, ?)");
    $stmt->execute([1, $sala_id, $horario_id]); // ⚠ Aquí deberías reemplazar 1 por el ID real del usuario

    echo json_encode(["success" => true, "message" => "Reserva guardada correctamente"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Error en la base de datos: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
exit;

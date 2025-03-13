<?php
// Backend/horarios_ocupados.php
header('Content-Type: application/json');
include('conexion.php');

// Validar que el ID de la sala esté presente y sea un número
if (!isset($_GET['sala_id'])) {
    echo json_encode(["success" => false, "error" => "El ID de la sala es requerido."]);
    exit;
}

$sala_id = $_GET['sala_id'];

if (!is_numeric($sala_id)) {
    echo json_encode(["success" => false, "error" => "El ID de la sala debe ser un número."]);
    exit;
}

try {
    // Consulta para obtener los horarios ocupados de la sala
    $query = "SELECT hora_inicio, hora_fin 
              FROM reservas 
              JOIN horarios ON reservas.Horario_Id = horarios.Horario_Id
              WHERE reservas.Sala_Id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sala_id]);
    $horarios_ocupados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear las horas para asegurar que estén en formato HH:MM
    foreach ($horarios_ocupados as &$horario) {
        $horario['hora_inicio'] = date('H:i', strtotime($horario['hora_inicio']));
        $horario['hora_fin'] = date('H:i', strtotime($horario['hora_fin']));
    }

    echo json_encode(["success" => true, "horarios_ocupados" => $horarios_ocupados]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Error al obtener los horarios ocupados: " . $e->getMessage()]);
} finally {
    // Cerrar la conexión a la base de datos
    $pdo = null;
}
?>
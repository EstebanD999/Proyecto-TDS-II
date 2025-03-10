<?php
// Backend/horarios_ocupados.php
header('Content-Type: application/json');
include('conexion.php');

$sala_id = $_GET['sala_id']; // Obtener el ID de la sala desde la solicitud

try {
    // Consulta para obtener los horarios ocupados de la sala
    $query = "SELECT hora_inicio, hora_fin 
              FROM reservas 
              JOIN horarios ON reservas.Horario_Id = horarios.Horario_Id
              WHERE reservas.Sala_Id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sala_id]);
    $horarios_ocupados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "horarios_ocupados" => $horarios_ocupados]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
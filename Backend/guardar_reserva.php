<?php
session_start();
include('conexion.php');
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["success" => false, "error" => "Usuario no autenticado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Verificar que los datos requeridos están presentes
if (!isset($data['sala_id'], $data['Hora_Inicio'], $data['Hora_Fin'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

$sala_id = $data['sala_id'];
$Hora_Inicio = $data['Hora_Inicio'];
$Hora_Fin = $data['Hora_Fin'];
$usuario_id = $_SESSION['usuario_id']; // Obtener usuario desde la sesión

// Dispositivos (booleanos), verificamos si están definidos y tienen un valor verdadero
$videoBeam = isset($data['video_beam']) && $data['video_beam'] ? 1 : 0;
$microfono = isset($data['microfono']) && $data['microfono'] ? 1 : 0;
$portatil = isset($data['portatil']) && $data['portatil'] ? 1 : 0;
$cables = isset($data['cables_adicionales']) && $data['cables_adicionales'] ? 1 : 0;

try {
    if (!isset($pdo)) {
        throw new Exception("Error en la conexión a la base de datos");
    }

    // Verificar si la sala existe
    $stmt = $pdo->prepare("SELECT * FROM salas WHERE Sala_Id = ?");
    $stmt->execute([$sala_id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(["success" => false, "error" => "La sala seleccionada no existe"]);
        exit;
    }

    // Verificar si ya existe una reserva con la misma hora de inicio o la misma hora de fin
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM reservas 
        JOIN horarios ON reservas.Horario_Id = horarios.Horario_Id 
        WHERE reservas.Sala_Id = ? 
        AND (horarios.Hora_Inicio = ? OR horarios.Hora_Fin = ?)
    ");
    $stmt->execute([$sala_id, $Hora_Inicio, $Hora_Fin]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo json_encode(["success" => false, "error" => "Ya existe una reserva en la misma hora de inicio o fin"]);
        exit;
    }

    // Insertar horario
    $stmt = $pdo->prepare("INSERT INTO horarios (Hora_Inicio, Hora_Fin) VALUES (?, ?)");
    $stmt->execute([$Hora_Inicio, $Hora_Fin]);
    $horario_id = $pdo->lastInsertId();  // Obtener el ID del horario recién insertado

    // Insertar reserva
    $stmt = $pdo->prepare("INSERT INTO reservas (Sala_Id, Horario_Id, Usuario_Id, video_beam, microfono, portatil, cables_adicionales) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$sala_id, $horario_id, $usuario_id, $videoBeam, $microfono, $portatil, $cables]);

    echo json_encode(["success" => true, "message" => "Reserva realizada correctamente"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Error: " . $e->getMessage()]);
}
?>
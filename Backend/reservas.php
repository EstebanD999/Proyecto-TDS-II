<?php
require_once "conexion.php"; // Asegúrate de que `$pdo` está definido antes de usarlo

if (!isset($pdo)) {
    die(json_encode(["error" => "Error en la conexión a la base de datos"]));
}

$sql = "SELECT 
            r.Usuario_Id, 
            u.Nombre AS Usuario_Nombre, 
            r.Sala_Id, 
            s.Nombre_Sala AS Sala_Nombre, 
            r.Horario_Id, 
            h.Hora_Inicio, 
            h.Hora_Fin, 
            r.Fecha_Reserva,  // Agregar la fecha de la reserva
            r.video_beam, 
            r.microfono, 
            r.portatil, 
            r.cables_adicionales 
        FROM reservas r
        JOIN usuarios u ON r.Usuario_Id = u.Usuario_Id
        JOIN salas s ON r.Sala_Id = s.Sala_Id
        JOIN horarios h ON r.Horario_Id = h.Horario_Id
        ORDER BY r.Fecha_Reserva, h.Hora_Inicio";  // Ordenar por fecha y hora

$stmt = $pdo->query($sql);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve un array asociativo

// Formatear la respuesta para que los dispositivos aparezcan en un solo campo de texto
foreach ($reservas as &$reserva) {
    $dispositivos = [];
    if ($reserva['video_beam']) $dispositivos[] = "VideoBeam";
    if ($reserva['microfono']) $dispositivos[] = "Micrófono";
    if ($reserva['portatil']) $dispositivos[] = "Portátil";
    if ($reserva['cables_adicionales']) $dispositivos[] = "Cables";
    
    $reserva['Dispositivos'] = !empty($dispositivos) ? implode(", ", $dispositivos) : "N/A";
    unset($reserva['video_beam'], $reserva['microfono'], $reserva['portatil'], $reserva['cables_adicionales']); // Eliminar campos individuales
}

echo json_encode(["reservas" => $reservas]);
?>
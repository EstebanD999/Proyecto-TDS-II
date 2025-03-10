<?php
require_once "conexion.php"; // Asegúrate de que `$pdo` está definido antes de usarlo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $horarioId = $_POST["Horario_Id"] ?? null;

    if (!$horarioId) {
        echo json_encode(["error" => "Falta el ID del horario"]);
        exit;
    }

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Eliminar la reserva asociada al horario
        $stmt1 = $pdo->prepare("DELETE FROM reservas WHERE Horario_Id = ?");
        $stmt1->execute([$horarioId]);

        // Eliminar el horario correspondiente
        $stmt2 = $pdo->prepare("DELETE FROM horarios WHERE Horario_Id = ?");
        $stmt2->execute([$horarioId]);

        // Confirmar transacción
        $pdo->commit();

        echo json_encode(["success" => "Reserva eliminada correctamente"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error" => "Error al eliminar: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>

<?php
include('conexion.php');

header('Content-Type: application/json');

try {
    // Consulta para obtener todas las salas con su disponibilidad
    $stmt = $pdo->query("SELECT Sala_Id, Nombre_Sala FROM salas");
    $salas = $stmt->fetchAll();

    echo json_encode(["success" => true, "salas" => $salas]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

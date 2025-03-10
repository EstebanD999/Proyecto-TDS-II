<?php
session_start();

$response = [
    "loggedIn" => isset($_SESSION['usuario_id']),
    "isAdmin" => isset($_SESSION['es_admin']) && $_SESSION['es_admin']
];

header("Content-Type: application/json");
echo json_encode($response);
?>

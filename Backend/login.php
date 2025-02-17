<?php

include ('conexion.php');

if($_SERVER['REQUEST_METHOD']== 'POST'){
    $email = $_POST['emailLogin'];
    $password = $_POST['passwordLogin'];

    if(empty($email) || empty ($password)){
        echo "Por favor, rellena los campos";
        exit;
    }

    $stmt = $pdo -> prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt -> bindParam(':email' , $email);
    $stmt -> exec ();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        header("Location: ../Frotend/inicio.html"); 
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
}
?>
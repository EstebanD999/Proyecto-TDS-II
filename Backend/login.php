<?php
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Manejar recuperación de contraseña
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'check_email') {
            $email = $_POST['email'];
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            echo ($stmt->rowCount() > 0) ? "exists" : "not_found";
            exit;
        }
        
        if ($action === 'reset_password') {
            $email = $_POST['email'];
            $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            if ($stmt->execute([$newPassword, $email])) {
                echo "success";
            } else {
                echo "error";
            }
            exit;
        }
    }

    $email = $_POST['emailLogin'];
    $password = $_POST['passwordLogin'];
    
    if (empty($email) || empty($password)) {
        echo "Por favor, rellena los campos";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        header("Location: ../Frontend/inicio.html"); 
        exit;
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>
<?php
session_start();
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // 📌 Verificar si el email existe para la recuperación de contraseña
        if ($action === 'check_email') {
            $email = $_POST['email'];
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            echo ($stmt->rowCount() > 0) ? "exists" : "not_found";
            exit;
        }

        // 📌 Restablecer la contraseña
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

    // 📌 Login normal
    $email = trim($_POST['emailLogin']);
    $password = trim($_POST['passwordLogin']);

    if (empty($email) || empty($password)) {
        echo "Por favor, rellena los campos";
        exit;
    }

    $stmt = $pdo->prepare("SELECT Usuario_Id, password, es_admin FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 📌 Verificar la contraseña
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario_id'] = $user['Usuario_Id'];
        $_SESSION['es_admin'] = $user['es_admin'];

        if ($user['es_admin']) {
            echo "admin"; // ✅ Redirigir al panel de administrador
        } else {
            echo "user"; // ✅ Redirigir al área de usuario normal
        }
        exit;
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

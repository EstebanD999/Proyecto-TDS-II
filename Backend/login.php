<?php
include ('conexion.php'); // Asegúrate de que este archivo tiene una conexión válida con $pdo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $email = $_POST['emailLogin'] ?? '';
    $password = $_POST['passwordLogin'] ?? '';

    // Validar que no estén vacíos
    if (empty($email) || empty($password)) {
        echo "Por favor, rellena todos los campos.";
        exit;
    }

    // Verificar la conexión a la base de datos
    if (!$pdo) {
        die("Error de conexión a la base de datos.");
    }

    // Consultar usuario por email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);

    if (!$stmt->execute()) {
        print_r($stmt->errorInfo()); // Muestra errores de SQL si existen
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Correo electrónico no registrado.";
        exit;
    }

    // Mostrar información para depuración (quitar en producción)
    echo "Hash almacenado: " . $user['password'] . "<br>";

    // Verificar la contraseña
    if (password_verify($password, $user['password'])) {
        echo "Inicio de sesión exitoso. Redirigiendo...";
        header("Location: ../Frontend/inicio.html");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
}
?>

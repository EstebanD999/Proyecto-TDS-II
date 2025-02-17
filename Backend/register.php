<?php
include('conexion.php'); // Incluye el archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si los campos están definidos en $_POST
    if (isset($_POST['nameRegister']) && isset($_POST['emailRegister']) && isset($_POST['passwordLogin'])) {
        $nombre = $_POST['nameRegister'];
        $email = $_POST['emailRegister'];
        $password = $_POST['passwordLogin'];

        // Verifica que los campos no estén vacíos
        if (empty($nombre) || empty($email) || empty($password)) {
            echo "Por favor, rellena todos los campos.";
            exit;
        }

        // Verifica si el correo electrónico ya está registrado
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "El correo electrónico ya está registrado.";
                exit;
            }
        } catch (PDOException $e) {
            echo "Error al verificar el correo electrónico: " . $e->getMessage();
            exit;
        }

        // Hashea la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserta el nuevo usuario en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            echo "Registro exitoso. Ahora puedes iniciar sesión.";
        } catch (PDOException $e) {
            echo "Error al registrar el usuario: " . $e->getMessage();
        }
    } else {
        echo "Los campos no se enviaron correctamente.";
    }
}
?>
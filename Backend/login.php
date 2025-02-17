<?php
<<<<<<< HEAD
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si los campos están definidos en $_POST
    if (isset($_POST['emailLogin']) && isset($_POST['passwordLogin'])) {
        $email = $_POST['emailLogin'];
        $password = $_POST['passwordLogin'];

        // Verifica que los campos no estén vacíos
        if (empty($email) || empty($password)) {
            echo "Por favor, rellena los campos";
            exit;
        }

        try {
            // Busca el usuario en la base de datos
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Ruta relativa al archivo login.php
                $ruta = "../Frontend/inicio.html";
            
                // Verifica si el archivo existe (opcional, para depuración)
                if (file_exists($ruta)) {
                    header("Location: " . $ruta);
                    exit;
                } else {
                    echo "El archivo no existe en la ruta: " . $ruta;
                }
            } else {
                echo "Correo electrónico o contraseña incorrectos.";
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Los campos no se enviaron correctamente.";
    }
}
?>
=======
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
>>>>>>> 8ea7a8db3efe42ca2de3d9dad3a6f99985ed4262

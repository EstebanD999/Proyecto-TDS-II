<?php
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
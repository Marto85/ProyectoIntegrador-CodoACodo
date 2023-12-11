<?php
include '../models/db_config.php';

// Verifica si se ha enviado una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $email = $_POST["email"];
    $calle = $_POST["calle"];
    $altura = $_POST["altura"];
    $localidad = $_POST["localidad"];
    $partido = $_POST["partido"];
    $telefono = $_POST["telefono"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];
    
    // Validar los datos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($calle) || empty($altura) || empty($localidad) || empty($partido) || empty($telefono) || empty($password) || empty($confirmPassword)) {
        echo "Todos los campos son obligatorios";
    } elseif ($password != $confirmPassword) {
        echo "Las contraseñas no coinciden";
    } else {
        // Hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Sentencia preparada para evitar inyección SQL
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, calle, altura, localidad, partido, telefono, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Vincular los parámetros
        $stmt->bind_param("ssssissss", $nombre, $apellido, $email, $calle, $altura, $localidad, $partido, $telefono, $hashedPassword);

        // Ejecutar la sentencia preparada
        if ($stmt->execute()) {
            // cookie de sesion vigente 30 minutos
            $expiryTime = time() + (30 * 60); // 30 minutos
            setcookie("user_id", $conn->insert_id, $expiryTime, "/"); // Cambiar "user_id" según tus necesidades
            header("Location: ../index.php");
        } else {
            echo "Error en el registro: " . $stmt->error;
        }

        // Cerrar la sentencia preparada y la conexión
        $stmt->close();
        $conn->close();
    }
} else {
    echo "Acceso no permitido";
}
?>
<?php
/**
 * Endpoint: Registro de nuevos usuarios
 * Método: POST
 * Body: { "nombre": "...", "email": "...", "password": "..." }
 */

require_once '../config/database.php';
require_once '../utils/http.php';

configurarCORS();
validarMetodoHTTP('POST');

// Obtener datos del body
$datos = obtenerDatosJSON();

// Validar campos requeridos
if (!validarCamposRequeridos($datos, ['nombre', 'email', 'password'])) {
    exit();
}

// Validar formato de email
if (!validarEmail($datos->email)) {
    enviarError(400, "El formato del email no es válido");
}

// Validar longitud mínima de contraseña
if (strlen($datos->password) < 6) {
    enviarError(400, "La contraseña debe tener al menos 6 caracteres");
}

try {
    // Conectar a la base de datos
    $database = new Database();
    $db = $database->getConnection();

    if ($db === null) {
        enviarError(500, "Error al conectar con la base de datos");
    }

    // Verificar si el email ya existe
    $query_check = "SELECT id FROM usuarios WHERE email = :email LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':email', $datos->email);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        enviarError(409, "El email ya está registrado");
    }

    // Insertar nuevo usuario
    $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'estudiante')";
    $stmt = $db->prepare($query);

    // Sanitizar datos
    $nombre = htmlspecialchars(strip_tags($datos->nombre));
    $email = htmlspecialchars(strip_tags($datos->email));
    $password = $datos->password; // En texto plano (como en la app original)
    // NOTA: En producción usar password_hash($datos->password, PASSWORD_DEFAULT)

    // Bind parámetros
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    // Ejecutar
    if ($stmt->execute()) {
        $usuario_id = $db->lastInsertId();

        $usuario = array(
            "id" => $usuario_id,
            "nombre" => $nombre,
            "email" => $email,
            "rol" => "estudiante"
        );

        enviarRespuesta(201, $usuario, "Usuario registrado exitosamente");
    } else {
        enviarError(500, "Error al registrar el usuario");
    }

} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}
?>

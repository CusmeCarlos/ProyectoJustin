<?php
/**
 * Endpoint: Login de usuarios
 * Método: POST
 * Body: { "email": "...", "password": "..." }
 */

require_once '../config/database.php';
require_once '../utils/http.php';

configurarCORS();
validarMetodoHTTP('POST');

// Obtener datos del body
$datos = obtenerDatosJSON();

// Validar campos requeridos
if (!validarCamposRequeridos($datos, ['email', 'password'])) {
    exit();
}

// Validar formato de email
if (!validarEmail($datos->email)) {
    enviarError(400, "El formato del email no es válido");
}

try {
    // Conectar a la base de datos
    $database = new Database();
    $db = $database->getConnection();

    if ($db === null) {
        enviarError(500, "Error al conectar con la base de datos");
    }

    // Consultar usuario por email
    $query = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $datos->email);
    $stmt->execute();

    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña (en texto plano por ahora, como en la app)
        // NOTA: En producción deberías usar password_hash() y password_verify()
        if ($datos->password === $row['password']) {
            // Login exitoso - no enviar la contraseña al cliente
            $usuario = array(
                "id" => $row['id'],
                "nombre" => $row['nombre'],
                "email" => $row['email'],
                "rol" => $row['rol']
            );

            enviarExito($usuario, "Login exitoso");
        } else {
            enviarError(401, "Contraseña incorrecta");
        }
    } else {
        enviarError(404, "Usuario no encontrado");
    }

} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}
?>

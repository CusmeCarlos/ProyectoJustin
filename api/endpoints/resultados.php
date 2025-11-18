<?php
/**
 * Endpoint: CRUD de Resultados (Historial de intentos)
 * Métodos: GET, POST, DELETE
 *
 * GET /resultados.php - Listar todos los resultados
 * GET /resultados.php?id=1 - Obtener un resultado específico
 * GET /resultados.php?usuario_id=1 - Filtrar por usuario
 * GET /resultados.php?universidad_id=1 - Filtrar por universidad
 * POST /resultados.php - Crear nuevo resultado
 * DELETE /resultados.php?id=1 - Eliminar resultado
 */

require_once '../config/database.php';
require_once '../utils/http.php';

configurarCORS();

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    enviarError(500, "Error al conectar con la base de datos");
}

$metodo = $_SERVER['REQUEST_METHOD'];

try {
    switch ($metodo) {
        case 'GET':
            obtenerResultados($db);
            break;
        case 'POST':
            crearResultado($db);
            break;
        case 'DELETE':
            eliminarResultado($db);
            break;
        default:
            enviarError(405, "Método HTTP no permitido");
    }
} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}

/**
 * GET - Obtener resultados
 */
function obtenerResultados($db) {
    $id = obtenerParametroGET('id');
    $usuario_id = obtenerParametroGET('usuario_id');
    $universidad_id = obtenerParametroGET('universidad_id');

    if ($id !== null) {
        // Obtener un resultado específico
        $query = "SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email,
                         uni.nombre as universidad_nombre
                  FROM resultados r
                  LEFT JOIN usuarios u ON r.usuario_id = u.id
                  LEFT JOIN universidades uni ON r.universidad_id = uni.id
                  WHERE r.id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            enviarExito($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            enviarError(404, "Resultado no encontrado");
        }
    } else if ($usuario_id !== null) {
        // Filtrar por usuario
        $query = "SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email,
                         uni.nombre as universidad_nombre
                  FROM resultados r
                  LEFT JOIN usuarios u ON r.usuario_id = u.id
                  LEFT JOIN universidades uni ON r.universidad_id = uni.id
                  WHERE r.usuario_id = :usuario_id
                  ORDER BY r.fecha DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        $resultados = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $row;
        }

        enviarExito($resultados);
    } else if ($universidad_id !== null) {
        // Filtrar por universidad
        $query = "SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email,
                         uni.nombre as universidad_nombre
                  FROM resultados r
                  LEFT JOIN usuarios u ON r.usuario_id = u.id
                  LEFT JOIN universidades uni ON r.universidad_id = uni.id
                  WHERE r.universidad_id = :universidad_id
                  ORDER BY r.fecha DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':universidad_id', $universidad_id);
        $stmt->execute();

        $resultados = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $row;
        }

        enviarExito($resultados);
    } else {
        // Obtener todos los resultados
        $query = "SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email,
                         uni.nombre as universidad_nombre
                  FROM resultados r
                  LEFT JOIN usuarios u ON r.usuario_id = u.id
                  LEFT JOIN universidades uni ON r.universidad_id = uni.id
                  ORDER BY r.fecha DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $resultados = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $row;
        }

        enviarExito($resultados);
    }
}

/**
 * POST - Crear nuevo resultado
 */
function crearResultado($db) {
    $datos = obtenerDatosJSON();

    if (!validarCamposRequeridos($datos, ['usuario_id', 'universidad_id', 'correctas', 'incorrectas', 'total'])) {
        exit();
    }

    // Verificar que el usuario existe
    $query_user = "SELECT id FROM usuarios WHERE id = :usuario_id LIMIT 1";
    $stmt_user = $db->prepare($query_user);
    $stmt_user->bindParam(':usuario_id', $datos->usuario_id);
    $stmt_user->execute();

    if ($stmt_user->rowCount() === 0) {
        enviarError(404, "Usuario no encontrado");
    }

    // Verificar que la universidad existe
    $query_uni = "SELECT id FROM universidades WHERE id = :universidad_id LIMIT 1";
    $stmt_uni = $db->prepare($query_uni);
    $stmt_uni->bindParam(':universidad_id', $datos->universidad_id);
    $stmt_uni->execute();

    if ($stmt_uni->rowCount() === 0) {
        enviarError(404, "Universidad no encontrada");
    }

    // Insertar resultado
    $query = "INSERT INTO resultados (usuario_id, universidad_id, correctas, incorrectas, total, tiempo)
              VALUES (:usuario_id, :universidad_id, :correctas, :incorrectas, :total, :tiempo)";
    $stmt = $db->prepare($query);

    $tiempo = isset($datos->tiempo) ? $datos->tiempo : null;

    $stmt->bindParam(':usuario_id', $datos->usuario_id);
    $stmt->bindParam(':universidad_id', $datos->universidad_id);
    $stmt->bindParam(':correctas', $datos->correctas);
    $stmt->bindParam(':incorrectas', $datos->incorrectas);
    $stmt->bindParam(':total', $datos->total);
    $stmt->bindParam(':tiempo', $tiempo);

    if ($stmt->execute()) {
        $resultado_id = $db->lastInsertId();

        // Obtener el resultado creado
        $query_select = "SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email,
                                uni.nombre as universidad_nombre
                         FROM resultados r
                         LEFT JOIN usuarios u ON r.usuario_id = u.id
                         LEFT JOIN universidades uni ON r.universidad_id = uni.id
                         WHERE r.id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $resultado_id);
        $stmt_select->execute();
        $resultado = $stmt_select->fetch(PDO::FETCH_ASSOC);

        enviarRespuesta(201, $resultado, "Resultado guardado exitosamente");
    } else {
        enviarError(500, "Error al guardar el resultado");
    }
}

/**
 * DELETE - Eliminar resultado
 */
function eliminarResultado($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de resultado es requerido");
    }

    // Verificar que el resultado existe
    $query_check = "SELECT id FROM resultados WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Resultado no encontrado");
    }

    // Eliminar resultado
    $query = "DELETE FROM resultados WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        enviarExito(null, "Resultado eliminado exitosamente");
    } else {
        enviarError(500, "Error al eliminar el resultado");
    }
}
?>

<?php
/**
 * Endpoint: CRUD de Preguntas
 * Métodos: GET, POST, PUT, DELETE
 *
 * GET /preguntas.php - Listar todas las preguntas
 * GET /preguntas.php?id=1 - Obtener una pregunta específica
 * GET /preguntas.php?area=razonamiento - Filtrar por área
 * POST /preguntas.php - Crear nueva pregunta
 * PUT /preguntas.php?id=1 - Actualizar pregunta
 * DELETE /preguntas.php?id=1 - Eliminar pregunta
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
            obtenerPreguntas($db);
            break;
        case 'POST':
            crearPregunta($db);
            break;
        case 'PUT':
            actualizarPregunta($db);
            break;
        case 'DELETE':
            eliminarPregunta($db);
            break;
        default:
            enviarError(405, "Método HTTP no permitido");
    }
} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}

/**
 * GET - Obtener preguntas
 */
function obtenerPreguntas($db) {
    $id = obtenerParametroGET('id');
    $area = obtenerParametroGET('area');

    if ($id !== null) {
        // Obtener una pregunta específica
        $query = "SELECT * FROM preguntas WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $pregunta = formatearPregunta($stmt->fetch(PDO::FETCH_ASSOC));
            enviarExito($pregunta);
        } else {
            enviarError(404, "Pregunta no encontrada");
        }
    } else if ($area !== null) {
        // Filtrar por área
        $query = "SELECT * FROM preguntas WHERE area = :area ORDER BY id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':area', $area);
        $stmt->execute();

        $preguntas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $preguntas[] = formatearPregunta($row);
        }

        enviarExito($preguntas);
    } else {
        // Obtener todas las preguntas
        $query = "SELECT * FROM preguntas ORDER BY id";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $preguntas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $preguntas[] = formatearPregunta($row);
        }

        enviarExito($preguntas);
    }
}

/**
 * POST - Crear nueva pregunta
 */
function crearPregunta($db) {
    $datos = obtenerDatosJSON();

    // Validar si vienen opciones como array o como campos individuales
    if (isset($datos->opciones) && is_array($datos->opciones)) {
        // Formato: { "texto": "...", "opciones": ["op1", "op2", "op3", "op4"], "correcta": 0, "area": "..." }
        if (count($datos->opciones) !== 4) {
            enviarError(400, "Deben proporcionarse exactamente 4 opciones");
        }
        $datos->opcion1 = $datos->opciones[0];
        $datos->opcion2 = $datos->opciones[1];
        $datos->opcion3 = $datos->opciones[2];
        $datos->opcion4 = $datos->opciones[3];
    }

    if (!validarCamposRequeridos($datos, ['texto', 'opcion1', 'opcion2', 'opcion3', 'opcion4', 'correcta', 'area'])) {
        exit();
    }

    // Validar que correcta esté entre 0 y 3
    if ($datos->correcta < 0 || $datos->correcta > 3) {
        enviarError(400, "El campo 'correcta' debe estar entre 0 y 3");
    }

    // Insertar pregunta
    $query = "INSERT INTO preguntas (texto, opcion1, opcion2, opcion3, opcion4, correcta, area)
              VALUES (:texto, :opcion1, :opcion2, :opcion3, :opcion4, :correcta, :area)";
    $stmt = $db->prepare($query);

    $texto = htmlspecialchars(strip_tags($datos->texto));
    $opcion1 = htmlspecialchars(strip_tags($datos->opcion1));
    $opcion2 = htmlspecialchars(strip_tags($datos->opcion2));
    $opcion3 = htmlspecialchars(strip_tags($datos->opcion3));
    $opcion4 = htmlspecialchars(strip_tags($datos->opcion4));

    $stmt->bindParam(':texto', $texto);
    $stmt->bindParam(':opcion1', $opcion1);
    $stmt->bindParam(':opcion2', $opcion2);
    $stmt->bindParam(':opcion3', $opcion3);
    $stmt->bindParam(':opcion4', $opcion4);
    $stmt->bindParam(':correcta', $datos->correcta);
    $stmt->bindParam(':area', $datos->area);

    if ($stmt->execute()) {
        $pregunta_id = $db->lastInsertId();

        // Obtener la pregunta creada
        $query_select = "SELECT * FROM preguntas WHERE id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $pregunta_id);
        $stmt_select->execute();
        $pregunta = formatearPregunta($stmt_select->fetch(PDO::FETCH_ASSOC));

        enviarRespuesta(201, $pregunta, "Pregunta creada exitosamente");
    } else {
        enviarError(500, "Error al crear la pregunta");
    }
}

/**
 * PUT - Actualizar pregunta
 */
function actualizarPregunta($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de pregunta es requerido");
    }

    $datos = obtenerDatosJSON();

    // Verificar que la pregunta existe
    $query_check = "SELECT id FROM preguntas WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Pregunta no encontrada");
    }

    // Manejar opciones si vienen como array
    if (isset($datos->opciones) && is_array($datos->opciones)) {
        if (count($datos->opciones) === 4) {
            $datos->opcion1 = $datos->opciones[0];
            $datos->opcion2 = $datos->opciones[1];
            $datos->opcion3 = $datos->opciones[2];
            $datos->opcion4 = $datos->opciones[3];
        }
    }

    // Construir query dinámicamente
    $campos = array();
    $valores = array();

    if (isset($datos->texto)) {
        $campos[] = "texto = :texto";
        $valores[':texto'] = htmlspecialchars(strip_tags($datos->texto));
    }
    if (isset($datos->opcion1)) {
        $campos[] = "opcion1 = :opcion1";
        $valores[':opcion1'] = htmlspecialchars(strip_tags($datos->opcion1));
    }
    if (isset($datos->opcion2)) {
        $campos[] = "opcion2 = :opcion2";
        $valores[':opcion2'] = htmlspecialchars(strip_tags($datos->opcion2));
    }
    if (isset($datos->opcion3)) {
        $campos[] = "opcion3 = :opcion3";
        $valores[':opcion3'] = htmlspecialchars(strip_tags($datos->opcion3));
    }
    if (isset($datos->opcion4)) {
        $campos[] = "opcion4 = :opcion4";
        $valores[':opcion4'] = htmlspecialchars(strip_tags($datos->opcion4));
    }
    if (isset($datos->correcta)) {
        if ($datos->correcta < 0 || $datos->correcta > 3) {
            enviarError(400, "El campo 'correcta' debe estar entre 0 y 3");
        }
        $campos[] = "correcta = :correcta";
        $valores[':correcta'] = $datos->correcta;
    }
    if (isset($datos->area)) {
        $campos[] = "area = :area";
        $valores[':area'] = $datos->area;
    }

    if (count($campos) === 0) {
        enviarError(400, "No se proporcionaron campos para actualizar");
    }

    $query = "UPDATE preguntas SET " . implode(", ", $campos) . " WHERE id = :id";
    $stmt = $db->prepare($query);

    $valores[':id'] = $id;

    foreach ($valores as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        // Obtener la pregunta actualizada
        $query_select = "SELECT * FROM preguntas WHERE id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $id);
        $stmt_select->execute();
        $pregunta = formatearPregunta($stmt_select->fetch(PDO::FETCH_ASSOC));

        enviarExito($pregunta, "Pregunta actualizada exitosamente");
    } else {
        enviarError(500, "Error al actualizar la pregunta");
    }
}

/**
 * DELETE - Eliminar pregunta
 */
function eliminarPregunta($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de pregunta es requerido");
    }

    // Verificar que la pregunta existe
    $query_check = "SELECT id FROM preguntas WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Pregunta no encontrada");
    }

    // Eliminar pregunta
    $query = "DELETE FROM preguntas WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        enviarExito(null, "Pregunta eliminada exitosamente");
    } else {
        enviarError(500, "Error al eliminar la pregunta");
    }
}

/**
 * Formatear pregunta para que coincida con el formato de la app
 * Convierte opcion1, opcion2, etc. en un array "opciones"
 */
function formatearPregunta($pregunta) {
    $pregunta['opciones'] = array(
        $pregunta['opcion1'],
        $pregunta['opcion2'],
        $pregunta['opcion3'],
        $pregunta['opcion4']
    );

    // Remover campos individuales de opciones
    unset($pregunta['opcion1']);
    unset($pregunta['opcion2']);
    unset($pregunta['opcion3']);
    unset($pregunta['opcion4']);

    // Convertir correcta a número
    $pregunta['correcta'] = (int)$pregunta['correcta'];

    return $pregunta;
}
?>

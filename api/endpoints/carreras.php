<?php
/**
 * Endpoint: CRUD de Carreras
 * Métodos: GET, POST, PUT, DELETE
 *
 * GET /carreras.php - Listar todas las carreras
 * GET /carreras.php?id=1 - Obtener una carrera específica
 * GET /carreras.php?universidad_id=1 - Filtrar por universidad
 * POST /carreras.php - Crear nueva carrera
 * PUT /carreras.php?id=1 - Actualizar carrera
 * DELETE /carreras.php?id=1 - Eliminar carrera
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
            obtenerCarreras($db);
            break;
        case 'POST':
            crearCarrera($db);
            break;
        case 'PUT':
            actualizarCarrera($db);
            break;
        case 'DELETE':
            eliminarCarrera($db);
            break;
        default:
            enviarError(405, "Método HTTP no permitido");
    }
} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}

/**
 * GET - Obtener carreras
 */
function obtenerCarreras($db) {
    $id = obtenerParametroGET('id');
    $universidad_id = obtenerParametroGET('universidad_id');

    if ($id !== null) {
        // Obtener una carrera específica
        $query = "SELECT c.*, u.nombre as universidad_nombre
                  FROM carreras c
                  LEFT JOIN universidades u ON c.universidad_id = u.id
                  WHERE c.id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $carrera = $stmt->fetch(PDO::FETCH_ASSOC);
            // Renombrar universidad_id a uniId para coincidir con la app
            $carrera['uniId'] = $carrera['universidad_id'];
            unset($carrera['universidad_id']);
            enviarExito($carrera);
        } else {
            enviarError(404, "Carrera no encontrada");
        }
    } else if ($universidad_id !== null) {
        // Filtrar por universidad
        $query = "SELECT c.*, u.nombre as universidad_nombre
                  FROM carreras c
                  LEFT JOIN universidades u ON c.universidad_id = u.id
                  WHERE c.universidad_id = :universidad_id
                  ORDER BY c.nombre";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':universidad_id', $universidad_id);
        $stmt->execute();

        $carreras = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Renombrar universidad_id a uniId para coincidir con la app
            $row['uniId'] = $row['universidad_id'];
            unset($row['universidad_id']);
            $carreras[] = $row;
        }

        enviarExito($carreras);
    } else {
        // Obtener todas las carreras
        $query = "SELECT c.*, u.nombre as universidad_nombre
                  FROM carreras c
                  LEFT JOIN universidades u ON c.universidad_id = u.id
                  ORDER BY c.nombre";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $carreras = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Renombrar universidad_id a uniId para coincidir con la app
            $row['uniId'] = $row['universidad_id'];
            unset($row['universidad_id']);
            $carreras[] = $row;
        }

        enviarExito($carreras);
    }
}

/**
 * POST - Crear nueva carrera
 */
function crearCarrera($db) {
    $datos = obtenerDatosJSON();

    // Aceptar tanto universidad_id como uniId
    if (isset($datos->uniId) && !isset($datos->universidad_id)) {
        $datos->universidad_id = $datos->uniId;
    }

    if (!validarCamposRequeridos($datos, ['nombre', 'universidad_id'])) {
        exit();
    }

    // Verificar que la universidad existe
    $query_check = "SELECT id FROM universidades WHERE id = :universidad_id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':universidad_id', $datos->universidad_id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Universidad no encontrada");
    }

    // Insertar carrera
    $query = "INSERT INTO carreras (universidad_id, nombre, modalidad, matriz)
              VALUES (:universidad_id, :nombre, :modalidad, :matriz)";
    $stmt = $db->prepare($query);

    $nombre = htmlspecialchars(strip_tags($datos->nombre));
    $modalidad = isset($datos->modalidad) ? $datos->modalidad : 'Presencial';
    $matriz = isset($datos->matriz) ? $datos->matriz : 'Matriz Central';

    $stmt->bindParam(':universidad_id', $datos->universidad_id);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':modalidad', $modalidad);
    $stmt->bindParam(':matriz', $matriz);

    if ($stmt->execute()) {
        $carrera_id = $db->lastInsertId();

        // Obtener la carrera creada
        $query_select = "SELECT c.*, u.nombre as universidad_nombre
                         FROM carreras c
                         LEFT JOIN universidades u ON c.universidad_id = u.id
                         WHERE c.id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $carrera_id);
        $stmt_select->execute();
        $carrera = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Renombrar universidad_id a uniId
        $carrera['uniId'] = $carrera['universidad_id'];
        unset($carrera['universidad_id']);

        enviarRespuesta(201, $carrera, "Carrera creada exitosamente");
    } else {
        enviarError(500, "Error al crear la carrera");
    }
}

/**
 * PUT - Actualizar carrera
 */
function actualizarCarrera($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de carrera es requerido");
    }

    $datos = obtenerDatosJSON();

    // Verificar que la carrera existe
    $query_check = "SELECT id FROM carreras WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Carrera no encontrada");
    }

    // Aceptar tanto universidad_id como uniId
    if (isset($datos->uniId) && !isset($datos->universidad_id)) {
        $datos->universidad_id = $datos->uniId;
    }

    // Construir query dinámicamente
    $campos = array();
    $valores = array();

    if (isset($datos->nombre)) {
        $campos[] = "nombre = :nombre";
        $valores[':nombre'] = htmlspecialchars(strip_tags($datos->nombre));
    }
    if (isset($datos->universidad_id)) {
        // Verificar que la universidad existe
        $query_uni = "SELECT id FROM universidades WHERE id = :universidad_id LIMIT 1";
        $stmt_uni = $db->prepare($query_uni);
        $stmt_uni->bindParam(':universidad_id', $datos->universidad_id);
        $stmt_uni->execute();
        if ($stmt_uni->rowCount() === 0) {
            enviarError(404, "Universidad no encontrada");
        }

        $campos[] = "universidad_id = :universidad_id";
        $valores[':universidad_id'] = $datos->universidad_id;
    }
    if (isset($datos->modalidad)) {
        $campos[] = "modalidad = :modalidad";
        $valores[':modalidad'] = $datos->modalidad;
    }
    if (isset($datos->matriz)) {
        $campos[] = "matriz = :matriz";
        $valores[':matriz'] = $datos->matriz;
    }

    if (count($campos) === 0) {
        enviarError(400, "No se proporcionaron campos para actualizar");
    }

    $query = "UPDATE carreras SET " . implode(", ", $campos) . " WHERE id = :id";
    $stmt = $db->prepare($query);

    $valores[':id'] = $id;

    foreach ($valores as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        // Obtener la carrera actualizada
        $query_select = "SELECT c.*, u.nombre as universidad_nombre
                         FROM carreras c
                         LEFT JOIN universidades u ON c.universidad_id = u.id
                         WHERE c.id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $id);
        $stmt_select->execute();
        $carrera = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Renombrar universidad_id a uniId
        $carrera['uniId'] = $carrera['universidad_id'];
        unset($carrera['universidad_id']);

        enviarExito($carrera, "Carrera actualizada exitosamente");
    } else {
        enviarError(500, "Error al actualizar la carrera");
    }
}

/**
 * DELETE - Eliminar carrera
 */
function eliminarCarrera($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de carrera es requerido");
    }

    // Verificar que la carrera existe
    $query_check = "SELECT id FROM carreras WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Carrera no encontrada");
    }

    // Eliminar carrera
    $query = "DELETE FROM carreras WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        enviarExito(null, "Carrera eliminada exitosamente");
    } else {
        enviarError(500, "Error al eliminar la carrera");
    }
}
?>

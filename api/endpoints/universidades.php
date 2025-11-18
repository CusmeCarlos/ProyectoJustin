<?php
/**
 * Endpoint: CRUD de Universidades
 * Métodos: GET, POST, PUT, DELETE
 *
 * GET /universidades.php - Listar todas las universidades (con tipos de prueba)
 * GET /universidades.php?id=1 - Obtener una universidad específica
 * POST /universidades.php - Crear nueva universidad
 * PUT /universidades.php?id=1 - Actualizar universidad
 * DELETE /universidades.php?id=1 - Eliminar universidad
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
            obtenerUniversidades($db);
            break;
        case 'POST':
            crearUniversidad($db);
            break;
        case 'PUT':
            actualizarUniversidad($db);
            break;
        case 'DELETE':
            eliminarUniversidad($db);
            break;
        default:
            enviarError(405, "Método HTTP no permitido");
    }
} catch(PDOException $e) {
    enviarError(500, "Error en el servidor: " . $e->getMessage());
}

/**
 * GET - Obtener universidades
 */
function obtenerUniversidades($db) {
    $id = obtenerParametroGET('id');

    if ($id !== null) {
        // Obtener una universidad específica
        $query = "SELECT * FROM universidades WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $universidad = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener tipos de prueba
            $universidad['Tipodeprueba'] = obtenerTiposPrueba($db, $id);

            enviarExito($universidad);
        } else {
            enviarError(404, "Universidad no encontrada");
        }
    } else {
        // Obtener todas las universidades
        $query = "SELECT * FROM universidades ORDER BY id";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $universidades = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Obtener tipos de prueba para cada universidad
            $row['Tipodeprueba'] = obtenerTiposPrueba($db, $row['id']);
            $universidades[] = $row;
        }

        enviarExito($universidades);
    }
}

/**
 * POST - Crear nueva universidad
 */
function crearUniversidad($db) {
    $datos = obtenerDatosJSON();

    if (!validarCamposRequeridos($datos, ['nombre', 'porc_examen', 'porc_grado'])) {
        exit();
    }

    // Validar que los porcentajes sumen 100
    if (($datos->porc_examen + $datos->porc_grado) != 100) {
        enviarError(400, "Los porcentajes de examen y grado deben sumar 100");
    }

    // Insertar universidad
    $query = "INSERT INTO universidades (nombre, porc_examen, porc_grado, modalidad)
              VALUES (:nombre, :porc_examen, :porc_grado, :modalidad)";
    $stmt = $db->prepare($query);

    $nombre = htmlspecialchars(strip_tags($datos->nombre));
    $modalidad = isset($datos->modalidad) ? $datos->modalidad : 'Presencial';

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':porc_examen', $datos->porc_examen);
    $stmt->bindParam(':porc_grado', $datos->porc_grado);
    $stmt->bindParam(':modalidad', $modalidad);

    if ($stmt->execute()) {
        $universidad_id = $db->lastInsertId();

        // Insertar tipos de prueba si fueron proporcionados
        if (isset($datos->Tipodeprueba) && is_array($datos->Tipodeprueba)) {
            insertarTiposPrueba($db, $universidad_id, $datos->Tipodeprueba);
        }

        // Obtener la universidad creada
        $query_select = "SELECT * FROM universidades WHERE id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $universidad_id);
        $stmt_select->execute();
        $universidad = $stmt_select->fetch(PDO::FETCH_ASSOC);
        $universidad['Tipodeprueba'] = obtenerTiposPrueba($db, $universidad_id);

        enviarRespuesta(201, $universidad, "Universidad creada exitosamente");
    } else {
        enviarError(500, "Error al crear la universidad");
    }
}

/**
 * PUT - Actualizar universidad
 */
function actualizarUniversidad($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de universidad es requerido");
    }

    $datos = obtenerDatosJSON();

    // Verificar que la universidad existe
    $query_check = "SELECT id FROM universidades WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Universidad no encontrada");
    }

    // Construir query dinámicamente
    $campos = array();
    $valores = array();

    if (isset($datos->nombre)) {
        $campos[] = "nombre = :nombre";
        $valores[':nombre'] = htmlspecialchars(strip_tags($datos->nombre));
    }
    if (isset($datos->porc_examen)) {
        $campos[] = "porc_examen = :porc_examen";
        $valores[':porc_examen'] = $datos->porc_examen;
    }
    if (isset($datos->porc_grado)) {
        $campos[] = "porc_grado = :porc_grado";
        $valores[':porc_grado'] = $datos->porc_grado;
    }
    if (isset($datos->modalidad)) {
        $campos[] = "modalidad = :modalidad";
        $valores[':modalidad'] = $datos->modalidad;
    }

    if (count($campos) === 0) {
        enviarError(400, "No se proporcionaron campos para actualizar");
    }

    $query = "UPDATE universidades SET " . implode(", ", $campos) . " WHERE id = :id";
    $stmt = $db->prepare($query);

    $valores[':id'] = $id;

    foreach ($valores as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($stmt->execute()) {
        // Actualizar tipos de prueba si fueron proporcionados
        if (isset($datos->Tipodeprueba) && is_array($datos->Tipodeprueba)) {
            // Eliminar tipos de prueba anteriores
            $query_delete = "DELETE FROM tipos_prueba_universidad WHERE universidad_id = :id";
            $stmt_delete = $db->prepare($query_delete);
            $stmt_delete->bindParam(':id', $id);
            $stmt_delete->execute();

            // Insertar nuevos tipos de prueba
            insertarTiposPrueba($db, $id, $datos->Tipodeprueba);
        }

        // Obtener la universidad actualizada
        $query_select = "SELECT * FROM universidades WHERE id = :id";
        $stmt_select = $db->prepare($query_select);
        $stmt_select->bindParam(':id', $id);
        $stmt_select->execute();
        $universidad = $stmt_select->fetch(PDO::FETCH_ASSOC);
        $universidad['Tipodeprueba'] = obtenerTiposPrueba($db, $id);

        enviarExito($universidad, "Universidad actualizada exitosamente");
    } else {
        enviarError(500, "Error al actualizar la universidad");
    }
}

/**
 * DELETE - Eliminar universidad
 */
function eliminarUniversidad($db) {
    $id = obtenerParametroGET('id');

    if ($id === null) {
        enviarError(400, "ID de universidad es requerido");
    }

    // Verificar que la universidad existe
    $query_check = "SELECT id FROM universidades WHERE id = :id LIMIT 1";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        enviarError(404, "Universidad no encontrada");
    }

    // Eliminar universidad (CASCADE eliminará tipos de prueba y carreras)
    $query = "DELETE FROM universidades WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        enviarExito(null, "Universidad eliminada exitosamente");
    } else {
        enviarError(500, "Error al eliminar la universidad");
    }
}

/**
 * Obtener tipos de prueba de una universidad
 */
function obtenerTiposPrueba($db, $universidad_id) {
    $query = "SELECT tipo_prueba FROM tipos_prueba_universidad WHERE universidad_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $universidad_id);
    $stmt->execute();

    $tipos = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tipos[] = $row['tipo_prueba'];
    }

    return $tipos;
}

/**
 * Insertar tipos de prueba para una universidad
 */
function insertarTiposPrueba($db, $universidad_id, $tipos) {
    $query = "INSERT INTO tipos_prueba_universidad (universidad_id, tipo_prueba) VALUES (:universidad_id, :tipo_prueba)";
    $stmt = $db->prepare($query);

    foreach ($tipos as $tipo) {
        $stmt->bindParam(':universidad_id', $universidad_id);
        $stmt->bindParam(':tipo_prueba', $tipo);
        $stmt->execute();
    }
}
?>

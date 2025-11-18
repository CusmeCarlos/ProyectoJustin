<?php
/**
 * Utilidades HTTP para la API REST
 */

/**
 * Configurar CORS para permitir peticiones desde la aplicación Ionic
 */
function configurarCORS() {
    // Permitir peticiones desde cualquier origen (para desarrollo)
    // En producción, especifica el dominio de tu app Ionic
    header("Access-Control-Allow-Origin: *");

    // Métodos HTTP permitidos
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    // Headers permitidos
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    // Tipo de contenido JSON
    header("Content-Type: application/json; charset=UTF-8");

    // Para peticiones OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Enviar respuesta JSON con código HTTP
 * @param int $codigo Código HTTP (200, 400, 404, etc.)
 * @param mixed $datos Datos a enviar (array, objeto, etc.)
 * @param string $mensaje Mensaje opcional
 */
function enviarRespuesta($codigo, $datos = null, $mensaje = "") {
    http_response_code($codigo);

    $respuesta = array();

    if ($mensaje !== "") {
        $respuesta['mensaje'] = $mensaje;
    }

    if ($datos !== null) {
        if (is_array($datos) || is_object($datos)) {
            $respuesta['datos'] = $datos;
        } else {
            $respuesta['datos'] = $datos;
        }
    }

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Enviar respuesta de error
 * @param int $codigo Código HTTP de error
 * @param string $mensaje Mensaje de error
 */
function enviarError($codigo, $mensaje) {
    enviarRespuesta($codigo, null, $mensaje);
}

/**
 * Enviar respuesta exitosa
 * @param mixed $datos Datos a enviar
 * @param string $mensaje Mensaje opcional
 */
function enviarExito($datos = null, $mensaje = "Operación exitosa") {
    enviarRespuesta(200, $datos, $mensaje);
}

/**
 * Obtener datos del cuerpo de la petición (JSON)
 * @return object|null Objeto con los datos o null si hay error
 */
function obtenerDatosJSON() {
    $datos = json_decode(file_get_contents("php://input"));

    if (json_last_error() !== JSON_ERROR_NONE) {
        enviarError(400, "Error al parsear JSON: " . json_last_error_msg());
        return null;
    }

    return $datos;
}

/**
 * Validar que existan los campos requeridos
 * @param object $datos Objeto con los datos
 * @param array $camposRequeridos Array con nombres de campos requeridos
 * @return bool True si todos los campos existen, false y envía error si falta alguno
 */
function validarCamposRequeridos($datos, $camposRequeridos) {
    foreach ($camposRequeridos as $campo) {
        if (!isset($datos->$campo) || empty($datos->$campo)) {
            enviarError(400, "El campo '$campo' es requerido");
            return false;
        }
    }
    return true;
}

/**
 * Validar método HTTP
 * @param string $metodoEsperado Método esperado (GET, POST, PUT, DELETE)
 */
function validarMetodoHTTP($metodoEsperado) {
    if ($_SERVER['REQUEST_METHOD'] !== $metodoEsperado) {
        enviarError(405, "Método HTTP no permitido. Se esperaba: $metodoEsperado");
    }
}

/**
 * Obtener parámetro GET
 * @param string $nombre Nombre del parámetro
 * @param mixed $default Valor por defecto si no existe
 * @return mixed Valor del parámetro
 */
function obtenerParametroGET($nombre, $default = null) {
    return isset($_GET[$nombre]) ? $_GET[$nombre] : $default;
}

/**
 * Validar email
 * @param string $email Email a validar
 * @return bool True si es válido
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>

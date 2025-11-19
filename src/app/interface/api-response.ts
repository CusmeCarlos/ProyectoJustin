/**
 * Interfaces para las respuestas de la API REST
 *
 * Estas interfaces mapean el formato de respuesta que envía la API PHP.
 */

/**
 * Respuesta genérica de la API
 */
export interface ApiResponse<T> {
  mensaje?: string;
  datos?: T;
}

/**
 * Respuesta de error de la API
 */
export interface ApiError {
  mensaje: string;
  codigo?: number;
}

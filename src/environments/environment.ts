/**
 * Configuración de environment para desarrollo
 *
 * Este archivo contiene las configuraciones para el entorno de desarrollo.
 * Para producción, usa environment.prod.ts
 */

export const environment = {
  production: false,

  // URL base de la API REST
  // Cambia esta URL según tu configuración de XAMPP
  apiUrl: 'http://localhost/ProyectoJustin/api/endpoints',

  // Configuraciones adicionales
  apiTimeout: 30000, // 30 segundos

  // Nombre de la aplicación
  appName: 'Proyecto Justin',
  appVersion: '1.0.0'
};

/**
 * Configuración de environment para producción
 *
 * Este archivo se usa cuando la aplicación está en producción.
 */

export const environment = {
  production: true,

  // URL base de la API REST en producción
  // IMPORTANTE: Cambia esta URL a tu servidor de producción
  apiUrl: 'https://tu-servidor.com/api/endpoints',

  // Configuraciones adicionales
  apiTimeout: 30000, // 30 segundos

  // Nombre de la aplicación
  appName: 'Proyecto Justin',
  appVersion: '1.0.0'
};

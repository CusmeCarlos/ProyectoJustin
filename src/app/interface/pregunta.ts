export interface Pregunta {
  id: number;
  texto: string;      // Enunciado
  opciones: string[]; // 4 opciones
  correcta: number;   // índice 0-3 de la opción correcta
  area: string;
}

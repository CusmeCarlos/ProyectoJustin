import { Injectable } from '@angular/core';
import { Universidad } from '../interface/universidad';
import { Carrera } from '../interface/carrera';
import { Resultado } from '../interface/resultado';

// ==========================
// INTERFACES
// ==========================
export interface Pregunta {
  id: number;
  texto: string;
  opciones: string[];
  correcta: number;
  area: string;
}

export interface Usuario {
  id: number;
  nombre: string;
  email: string;
  password: string;
  rol: 'admin' | 'estudiante';
}

// ==========================
// SERVICIO PRINCIPAL
// ==========================
@Injectable({
  providedIn: 'root'
})
export class DataService {

  constructor() {
    this.crearAdminPorDefecto();
    this.repararUniversidadesGuardadas(); // 👈 REPARA DATOS VACÍOS
  }

  // ================================================
  // ===============   USUARIOS   ====================
  // ================================================
  crearAdminPorDefecto() {
    const lista = this.getUsuarios();
    const admin = lista.find(u => u.rol === 'admin');

    if (!admin) {
      lista.push({
        id: Date.now(),
        nombre: 'Administrador',
        email: 'admin@admin.com',
        password: '123456',
        rol: 'admin'
      });
      localStorage.setItem('usuarios', JSON.stringify(lista));
    }
  }

  getUsuarios(): Usuario[] {
    return JSON.parse(localStorage.getItem('usuarios') || '[]');
  }

  registrarUsuario(u: Usuario): boolean {
    const lista = this.getUsuarios();
    if (lista.find(x => x.email === u.email)) return false;
    lista.push(u);
    localStorage.setItem('usuarios', JSON.stringify(lista));
    return true;
  }

  login(email: string, password: string): Usuario | null {
    const lista = this.getUsuarios();
    const user = lista.find(u => u.email === email && u.password === password);

    if (user) {
      localStorage.setItem('usuario_actual', JSON.stringify(user));
      return user;
    }
    return null;
  }

  getUsuarioActual(): Usuario | null {
    return JSON.parse(localStorage.getItem('usuario_actual') || 'null');
  }

  logout() {
    localStorage.removeItem('usuario_actual');
  }

  // ================================================
  // ===============   UNIVERSIDADES   ===============
  // ================================================
  getUniversidadesDefault(): Universidad[] {
    return [
      { id: 1, nombre: 'Universidad de Guayaquil', porcExamen: 60, porcGrado: 40, Tipodeprueba: ['razonamiento'], modalidad: 'Presencial' },
      { id: 2, nombre: 'ESPOL', porcExamen: 50, porcGrado: 50, Tipodeprueba: ['razonamiento', 'conocimientos'], modalidad: 'Presencial' },
      { id: 3, nombre: 'UCE', porcExamen: 65, porcGrado: 35, Tipodeprueba: ['generales', 'conocimientos'], modalidad: 'Virtual' },
      { id: 4, nombre: 'UNEMI', porcExamen: 70, porcGrado: 30, Tipodeprueba: ['razonamiento', 'generales'], modalidad: 'Presencial' },
    ];
  }

  getUniversidades(): Universidad[] {
    const guardadas = localStorage.getItem('universidades');
    return guardadas ? JSON.parse(guardadas) : this.getUniversidadesDefault();
  }

  guardarUniversidades(lista: Universidad[]) {
    localStorage.setItem('universidades', JSON.stringify(lista));
  }

  agregarUniversidad(u: Universidad) {
    const arr = this.getUniversidades();
    arr.push(u);
    this.guardarUniversidades(arr);
  }

  editarUniversidad(u: Universidad) {
    const lista = this.getUniversidades();
    const index = lista.findIndex(x => x.id === u.id);
    if (index !== -1) {
      lista[index] = u;
      this.guardarUniversidades(lista);
    }
  }

  eliminarUniversidad(id: number) {
    let lista = this.getUniversidades();
    lista = lista.filter(u => u.id !== id);
    this.guardarUniversidades(lista);
  }

  // ========================================================
  // 👇 FUNCIÓN NUEVA — REPARA UNIVERSIDADES CON CAMPOS VACÍOS
  // ========================================================
  repararUniversidadesGuardadas() {
    let lista = this.getUniversidades();
    let cambios = false;

    lista = lista.map(u => {
      let cambio = false;

      if (u.porcExamen === undefined || u.porcExamen === null) {
        u.porcExamen = 50;
        cambio = true;
      }

      if (u.porcGrado === undefined || u.porcGrado === null) {
        u.porcGrado = 50;
        cambio = true;
      }

      if (!u.Tipodeprueba) {
        u.Tipodeprueba = [];
        cambio = true;
      }

      if (!u.modalidad || u.modalidad === '') {
        u.modalidad = 'Presencial';
        cambio = true;
      }

      if (cambio) cambios = true;
      return u;
    });

    if (cambios) {
      this.guardarUniversidades(lista);
      console.warn("✔ Universidades reparadas automáticamente");
    }
  }

  // ================================================
  // ===============   CARRERAS   ====================
  // ================================================
  getCarrerasDefault(): Carrera[] {
    return [
      { id: 1, uniId: 1, nombre: 'Ingeniería en Sistemas', modalidad: 'Presencial', matriz: 'Matriz Central' },
      { id: 2, uniId: 1, nombre: 'Medicina', modalidad: 'Presencial', matriz: 'Matriz Sur' },
      { id: 3, uniId: 2, nombre: 'Economía', modalidad: 'Online', matriz: 'Matriz Costa' },
      { id: 4, uniId: 3, nombre: 'Diseño Gráfico', modalidad: 'Presencial', matriz: 'Matriz Norte' }
    ];
  }

  getCarreras(): Carrera[] {
    const guardadas = localStorage.getItem('carreras');
    return guardadas ? JSON.parse(guardadas) : this.getCarrerasDefault();
  }

  guardarCarreras(lista: Carrera[]) {
    localStorage.setItem('carreras', JSON.stringify(lista));
  }

  agregarCarrera(c: Carrera) {
    const arr = this.getCarreras();
    arr.push(c);
    this.guardarCarreras(arr);
  }

  editarCarrera(c: Carrera) {
    const lista = this.getCarreras();
    const index = lista.findIndex(x => x.id === c.id);
    if (index !== -1) {
      lista[index] = c;
      this.guardarCarreras(lista);
    }
  }

  eliminarCarrera(id: number) {
    let lista = this.getCarreras();
    lista = lista.filter(c => c.id !== id);
    this.guardarCarreras(lista);
  }

  // ================================================
  // ===============   PREGUNTAS   ===================
  // ================================================
  preguntasPorUniversidadBase: { [key: number]: Pregunta[] } = {
    1: [
      {
        id: 1,
        texto: '¿Cuánto es 2 + 2?',
        opciones: ['1', '2', '3', '4'],
        correcta: 3,
        area: 'razonamiento'
      }
    ],
    2: [
      {
        id: 2,
        texto: 'El sol es:',
        opciones: ['Un planeta', 'Una estrella', 'Un satélite', 'Un cometa'],
        correcta: 1,
        area: 'conocimientos'
      }
    ],
    3: [],
    4: []
  };

  getPreguntasByUniversidad(uniId: number): Pregunta[] {
    const guardadas = localStorage.getItem('preguntas_' + uniId);
    return guardadas ? JSON.parse(guardadas) : this.preguntasPorUniversidadBase[uniId] || [];
  }

  guardarPreguntas(uniId: number, lista: Pregunta[]) {
    localStorage.setItem('preguntas_' + uniId, JSON.stringify(lista));
  }

  agregarPregunta(uniId: number, p: Pregunta) {
    const arr = this.getPreguntasByUniversidad(uniId);
    arr.push(p);
    this.guardarPreguntas(uniId, arr);
  }

  importarPreguntasExcel(uniId: number, preguntasExcel: Pregunta[]) {
    const arr = this.getPreguntasByUniversidad(uniId);

    preguntasExcel.forEach(p => {
      arr.push({
        id: p.id ?? Date.now(),
        texto: p.texto,
        opciones: p.opciones,
        correcta: p.correcta,
        area: p.area
      });
    });

    this.guardarPreguntas(uniId, arr);
  }

  editarPregunta(uniId: number, p: Pregunta) {
    const lista = this.getPreguntasByUniversidad(uniId);
    const index = lista.findIndex(x => x.id === p.id);
    if (index !== -1) {
      lista[index] = p;
      this.guardarPreguntas(uniId, lista);
    }
  }

  eliminarPregunta(uniId: number, id: number) {
    let lista = this.getPreguntasByUniversidad(uniId);
    lista = lista.filter(p => p.id !== id);
    this.guardarPreguntas(uniId, lista);
  }

  // ================================================
  // ===============   HISTORIAL   ===================
  // ================================================
  guardarIntento(uniId: number, datos: Resultado) {
    const key = `simulador_${uniId}`;
    const historial = JSON.parse(localStorage.getItem(key) || '[]');

    historial.push({
      ...datos,
      fecha: new Date().toLocaleString()
    });

    localStorage.setItem(key, JSON.stringify(historial));
  }

  obtenerHistorial(uniId: number) {
    return JSON.parse(localStorage.getItem(`simulador_${uniId}`) || '[]');
  }
}

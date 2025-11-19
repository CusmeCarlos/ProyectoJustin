import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError, BehaviorSubject, firstValueFrom } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { Universidad } from '../interface/universidad';
import { Carrera } from '../interface/carrera';
import { Resultado } from '../interface/resultado';
import { ApiResponse } from '../interface/api-response';
import { environment } from '../../environments/environment';

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
// SERVICIO PRINCIPAL CON API REST
// ==========================
@Injectable({
  providedIn: 'root'
})
export class DataService {

  private apiUrl = environment.apiUrl;
  private usuarioActualSubject = new BehaviorSubject<Usuario | null>(null);
  public usuarioActual$ = this.usuarioActualSubject.asObservable();

  constructor(private http: HttpClient) {
    // Cargar usuario actual desde localStorage al iniciar
    const usuarioGuardado = localStorage.getItem('usuario_actual');
    if (usuarioGuardado) {
      this.usuarioActualSubject.next(JSON.parse(usuarioGuardado));
    }
  }

  // ================================================
  // ===============   MANEJO DE ERRORES   ===========
  // ================================================
  private handleError(error: HttpErrorResponse) {
    let errorMessage = 'Ocurrió un error desconocido';

    if (error.error instanceof ErrorEvent) {
      // Error del lado del cliente
      errorMessage = `Error: ${error.error.message}`;
    } else {
      // Error del lado del servidor
      if (error.error && error.error.mensaje) {
        errorMessage = error.error.mensaje;
      } else {
        errorMessage = `Código de error ${error.status}: ${error.message}`;
      }
    }

    console.error('Error en la API:', errorMessage);
    return throwError(() => new Error(errorMessage));
  }

  // ================================================
  // ===============   USUARIOS   ====================
  // ================================================

  /**
   * Login de usuario con la API
   */
  async login(email: string, password: string): Promise<Usuario | null> {
    try {
      const response = await firstValueFrom(
        this.http.post<ApiResponse<Usuario>>(`${this.apiUrl}/login.php`, {
          email,
          password
        }).pipe(catchError(this.handleError))
      );

      if (response.datos) {
        const usuario = response.datos;
        localStorage.setItem('usuario_actual', JSON.stringify(usuario));
        this.usuarioActualSubject.next(usuario);
        return usuario;
      }

      return null;
    } catch (error) {
      console.error('Error en login:', error);
      return null;
    }
  }

  /**
   * Registro de nuevo usuario
   */
  async registrarUsuario(u: Omit<Usuario, 'id' | 'rol'>): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.post<ApiResponse<Usuario>>(`${this.apiUrl}/register.php`, {
          nombre: u.nombre,
          email: u.email,
          password: u.password
        }).pipe(catchError(this.handleError))
      );

      return !!response.datos;
    } catch (error) {
      console.error('Error en registro:', error);
      return false;
    }
  }

  /**
   * Obtener usuario actual desde localStorage
   */
  getUsuarioActual(): Usuario | null {
    return this.usuarioActualSubject.value;
  }

  /**
   * Logout del usuario
   */
  logout() {
    localStorage.removeItem('usuario_actual');
    this.usuarioActualSubject.next(null);
  }

  // Métodos legacy para compatibilidad (no implementados con API)
  getUsuarios(): Usuario[] {
    return [];
  }

  crearAdminPorDefecto() {
    // El admin ya existe en la BD
  }

  // ================================================
  // ===============   UNIVERSIDADES   ===============
  // ================================================

  /**
   * Obtener todas las universidades desde la API
   */
  async getUniversidades(): Promise<Universidad[]> {
    try {
      const response = await firstValueFrom(
        this.http.get<ApiResponse<Universidad[]>>(`${this.apiUrl}/universidades.php`)
          .pipe(catchError(this.handleError))
      );

      return response.datos || [];
    } catch (error) {
      console.error('Error al obtener universidades:', error);
      return [];
    }
  }

  /**
   * Agregar nueva universidad
   */
  async agregarUniversidad(u: Omit<Universidad, 'id'>): Promise<Universidad | null> {
    try {
      const response = await firstValueFrom(
        this.http.post<ApiResponse<Universidad>>(`${this.apiUrl}/universidades.php`, {
          nombre: u.nombre,
          porc_examen: u.porcExamen,
          porc_grado: u.porcGrado,
          modalidad: u.modalidad,
          Tipodeprueba: u.Tipodeprueba
        }).pipe(catchError(this.handleError))
      );

      return response.datos || null;
    } catch (error) {
      console.error('Error al agregar universidad:', error);
      return null;
    }
  }

  /**
   * Editar universidad existente
   */
  async editarUniversidad(u: Universidad): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.put<ApiResponse<Universidad>>(`${this.apiUrl}/universidades.php?id=${u.id}`, {
          nombre: u.nombre,
          porc_examen: u.porcExamen,
          porc_grado: u.porcGrado,
          modalidad: u.modalidad,
          Tipodeprueba: u.Tipodeprueba
        }).pipe(catchError(this.handleError))
      );

      return !!response.datos;
    } catch (error) {
      console.error('Error al editar universidad:', error);
      return false;
    }
  }

  /**
   * Eliminar universidad
   */
  async eliminarUniversidad(id: number): Promise<boolean> {
    try {
      await firstValueFrom(
        this.http.delete<ApiResponse<any>>(`${this.apiUrl}/universidades.php?id=${id}`)
          .pipe(catchError(this.handleError))
      );

      return true;
    } catch (error) {
      console.error('Error al eliminar universidad:', error);
      return false;
    }
  }

  // Métodos legacy para compatibilidad
  getUniversidadesDefault(): Universidad[] {
    return [];
  }

  guardarUniversidades(lista: Universidad[]) {
    // Ya no se usa localStorage
  }

  repararUniversidadesGuardadas() {
    // Ya no es necesario
  }

  // ================================================
  // ===============   CARRERAS   ====================
  // ================================================

  /**
   * Obtener todas las carreras desde la API
   */
  async getCarreras(): Promise<Carrera[]> {
    try {
      const response = await firstValueFrom(
        this.http.get<ApiResponse<Carrera[]>>(`${this.apiUrl}/carreras.php`)
          .pipe(catchError(this.handleError))
      );

      return response.datos || [];
    } catch (error) {
      console.error('Error al obtener carreras:', error);
      return [];
    }
  }

  /**
   * Agregar nueva carrera
   */
  async agregarCarrera(c: Omit<Carrera, 'id'>): Promise<Carrera | null> {
    try {
      const response = await firstValueFrom(
        this.http.post<ApiResponse<Carrera>>(`${this.apiUrl}/carreras.php`, {
          nombre: c.nombre,
          universidad_id: c.uniId,
          modalidad: c.modalidad,
          matriz: c.matriz
        }).pipe(catchError(this.handleError))
      );

      return response.datos || null;
    } catch (error) {
      console.error('Error al agregar carrera:', error);
      return null;
    }
  }

  /**
   * Editar carrera existente
   */
  async editarCarrera(c: Carrera): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.put<ApiResponse<Carrera>>(`${this.apiUrl}/carreras.php?id=${c.id}`, {
          nombre: c.nombre,
          universidad_id: c.uniId,
          modalidad: c.modalidad,
          matriz: c.matriz
        }).pipe(catchError(this.handleError))
      );

      return !!response.datos;
    } catch (error) {
      console.error('Error al editar carrera:', error);
      return false;
    }
  }

  /**
   * Eliminar carrera
   */
  async eliminarCarrera(id: number): Promise<boolean> {
    try {
      await firstValueFrom(
        this.http.delete<ApiResponse<any>>(`${this.apiUrl}/carreras.php?id=${id}`)
          .pipe(catchError(this.handleError))
      );

      return true;
    } catch (error) {
      console.error('Error al eliminar carrera:', error);
      return false;
    }
  }

  // Métodos legacy para compatibilidad
  getCarrerasDefault(): Carrera[] {
    return [];
  }

  guardarCarreras(lista: Carrera[]) {
    // Ya no se usa localStorage
  }

  // ================================================
  // ===============   PREGUNTAS   ===================
  // ================================================

  /**
   * Obtener preguntas filtradas por área (opcional)
   */
  async getPreguntasByUniversidad(uniId?: number, area?: string): Promise<Pregunta[]> {
    try {
      let url = `${this.apiUrl}/preguntas.php`;

      // Si se especifica un área, filtrar por ella
      if (area) {
        url += `?area=${area}`;
      }

      const response = await firstValueFrom(
        this.http.get<ApiResponse<Pregunta[]>>(url)
          .pipe(catchError(this.handleError))
      );

      return response.datos || [];
    } catch (error) {
      console.error('Error al obtener preguntas:', error);
      return [];
    }
  }

  /**
   * Agregar nueva pregunta
   */
  async agregarPregunta(uniId: number, p: Omit<Pregunta, 'id'>): Promise<Pregunta | null> {
    try {
      const response = await firstValueFrom(
        this.http.post<ApiResponse<Pregunta>>(`${this.apiUrl}/preguntas.php`, {
          texto: p.texto,
          opciones: p.opciones,
          correcta: p.correcta,
          area: p.area
        }).pipe(catchError(this.handleError))
      );

      return response.datos || null;
    } catch (error) {
      console.error('Error al agregar pregunta:', error);
      return null;
    }
  }

  /**
   * Importar preguntas desde Excel
   */
  async importarPreguntasExcel(uniId: number, preguntasExcel: Pregunta[]): Promise<boolean> {
    try {
      // Importar cada pregunta individualmente
      for (const pregunta of preguntasExcel) {
        await this.agregarPregunta(uniId, pregunta);
      }

      return true;
    } catch (error) {
      console.error('Error al importar preguntas:', error);
      return false;
    }
  }

  /**
   * Editar pregunta existente
   */
  async editarPregunta(uniId: number, p: Pregunta): Promise<boolean> {
    try {
      const response = await firstValueFrom(
        this.http.put<ApiResponse<Pregunta>>(`${this.apiUrl}/preguntas.php?id=${p.id}`, {
          texto: p.texto,
          opciones: p.opciones,
          correcta: p.correcta,
          area: p.area
        }).pipe(catchError(this.handleError))
      );

      return !!response.datos;
    } catch (error) {
      console.error('Error al editar pregunta:', error);
      return false;
    }
  }

  /**
   * Eliminar pregunta
   */
  async eliminarPregunta(uniId: number, id: number): Promise<boolean> {
    try {
      await firstValueFrom(
        this.http.delete<ApiResponse<any>>(`${this.apiUrl}/preguntas.php?id=${id}`)
          .pipe(catchError(this.handleError))
      );

      return true;
    } catch (error) {
      console.error('Error al eliminar pregunta:', error);
      return false;
    }
  }

  // Métodos legacy para compatibilidad
  preguntasPorUniversidadBase: { [key: number]: Pregunta[] } = {};

  guardarPreguntas(uniId: number, lista: Pregunta[]) {
    // Ya no se usa localStorage
  }

  // ================================================
  // ===============   HISTORIAL   ===================
  // ================================================

  /**
   * Guardar intento de simulador
   */
  async guardarIntento(uniId: number, datos: Resultado): Promise<boolean> {
    try {
      const usuario = this.getUsuarioActual();
      if (!usuario) {
        console.error('No hay usuario autenticado');
        return false;
      }

      const response = await firstValueFrom(
        this.http.post<ApiResponse<any>>(`${this.apiUrl}/resultados.php`, {
          usuario_id: usuario.id,
          universidad_id: uniId,
          correctas: datos.correctas,
          incorrectas: datos.incorrectas,
          total: datos.total,
          tiempo: datos.tiempo
        }).pipe(catchError(this.handleError))
      );

      return !!response.datos;
    } catch (error) {
      console.error('Error al guardar intento:', error);
      return false;
    }
  }

  /**
   * Obtener historial de intentos por universidad
   */
  async obtenerHistorial(uniId: number): Promise<any[]> {
    try {
      const response = await firstValueFrom(
        this.http.get<ApiResponse<any[]>>(`${this.apiUrl}/resultados.php?universidad_id=${uniId}`)
          .pipe(catchError(this.handleError))
      );

      return response.datos || [];
    } catch (error) {
      console.error('Error al obtener historial:', error);
      return [];
    }
  }
}

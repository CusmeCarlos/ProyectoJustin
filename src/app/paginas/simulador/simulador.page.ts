import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule } from '@angular/forms';
import { DataService, Pregunta } from '../../servicios/data.service';

interface Intento {
  universidadId: number;
  correctas: number;
  incorrectas: number;
  total: number;
  tiempo: string;
  fecha?: string;
}

@Component({
  selector: 'app-simulador',
  standalone: true,
  imports: [IonicModule, CommonModule, FormsModule],
  templateUrl: './simulador.page.html',
  styleUrls: ['./simulador.page.scss']
})
export class SimuladorPage {

  universidades: any[] = [];
  universidad: any | null = null;

  preguntas: Pregunta[] = [];
  respuestas: { [key: number]: any } = {};

  inicio: number = 0;
  tiempoTranscurrido: number = 0; // en segundos
  timerInterval: any;

  correctas: number = 0;
  incorrectas: number = 0;

  terminado: boolean = false;
  enCurso: boolean = false;

  historial: Intento[] = [];

  constructor(private data: DataService) {
    this.universidades = this.data.getUniversidades();
  }

  // Iniciar simulador
  comenzar() {
    if (!this.universidad) return;

    // Cargar preguntas y reiniciar estado
    this.preguntas = this.data.getPreguntasByUniversidad(this.universidad.id);
    this.respuestas = {};
    this.terminado = false;
    this.enCurso = true;
    this.tiempoTranscurrido = 0;

    // Iniciar temporizador
    this.timerInterval = setInterval(() => {
      this.tiempoTranscurrido++;
    }, 1000);

    this.inicio = Date.now();

    // Traer historial solo de esta universidad
    this.historial = this.data.obtenerHistorial(this.universidad.id);
  }

  // Enviar respuestas
  enviar() {
    if (!this.preguntas.length) return;

    clearInterval(this.timerInterval);
    this.enCurso = false;

    this.correctas = this.preguntas.filter(p => this.respuestas[p.id] === p.correcta).length;
    this.incorrectas = this.preguntas.length - this.correctas;

    const min = Math.floor(this.tiempoTranscurrido / 60);
    const seg = this.tiempoTranscurrido % 60;

    const intento: Intento = {
      universidadId: this.universidad!.id,
      correctas: this.correctas,
      incorrectas: this.incorrectas,
      total: this.preguntas.length,
      tiempo: `${min} min ${seg} seg`,
      fecha: new Date().toLocaleString()
    };

    // Guardar intento
    this.data.guardarIntento(this.universidad.id, intento);

    // Recargar historial solo de esta universidad
    this.historial = this.data.obtenerHistorial(this.universidad!.id);

    this.terminado = true;
  }

  // Para mostrar el tiempo en formato mm:ss
  get tiempoFormateado() {
    const min = Math.floor(this.tiempoTranscurrido / 60);
    const seg = this.tiempoTranscurrido % 60;
    return `${min.toString().padStart(2, '0')}:${seg.toString().padStart(2, '0')}`;
  }
}

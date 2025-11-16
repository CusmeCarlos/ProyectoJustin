import { Component } from '@angular/core';
import { CommonModule, NgFor, NgIf } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';

import * as XLSX from 'xlsx';

import { DataService, Pregunta } from '../../servicios/data.service';

@Component({
  selector: 'app-admin-preguntas',
  standalone: true,
  templateUrl: './admin-preguntas.page.html',
  styleUrls: ['./admin-preguntas.page.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    NgFor,
    NgIf
  ]
})
export class AdminPreguntasPage {

  universidades: any[] = [];
  preguntas: Pregunta[] = [];
  areas: string[] = [];

  uniId = 1;

  // === NUEVO PARA EL MODAL ===
  modalAbierto = false; 
  editando = false;

  nuevaPregunta: Pregunta = {
    id: 0,
    texto: '',
    opciones: ['', '', '', ''],
    correcta: 0,
    area: ''
  };

  constructor(private data: DataService) {}

  ngOnInit() {
    this.universidades = this.data.getUniversidades();
    if (this.universidades.length > 0) {
      this.uniId = this.universidades[0].id;
      this.cargar();
    }
  }

  cargar() {
    const uni = this.universidades.find(u => u.id === this.uniId);
    this.areas = uni?.areas || [];

    this.preguntas = this.data.getPreguntasByUniversidad(this.uniId);
  }

  // ================================
  //        ABRIR MODAL - CREAR
  // ================================
  abrirModalCrear() {
    this.editando = false;
    this.resetPregunta();
    this.modalAbierto = true;
  }

  // ================================
  //        ABRIR MODAL - EDITAR
  // ================================
  abrirModalEditar(p: Pregunta) {
    this.editando = true;
    this.nuevaPregunta = JSON.parse(JSON.stringify(p));
    this.modalAbierto = true;
  }

  // ================================
  //        CERRAR MODAL
  // ================================
  cerrarModal() {
    this.modalAbierto = false;
    this.resetPregunta();
  }

  // ================================
  //       CREAR PREGUNTA
  // ================================
  guardarPregunta() {

    if (!this.nuevaPregunta.texto.trim()) {
      alert("La pregunta no puede estar vacía");
      return;
    }

    if (this.nuevaPregunta.opciones.some(op => !op.trim())) {
      alert("Todas las opciones deben estar llenas");
      return;
    }

    if (!this.areas.includes(this.nuevaPregunta.area)) {
      alert("Seleccione un área válida");
      return;
    }

    this.nuevaPregunta.id = Date.now();

    this.data.agregarPregunta(this.uniId, { ...this.nuevaPregunta });

    this.cerrarModal();
    this.cargar();
  }

  // ================================
  //       GUARDAR EDICIÓN
  // ================================
  guardarEdicion() {
    this.data.editarPregunta(this.uniId, this.nuevaPregunta);

    alert("Pregunta actualizada correctamente");

    this.cerrarModal();
    this.cargar();
  }

  // ================================
  //        ELIMINAR PREGUNTA
  // ================================
  eliminar(id: number) {
    if (!confirm("¿Seguro que deseas eliminar esta pregunta?")) return;

    this.data.eliminarPregunta(this.uniId, id);
    this.cargar();
  }

  // ================================
  //        RESET FORM
  // ================================
  resetPregunta() {
    this.nuevaPregunta = {
      id: 0,
      texto: '',
      opciones: ['', '', '', ''],
      correcta: 0,
      area: ''
    };
  }

  // ================================
  //      IMPORTAR EXCEL
  // ================================
  importarExcel(event: any) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = (e: any) => {
      const arr = new Uint8Array(e.target.result);
      const workbook = XLSX.read(arr, { type: 'array' });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];

      const rows: any[] = XLSX.utils.sheet_to_json(sheet);

      const preguntasImportadas: Pregunta[] = rows.map(fila => ({
        id: Date.now() + Math.random(),
        texto: fila.texto,
        opciones: [
          fila.op1 || '',
          fila.op2 || '',
          fila.op3 || '',
          fila.op4 || ''
        ],
        correcta: Number(fila.correcta),
        area: fila.area || ''
      }));

      this.data.importarPreguntasExcel(this.uniId, preguntasImportadas);

      this.cargar();
      alert("Preguntas importadas correctamente.");
    };

    reader.readAsArrayBuffer(file);
  }
}

import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';

import { DataService } from '../../servicios/data.service';
import { Carrera } from '../../interface/carrera';

@Component({
  selector: 'app-admin-carreras',
  standalone: true,
  templateUrl: './admin-carreras.page.html',
  styleUrls: ['./admin-carreras.page.scss'],
  imports: [CommonModule, FormsModule, IonicModule]
})
export class AdminCarrerasPage {

  universidades: any[] = [];
  carreras: Carrera[] = [];

  uniId = 1;

  // PARA EL MODAL
  modalAbierto = false;
  editando = false;

  nuevaCarrera: Carrera = {
    id: 0,
    uniId: 0,
    nombre: '',
    modalidad: '',
    matriz: ''
  };

  constructor(private data: DataService) {}

  ngOnInit() {
    this.universidades = this.data.getUniversidades();
    if (this.universidades.length > 0) {
      this.uniId = this.universidades[0].id;
    }
    this.cargar();
  }

  cargar() {
    const todas = this.data.getCarreras();
    this.carreras = todas.filter(c => c.uniId === this.uniId);
  }

  // =====================
  // MODAL NUEVA CARRERA
  // =====================
  abrirModalCrear() {
    this.editando = false;
    this.nuevaCarrera = {
      id: 0,
      uniId: this.uniId,
      nombre: '',
      modalidad: '',
      matriz: ''
    };
    this.modalAbierto = true;
  }

  // =====================
  // MODAL EDITAR CARRERA
  // =====================
  abrirModalEditar(c: Carrera) {
    this.editando = true;
    this.nuevaCarrera = { ...c };
    this.modalAbierto = true;
  }

  cerrarModal() {
    this.modalAbierto = false;
  }

  // =====================
  // AGREGAR
  // =====================
  agregar() {

    if (!this.nuevaCarrera.nombre.trim()) return alert("Falta el nombre");
    if (!this.nuevaCarrera.modalidad.trim()) return alert("Falta la modalidad");
    if (!this.nuevaCarrera.matriz.trim()) return alert("Falta la matriz");

    const nueva: Carrera = {
      id: Date.now(),
      uniId: this.uniId,
      nombre: this.nuevaCarrera.nombre,
      modalidad: this.nuevaCarrera.modalidad,
      matriz: this.nuevaCarrera.matriz
    };

    this.data.agregarCarrera(nueva);

    this.cerrarModal();
    this.cargar();
  }

  // =====================
  // GUARDAR EDICIÓN
  // =====================
  guardarEdicion() {

    if (!this.nuevaCarrera.nombre.trim()) return alert("Falta el nombre");
    if (!this.nuevaCarrera.modalidad.trim()) return alert("Falta la modalidad");
    if (!this.nuevaCarrera.matriz.trim()) return alert("Falta la matriz");

    this.data.editarCarrera(this.nuevaCarrera);

    alert("Carrera actualizada correctamente");
    this.cerrarModal();
    this.cargar();
  }

  // =====================
  // ELIMINAR
  // =====================
  eliminar(id: number) {
    if (!confirm("¿Seguro deseas eliminar esta carrera?")) return;

    this.data.eliminarCarrera(id);
    this.cargar();
  }
}

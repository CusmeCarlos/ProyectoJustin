import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { DataService } from '../../servicios/data.service';
import { Universidad } from '../../interface/universidad';

@Component({
  selector: 'app-admin-universidades',
  standalone: true,
  templateUrl: './admin-universidades.page.html',
  styleUrls: ['./admin-universidades.page.scss'],
  imports: [CommonModule, FormsModule, IonicModule]
})
export class AdminUniversidadesPage implements OnInit {

  universidades: Universidad[] = [];

  modalAbierto = false;

  nuevaUni: Universidad = {
    id: 0,
    nombre: '',
    porcExamen: 50,
    porcGrado: 50,
    Tipodeprueba: [],
    modalidad: ''
  };

  editando = false;

  areasDisponibles = ['razonamiento', 'conocimientos', 'generales'];

  constructor(private data: DataService) {}

  ngOnInit() {
    this.universidades = this.data.getUniversidades();
  }

  // Abrir Modal
  abrirModal() {
    this.modalAbierto = true;
  }

  // Cerrar modal
  cerrarModal() {
    this.modalAbierto = false;
    this.resetFormulario();
  }

  // Manejo de checkboxes
  toggleArea(area: string, checked: boolean) {
    if (checked) {
      if (!this.nuevaUni.Tipodeprueba.includes(area)) {
        this.nuevaUni.Tipodeprueba.push(area);
      }
    } else {
      this.nuevaUni.Tipodeprueba = this.nuevaUni.Tipodeprueba.filter(
        (a: string) => a !== area
      );
    }
  }

  // Crear nueva universidad
  agregar() {
    if (!this.nuevaUni.nombre.trim()) {
      alert('Debe ingresar un nombre');
      return;
    }

    this.nuevaUni.id = Date.now();
    this.data.agregarUniversidad({ ...this.nuevaUni });

    alert('Universidad guardada');

    this.cerrarModal();
  }

  // Cargar datos en el formulario para editar
  editar(u: Universidad) {
    this.editando = true;
    this.nuevaUni = JSON.parse(JSON.stringify(u)); // Clonar
  }

  // Guardar edición
  guardarEdicion() {
    this.data.editarUniversidad(this.nuevaUni);

    alert("Universidad actualizada correctamente");

    this.cerrarModal();
  }

  // Eliminar universidad
  eliminar(id: number) {
    if (!confirm("¿Seguro que deseas eliminar esta universidad?")) return;

    this.data.eliminarUniversidad(id);
    this.universidades = this.data.getUniversidades();
  }

  // Resetear formulario
  resetFormulario() {
    this.nuevaUni = {
      id: 0,
      nombre: '',
      porcExamen: 50,
      porcGrado: 50,
      Tipodeprueba: [],
      modalidad: ''
    };

    this.editando = false;

    this.universidades = this.data.getUniversidades();
  }
}

import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule } from '@angular/forms';
import { DataService } from '../../servicios/data.service';

@Component({
  selector: 'app-porcentaje',
  standalone: true,
  imports: [IonicModule, CommonModule, FormsModule],
  templateUrl: './porcentaje.page.html',
  styleUrls: ['./porcentaje.page.scss']
})
export class PorcentajePage {

  universidades: any[] = [];
  universidadSeleccionada: any = null;

  notaExamen: number | null = null;
  notaGrado: number | null = null;

  puntajeFinal: number | null = null;

  constructor(private data: DataService) {
    this.universidades = this.data.getUniversidades(); // ✔ CORREGIDO
  }

  calcular() {
    if (!this.universidadSeleccionada || this.notaExamen === null || this.notaGrado === null) {
      this.puntajeFinal = null;
      return;
    }

    const uni = this.universidadSeleccionada;

    const escalaExamen = (this.notaExamen / 1000) * uni.porcExamen;
    const escalaGrado = (this.notaGrado / 10) * uni.porcGrado;

    const total = (escalaExamen + escalaGrado) * 10;

    this.puntajeFinal = Math.round(total);
  }
}

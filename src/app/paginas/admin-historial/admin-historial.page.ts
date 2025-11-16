import { Component } from '@angular/core';
import { IonicModule } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';   // 👈 IMPORTANTE
import { DataService } from '../../servicios/data.service';

@Component({
  selector: 'app-admin-historial',
  standalone: true,
  templateUrl: './admin-historial.page.html',
  styleUrls: ['./admin-historial.page.scss'],
  imports: [
    CommonModule,
    IonicModule,
    FormsModule   // 👈 AGREGA ESTO
  ]
})
export class AdminHistorialPage {

  universidades: any[] = [];
  uniId = 1;
  historial: any[] = [];

  constructor(private data: DataService) {}

  ngOnInit() {
    this.universidades = this.data.getUniversidades();
    this.uniId = this.universidades[0].id;
    this.cargar();
  }

  cargar() {
    this.historial = this.data.obtenerHistorial(this.uniId);
  }
}

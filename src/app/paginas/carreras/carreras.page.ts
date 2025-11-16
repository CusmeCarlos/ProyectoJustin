import { Component, OnInit } from '@angular/core';
import { IonicModule } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DataService } from '../../servicios/data.service';
import { Carrera } from '../../interface/carrera';

@Component({
  selector: 'app-carreras',
  standalone: true,
  imports: [IonicModule, CommonModule, FormsModule],
  templateUrl: './carreras.page.html',
  styleUrls: ['./carreras.page.scss']
})
export class CarrerasPage implements OnInit {

  carreras: Carrera[] = [];
  universidadesMap = new Map<number, string>();

  // Filtros
  filtroCarrera: string = '';
  filtroModalidad: string = '';
  filtroUniversidad: number | null = null;

  constructor(private data: DataService) {}

  ngOnInit() {
    // Cargar carreras y universidades desde el servicio
    this.carreras = this.data.getCarreras();
    const unis = this.data.getUniversidades();
    unis.forEach(u => this.universidadesMap.set(u.id, u.nombre));
  }

  // Método para obtener el nombre de la universidad sin prefijo extra
  nombreUni(id: number): string {
    return this.universidadesMap.get(id) || 'Sin universidad';
  }

  // Getter para filtrar las carreras según los filtros seleccionados
  get carrerasFiltradas() {
    return this.carreras.filter(c => {
      const nombreMatch = this.filtroCarrera
        ? c.nombre === this.filtroCarrera
        : true;

      const modalidadMatch = this.filtroModalidad
        ? c.modalidad.toLowerCase() === this.filtroModalidad.toLowerCase()
        : true;

      const universidadMatch = this.filtroUniversidad
        ? c.uniId === this.filtroUniversidad
        : true;

      return nombreMatch && modalidadMatch && universidadMatch;
    });
  }

  // Getter para obtener la lista de nombres únicos de carreras
  get listaCarreras(): string[] {
    const nombres = this.carreras.map(c => c.nombre);
    return Array.from(new Set(nombres)); // elimina duplicados
  }

}

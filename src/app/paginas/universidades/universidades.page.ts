import { Component, OnInit } from '@angular/core';
import { IonicModule } from '@ionic/angular';
import { CommonModule, NgIf, NgFor } from '@angular/common';
import { DataService } from '../../servicios/data.service';

@Component({
  selector: 'app-universidades',
  standalone: true,
  imports: [
    IonicModule,
    CommonModule,
    NgIf,
    NgFor
  ],
  templateUrl: './universidades.page.html',
  styleUrls: ['./universidades.page.scss']
})
export class UniversidadesPage implements OnInit {

  universidades: any[] = [];

  constructor(private data: DataService) {}

  ngOnInit() {
    this.universidades = this.data.getUniversidades();
  }
}

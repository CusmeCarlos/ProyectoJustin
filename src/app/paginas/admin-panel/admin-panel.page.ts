import { Component } from '@angular/core';
import { IonicModule } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin-panel',
  standalone: true,
  templateUrl: './admin-panel.page.html',
  styleUrls: ['./admin-panel.page.scss'],
  imports: [CommonModule, IonicModule]
})
export class AdminPanelPage {

  constructor(private router: Router) {}

  ir(ruta: string) {
    this.router.navigate([ruta]);
  }
}

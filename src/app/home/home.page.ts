import { Component } from '@angular/core';
import { RouterModule, Router } from '@angular/router';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [IonicModule, RouterModule],
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss']
})
export class HomePage {
  rol = localStorage.getItem('rol');

  constructor(private router: Router) {}

  logout() {
    localStorage.removeItem('rol');
    localStorage.removeItem('user');
    this.router.navigate(['/login']);
  }
}

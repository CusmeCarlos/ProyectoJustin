import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { DataService } from '../../servicios/data.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, IonicModule],
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss']
})
export class LoginPage {

  email = '';
  password = '';

  constructor(private data: DataService, private router: Router) {}

  login() {

    if (!this.email || !this.password) {
      alert("Completa todos los campos");
      return;
    }

    console.log("Intentando login con:", this.email, this.password);

    const usuario = this.data.login(this.email, this.password);

    console.log("Resultado login:", usuario);

    if (!usuario) {
      alert('Correo o contraseña incorrectos');
      return;
    }

    // Redirección correcta
    if (usuario.rol === 'admin') {
      this.router.navigate(['/admin-panel']);
    } else {
      this.router.navigate(['/home']);
    }
  }
}

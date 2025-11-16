import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { DataService } from '../../servicios/data.service';

@Component({
  selector: 'app-register',
  standalone: true,
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
  imports: [CommonModule, FormsModule, IonicModule]
})
export class RegisterPage {

  nombre = '';
  email = '';
  password = '';

  constructor(private data: DataService, private router: Router) {}

 registrar() {

  if (!this.nombre || !this.email || !this.password) {
    alert("Todos los campos son obligatorios");
    return;
  }

  const ok = this.data.registrarUsuario({
    id: Date.now(),
    nombre: this.nombre,
    email: this.email,
    password: this.password,
    rol: 'estudiante'
  });

  if (!ok) {
    alert('El correo ya está registrado');
    return;
  }

  alert('Usuario registrado con éxito');
  this.router.navigate(['/login']);
}

}

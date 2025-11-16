import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'home',
    loadComponent: () => import('./home/home.page').then((m) => m.HomePage),
  },
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
  {
    path: 'login',
    loadComponent: () => import('./paginas/login/login.page').then( m => m.LoginPage)
  },
  {
    path: 'universidades',
    loadComponent: () => import('./paginas/universidades/universidades.page').then( m => m.UniversidadesPage)
  },
  {
    path: 'carreras',
    loadComponent: () => import('./paginas/carreras/carreras.page').then( m => m.CarrerasPage)
  },
  {
    path: 'porcentaje',
    loadComponent: () => import('./paginas/porcentaje/porcentaje.page').then( m => m.PorcentajePage)
  },
  {
    path: 'simulador',
    loadComponent: () => import('./paginas/simulador/simulador.page').then( m => m.SimuladorPage)
  },
  {
    path: 'admin-preguntas',
    loadComponent: () => import('./paginas/admin-preguntas/admin-preguntas.page').then( m => m.AdminPreguntasPage)
  },
  {
    path: 'admin-universidades',
    loadComponent: () => import('./paginas/admin-universidades/admin-universidades.page').then( m => m.AdminUniversidadesPage)
  },
  {
    path: 'admin-carreras',
    loadComponent: () => import('./paginas/admin-carreras/admin-carreras.page').then( m => m.AdminCarrerasPage)
  },
  {
    path: 'admin-panel',
    loadComponent: () => import('./paginas/admin-panel/admin-panel.page').then( m => m.AdminPanelPage)
  },
  {
    path: 'admin-historial',
    loadComponent: () => import('./paginas/admin-historial/admin-historial.page').then( m => m.AdminHistorialPage)
  },
  {
    path: 'register',
    loadComponent: () => import('./paginas/register/register.page').then( m => m.RegisterPage)
  },
];

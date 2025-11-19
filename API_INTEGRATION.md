# Integración de la Aplicación Ionic con la API REST

Este documento explica cómo la aplicación Ionic standalone se conecta con la API REST PHP.

## Arquitectura

La aplicación ahora utiliza **HttpClient de Angular** para consumir la API REST en lugar de localStorage. Todos los datos se persisten en la base de datos MySQL.

## Configuración

### 1. Variables de Entorno

Las URLs de la API se configuran en los archivos de environment:

**Desarrollo** (`src/environments/environment.ts`):
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost/ProyectoJustin/api/endpoints'
};
```

**Producción** (`src/environments/environment.prod.ts`):
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://tu-servidor.com/api/endpoints'
};
```

### 2. Proveedor HttpClient

El `HttpClient` se provee en `src/main.ts`:

```typescript
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';

bootstrapApplication(AppComponent, {
  providers: [
    provideHttpClient(withInterceptorsFromDi()),
    // ...otros providers
  ],
});
```

## DataService Actualizado

El `DataService` ahora es **100% asíncrono** y usa la API REST para todas las operaciones.

### Métodos Principales

#### Autenticación

```typescript
// Login (ahora retorna Promise en lugar de valor síncrono)
const usuario = await this.dataService.login(email, password);

// Registro
const exito = await this.dataService.registrarUsuario({
  nombre: 'Juan',
  email: 'juan@example.com',
  password: '123456'
});

// Obtener usuario actual (sigue siendo síncrono)
const usuario = this.dataService.getUsuarioActual();

// Logout
this.dataService.logout();
```

#### Universidades

```typescript
// Obtener todas
const universidades = await this.dataService.getUniversidades();

// Agregar
const nueva = await this.dataService.agregarUniversidad({
  nombre: 'Nueva Universidad',
  porcExamen: 60,
  porcGrado: 40,
  modalidad: 'Presencial',
  Tipodeprueba: ['razonamiento', 'conocimientos']
});

// Editar
const exito = await this.dataService.editarUniversidad(universidad);

// Eliminar
const exito = await this.dataService.eliminarUniversidad(id);
```

#### Carreras

```typescript
// Obtener todas
const carreras = await this.dataService.getCarreras();

// Agregar
const nueva = await this.dataService.agregarCarrera({
  nombre: 'Ingeniería en Software',
  uniId: 1,
  modalidad: 'Presencial',
  matriz: 'Matriz Central'
});

// Editar
const exito = await this.dataService.editarCarrera(carrera);

// Eliminar
const exito = await this.dataService.eliminarCarrera(id);
```

#### Preguntas

```typescript
// Obtener todas las preguntas
const preguntas = await this.dataService.getPreguntasByUniversidad();

// Obtener preguntas filtradas por área
const preguntasRazonamiento = await this.dataService.getPreguntasByUniversidad(undefined, 'razonamiento');

// Agregar
const nueva = await this.dataService.agregarPregunta(uniId, {
  texto: '¿Pregunta?',
  opciones: ['A', 'B', 'C', 'D'],
  correcta: 0,
  area: 'razonamiento'
});

// Importar desde Excel
const exito = await this.dataService.importarPreguntasExcel(uniId, preguntasArray);

// Editar
const exito = await this.dataService.editarPregunta(uniId, pregunta);

// Eliminar
const exito = await this.dataService.eliminarPregunta(uniId, preguntaId);
```

#### Historial de Resultados

```typescript
// Guardar intento
const exito = await this.dataService.guardarIntento(uniId, {
  correctas: 15,
  incorrectas: 5,
  total: 20,
  tiempo: '8 min 30 seg'
});

// Obtener historial
const historial = await this.dataService.obtenerHistorial(uniId);
```

## Migración de Componentes

### ANTES (localStorage - síncrono)

```typescript
export class UniversidadesPage {
  universidades: Universidad[] = [];

  ngOnInit() {
    this.universidades = this.dataService.getUniversidades();
  }

  eliminar(id: number) {
    this.dataService.eliminarUniversidad(id);
    this.universidades = this.dataService.getUniversidades();
  }
}
```

### DESPUÉS (API - asíncrono)

```typescript
export class UniversidadesPage {
  universidades: Universidad[] = [];

  async ngOnInit() {
    await this.cargarUniversidades();
  }

  async cargarUniversidades() {
    this.universidades = await this.dataService.getUniversidades();
  }

  async eliminar(id: number) {
    const exito = await this.dataService.eliminarUniversidad(id);
    if (exito) {
      await this.cargarUniversidades();
    }
  }
}
```

## Manejo de Errores

Todos los métodos del DataService incluyen manejo de errores interno:

```typescript
async login(email: string, password: string) {
  try {
    const usuario = await this.dataService.login(email, password);
    
    if (usuario) {
      // Login exitoso
      this.router.navigate(['/home']);
    } else {
      // Credenciales incorrectas
      this.mostrarError('Usuario o contraseña incorrectos');
    }
  } catch (error) {
    // Error de red o servidor
    this.mostrarError('Error al conectar con el servidor');
  }
}
```

## Observables vs Promises

El DataService usa **Promises (async/await)** en lugar de Observables para facilitar el uso en componentes:

```typescript
// ✅ Forma correcta (Promise)
const universidades = await this.dataService.getUniversidades();

// ❌ NO usar (Observable ya no está expuesto)
this.dataService.getUniversidades().subscribe(...);
```

Si necesitas Observables para casos específicos, puedes envolver las Promises:

```typescript
import { from } from 'rxjs';

const universidades$ = from(this.dataService.getUniversidades());
universidades$.subscribe(data => console.log(data));
```

## Estados de Carga

Para una mejor UX, implementa estados de carga en tus componentes:

```typescript
export class UniversidadesPage {
  universidades: Universidad[] = [];
  cargando = false;

  async ngOnInit() {
    await this.cargarUniversidades();
  }

  async cargarUniversidades() {
    this.cargando = true;
    try {
      this.universidades = await this.dataService.getUniversidades();
    } catch (error) {
      console.error('Error al cargar universidades:', error);
    } finally {
      this.cargando = false;
    }
  }
}
```

En el template:

```html
<ion-spinner *ngIf="cargando"></ion-spinner>

<ion-list *ngIf="!cargando">
  <ion-item *ngFor="let u of universidades">
    {{ u.nombre }}
  </ion-item>
</ion-list>
```

## Configuración de CORS

La API ya está configurada para permitir peticiones desde cualquier origen. Si necesitas restringir el acceso, edita `/api/utils/http.php`:

```php
// Permitir solo desde tu app Ionic
header("Access-Control-Allow-Origin: http://localhost:8100");
```

## Testing Local

### 1. Asegúrate de que XAMPP esté corriendo:
- Apache: puerto 80
- MySQL: puerto 3306

### 2. Ejecuta la aplicación Ionic:
```bash
ionic serve
```

### 3. La app se conectará a:
```
http://localhost/ProyectoJustin/api/endpoints
```

## Producción

### 1. Actualiza `environment.prod.ts` con tu URL de producción

### 2. Construye la app:
```bash
ionic build --prod
```

### 3. Despliega el backend PHP en tu servidor

### 4. Asegúrate de configurar HTTPS en producción

## Troubleshooting

### Error: "Access to fetch has been blocked by CORS policy"
- Verifica que Apache esté corriendo
- Revisa que el archivo `api/utils/http.php` tenga la configuración de CORS correcta

### Error: "Failed to fetch" o "net::ERR_CONNECTION_REFUSED"
- Verifica que la URL de la API sea correcta en `environment.ts`
- Asegúrate de que XAMPP esté corriendo

### Error: "Unknown error occurred"
- Revisa la consola del navegador para ver el error exacto
- Verifica los logs de PHP en XAMPP

### Las peticiones funcionan pero no devuelven datos
- Verifica que la base de datos esté creada e importada correctamente
- Revisa que haya datos en las tablas de la BD

## Ventajas de la Arquitectura API-First

1. **Separación de responsabilidades**: Frontend y backend independientes
2. **Escalabilidad**: La API puede servir a múltiples clientes (web, móvil, etc.)
3. **Datos centralizados**: Un solo punto de verdad en la base de datos
4. **Testing**: Puedes probar la API con Postman independientemente de la app
5. **Mantenimiento**: Cambios en el backend no afectan el frontend (si se respeta el contrato de la API)

## Recursos

- [Documentación de la API](./api/README.md)
- [Colección de Postman](./api/Postman_Collection.json)
- [Guía de instalación de la API](./api/INSTALACION.md)

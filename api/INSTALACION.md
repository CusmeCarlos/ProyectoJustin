# 🚀 Guía de Instalación Rápida

## Paso 1: Instalar XAMPP

1. Descarga XAMPP desde: https://www.apachefriends.org/
2. Instala XAMPP en `C:\xampp` (Windows) o `/opt/lampp` (Linux)
3. Inicia **Apache** y **MySQL** desde el panel de control de XAMPP

## Paso 2: Copiar los Archivos

### Opción A: Proyecto completo
Copia todo el proyecto `ProyectoJustin` en la carpeta `htdocs` de XAMPP:
```
C:\xampp\htdocs\ProyectoJustin\
```

### Opción B: Solo la API
Copia la carpeta `api` en:
```
C:\xampp\htdocs\ProyectoJustin\api\
```

## Paso 3: Crear la Base de Datos

1. Abre tu navegador
2. Ve a: http://localhost/phpmyadmin
3. Click en la pestaña **SQL**
4. Copia y pega el contenido del archivo `database.sql`
5. Click en **Ejecutar** o **Go**

Esto creará:
- Base de datos: `proyecto_justin`
- 6 tablas: usuarios, universidades, tipos_prueba_universidad, carreras, preguntas, resultados
- Datos de ejemplo (4 universidades, 5 carreras, 4 preguntas, 1 admin)

## Paso 4: Verificar la Instalación

Abre en tu navegador:
```
http://localhost/ProyectoJustin/api/
```

Deberías ver una página con el estado de la API y todos los endpoints disponibles.

Si ves **"✅ Estado: Operativa"**, ¡todo está funcionando correctamente!

## Paso 5: Probar con Postman

### Prueba 1: Listar Universidades
```
GET http://localhost/ProyectoJustin/api/endpoints/universidades.php
```

### Prueba 2: Login
```
POST http://localhost/ProyectoJustin/api/endpoints/login.php
Content-Type: application/json

{
  "email": "admin@admin.com",
  "password": "123456"
}
```

## 🔧 Configuración Adicional (Opcional)

### Si MySQL tiene contraseña

Edita el archivo `api/config/database.php` línea 13:
```php
private $password = "tu_contraseña";
```

### Si el proyecto está en otra carpeta

Edita las URLs en tus requests de Postman para reflejar la ruta correcta.

## ❌ Solución de Problemas

### Error: "No se pudo conectar a la base de datos"
- Verifica que MySQL esté corriendo en XAMPP
- Verifica que la base de datos `proyecto_justin` exista en phpMyAdmin

### Error 404: Not Found
- Verifica que los archivos estén en `C:\xampp\htdocs\ProyectoJustin\api\`
- Verifica que Apache esté corriendo

### Error: "Access denied for user 'root'@'localhost'"
- Revisa la configuración de contraseña en `config/database.php`

## 📱 Conectar con la App Ionic

En tu app Ionic, cambia la URL base del servicio HTTP:

**Antes (localStorage):**
```typescript
// servicios/data.service.ts
private preguntas: Pregunta[] = [];
```

**Después (API):**
```typescript
import { HttpClient } from '@angular/common/http';

export class DataService {
  private apiUrl = 'http://localhost/ProyectoJustin/api/endpoints';

  constructor(private http: HttpClient) {}

  getUniversidades() {
    return this.http.get(`${this.apiUrl}/universidades.php`);
  }
}
```

## ✅ ¡Listo!

Tu API está funcionando y lista para usarse con Postman o desde tu aplicación Ionic.

Para más detalles, consulta el archivo `README.md`.

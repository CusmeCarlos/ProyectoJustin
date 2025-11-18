# API REST - Proyecto Justin

API REST desarrollada en PHP para la aplicación de simulación de pruebas de admisión universitaria. Compatible con XAMPP y Postman.

## 📋 Requisitos

- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **Postman** (para pruebas)
- Base de datos MySQL

## 🚀 Instalación

### 1. Configurar XAMPP

1. Instala XAMPP desde https://www.apachefriends.org/
2. Copia la carpeta `api` en `C:\xampp\htdocs\ProyectoJustin\api`
3. Inicia Apache y MySQL desde el panel de control de XAMPP

### 2. Crear la Base de Datos

1. Abre phpMyAdmin en http://localhost/phpmyadmin
2. Importa el archivo `database.sql` o ejecuta el script SQL manualmente
3. Esto creará la base de datos `proyecto_justin` con datos de ejemplo

### 3. Verificar la Configuración

Abre en tu navegador:
```
http://localhost/ProyectoJustin/api/endpoints/universidades.php
```

Deberías ver un JSON con las universidades de ejemplo.

## 📡 Endpoints Disponibles

Base URL: `http://localhost/ProyectoJustin/api/endpoints/`

### 🔐 Autenticación

#### Login
```http
POST /login.php
Content-Type: application/json

{
  "email": "admin@admin.com",
  "password": "123456"
}
```

**Respuesta exitosa:**
```json
{
  "mensaje": "Login exitoso",
  "datos": {
    "id": 1,
    "nombre": "Administrador",
    "email": "admin@admin.com",
    "rol": "admin"
  }
}
```

#### Registro
```http
POST /register.php
Content-Type: application/json

{
  "nombre": "Juan Pérez",
  "email": "juan@example.com",
  "password": "123456"
}
```

**Respuesta exitosa (201):**
```json
{
  "mensaje": "Usuario registrado exitosamente",
  "datos": {
    "id": 2,
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "rol": "estudiante"
  }
}
```

---

### 🎓 Universidades

#### Listar todas las universidades
```http
GET /universidades.php
```

#### Obtener una universidad específica
```http
GET /universidades.php?id=1
```

#### Crear universidad
```http
POST /universidades.php
Content-Type: application/json

{
  "nombre": "Universidad Técnica de Manabí",
  "porc_examen": 60,
  "porc_grado": 40,
  "modalidad": "Presencial",
  "Tipodeprueba": ["razonamiento", "conocimientos", "generales"]
}
```

#### Actualizar universidad
```http
PUT /universidades.php?id=1
Content-Type: application/json

{
  "nombre": "Universidad de Guayaquil - Actualizada",
  "porc_examen": 65,
  "porc_grado": 35
}
```

#### Eliminar universidad
```http
DELETE /universidades.php?id=1
```

---

### 📚 Carreras

#### Listar todas las carreras
```http
GET /carreras.php
```

#### Filtrar por universidad
```http
GET /carreras.php?universidad_id=1
```

#### Obtener una carrera específica
```http
GET /carreras.php?id=1
```

#### Crear carrera
```http
POST /carreras.php
Content-Type: application/json

{
  "nombre": "Ingeniería en Software",
  "universidad_id": 1,
  "modalidad": "Presencial",
  "matriz": "Matriz Central"
}
```

**Nota:** También puedes usar `uniId` en lugar de `universidad_id` para compatibilidad con la app.

#### Actualizar carrera
```http
PUT /carreras.php?id=1
Content-Type: application/json

{
  "nombre": "Ingeniería en Sistemas Computacionales",
  "modalidad": "Híbrida"
}
```

#### Eliminar carrera
```http
DELETE /carreras.php?id=1
```

---

### ❓ Preguntas

#### Listar todas las preguntas
```http
GET /preguntas.php
```

#### Filtrar por área
```http
GET /preguntas.php?area=razonamiento
```

Áreas válidas: `razonamiento`, `conocimientos`, `generales`

#### Obtener una pregunta específica
```http
GET /preguntas.php?id=1
```

#### Crear pregunta
```http
POST /preguntas.php
Content-Type: application/json

{
  "texto": "¿Cuál es la raíz cuadrada de 144?",
  "opciones": ["10", "11", "12", "13"],
  "correcta": 2,
  "area": "razonamiento"
}
```

**O con opciones individuales:**
```json
{
  "texto": "¿Cuál es la raíz cuadrada de 144?",
  "opcion1": "10",
  "opcion2": "11",
  "opcion3": "12",
  "opcion4": "13",
  "correcta": 2,
  "area": "razonamiento"
}
```

**Nota:** `correcta` es el índice (0-3) de la opción correcta.

#### Actualizar pregunta
```http
PUT /preguntas.php?id=1
Content-Type: application/json

{
  "texto": "¿Cuál es la raíz cuadrada de 169?",
  "opciones": ["11", "12", "13", "14"],
  "correcta": 2
}
```

#### Eliminar pregunta
```http
DELETE /preguntas.php?id=1
```

---

### 📊 Resultados (Historial)

#### Listar todos los resultados
```http
GET /resultados.php
```

#### Filtrar por usuario
```http
GET /resultados.php?usuario_id=1
```

#### Filtrar por universidad
```http
GET /resultados.php?universidad_id=1
```

#### Obtener un resultado específico
```http
GET /resultados.php?id=1
```

#### Crear resultado
```http
POST /resultados.php
Content-Type: application/json

{
  "usuario_id": 1,
  "universidad_id": 1,
  "correctas": 15,
  "incorrectas": 5,
  "total": 20,
  "tiempo": "8 min 45 seg"
}
```

#### Eliminar resultado
```http
DELETE /resultados.php?id=1
```

---

## 🧪 Pruebas en Postman

### Colección de Postman

Puedes importar esta colección en Postman:

1. Abre Postman
2. Click en "Import"
3. Crea una nueva colección llamada "Proyecto Justin API"
4. Agrega las siguientes requests:

**Variables de entorno:**
- `base_url`: `http://localhost/ProyectoJustin/api/endpoints`

**Requests de ejemplo:**

1. **Login**
   - POST `{{base_url}}/login.php`
   - Body (raw JSON):
   ```json
   {
     "email": "admin@admin.com",
     "password": "123456"
   }
   ```

2. **Listar Universidades**
   - GET `{{base_url}}/universidades.php`

3. **Crear Universidad**
   - POST `{{base_url}}/universidades.php`
   - Body (raw JSON):
   ```json
   {
     "nombre": "Universidad Técnica de Manabí",
     "porc_examen": 60,
     "porc_grado": 40,
     "modalidad": "Presencial",
     "Tipodeprueba": ["razonamiento", "conocimientos"]
   }
   ```

---

## 🔧 Estructura del Proyecto

```
api/
├── config/
│   └── database.php          # Configuración de conexión a MySQL
├── endpoints/
│   ├── login.php             # Endpoint de login
│   ├── register.php          # Endpoint de registro
│   ├── universidades.php     # CRUD de universidades
│   ├── carreras.php          # CRUD de carreras
│   ├── preguntas.php         # CRUD de preguntas
│   └── resultados.php        # CRUD de resultados
├── utils/
│   └── http.php              # Utilidades HTTP (CORS, respuestas JSON, etc.)
├── database.sql              # Script de base de datos
└── README.md                 # Esta documentación
```

---

## ⚠️ Notas Importantes

### Seguridad

**IMPORTANTE:** Esta API almacena contraseñas en **texto plano** para mantener compatibilidad con la versión de localStorage de la aplicación.

**Para producción:**
- Usar `password_hash()` y `password_verify()` de PHP
- Implementar autenticación JWT
- Agregar validación de tokens
- Habilitar HTTPS

### CORS

La API está configurada para permitir peticiones desde cualquier origen (`Access-Control-Allow-Origin: *`). En producción, especifica el dominio de tu app Ionic:

```php
header("Access-Control-Allow-Origin: http://localhost:8100");
```

### Configuración de Base de Datos

Si tu instalación de XAMPP tiene contraseña para MySQL, edita `/api/config/database.php`:

```php
private $password = "tu_contraseña_aqui";
```

---

## 📝 Datos de Prueba

### Usuario Administrador
- Email: `admin@admin.com`
- Password: `123456`
- Rol: `admin`

### Universidades Pre-cargadas
1. Universidad de Guayaquil (60% examen, 40% grado)
2. ESPOL (50% examen, 50% grado)
3. UCE (65% examen, 35% grado)
4. UNEMI (70% examen, 30% grado)

---

## 🐛 Solución de Problemas

### Error de conexión a base de datos
- Verifica que MySQL esté corriendo en XAMPP
- Confirma que la base de datos `proyecto_justin` existe
- Revisa las credenciales en `config/database.php`

### CORS bloqueado
- Asegúrate de que Apache esté corriendo
- Verifica que el archivo `utils/http.php` esté incluido en los endpoints

### 404 Not Found
- Verifica que la ruta del proyecto sea correcta en XAMPP
- Confirma que los archivos estén en `htdocs/ProyectoJustin/api/`

---

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

---

## 📄 Licencia

Este proyecto es parte del sistema de simulación de pruebas de admisión universitaria Proyecto Justin.

# UesPlay API

API REST backend para la plataforma **UesPlay**, un repositorio digital de recursos educativos y multimedia de la Universidad de El Salvador. Permite gestionar usuarios, recursos, plataformas, dispositivos, licencias, tipos de medios, comentarios y más, con autenticación basada en JWT y control de acceso por roles y permisos.

---

## Índice

1. [Tecnologías](#tecnologías)
2. [Estructura de carpetas](#estructura-de-carpetas)
3. [Endpoints](#endpoints)
   - [Públicos](#públicos)
   - [Autenticación](#autenticación)
   - [Administración (requiere JWT)](#administración-requiere-jwt)
4. [Restauración y ejecución local](#restauración-y-ejecución-local)

---

## Tecnologías

| Tecnología / Librería              | Versión       | Descripción                                      |
|------------------------------------|---------------|--------------------------------------------------|
| PHP                                | ^8.2          | Lenguaje principal del backend                   |
| Laravel Framework                  | ^12.0         | Framework PHP para desarrollo web / API          |
| Laravel Sanctum                    | ^4.1          | Autenticación por tokens (Sanctum)               |
| php-open-source-saver/jwt-auth     | ^2.8          | Autenticación JWT                                |
| Laravel Tinker                     | ^2.10.1       | REPL para interacción con la aplicación          |
| aws/aws-sdk-php                    | ^3.367        | Integración con servicios AWS (S3)               |
| league/flysystem-aws-s3-v3         | ^3.30         | Adaptador Flysystem para S3                      |
| phpoffice/phpspreadsheet           | ^5.0          | Generación y lectura de archivos Excel           |
| PostgreSQL                         | —             | Motor de base de datos relacional                |
| Vite                               | ^6.2.4        | Bundler de assets frontend                       |
| Tailwind CSS                       | ^4.0.0        | Framework CSS de utilidades                      |
| PHPUnit                            | ^11.5.3       | Framework de pruebas unitarias                   |

---

## Estructura de carpetas

```
UesPlayAPI/
├── app/                        # Núcleo Laravel (Middleware, Modelos base, Providers)
├── bootstrap/                  # Arranque de la aplicación
├── config/                     # Archivos de configuración (auth, db, jwt, mail…)
├── database/                   # Migraciones, factories y seeders
├── public/                     # Punto de entrada HTTP (index.php, assets)
├── resources/                  # Vistas blade, CSS y JS sin compilar
├── routes/                     # Definición de rutas Laravel (api.php, web.php)
├── src/                        # Código fuente principal (arquitectura por capas)
│   ├── Application/            # Capa de aplicación
│   │   ├── Controllers/        # Controladores HTTP de la API
│   │   ├── Providers/          # Service providers específicos del dominio
│   │   └── Routes/             # Registro de rutas de la API (routes.php)
│   ├── Domain/                 # Capa de dominio (lógica de negocio)
│   │   ├── Entities/           # Entidades del negocio
│   │   ├── Exceptions/         # Excepciones personalizadas
│   │   ├── Helpers/            # Utilidades y helpers de dominio
│   │   ├── Interfaces/         # Contratos / interfaces
│   │   ├── Mappers/            # Transformación de datos entre capas
│   │   └── Services/           # Servicios de negocio
│   └── Infrastructure/         # Capa de infraestructura
│       ├── Functions/          # Funciones de infraestructura / utilidades
│       └── Repositories/       # Implementación de repositorios (acceso a datos)
├── storage/                    # Logs, caché, archivos generados
└── tests/                      # Pruebas unitarias y de integración
```

---

## Endpoints

> **Base URL:** `http://localhost:8000/api`  
> Los endpoints bajo `/admin` requieren el header `Authorization: Bearer <token>`.

### Públicos

| Método | Ruta                                               | Descripción                            |
|--------|----------------------------------------------------|----------------------------------------|
| GET    | `/devices`                                         | Lista de dispositivos                  |
| GET    | `/genres`                                          | Lista de géneros multimedia            |
| GET    | `/languages`                                       | Lista de idiomas                       |
| GET    | `/licences`                                        | Lista de licencias                     |
| GET    | `/platforms`                                       | Lista de plataformas                   |
| GET    | `/media-types`                                     | Lista de tipos de medios               |
| GET    | `/media-genres`                                    | Lista de géneros de medios             |
| GET    | `/resources-types`                                 | Lista de tipos de recurso              |
| GET    | `/resources`                                       | Lista de recursos                      |
| GET    | `/resources/{resourceId}`                          | Detalle de un recurso                  |
| GET    | `/resources/{resourceId}/comments`                 | Comentarios de un recurso              |
| POST   | `/resources/{resourceId}/comments`                 | Crear comentario en un recurso         |
| GET    | `/resources/{resourceId}/versions/{versionId}/download` | Descargar versión de recurso      |

### Autenticación

| Método | Ruta                        | Descripción                              |
|--------|-----------------------------|------------------------------------------|
| POST   | `/auth/login`               | Iniciar sesión (obtener JWT)             |
| POST   | `/auth/refresh`             | Refrescar token JWT                      |
| POST   | `/auth/verifyaccount`       | Verificar cuenta de usuario              |
| POST   | `/auth/recoveryrequest`     | Solicitar correo de recuperación         |
| POST   | `/auth/recoverypassword`    | Recuperar contraseña con token           |
| POST   | `/auth/reset-password`      | Restablecer contraseña                   |
| POST   | `/auth/userSettings`        | Obtener configuración del usuario (JWT)  |

### Administración (requiere JWT)

#### Datos de apoyo
| Método | Ruta                             | Descripción                         |
|--------|----------------------------------|-------------------------------------|
| GET    | `/admin/available-areas`         | Áreas disponibles                   |
| GET    | `/admin/available-licenses`      | Licencias disponibles               |
| GET    | `/admin/available-platforms`     | Plataformas disponibles             |
| GET    | `/admin/available-devices`       | Dispositivos disponibles            |
| GET    | `/admin/available-languages`     | Idiomas disponibles                 |
| GET    | `/admin/available-roles`         | Roles disponibles                   |
| GET    | `/admin/available-userstates`    | Estados de usuario disponibles      |
| GET    | `/admin/menus`                   | Menús según permisos del usuario    |
| GET    | `/admin/permissions`             | Lista de permisos                   |
| GET    | `/admin/resources-types`         | Tipos de recurso                    |
| GET    | `/admin/resources-states`        | Estados de recurso                  |

#### Roles (`/admin/roles`)
| Método | Ruta                                          | Descripción                     |
|--------|-----------------------------------------------|---------------------------------|
| GET    | `/admin/roles`                                | Listar roles                    |
| POST   | `/admin/roles`                                | Crear rol                       |
| GET    | `/admin/roles/summary`                        | Resumen de roles                |
| GET    | `/admin/roles/{rolId}`                        | Detalle de un rol               |
| PUT    | `/admin/roles/{rolId}`                        | Actualizar rol                  |
| DELETE | `/admin/roles/{rolId}`                        | Eliminar rol                    |
| POST   | `/admin/roles/{rolId}/permissions/{permissionId}` | Agregar permiso al rol      |
| DELETE | `/admin/roles/{rolId}/permissions/{permissionId}` | Quitar permiso del rol      |
| POST   | `/admin/roles/{rolId}/menus/{menuId}`         | Agregar menú al rol             |
| DELETE | `/admin/roles/{rolId}/menus/{menuId}`         | Quitar menú del rol             |

#### Dispositivos (`/admin/devices`)
| Método | Ruta                              | Descripción              |
|--------|-----------------------------------|--------------------------|
| GET    | `/admin/devices`                  | Listar dispositivos      |
| POST   | `/admin/devices`                  | Crear dispositivo        |
| GET    | `/admin/devices/summary`          | Resumen de dispositivos  |
| GET    | `/admin/devices/{deviceId}`       | Detalle de dispositivo   |
| POST   | `/admin/devices/{deviceId}`       | Actualizar dispositivo   |
| DELETE | `/admin/devices/{deviceId}`       | Eliminar dispositivo     |

#### Plataformas (`/admin/platforms`)
| Método | Ruta                                 | Descripción             |
|--------|--------------------------------------|-------------------------|
| GET    | `/admin/platforms`                   | Listar plataformas      |
| POST   | `/admin/platforms`                   | Crear plataforma        |
| GET    | `/admin/platforms/summary`           | Resumen de plataformas  |
| GET    | `/admin/platforms/{platformId}`      | Detalle de plataforma   |
| POST   | `/admin/platforms/{platformId}`      | Actualizar plataforma   |
| DELETE | `/admin/platforms/{platformId}`      | Eliminar plataforma     |

#### Licencias (`/admin/licenses`)
| Método | Ruta                                 | Descripción            |
|--------|--------------------------------------|------------------------|
| GET    | `/admin/licenses`                    | Buscar licencias       |
| POST   | `/admin/licenses`                    | Crear licencia         |
| GET    | `/admin/licenses/summary`            | Resumen de licencias   |
| POST   | `/admin/licenses/{licenceId}`        | Actualizar licencia    |
| DELETE | `/admin/licenses/{licenceId}`        | Eliminar licencia      |

#### Tipos de medio (`/admin/mediatypes`)
| Método | Ruta                                                        | Descripción                  |
|--------|-------------------------------------------------------------|------------------------------|
| GET    | `/admin/mediatypes`                                         | Buscar tipos de medio        |
| POST   | `/admin/mediatypes`                                         | Crear tipo de medio          |
| GET    | `/admin/mediatypes/{mediaTypeId}`                           | Detalle de tipo de medio     |
| POST   | `/admin/mediatypes/{mediaTypeId}`                           | Actualizar tipo de medio     |
| DELETE | `/admin/mediatypes/{mediaTypeId}`                           | Eliminar tipo de medio       |
| GET    | `/admin/mediatypes/{mediaTypeId}/genres`                    | Géneros de un tipo de medio  |
| POST   | `/admin/mediatypes/{mediaTypeId}/genres`                    | Crear género                 |
| POST   | `/admin/mediatypes/{mediaTypeId}/genres/{mediaGenreId}`     | Actualizar género            |
| DELETE | `/admin/mediatypes/{mediaTypeId}/genres/{mediaGenreId}`     | Eliminar género              |

#### Recursos (`/admin/resources`)
| Método | Ruta                                                   | Descripción                      |
|--------|--------------------------------------------------------|----------------------------------|
| GET    | `/admin/resources`                                     | Buscar recursos                  |
| POST   | `/admin/resources`                                     | Crear recurso                    |
| GET    | `/admin/resources/{resourceId}`                        | Detalle de recurso               |
| POST   | `/admin/resources/{resourceId}`                        | Actualizar recurso               |
| PUT    | `/admin/resources/{resourceId}`                        | Actualizar estado de recurso     |
| GET    | `/admin/resources/{resourceId}/authors`                | Autores de un recurso            |
| POST   | `/admin/resources/{resourceId}/authors`                | Agregar autor                    |
| DELETE | `/admin/resources/{resourceId}/authors/{authorId}`     | Quitar autor                     |
| GET    | `/admin/resources/{resourceId}/files`                  | Archivos adjuntos de recurso     |
| POST   | `/admin/resources/{resourceId}/files`                  | Agregar archivo                  |
| DELETE | `/admin/resources/{resourceId}/files/{fileId}`         | Eliminar archivo                 |
| GET    | `/admin/resources/{resourceId}/versions`               | Versiones de recurso             |
| POST   | `/admin/resources/{resourceId}/versions`               | Crear versión                    |
| GET    | `/admin/resources/{resourceId}/versions/{versionId}/download` | Descargar versión          |
| GET    | `/admin/resources/{resourceId}/comments`               | Comentarios (admin)              |
| POST   | `/admin/resources/{resourceId}/comments`               | Crear comentario (admin)         |
| POST   | `/admin/resources/{resourceId}/comments/{commentId}`   | Actualizar comentario            |
| DELETE | `/admin/resources/{resourceId}/comments/{commentId}`   | Eliminar comentario              |

#### Tipos de recurso (`/admin/resource/types`)
| Método | Ruta                                   | Descripción                |
|--------|----------------------------------------|----------------------------|
| GET    | `/admin/resource/types`                | Listar tipos de recurso    |
| POST   | `/admin/resource/types`                | Crear tipo de recurso      |
| POST   | `/admin/resource/types/{typeId}`       | Actualizar tipo de recurso |
| DELETE | `/admin/resource/types/{typeId}`       | Eliminar tipo de recurso   |

#### Usuarios (`/admin/users`)
| Método | Ruta                             | Descripción                      |
|--------|----------------------------------|----------------------------------|
| GET    | `/admin/users`                   | Listar usuarios                  |
| POST   | `/admin/users`                   | Crear usuario                    |
| GET    | `/admin/users/summary`           | Resumen de usuarios              |
| GET    | `/admin/users/bulkUpload`        | Descargar plantilla de carga     |
| POST   | `/admin/users/bulkUpload`        | Procesar carga masiva de usuarios|
| POST   | `/admin/users/{userId}`          | Actualizar usuario               |

#### Áreas de recurso (`/admin/resource-areas`)
| Método | Ruta                                   | Descripción               |
|--------|----------------------------------------|---------------------------|
| GET    | `/admin/resource-areas`                | Listar áreas              |
| POST   | `/admin/resource-areas`                | Crear área                |
| GET    | `/admin/resource-areas/summary`        | Resumen de áreas          |
| POST   | `/admin/resource-areas/{areaId}`       | Actualizar área           |
| DELETE | `/admin/resource-areas/{areaId}`       | Eliminar área             |

---

## Restauración y ejecución local

### Requisitos previos

- PHP >= 8.2
- Composer
- Node.js >= 18 y npm
- PostgreSQL

### Pasos

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd UesPlayAPI

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Copiar y configurar el archivo de entorno
cp .env.example .env

# 5. Editar .env con los datos de tu base de datos y claves
#    DB_CONNECTION=pgsql
#    DB_HOST=127.0.0.1
#    DB_PORT=5432
#    DB_DATABASE=uesplayapi
#    DB_USERNAME=<tu_usuario>
#    DB_PASSWORD=<tu_contraseña>

# 6. Generar la clave de la aplicación
php artisan key:generate

# 7. Generar el secreto JWT
php artisan jwt:secret

# 8. Ejecutar las migraciones
php artisan migrate

# 9. (Opcional) Ejecutar seeders
php artisan db:seed

# 10. Levantar el servidor de desarrollo
php artisan serve
```

> La API quedará disponible en `http://localhost:8000/api`.

Para iniciar todos los servicios simultáneamente (servidor, cola y Vite):

```bash
composer run dev
```
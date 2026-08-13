# Babidibu Records — Web v4

Sitio estático con CMS propio para gestionar contenido sin tocar código.

---

## Estructura del proyecto

```
/
├── index.html              ← Sitio principal
├── style.css
├── content-loader.js       ← Carga contenido del CMS (si el servidor responde)
├── php-admin/              ← Panel de administración (privado)
├── php-api/                ← APIs JSON para el frontend
├── setup/                  ← Instalador (eliminar después de usar)
└── imagenes-babidibu-records/cms/   ← Imágenes subidas desde el panel
```

---

## Instalación en el servidor (Hostinger)

### 1. Subir los archivos

Subir **todo el contenido** de esta carpeta a la raíz del dominio en Hostinger (via FTP o el administrador de archivos). La estructura debe quedar así en el servidor:

```
public_html/
├── index.html
├── style.css
├── content-loader.js
├── php-admin/
├── php-api/
├── setup/
└── imagenes-babidibu-records/
```

### 2. Crear la base de datos en Hostinger

1. Ir a **hPanel → Bases de datos → MySQL**
2. Crear una base de datos nueva (por ejemplo: `babidibu_cms`)
3. Crear un usuario y asignarle **todos los permisos** sobre esa base de datos
4. Anotar: host, nombre de la base, usuario y contraseña

### 3. Configurar las credenciales

Editar el archivo `php-admin/config.php` con los datos de la base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'babidibu_cms');   // nombre de tu base de datos
define('DB_USER', 'tu_usuario');     // usuario de Hostinger
define('DB_PASS', 'tu_contraseña'); // contraseña del usuario
```

También editar `setup/install.php` con los mismos datos (sección `$config` al inicio del archivo).

### 4. Correr el instalador

En el navegador, abrir:

```
https://tudominio.com/setup/install.php
```

Si todo está bien va a mostrar un mensaje de éxito con el email y contraseña del admin.

> **Importante:** después de instalar, **eliminar el archivo** `setup/install.php` del servidor. Dejarlo accesible es un riesgo de seguridad.

### 5. Entrar al panel

```
https://tudominio.com/php-admin/login.php
```

**Credenciales iniciales:**
- Email: `info@babidiburecords.com`
- Contraseña: `BabidibuAdmin2024!`

Cambiar la contraseña después del primer ingreso desde **Usuarios → Editar**.

---

## Uso del panel

El panel permite gestionar todo el contenido del sitio sin tocar código:

| Sección | Qué se puede editar |
|---|---|
| **Lanzamientos** | Videos de YouTube que aparecen en "Últimos lanzamientos" |
| **Artistas** | Nombre, bio, foto, links a Spotify / YouTube / Instagram |
| **Babidibu TV** | Videos del bloque Babidibu TV |
| **Hero** | Imágenes del carousel principal |
| **Usuarios** | Agregar o quitar personas con acceso al panel |

Cada sección tiene botones para agregar, editar y eliminar. Los cambios se reflejan en el sitio de forma automática.

### Roles de usuario

- **Admin** — acceso completo, puede gestionar usuarios
- **Editor** — puede editar contenido pero no gestionar usuarios

---

## Cómo funciona el CMS

El sitio tiene contenido hardcodeado como respaldo. Cuando el servidor tiene la base de datos activa, `content-loader.js` reemplaza ese contenido con lo que haya en el panel. Si la base de datos no responde (por ejemplo en GitHub Pages), el sitio igual se ve correctamente con el contenido fijo.

---

## GitHub Pages

El repositorio está en `records-design/web-babidibu-version-4` y se publica automáticamente en:

```
https://records-design.github.io/web-babidibu-version-4/
```

En GitHub Pages el CMS no funciona (no hay PHP), pero el sitio se ve igual con el contenido hardcodeado.

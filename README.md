# Huerto Comunitario

Sistema de Gestión de Huertos Urbanos y Comunitarios — Trabajo Práctico Integrador de **Práctica Profesionalizante III** (Tecnicatura Superior en Desarrollo de Software).

Permite llevar una bitácora de cultivos, el historial de riego y un panel visual de alertas que indica qué plantas necesitan riego hoy o ya están listas para cosechar, calculado a partir de las fechas registradas.

## Tecnologías

- PHP 8.1+
- CodeIgniter 4.x
- MySQL / MariaDB
- Bootstrap 5

## Requisitos previos

- Servidor local con Apache y MySQL
- PHP 8.1 o superior con las extensiones `intl`, `mbstring` y `mysqli` habilitadas
- Composer instalado globalmente
- Git

## Instalación

1. Clonar el repositorio dentro de la carpeta web del servidor local:

   ```bash
   git clone https:///Martin132004/hurto-comunitario.git
   cd huerto-comunitario
   composer install
   ```

2. Configurar el archivo de entorno. Copiar `env` a `.env` y verificar los datos de conexión a la base:

   ```ini
   CI_ENVIRONMENT = development

   database.default.hostname = localhost
   database.default.database = huerto_db
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

3. Crear la base de datos `huerto_db` en MySQL (vacía, sin tablas — las tablas las crea la migración).

4. Ejecutar la migración para crear la tabla `cultivos`:

   ```bash
   php spark migrate
   ```

5. Iniciar el servidor de desarrollo:

   ```bash
   php spark serve
   ```

6. Abrir `http://localhost:8080` en el navegador.

## Estructura del proyecto

```
app/
├── Config/Routes.php                                → rutas de la aplicación
├── Database/Migrations/..._CreateTableCultivos.php  → estructura de la tabla 'cultivos'
├── Models/CultivoModel.php                          → acceso a datos de cultivos
├── Controllers/Huerto.php                           → lógica de negocio (alertas, alta, riego, cosecha, baja)
└── Views/
    ├── layout/template.php                          → plantilla base (navbar, Bootstrap, footer)
    └── huerto/
        ├── index.php                                 → panel de alertas
        └── crear.php                                  → formulario de alta de cultivo
```

## Entidad `cultivos`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | INT | Clave primaria auto-incremental |
| `nombre_planta` | VARCHAR | Nombre común del cultivo (ej. Tomate) |
| `variedad` | VARCHAR, opcional | Subtipo o variedad (ej. Tomate Cherry) |
| `fecha_siembra` | DATE | Fecha de plantación |
| `dias_cosecha_estimados` | INT | Días aproximados hasta la cosecha |
| `frecuencia_riego_dias` | INT | Cada cuántos días requiere riego |
| `ultimo_riego` | DATETIME | Fecha y hora del último riego registrado |
| `estado` | VARCHAR | En Crecimiento / Listo para Cosechar / Cosechado |
| `created_at` / `updated_at` | DATETIME | Gestionados automáticamente por el framework |

## Funcionalidades

- **Panel de alertas** (`/`): calcula por fecha qué plantas necesitan riego hoy (rojo) o están listas para cosechar (verde).
- **Alta de cultivo** (`/huerto/crear`): formulario para registrar un nuevo cultivo.
- **Registrar riego** (`/huerto/riego/{id}`): actualiza `ultimo_riego` a la fecha/hora actual.
- **Cambiar estado** (`/huerto/estado/{id}`): marca el cultivo como Cosechado.
- **Eliminar** (`/huerto/eliminar/{id}`): borra el registro del cultivo.

## Autores

Rodríguez & Carrizo — Práctica Profesionalizante III, 2° Año.

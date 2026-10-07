# AgroTech

Sistema de Gestión de Huertos Urbanos y Comunitarios — Trabajo Práctico Integrador de **Práctica Profesionalizante III** (Tecnicatura Superior en Desarrollo de Software).

Permite llevar una bitácora de cultivos, el historial de riego y un panel visual de alertas que indica qué plantas necesitan riego hoy o ya están listas para cosechar, calculado a partir de las fechas registradas.

## Tecnologías

- PHP 8.1+
- CodeIgniter 4.x
- MySQL / MariaDB
- Bootstrap 5
- Dompdf (exportación de reportes a PDF, se instala con `composer install`)

## Requisitos previos

- Servidor local con Apache y MySQL
- PHP 8.1 o superior con las extensiones `intl`, `mbstring` y `mysqli` habilitadas
- Composer instalado globalmente
- Git

## Instalación

1. Clonar el repositorio dentro de la carpeta web del servidor local:

   ```bash
   git clone https://github.com/Martin132004/hurto_comunitarioisft.git huerto-comunitario
   cd huerto-comunitario
   composer install
   ```

2. Configurar el archivo de entorno. Copiar la plantilla `env` a `.env` (el `.env` no se sube al repositorio) y verificar los datos de conexión a la base:

   ```bash
   cp env .env        # en Windows (cmd): copy env .env
   ```

   ```ini
   CI_ENVIRONMENT = development

   database.default.hostname = localhost
   database.default.database = huerto_db
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

3. Crear la base de datos `huerto_db` en MySQL (vacía, sin tablas — las tablas las crea la migración).

4. Ejecutar las migraciones para crear las tablas `cultivos`, `especies`, `zonas`, `huertos`, `problemas`, `reportes_problemas`, `riegos`, `cosechas`, `plano_elementos` y `siembras_planificadas`:

   ```bash
   php spark migrate
   ```

5. Cargar el catálogo de zonas, especies y problemas (plagas y enfermedades) (se puede volver a ejecutar sin duplicar datos):

   ```bash
   php spark db:seed CatalogoSeeder
   ```

6. Iniciar el servidor de desarrollo:

   ```bash
   php spark serve
   ```

7. Abrir `http://localhost:8080` en el navegador.

## Estructura del proyecto

```
app/
├── Config/Routes.php                                → rutas de la aplicación
├── Database/Migrations/..._CreateTableCultivos.php  → estructura de la tabla 'cultivos'
├── Database/Migrations/..._CrearTablaEspecies.php   → estructura de la tabla 'especies'
├── Database/Migrations/..._CrearTablasZonasYHuertos.php → tablas 'zonas' y 'huertos'
├── Database/Migrations/..._AgregarSistemaDeRiego.php → sistema de riego del huerto y ajuste mensual por zona
├── Database/Migrations/..._CrearTablasProblemas.php → catálogo de problemas y reportes de los huertos
├── Database/Migrations/..._CrearTablasRiegosYCosechas.php → historial de riegos y cosechas con kg
├── Database/Seeds/CatalogoSeeder.php                → carga zonas, especies y problemas
├── Database/Seeds/ZonasSeeder.php                   → zonas agroclimáticas y fechas de helada
├── Database/Seeds/EspeciesSeeder.php                → datos del catálogo de especies
├── Database/Seeds/ProblemasSeeder.php               → plagas, enfermedades y daños con su manejo
├── Models/CultivoModel.php                          → acceso a datos de cultivos
├── Models/EspecieModel.php                          → acceso al catálogo de especies
├── Models/ZonaModel.php                             → acceso a las zonas
├── Models/HuertoModel.php                           → datos del huerto (zona elegida)
├── Models/ProblemaModel.php                         → catálogo de problemas y meses de riesgo
├── Models/ReporteProblemaModel.php                  → reportes, respuesta del técnico y alertas por zona
├── Models/RiegoModel.php                            → historial de riegos
├── Models/CosechaModel.php                          → cosechas y kg
├── Libraries/PlanRiego.php                          → cálculos de riego (litros por mes y método, turnos, reservorio)
├── Libraries/ReporteMensual.php                     → datos del reporte mensual
├── Controllers/Huerto.php                           → lógica de negocio (alertas, alta, riego, cosecha, baja)
├── Controllers/Problemas.php                        → reporte de problemas y plagas
├── Controllers/Reportes.php                         → reporte mensual y exportación a PDF
└── Views/
    ├── layout/template.php                          → plantilla base (navbar, Bootstrap, footer)
    └── huerto/
        ├── index.php                                 → panel de alertas
        ├── crear.php                                  → formulario de alta con guía de cultivo
        ├── configuracion.php                          → datos del huerto y zona
        ├── regando.php                                → pantalla de riego y plan de riego
        └── cosechar.php                               → registro de cosecha con kg
    └── problemas/
        ├── index.php                                 → alertas, problemas a vigilar, reportes y guía
        ├── nuevo.php                                  → formulario de reporte con foto
        └── ver.php                                    → detalle y respuesta del técnico
    └── reportes/
        ├── index.php                                 → vista previa del reporte mensual
        ├── hoja.php                                   → hoja con formato de comprobante (pantalla y PDF)
        └── pdf.php                                    → documento PDF
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

## Catálogo de la guía de cultivo (adaptado a San Juan)

Los datos son propios del sistema (no dependen de servicios externos) y están pensados para las condiciones de San Juan: clima árido con riego, sol intenso, viento Zonda, heladas y suelos con sales.

- **`zonas`**: zonas agroclimáticas de la provincia (Valle de Tulum/Ullum/Zonda, Valle Fértil, Jáchal, Calingasta e Iglesia) con su altitud, fechas medias de primera y última helada, y cuántos meses se atrasa la siembra de las especies sensibles al frío.
- **`especies`**: 25 especies con sinónimos, familia, días a cosecha, riego recomendado, meses de almácigo y de siembra/trasplante (referencia: valle de Tulum), sensibilidad a la helada, tolerancia a las sales, exposición, distancia, profundidad, buenos y malos vecinos, y consejos de manejo.
- **`zonas.coef_riego_mensual`**: factor de enero a diciembre que ajusta los litros a la demanda de agua de cada mes (más en verano, menos en invierno).
- **`huertos`**: datos del huerto elegidos en **Mi huerto**: zona, fuente de agua (red, pozo o turno de canal), turno (cada N días o días fijos de la semana), método de riego y litros que se pueden guardar.

> Las fechas de helada y los valores de cultivo son orientativos. Conviene validarlos con el INTA EEA San Juan / ProHuerta antes de usarlos como recomendación oficial.

## Funcionalidades

- **Panel de alertas** (`/`): calcula por fecha qué plantas necesitan riego hoy (rojo) o están listas para cosechar (verde).
- **Alta de cultivo** (`/huerto/crear`): formulario para registrar un nuevo cultivo.
- **Mi huerto** (`/huerto/configuracion`): nombre del huerto, zona de San Juan donde está y su sistema de riego, que elige cada usuario según cómo riega.
- **Sistema de riego**: los litros de cada cultivo se cargan como lo que necesita la planta en primavera u otoño y el sistema calcula cuánto aplicar según el mes de la zona y la eficiencia del método (goteo 90%, manguera 80%, aspersión 70%, surco 60%, manto 50%). Si el agua llega por turno de canal, muestra los próximos turnos, marca qué riegos caen en día de turno y cuáles hay que hacer con agua guardada, y avisa si lo que se puede guardar no alcanza entre turnos. Esto se ve en el panel, en la pantalla de riego, en Mi huerto y en la guía al agregar un cultivo.
- **Guía de cultivo** (en el alta): al escribir el nombre de la planta, el sistema reconoce la especie (acepta plurales, tildes y sinónimos como "morrón" o "choclo"), muestra cómo cultivarla con el calendario de almácigo y trasplante de la zona, permite completar el formulario con los valores recomendados y analiza el caso concreto: si la fecha está dentro de la temporada de la zona, riesgo de helada al plantar y si la cosecha llega antes de la primera helada, tolerancia a las sales, la cosecha estimada, valores cargados muy distintos a los recomendados, buenos y malos vecinos entre los cultivos que ya están en el huerto, familias repetidas, siembras muy seguidas de la misma especie y el consumo semanal de agua del huerto.
- **Problemas y plagas** (`/huerto/problemas`): el huertero reporta un problema de un cultivo con foto (el navegador la achica antes de subirla para que se envíe rápido con poca señal), cuánto afecta y lo que ve; puede elegirlo de una lista filtrada según el cultivo o dejarlo como "No sé qué es". El técnico responde desde el mismo reporte y confirma o corrige el diagnóstico. El sistema muestra qué problemas vigilar este mes según los cultivos del huerto y la zona, avisa si el mismo problema aparece en varios cultivos y genera una alerta de zona cuando varias huertas de la misma zona reportan lo mismo en los últimos 21 días. Incluye una guía con síntomas, manejo agroecológico y prevención de 15 problemas frecuentes (pulgones, arañuela, mosca blanca, trips, polilla del tomate, oídio, podredumbre apical, golpe de sol, helada, Zonda, sales y otros). Las fotos se guardan en `writable/uploads/problemas`, fuera de la carpeta pública.
- **Registrar riego** (`POST /huerto/riego/{id}`): actualiza `ultimo_riego` y guarda el riego en el historial (`riegos`) con los litros ajustados al mes y al método.
- **Cosechar** (`/huerto/cosechar/{id}`): registra la fecha y los kg cosechados (se puede dejar sin pesar). Admite cosechas parciales (tomate, acelga) y la cosecha final, que pasa el cultivo a Cosechado.
- **Reportes mensuales** (`/huerto/reportes`): hoja con formato de comprobante (número de reporte, período, datos del huerto, detalle y totales) con producción en kg, riegos registrados contra los que correspondían según el plan, litros aplicados, siembras, problemas reportados, agua por kg cosechado y espacio para observaciones y firmas del responsable y del técnico. Se elige el mes y se descarga en PDF (A4).
- **Eliminar** (`POST /huerto/eliminar/{id}`, con confirmación): borra el registro del cultivo.

## Autores

Rodríguez & Carrizo — Práctica Profesionalizante III, 2° Año.

# JJVV Digital

Sistema web para la digitalización y centralización de la gestión administrativa de una Junta de Vecinos.

## Descripción del proyecto

JJVV Digital es una aplicación web orientada a modernizar los procesos administrativos y mejorar el acceso a la información de una Junta de Vecinos.

El proyecto busca centralizar la información de los socios, facilitar la gestión de solicitudes de inscripción y organizar las reservas de atención presencial. A futuro, contempla incorporar herramientas para administrar certificados, movimientos financieros, actas, proyectos, agrupaciones, inventario y reportes.

La plataforma pretende reducir la dependencia de documentos físicos, planillas de Excel, archivos Word y canales informales de comunicación, manteniendo los procedimientos presenciales que sean necesarios para cumplir con los requisitos legales y organizacionales.

**Repositorio:** https://github.com/junta-vecinal-digital/jjvv-digital

## Objetivos

- Centralizar la información administrativa de la organización.
- Facilitar la solicitud y revisión de inscripciones de socios.
- Permitir la reserva de horas para realizar trámites presenciales.
- Controlar el acceso a la información según el rol de cada usuario.
- Mejorar la trazabilidad de los procesos administrativos.
- Facilitar el acceso a noticias, información institucional y documentos de interés.
- Incorporar progresivamente herramientas de gestión financiera y documental.

## Tecnologías utilizadas

### Backend

- **PHP 8.3:** lenguaje de programación.
- **Laravel 13:** framework para el desarrollo de la aplicación web.
- **Laravel Breeze:** scaffolding de autenticación y componentes base.
- **Eloquent ORM:** gestión de modelos y acceso a la base de datos.
- **Blade:** motor de plantillas de Laravel.

### Base de datos

- **SQLite:** sistema de gestión de base de datos relacional.
- **Migraciones de Laravel:** definición y evolución de la estructura de la base de datos.

### Frontend

- **Vite:** compilación y administración de recursos frontend.
- **Tailwind CSS:** framework de estilos.
- **Alpine.js:** interactividad en la interfaz.

### Herramientas adicionales

- **Composer:** administración de dependencias de PHP.
- **npm:** administración de dependencias frontend.
- **Pest:** herramienta de pruebas configurada en el proyecto.

## Arquitectura general

El proyecto utiliza la arquitectura MVC (Modelo-Vista-Controlador) proporcionada por Laravel.

- **Modelos:** representan las entidades y permiten interactuar con la base de datos.
- **Vistas:** presentan la interfaz mediante plantillas Blade.
- **Controladores:** procesan las solicitudes, validan los datos y coordinan las operaciones.
- **Rutas:** definen los puntos de acceso a la aplicación y sus restricciones.
- **Migraciones:** permiten crear y actualizar las tablas de la base de datos.

## Funcionalidades implementadas

El estado que se describe en esta sección corresponde a la estructura y al código revisados del repositorio. La existencia de una ruta o un controlador no implica, por sí sola, que todas las funcionalidades estén terminadas o probadas.

### 1. Autenticación y usuarios

- Autenticación mediante credenciales.
- Rutas protegidas para usuarios autenticados.
- Administración de usuarios desde el panel correspondiente.
- Creación y actualización de usuarios.
- Asignación de roles.
- Validación de datos y contraseñas.

Los roles contemplados en las rutas incluyen:

- Administrador.
- Presidente.
- Secretario.
- Tesorero.
- Vecino.

El acceso a determinadas secciones está restringido según el rol asignado.

### 2. Solicitudes de inscripción de socios

El sistema contempla un flujo para solicitar la inscripción como socio de la Junta de Vecinos.

Entre las funcionalidades presentes en el código se encuentran:

- Registro de datos personales y de contacto.
- Registro del RUT y la dirección.
- Validación de una residencia mínima de seis meses.
- Carga de una copia del documento de identidad.
- Carga de un comprobante de domicilio.
- Registro de la solicitud asociada al usuario autenticado.
- Consulta y revisión de solicitudes por parte de dirigentes autorizados.
- Actualización del estado de la solicitud.
- Registro del usuario que revisa la solicitud.
- Registro de la fecha de ingreso cuando se activa al socio.

Los estados contemplados para la solicitud son:

- `por_firmar`
- `activo`
- `rechazado`

La aceptación definitiva debe respetar el procedimiento presencial de firma del libro de registro de socios.

### 3. Reserva de horas para atención presencial

El sistema incluye funcionalidades para organizar las horas de atención relacionadas con la inscripción de socios.

Se contempla:

- Visualización de horas disponibles.
- Reserva de una hora por parte del vecino.
- Cancelación de una reserva futura.
- Creación de horarios por parte de dirigentes autorizados.
- Definición de fecha, hora de inicio, hora de término y duración.
- Visualización de la agenda de atención.
- Liberación de horas reservadas.
- Eliminación de horarios que no estén reservados.

Las reservas están orientadas a facilitar la coordinación de la atención presencial necesaria para completar la inscripción.

### 4. Módulo financiero

Las rutas contemplan un acceso inicial al módulo financiero para usuarios autenticados con determinados roles.

La gestión efectiva de ingresos y egresos permanece pendiente de desarrollo y verificación.

### 5. Otras funcionalidades proyectadas

Los siguientes módulos forman parte del alcance general del proyecto, pero no deben considerarse terminados hasta que se implementen y verifiquen:

- Emisión y validación de certificados.
- Gestión de ingresos y egresos.
- Calendario general de uso y arriendo de la sede.
- Registro digital de actas de asamblea y directiva.
- Gestión de agrupaciones asociadas.
- Seguimiento de proyectos postulados y adjudicados.
- Inventario de activos e insumos.
- Registro de préstamos de materiales.
- Generación y exportación de reportes PDF y Excel.
- Canal público de noticias e información institucional.
- Formulario de contacto con la directiva.

## Requisitos del sistema

Para ejecutar el proyecto localmente se necesita:

- PHP 8.3 o compatible con las restricciones de Composer.
- Composer.
- Node.js y npm.
- SQLite.
- Un navegador web moderno.

También es necesario disponer de las extensiones de PHP requeridas por Laravel y sus dependencias.

## Instalación y configuración

### 1. Clonar el repositorio

```bash
git clone https://github.com/junta-vecinal-digital/jjvv-digital.git
cd jjvv-digital
```

### 2. Instalar las dependencias

Instalar las dependencias de PHP:

```bash
composer install
```

Instalar las dependencias frontend:

```bash
npm install
```

### 3. Configurar el entorno

Crear el archivo `.env` a partir de la configuración de ejemplo:

```bash
cp .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

Revisar la configuración de la aplicación en el archivo `.env`.

Por defecto, el proyecto está configurado para utilizar SQLite.

### 4. Configurar la base de datos

Verificar que exista el archivo de base de datos:

```text
database/database.sqlite
```

Si no existe, crearlo antes de ejecutar las migraciones:

```bash
touch database/database.sqlite
```

En Windows, el archivo puede crearse desde el explorador de archivos o mediante PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Ejecutar las migraciones:

```bash
php artisan migrate
```

Este comando crea las tablas definidas en las migraciones del proyecto.

### 5. Compilar los recursos frontend

Para generar los archivos frontend de producción:

```bash
npm run build
```

Durante el desarrollo, se puede utilizar Vite en modo observación:

```bash
npm run dev
```

### 6. Ejecutar la aplicación

Iniciar el servidor de desarrollo de Laravel:

```bash
php artisan serve
```

Abrir la dirección indicada por Laravel, normalmente:

http://127.0.0.1:8000

Para trabajar con actualizaciones automáticas del frontend, mantener `npm run dev` ejecutándose en otra terminal.

## Comandos útiles

| Comando | Descripción |
|---|---|
| `composer install` | Instala las dependencias de PHP. |
| `npm install` | Instala las dependencias frontend. |
| `php artisan migrate` | Ejecuta las migraciones de la base de datos. |
| `php artisan migrate:status` | Consulta el estado de las migraciones. |
| `php artisan serve` | Inicia el servidor de desarrollo. |
| `npm run dev` | Inicia Vite en modo desarrollo. |
| `npm run build` | Compila los recursos frontend. |
| `php artisan test` | Ejecuta las pruebas configuradas en el proyecto. |

## Organización del repositorio

La estructura principal del proyecto incluye:

```text
jjvv-digital/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   ├── seeders/
│   └── database.sqlite
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   └── auth.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── README.md
```

Esta estructura resume los directorios principales de Laravel; el contenido concreto de cada carpeta puede evolucionar durante el desarrollo.

## Plan de desarrollo

El desarrollo se organiza en tres sprints, según la prioridad de las funcionalidades.

### Sprint 1: MUST HAVE

**Objetivo:** construir la base indispensable del sistema.

Entregables planificados:

- Configuración del entorno web.
- Base de datos central.
- Gestión de usuarios.
- Autenticación.
- Roles y restricciones de acceso.
- Funcionalidades iniciales de socios y certificados.

### Sprint 2: SHOULD HAVE

**Objetivo:** incorporar funcionalidades de alto valor para la gestión cotidiana.

Entregables planificados:

- Módulo de ingresos y egresos.
- Validador de certificados en línea.
- Calendario digital de reservas de la sede.

### Sprint 3: COULD HAVE

**Objetivo:** incorporar herramientas complementarias de gestión y generación de información.

Entregables planificados:

- Bitácora de actas digitales.
- Módulo de proyectos.
- Mantenedor de inventario.
- Exportación de reportes en PDF y Excel.

La planificación representa el alcance previsto y no significa que todas estas funcionalidades estén actualmente implementadas.

## Consideraciones organizacionales

El sistema debe respetar los procedimientos internos de la Junta de Vecinos.

- La inscripción definitiva de un socio requiere la firma presencial del libro de registro.
- Para emitir un certificado de residencia, se deben revisar presencialmente los documentos exigidos por la organización.
- Las actas de asamblea deben mantenerse en el libro físico correspondiente. El registro digital funciona como respaldo y mecanismo de transparencia.
- Las actas de directiva deben contar con acceso restringido a los miembros autorizados.
- Los datos personales y documentos de los vecinos deben estar protegidos mediante controles de acceso adecuados.

## Alcance y limitaciones

El proyecto busca centralizar procesos administrativos, mejorar la transparencia y facilitar la comunicación entre la directiva y los vecinos.

No contempla, dentro del alcance definido:

- Desarrollo de una aplicación móvil nativa.
- Implementación de un sistema de pagos en línea.
- Sistema de votación electrónica.

La aplicación web no reemplaza los procedimientos físicos que sean obligatorios para la organización.

## Estado del proyecto

JJVV Digital se encuentra en desarrollo.

La base tecnológica está configurada sobre Laravel, con autenticación, administración de usuarios, roles y funcionalidades iniciales para solicitudes de inscripción de socios y reservas de atención presencial.

Los módulos adicionales deben incorporarse progresivamente de acuerdo con la planificación de los sprints y verificarse antes de considerarlos terminados.

## Licencia

La licencia del proyecto debe definirse de acuerdo con las decisiones de sus responsables. No se establece aquí una licencia específica para JJVV Digital.

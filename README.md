# JJVV Digital

Sistema web para la digitalización y centralización de la gestión administrativa de una Junta de Vecinos.

## Descripción del proyecto

JJVV Digital es una aplicación web orientada a modernizar los procesos administrativos y mejorar el acceso a la información de una Junta de Vecinos.

El proyecto busca centralizar la información de los socios, facilitar la gestión de solicitudes de inscripción y organizar las reservas de atención presencial. A futuro, contempla incorporar herramientas para administrar certificados, movimientos financieros, actas, proyectos, agrupaciones, inventario y reportes.

La plataforma pretende reducir la dependencia de documentos físicos, planillas de Excel, archivos Word y canales informales de comunicación, manteniendo los procedimientos presenciales que sean necesarios para cumplir con los requisitos legales y organizacionales.

## Requisitos

### Funcionales de usuario (RFU)

- RFU-01: El vecino debe poder iniciar una solicitud de inscripción como socio desde el sitio público.
- RFU-02: El vecino debe poder solicitar certificados de residencia o de socio desde el sitio público.
- RFU-03: El vecino debe poder consultar noticias e información institucional sin crear una cuenta.
- RFU-04: El vecino debe poder contactar a la directiva mediante un formulario.
- RFU-05: El dirigente debe poder autenticarse en el panel de administración mediante credenciales.
- RFU-06: El dirigente debe poder registrar y actualizar la base de datos de socios.
- RFU-07: El dirigente debe poder registrar movimientos financieros de ingresos y egresos.
- RFU-08: El dirigente debe poder registrar actas de asamblea en una bitácora digital, con fecha y acuerdos.
- RFU-09: El dirigente debe poder registrar agrupaciones asociadas y proyectos postulados o adjudicados.
- RFU-10: El dirigente debe poder revisar y validar manualmente una solicitud de certificado antes de entregarlo.

### Funcionales del sistema (RFS)

- RFS-01: El sistema debe generar un PDF estandarizado a partir de los datos del solicitante para revisión por parte de un dirigente y, cuando corresponda, para la emisión final.
- RFS-02: La información de socios, finanzas, actas, agrupaciones y proyectos debe almacenarse en una base de datos relacional centralizada.
- RFS-03: El panel administrativo debe restringirse a usuarios autenticados y autorizados según sus roles.
- RFS-04: El sistema debe registrar la fecha, el autor y los cambios relevantes en los datos de socios y finanzas. La trazabilidad debe extenderse de forma coherente a otros datos sensibles cuando corresponda.

### Requisitos organizacionales

-RO-01: La inscripción definitiva del socio se completa presencialmente mediante la firma del libro físico.
- RO-02: Para emitir un certificado de residencia, la directiva debe revisar presencialmente la cédula de identidad y la boleta de luz o agua exigida por la organización.
- RO-03: Las actas de asamblea deben seguir asentándose en el libro físico; el módulo web no sustituye ese registro.

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

- La inscripción definitiva de un socio requiere la firma presencial del libro de registro.
- Las actas de directiva deben contar con acceso restringido a los miembros autorizados.
- Los datos personales y documentos de los vecinos deben estar protegidos mediante controles de acceso adecuados.

## Estado del proyecto

JJVV Digital se encuentra en desarrollo.

La base tecnológica está configurada sobre Laravel, con autenticación, administración de usuarios, roles y funcionalidades iniciales para solicitudes de inscripción de socios y reservas de atención presencial.

Los módulos adicionales deben incorporarse progresivamente de acuerdo con la planificación de los sprints y verificarse antes de considerarlos terminados.


# JJVV Digital

Sistema web para la digitalización y centralización de la gestión administrativa de una Junta de Vecinos.

## Descripción del proyecto

JJVV Digital es una aplicación web orientada a modernizar los procesos administrativos y mejorar el acceso a la información de una Junta de Vecinos.

El proyecto busca centralizar la información de los socios, facilitar la gestión de solicitudes de inscripción y organizar las reservas de atención presencial. A futuro, contempla incorporar herramientas para administrar certificados, movimientos financieros, actas, proyectos, agrupaciones, inventario y reportes.

La plataforma pretende reducir la dependencia de documentos físicos, planillas de Excel, archivos Word y canales informales de comunicación, manteniendo los procedimientos presenciales que sean necesarios para cumplir con los requisitos legales y organizacionales.


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


## Consideraciones organizacionales

- La inscripción definitiva de un socio requiere la firma presencial del libro de registro.
- Las actas de directiva deben contar con acceso restringido a los miembros autorizados.
- Los datos personales y documentos de los vecinos deben estar protegidos mediante controles de acceso adecuados.

## Estado del proyecto

JJVV Digital se encuentra en desarrollo.

La base tecnológica está configurada sobre Laravel, con autenticación, administración de usuarios, roles y funcionalidades iniciales para solicitudes de inscripción de socios y reservas de atención presencial.

Los módulos adicionales deben incorporarse progresivamente de acuerdo con la planificación de los sprints y verificarse antes de considerarlos terminados.


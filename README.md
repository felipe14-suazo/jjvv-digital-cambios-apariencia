# Proyecto de título: JJVV Digital

Sistema web para la digitalización y centralización de la gestión administrativa de una Junta de Vecinos.

## Objetivos

- Centralizar la información administrativa de la organización.
- Facilitar la solicitud y revisión de inscripciones de socios.
- Permitir la reserva de horas para realizar trámites presenciales.
- Controlar el acceso a la información según el rol de cada usuario.
- Mejorar la trazabilidad de los procesos administrativos.
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
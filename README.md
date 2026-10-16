# JJVV Digital

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

## Plan de sprints

### Sprint 1: MUST HAVE

Objetivo: Lo indispensable para que el sistema exista y resuelva el trámite central: cuentas, roles, socios y certificados.

#### Entregables:

- Configuración del entorno web y base de datos central.
- Roles y permisos (Administrador, Presidente, Secretario, Tesorero, Vecino). “Socio” se deduce de la ficha activa.
- Parámetros configurables del sistema (horario de la sede, vigencia del certificado, plazos y valores).
- Registro de vecinos y solicitud de inscripción, con rechazo con motivo y reapertura para corregirla.
- Agenda de citas de firma del libro de socios.
- Mantenedor de socios: registro en ventanilla, búsqueda, edición, baja y reactivación, y creación de cuenta para socios registrados en ventanilla.
- Certificados de residencia: solicitud en línea, emisión con folio y PDF, descarga con plazo, retiro presencial, rechazo, anulación, certificado para no socios en ventanilla y validación pública.
- Registro base de ingresos, para guardar el pago del certificado.
- Auditoría de socios, certificados y usuarios.
- Gestión de usuarios y roles por el administrador, y eliminación de la propia cuenta.

### Sprint 2: SHOULD HAVE

Objetivo: Funcionalidades de alto valor para la gestión diaria de la sede: quién la usa, cuándo y cuánto se paga.

#### Entregables:

- Grupos y organizaciones, con su representante vigente.
- Calendario de espacios: reservas, horarios fijos de grupos, arriendos con confirmación de pago y garantía, y horario imprimible.
- Módulo financiero completo: ingresos (con cuotas ordinarias y extraordinarias), garantías de arriendo, egresos, devoluciones, anulaciones y saldo de caja.
- Auditoría de finanzas y consulta de auditoría con filtros por fecha, autor y tipo de registro.

### Sprint 3: COULD HAVE

Objetivo: Módulos complementarios de transparencia y generación de salidas.

#### Entregables:

- Actas de asamblea y de directiva, con acceso según rol.
- Proyectos postulados y adjudicados, visibles para todo usuario con sesión iniciada.
- Inventario y préstamos de insumos.
- Reportes: financieros y listado de socios en Excel y PDF.

#### Sprint 4: TESTING & BUFFER

Objetivo: Pruebas, corrección de errores (bugs) y empaquetado para la entrega final. 




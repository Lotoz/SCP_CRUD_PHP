# Sistema de Base de Datos Segura de la Fundación SCP

<div align="center">

<img src="./pictures_README/banner.png" alt="Project Banner" width="600px" height="800px">

![Tech Stack](https://skillicons.dev/icons?i=php,mysql,html,css,js,bootstrap,vscode)

![Status](https://img.shields.io/badge/Status-En%20Desarrollo-green?style=for-the-badge)
![License](https://img.shields.io/badge/Licencia-GPLv3-blue?style=for-the-badge)

</div>

<p align="center">
  <a href="README.md">🇺🇸 Versión README en Inglés</a>
  <a href="https://www.notion.so/SCP-Foundation-Secure-Database-System-2ed4d660fb3b80ea82cff7f8b43f28aa?source=copy_link" > Documentacion del proyecto (mas extensa)</a>
</p>

> **⚠️ ADVERTENCIA:** Este software es un **proyecto educativo** desarrollado para demostrar habilidades en **PHP Nativo, OOP y Arquitectura MVC**. No está diseñado para su uso en entornos de producción críticos.

## 📖 Descripción General

El **Sistema de Base de Datos Segura SCP** es una aplicación web *Full-Stack* que simula la base de datos clasificada de la Fundación SCP. Permite la gestión segura de archivos de anomalías, administración de personal, asignaciones de sitios de contención y tareas operativas.

El sistema está construido estrictamente siguiendo el patrón **Modelo-Vista-Controlador (MVC)** sin usar frameworks, asegurando un código limpio, modular y escalable.

---

## 🚀 Características Principales

### 🔐 Seguridad y Autenticación

* **Sistema de Login:** Roles y Niveles de Seguridad (1 a 5).
* **Protección:** Hashing de contraseñas (`Bcrypt`), tokens CSRF en formularios y protección contra *Session Fixation*.
* **Prevención de Errores:** Bloqueo de autoeliminación (un administrador no puede eliminar su propia cuenta).

### 📂 Gestión de Anomalías (SCPs)

* **CRUD Completo:** Crear, leer, editar y eliminar archivos.
* **Gestión Multimedia:** Subida de imágenes con **renombrado automático** basado en el ID del SCP.
* **Wiki Pública:** Visualización dinámica de tarjetas con estilos según la clase del objeto (Safe, Euclid, Keter).

### 🛠️ Administración de Personal

* **Control Total:** Panel exclusivo para Nivel 5 (Consejo O5).
* **UX:** Visualización de contraseñas con botón *toggle* y validaciones en tiempo real (JS).

---

## ⚙️ Tecnologías Utilizadas

* **Backend:** PHP 8.2+ (Nativo, OOP).
* **Base de Datos:** MySQL / MariaDB (InnoDB).
* **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (ES6).
* **Servidor:** Apache (XAMPP/LAMPP).

---

## 📋 Reglas del Sistema (Lore & Lógica de Negocio)

Este proyecto implementa reglas estrictas para simular la burocracia de la Fundación SCP:

1. **Nomenclatura SCP:** Todos los IDs de anomalías deben comenzar estrictamente con **`SCP-`** (ej., `SCP-173`).
2. **Protocolo de Contención:**
    * Las clases **SAFE, EUCLID y NEUTRALIZED** *deben* tener un Sitio de Contención asignado obligatoriamente.
    * Las clases **KETER y THAUMIEL** son las únicas que pueden tener ubicación desconocida (`NULL`).
3. **Integridad de Usuario:**
    * Los nombres de usuario solo aceptan letras, números, guiones y guiones bajos (Regex: `/^[a-zA-Z0-9_-]+$/`).
    * Un usuario de Nivel 0 (Clase-D) tiene acceso *Solo Lectura* a sus tareas.

---

## 🔧 Instalación y Despliegue

Sigue estos pasos para configurar el proyecto en tu entorno local:

### 1. Clona el Repositorio

```bash
git clone https://github.com/Lotoz/SCP_CRUD_PHP
```

### 2. Configura el Servidor Local

1. Base de Datos

    Abre tu gestor de base de datos (phpMyAdmin, DBeaver, etc.).

    Crea una base de datos llamada scp_data.

    Importa el script ubicado en: 📂 DATABASE/scp_data.sql

2. Configuración

Edita el archivo de configuración con tus credenciales locales: 📂 SCP_CRUD_PHP/config/Database.php (Si quieres, puedes usar los predeterminados: view.)

```php
private $host = "localhost";
private $db_name = "scp_data";
private $username = ""; // Tu nombre de usuario
private $password = "";     // Tu contraseña
```

Las credenciales predeterminadas son view / yX/I!geU1xKbG3F[ para fines de prueba.

3. Copia el directorio a la carpeta raíz de tu servidor (ej., `htdocs` para XAMPP).

Debes copiar SCP_CRUD_PHP/ a la raíz del servidor. Este directorio contiene todo el código fuente.

4. Permisos (Solo Linux/Mac)

Asegúrate de que la carpeta de subida tenga permisos de escritura:

```bash
chmod -R 777 views/CRUD/anomalies/assets/img/
```
5. Habilita tu servidor o inicia XAMPP/LAMPP.

6. Accede a la aplicación a través de tu navegador:

```bash
http://localhost/SCP_CRUD_PHP/
```

7. ¡Disfruta explorando el Sistema de Base de Datos Segura de la Fundación SCP!

---

## 📂 Estructura del Proyecto

```
/
├── DATABASE/           # Scripts SQL y Seeders
├── EXTRA/              # Credenciales de Prueba y Notas
├── pictures_README/    # Imágenes para Documentación
├── SCP_CRUD_PHP/       # CÓDIGO FUENTE DE LA APLICACIÓN
│   ├── config/         # Conexión BD y SessionManager
│   ├── controllers/    # Lógica de Negocio
│   ├── models/         # Entidades
│   ├── repositories/   # Consultas SQL (Patrón Repository)
│   ├── views/          # Interfaz de Usuario (HTML/PHP)
│   └── index.php       # Router Principal
└── README.md           # Este archivo
```

---

## 🔑 Credenciales de Acceso (Demo)

Puedes encontrar una lista completa de usuarios de prueba en la carpeta 📂 EXTRA/.

---

### 📸 Imágenes

| Login | Registro | Dashboard |
|-------|----------|-----------|
| ![Pantalla de Login](pictures_README/login.png) | ![Pantalla de Registro](pictures_README/register.png) | ![Dashboard](pictures_README/admin.png) |

**Temas Disponibles:**

| Gears | Ice | Sophie |
|-------|-----|--------|
| ![Tema Gears](pictures_README/gears.png) | ![Tema Ice](pictures_README/ice.png) | ![Tema Sophie](pictures_README/sophie.png) |

| Unicorn | Clef | Admin |
|---------|------|-------|
| ![Tema Unicorn](pictures_README/unicorn.png) | ![Tema Clef](pictures_README/clef.png) | ![Tema Admin](pictures_README/admin.png) |

## Ejemplo de Gestión de Anomalías

| Anomalías | Editar | Crear |
|---------  |------| -------|
| ![Anomalías](pictures_README/anomalies.png) | ![Tema Clef](pictures_README/editAnomalies.png) | ![Tema Admin](pictures_README/createAnomalies.png) |

## Example of view Class-D (Level 0)

| Tasks | View SCPs |
|-------|-----------|
| ![Tasks](pictures_README/classD1.png) | ![View SCPs](pictures_README/classD2.png) |

## SCP Wiki Public View

| SCP Wiki | SCP  |
|----------|------|
| ![SCP Wiki](pictures_README/scpWiki.png) | ![SCP Card](pictures_README/scpWiki2.png) |

## 🎥 Video Demo

Puedes ver la aplicación en este video: [Sistema de Base de Datos Segura SCP - Video Demo]()

---

## Lista de Tareas Pendientes

- [] Implementar Pruebas Unitarias (PHPUnit).
- [] Agregar más roles de usuario y permisos.
- [] Mejorar el frontend con más características interactivas (AJAX).
- [] Mejorar la estética de las alertas.
- [] Más integración de lore de la Fundación SCP.
- [] Agregar sistema de notificaciones. (Esto quizás envíe alertas aleatorias a los usuarios sobre brechas de contención, etc.)

---
Secure. Contain. Protect.

<div align="center"> <sub>Desarrollado con ❤️ por <a href="https://github.com/Lotoz">Lotoz</a></sub> </div>

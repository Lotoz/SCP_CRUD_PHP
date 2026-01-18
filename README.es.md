# 📂 SCP Foundation Secure Database System

![SCP Logo](https://upload.wikimedia.org/wikipedia/commons/e/ec/SCP_Foundation_%28edificio%29_logo.svg)

> **⚠️ AVISO:** Este software es un **proyecto educativo** desarrollado para demostrar competencias en **PHP Nativo, POO y Arquitectura MVC**. No está diseñado para uso en entornos de producción crítica.

## 📖 Descripción General

El **SCP Secure Database System** es una aplicación web *Full-Stack* que simula la base de datos clasificada de la Fundación SCP. Permite la gestión segura de expedientes de anomalías, administración de personal, asignación de sitios de contención y tareas operativas.

El sistema está construido siguiendo estrictamente el patrón **Modelo-Vista-Controlador (MVC)** sin el uso de frameworks, garantizando un código limpio, modular y escalable.

---

## 🚀 Características Principales

### 🔐 Seguridad y Autenticación

* **Sistema de Login:** Roles y Niveles de Seguridad (1 al 5).
* **Protección:** Hashing de contraseñas (`Bcrypt`), Tokens CSRF en formularios y protección contra *Session Fixation*.
* **Prevención de Errores:** Bloqueo de auto-eliminación (un administrador no puede borrar su propia cuenta).

### 📂 Gestión de Anomalías (SCPs)

* **CRUD Completo:** Crear, leer, editar y borrar expedientes.
* **Gestión Multimedia:** Subida de imágenes con **renombramiento automático** basado en el ID del SCP.
* **Wiki Pública:** Visualización de tarjetas dinámicas con estilos según la clase del objeto (Safe, Euclid, Keter).

### 🛠️ Administración de Personal

* **Control Total:** Panel exclusivo para Nivel 5 (Consejo O5).
* **UX:** Visualización de contraseñas con botón *toggle* y validaciones en tiempo real (JS).

---

## ⚙️ Tecnologías Utilizadas

* **Backend:** PHP 8.2+ (Nativo, POO).
* **Base de Datos:** MySQL / MariaDB (InnoDB).
* **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (ES6).
* **Servidor:** Apache (XAMPP/LAMPP).

---

## 📋 Reglas del Sistema (Lore & Lógica de Negocio)

Este proyecto implementa reglas estrictas para simular la burocracia de la Fundación SCP:

1. **Nomenclatura SCP:** Todos los IDs de anomalías deben comenzar estrictamente con **`SCP-`** (ej: `SCP-173`).
2. **Protocolo de Contención:**
    * Las clases **SAFE, EUCLID y NEUTRALIZED** *deben* tener un Sitio de Contención asignado obligatoriamente.
    * Las clases **KETER y THAUMIEL** son las únicas que pueden tener ubicación desconocida (`NULL`).
3. **Integridad de Usuarios:**
    * Los Nombres de Usuario solo aceptan letras, números, guiones medios y bajos (Regex: `/^[a-zA-Z0-9_-]+$/`).
    * Un usuario **Nivel 0 (Clase-D)** tiene acceso de *Solo Lectura* a sus tareas.

---

## 🔧 Instalación y Despliegue

Sigue estos pasos para levantar el proyecto en tu entorno local:

### 1. Clonar el Repositorio

```bash
git clone [https://github.com/tu-usuario/scp-crud-php.git](https://github.com/tu-usuario/scp-crud-php.git)
cd scp-crud-php
```

### 2. Configurar el Servidor Local

1. Base de Datos

    Abre tu gestor de base de datos (phpMyAdmin, DBeaver, etc.).

    Crea una base de datos llamada scp_data.

    Importa el script ubicado en: 📂 DATABASE/scp_data.sql

2. Configuración

Edita el archivo de configuración con tus credenciales locales: 📂 SCP_CRUD_PHP/config/Database.php
PHP

private $host = "localhost";
private $db_name = "scp_data";
private $username = "view"; // Tu usuario
private $password = "";     // Tu contraseña

Por defecto, las credenciales son: view / yX/I!geU1xKbG3F[ para propósitos de prueba.

3. Permisos (Solo Linux/Mac)

Asegúrate de que la carpeta de subidas tenga permisos de escritura:
Bash

chmod -R 777 views/CRUD/anomalies/assets/img/

📂 Estructura del Proyecto
Plaintext

/
├── DATABASE/           # Scripts SQL y Seeders
├── EXTRA/              # Credenciales de prueba y notas
├── pictures_README/    # Imágenes para documentación
├── SCP_CRUD_PHP/       # CÓDIGO FUENTE DE LA APLICACIÓN
│   ├── config/         # Conexión DB y SessionManager
│   ├── controllers/    # Lógica de negocio
│   ├── models/         # Entidades
│   ├── repositories/   # Consultas SQL (Pattern Repository)
│   ├── views/          # Interfaz de usuario (HTML/PHP)
│   └── index.php       # Router principal
└── README.md           # Este archivo

🔑 Credenciales de Acceso (Demo)

Puedes encontrar una lista completa de usuarios de prueba en la carpeta 📂 EXTRA/.

---

## 📜 Licencia

Este proyecto es de código abierto y está bajo la licencia MIT. Consulta el archivo LICENSE para más detalles.

---
Secure. Contain. Protect.

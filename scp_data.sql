-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-12-2025 a las 16:49:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `scp_data`
--
CREATE DATABASE IF NOT EXISTS `scp_data` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `scp_data`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` varchar(25) NOT NULL,
  `password` varchar(250) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `apellido` varchar(70) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rol` varchar(60) NOT NULL,
  `level` int(2) NOT NULL DEFAULT 1,
  `theme` varchar(60) NOT NULL DEFAULT 'gears',
  `intentosFallidos` int(2) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 0,
  `fechaCreacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `password`, `nombre`, `apellido`, `email`, `rol`, `level`, `theme`, `intentosFallidos`, `activo`, `fechaCreacion`) VALUES
('Afton', '$2y$10$0RkS08Ar5f/rdQceioubReKHSQqf585nmx3.f.nNCoZyTKSB/QOga', 'William', 'Afton', 'afton@scp.com', 'cleaner', 1, 'unicorn', 0, 1, '2025-12-19'),
('Alto_clef', '$2y$10$YERfqb/FAx2QeFWzW1X0a.NWdI8iaeC3JLVccneu3/ew/kT2cKtaO', 'Francis', 'Wojcienchowski', 'alto.clef@scp.com', 'scienct', 3, 'clef', 0, 1, '2025-12-19'),
('breenaIce', '$2y$10$VJhuLlEtxafg0ljtsCNlzeoMnXT50CBuJVwRz.EvxWerMLq24dymS', 'Breena', 'Icefrost', 'breena.icefrost@scp.com', 'scienct', 4, 'ice', 0, 1, '2025-12-19'),
('DrGears', '$2y$10$zgNXIY.I4NRYI.LUJnUriedf.h4d.GIg7yON27xyaENhQvv2dZZKq', 'Charles', 'Ogden Gears', 'charles.gears@scp.com', 'scienct', 5, 'gears', 0, 1, '2025-12-19'),
('Lotoz', '$2y$10$QPCGbTW/lO2/ssTQ9uxQ2.bTZ2dkfFLhbQYrP5njU.7yBA0FQcfv.', 'Lotoz', 'Darken', 'lotoz.scp@scp.com', 'scienct', 6, 'admin', 0, 1, '2025-12-19'),
('sophieR', '$2y$10$VO1dwpzvkn4FM35Ef1kVb.Z/kXbiIXeOZ2mnKkwFlp0BjHrcRg.PC', 'Sophie Ariadna', 'Scarlett', 'scarlettSophie@scp.com', 'researcher', 2, 'sophie', 0, 1, '2025-12-19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Privilegios para `view`@`%` user necesario para el uso de la base de datos su usario es especifico
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO `view`@`%` IDENTIFIED BY PASSWORD '*5D4B9AEB6CE62913970923D2B7A5BC15F2199608';

GRANT ALL PRIVILEGES ON `scp_data`.* TO `view`@`%`;

FLUSH PRIVILEGES;

-- Nueva implementacion 
-- 1. Tabla EX-EMPLEADOS (Historial de usuarios borrados)
-- Guardará a los usuarios borrados. Usamos varchar(25) para el ID.
CREATE TABLE IF NOT EXISTS `ex_empleados` (
    `id` varchar(25) NOT NULL,
    `nombre` varchar(70),
    `apellido` varchar(70),
    `rol` varchar(60),
    `level` int(2),
    `fecha_eliminacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- 2. Tabla SITIO
-- El administrador se vincula a users(id) que es varchar(25)
CREATE TABLE IF NOT EXISTS `sitio` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_sitio` VARCHAR(100) NOT NULL,
    `ubicacion` TEXT,
    `id_administrador` VARCHAR(25),
    INDEX (`id_administrador`),
    CONSTRAINT `fk_sitio_admin` FOREIGN KEY (`id_administrador`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- 3. Tabla SCP (Las Anomalías)
CREATE TABLE IF NOT EXISTS `scp` (
    `id` VARCHAR(20) PRIMARY KEY, -- Ej: 'SCP-173'
    `apodo` VARCHAR(100),
    `class` ENUM('Keter', 'Euclid', 'Safe', 'Apollyn', 'CognitoPeligroso', 'Anulado') NOT NULL,
    `contencion` TEXT,
    `descripcion` TEXT,
    `doc_extensa` VARCHAR(255),
    `id_sitio` INT,
    INDEX (`id_sitio`),
    CONSTRAINT `fk_scp_sitio` FOREIGN KEY (`id_sitio`) REFERENCES `sitio`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- 4. Tabla TAREAS ASIGNADAS
-- Vinculada al usuario que debe realizarla
CREATE TABLE IF NOT EXISTS `tareas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `descripcion` TEXT NOT NULL,
    `completado` TINYINT(1) DEFAULT 0,
    `id_usuario` VARCHAR(25) NOT NULL,
    INDEX (`id_usuario`),
    CONSTRAINT `fk_tarea_user` FOREIGN KEY (`id_usuario`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- 5. Tabla N:M PERSONAL_ASIGNADO
-- Quién cuida a qué SCP. Vincula users(id) con scp(id)
CREATE TABLE IF NOT EXISTS `personal_asignado` (
    `id_usuario` VARCHAR(25) NOT NULL,
    `id_scp` VARCHAR(20) NOT NULL,
    `rol_anomalia` VARCHAR(50), -- Ej: 'Investigador Jefe'
    PRIMARY KEY (`id_usuario`, `id_scp`),
    CONSTRAINT `fk_pa_user` FOREIGN KEY (`id_usuario`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pa_scp` FOREIGN KEY (`id_scp`) REFERENCES `scp`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------
-- LOGICA AUTOMÁTICA (TRIGGERS Y VISTAS) -> Quita carga al servidor de php y es un trabajo que debe hacer la base de datos
-- --------------------------------------------------------

-- A. Trigger para Ex-Empleados
-- Si borras a alguien de 'users', se guarda aquí automáticamente.
DROP TRIGGER IF EXISTS `before_user_delete`;
DELIMITER //
CREATE TRIGGER `before_user_delete`
BEFORE DELETE ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO `ex_empleados` (id, nombre, apellido, rol, level, fecha_eliminacion)
    VALUES (OLD.id, OLD.nombre, OLD.apellido, OLD.rol, OLD.level, NOW());
END;
//
DELIMITER ;

-- B. Vista de Detalles del Sitio (Cálculo automático de personal)
-- Te dice cuántos empleados únicos están asignados a SCPs dentro de un sitio
-- Sirve para ahorrar consultas a la base de datos
CREATE OR REPLACE VIEW `vista_sitio_detalles` AS
SELECT 
    s.id AS Sitio_ID,
    s.nombre_sitio,
    CONCAT(u.nombre, ' ', u.apellido) AS Administrador,
    COUNT(DISTINCT pa.id_usuario) AS Total_Personal_Asignado
FROM `sitio` s
LEFT JOIN `users` u ON s.id_administrador = u.id
LEFT JOIN `scp` sc ON s.id = sc.id_sitio
LEFT JOIN `personal_asignado` pa ON sc.id = pa.id_scp
GROUP BY s.id;
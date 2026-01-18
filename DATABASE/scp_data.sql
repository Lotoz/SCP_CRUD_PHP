-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Servidor: 127.0.0.1
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
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(25) NOT NULL,
  `password` varchar(250) NOT NULL,
  `name` varchar(70) NOT NULL,
  `lastname` varchar(70) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rol` varchar(60) NOT NULL,
  `level` int(2) NOT NULL DEFAULT 1,
  `theme` varchar(60) NOT NULL DEFAULT 'gears',
  `tryAttempts` int(2) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `creationDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Gestión de usuarios y permisos
--
GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO `view`@`localhost` IDENTIFIED BY PASSWORD '*5D4B9AEB6CE62913970923D2B7A5BC15F2199608';
GRANT ALL PRIVILEGES ON `scp_data`.* TO `view`@`localhost`;
FLUSH PRIVILEGES;

-- --------------------------------------------------------
-- NUEVA IMPLEMENTACIÓN DE TABLAS
-- --------------------------------------------------------

-- Tabla EX-EMPLEADOS (Historial de usuarios borrados)
CREATE TABLE IF NOT EXISTS `ex_empleados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(70),
    `lastname` varchar(70),
    `rol` varchar(60),
    `level` int(2),
    `fecha_eliminacion` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Tabla SITIO
CREATE TABLE IF NOT EXISTS `sitio` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_sitio` VARCHAR(100) NOT NULL,
    `ubicacion` TEXT,
    `id_administrador` VARCHAR(25),
    INDEX (`id_administrador`),
    CONSTRAINT `fk_sitio_admin` FOREIGN KEY (`id_administrador`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Tabla SCP (Las Anomalías)
CREATE TABLE IF NOT EXISTS `anomalies` (
    `id` VARCHAR(20) PRIMARY KEY,
    `nickname` VARCHAR(100),
    `class` VARCHAR(255) NOT NULL,
    `contencion` TEXT,
    `description` TEXT,
    `doc_extensa` VARCHAR(255),
    `img_url` TEXT,
    `id_sitio` INT,
    INDEX (`id_sitio`),
    CONSTRAINT `fk_scp_sitio` FOREIGN KEY (`id_sitio`) REFERENCES `sitio`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Tabla TAREAS
CREATE TABLE IF NOT EXISTS `tasks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `description` TEXT NOT NULL,
    `due_date` DATE DEFAULT NULL,
    `completado` TINYINT(1) DEFAULT 0,
    `id_usuario` VARCHAR(25) NOT NULL,
    INDEX (`id_usuario`),
    CONSTRAINT `fk_tarea_user` FOREIGN KEY (`id_usuario`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Tabla N:M PERSONAL_ASIGNADO
CREATE TABLE IF NOT EXISTS `assigned_personnel` (
    `user_id` VARCHAR(25) NOT NULL,
    `scp_id` VARCHAR(20) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'Containment Specialist',
    PRIMARY KEY (`user_id`, `scp_id`),
    CONSTRAINT `fk_ap_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ap_scp` FOREIGN KEY (`scp_id`) REFERENCES `anomalies`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------
-- LOGICA AUTOMÁTICA (TRIGGERS)
-- --------------------------------------------------------

-- Trigger para Ex-Empleados
DROP TRIGGER IF EXISTS `before_user_delete`;

DELIMITER $$
CREATE TRIGGER `before_user_delete` BEFORE DELETE ON `users`
FOR EACH ROW 
BEGIN
    INSERT INTO `ex_empleados` (name, lastname, rol, level, fecha_eliminacion)
    VALUES (OLD.name, OLD.lastname, OLD.rol, OLD.level, NOW());
END$$
DELIMITER ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
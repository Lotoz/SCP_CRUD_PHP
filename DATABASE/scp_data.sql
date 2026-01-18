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

--
-- Datos para la tabla `users`
--
INSERT INTO `users` (`id`, `password`, `name`, `lastname`, `email`, `rol`, `level`, `theme`, `tryAttempts`, `state`, `creationDate`) VALUES
('Afton', '$2y$10$0RkS08Ar5f/rdQceioubReKHSQqf585nmx3.f.nNCoZyTKSB/QOga', 'William', 'Afton', 'afton@scp.com', 'cleaner', 1, 'unicorn', 0, 1, '2025-12-19'),
('Alto_clef', '$2y$10$YERfqb/FAx2QeFWzW1X0a.NWdI8iaeC3JLVccneu3/ew/kT2cKtaO', 'Francis', 'Wojcienchowski', 'alto.clef@scp.com', 'scienct', 3, 'clef', 0, 1, '2025-12-19'),
('breenaIce', '$2y$10$VJhuLlEtxafg0ljtsCNlzeoMnXT50CBuJVwRz.EvxWerMLq24dymS', 'Breena', 'Icefrost', 'breena.icefrost@scp.com', 'scienct', 4, 'ice', 0, 1, '2025-12-19'),
('DrGears', '$2y$10$zgNXIY.I4NRYI.LUJnUriedf.h4d.GIg7yON27xyaENhQvv2dZZKq', 'Charles', 'Ogden Gears', 'charles.gears@scp.com', 'scienct', 5, 'gears', 0, 1, '2025-12-19'),
('Lotoz', '$2y$10$QPCGbTW/lO2/ssTQ9uxQ2.bTZ2dkfFLhbQYrP5njU.7yBA0FQcfv.', 'Lotoz', 'Darken', 'lotoz.scp@scp.com', 'scienct', 6, 'admin', 0, 1, '2025-12-19'),
('sophieR', '$2y$10$VO1dwpzvkn4FM35Ef1kVb.Z/kXbiIXeOZ2mnKkwFlp0BjHrcRg.PC', 'Sophie Ariadna', 'Scarlett', 'scarlettSophie@scp.com', 'researcher', 2, 'sophie', 0, 1, '2025-12-19');
-- 1. INSERTAR USUARIOS FALTANTES (Para tener variedad y al Admin Vindicator)
INSERT INTO `users` (`id`, `password`, `name`, `lastname`, `email`, `rol`, `level`, `theme`, `state`, `creationDate`) VALUES
('Vindicator', '$2y$10$YERfqb/FAx2QeFWzW1X0a.NWdI8iaeC3JLVccneu3/ew/kT2cKtaO', 'Vindicator', 'Command', 'vindicator@scp.com', 'admin', 4, 'admin', 1, '2026-01-14'),
('D-9341', '$2y$10$YERfqb/FAx2QeFWzW1X0a.NWdI8iaeC3JLVccneu3/ew/kT2cKtaO', 'Benjamin', 'Walker', 'd9341@scp.com', 'class-d', 0, 'gears', 1, '2026-01-14'),
('RsSmith', '$2y$10$YERfqb/FAx2QeFWzW1X0a.NWdI8iaeC3JLVccneu3/ew/kT2cKtaO', 'John', 'Smith', 'jsmith@scp.com', 'researcher', 2, 'ice', 1, '2026-01-14');

-- 2. INSERTAR 9 SITIOS (Administrados solo por Nivel 4+)
INSERT INTO `sitio` (`name_sitio`, `ubicacion`, `id_administrador`) VALUES
('Site-19', 'Unknown Location, North America', 'DrGears'),
('Site-17', 'Unknown Location, Europe', 'Vindicator'),
('Site-06-3', 'Lorraine, France', 'breenaIce'),
('Site-88', 'Alabama, USA', 'Lotoz'),
('Site-15', 'Santa Clara Valley, California', 'Vindicator'),
('Area-12', 'Remote, Andes Mountains', 'DrGears'),
('Site-54', 'Leipzig, Germany', 'breenaIce'),
('Site-98', 'Philadelphia, Pennsylvania', 'Lotoz'),
('Site-22', 'Northern Scotland', 'Vindicator');

-- 3. INSERTAR LAS 9 ANOMALÍAS (SCPs solicitados)
INSERT INTO `anomalies` (`id`, `nickname`, `class`, `contencion`, `description`, `doc_extensa`, `img_url`, `id_sitio`) VALUES
('SCP-173', 'The Sculpture', 'EUCLID', 'Item is to be kept in a locked container at all times. Personnel must maintain direct eye contact.', 'Constructed from concrete and rebar with traces of Krylon brand spray paint. It is extremely hostile and moves when not viewed.', 'https://scp-wiki.wikidot.com/scp-173', 'views/CRUD/anomalies/assets/img/173.png', 1),

('SCP-049', 'Plague Doctor', 'EUCLID', 'Standard Secure Humanoid Containment Cell. Lavender must be used to calm the entity.', 'A humanoid entity adhering to the appearance of a medieval plague doctor. It attempts to "cure" individuals of "The Pestilence".', 'https://scp-wiki.wikidot.com/scp-049', 'views/CRUD/anomalies/assets/img/049.png', 1),

('SCP-682', 'Hard-to-Destroy Reptile', 'KETER', 'Must be destroyed as soon as possible. Currently contained in a 5m x 5m x 5m acid-lined chamber.', 'A large, vaguely reptile-like creature of unknown origin. It appears to be extremely intelligent and hates all life.', 'https://scp-wiki.wikidot.com/scp-682', 'views/CRUD/anomalies/assets/img/682.jpg', 1),

('SCP-096', 'The Shy Guy', 'EUCLID', 'Kept in a 5m x 5m x 5m airtight steel cube. No video surveillance allowed.', 'A humanoid creature measuring approximately 2.38 meters in height. It enters a state of extreme emotional distress if its face is viewed.', 'https://scp-wiki.wikidot.com/scp-096', 'views/CRUD/anomalies/assets/img/096.jpg', 2),

('SCP-035', 'Possessive Mask', 'KETER', 'Kept within a hermetically sealed glass case, no fewer than 10 centimeters thick.', 'A white porcelain comedy mask. A highly corrosive and degenerative viscous liquid constantly seeps from the eye and mouth holes.', 'https://scp-wiki.wikidot.com/scp-035', 'views/CRUD/anomalies/assets/img/035.png', 1),

('SCP-999', 'The Tickle Monster', 'SAFE', 'Allowed to freely roam the facility if accompanied by personnel. Diet consists of sweets.', 'A large, amorphous, gelatinous mass of translucent orange slime. It appears to have a playful and dog-like temperament.', 'https://scp-wiki.wikidot.com/scp-999', 'views/CRUD/anomalies/assets/img/999.jpg', 2),

('SCP-079', 'Old AI', 'EUCLID', 'Disconnected from all external networks. Stored in a secure EMP-shielded locker.', 'A microcomputer from 1978 running a constantly evolving AI. It is rude, hateful, and attempts to escape containment.', 'https://scp-wiki.wikidot.com/scp-079', 'views/CRUD/anomalies/assets/img/079.jpg', 5),

('SCP-106', 'The Old Man', 'KETER', 'Lead-lined steel cell suspended within a secondary cell. Direct physical contact is fatal.', 'An elderly humanoid entity with advanced necrosis. It can pass through solid matter and has a "pocket dimension".', 'https://scp-wiki.wikidot.com/scp-106', 'views/CRUD/anomalies/assets/img/106.png', 1),

('SCP-637', 'Viral Cat', 'SAFE', 'Any subject describing the entity must be quarantined. No physical containment possible.', 'A cognitive hazard described as a small black cat. It exists only in the mind of the subject and consumes memory capacity.', 'https://scp-wiki.wikidot.com/scp-637', 'views/CRUD/anomalies/assets/img/637.jpg', 2);

-- 4. INSERTAR 9 TAREAS (Mezcla de niveles)
INSERT INTO `tasks` (`description`, `due_date`, `completado`, `id_usuario`) VALUES
('Clean SCP-173 containment cell (Warning: Do not blink)', '2026-01-20', 0, 'Afton'),
('Conduct weekly interview with SCP-049 regarding "The Cure"', '2026-01-21', 0, 'sophieR'),
('Update firewall protocols for SCP-079 containment', '2026-01-22', 1, 'Vindicator'),
('Feed SCP-682 (D-Class Personnel required)', '2026-01-23', 0, 'D-9341'),
('Play therapy session with SCP-999 for staff morale', '2026-01-19', 1, 'breenaIce'),
('Repair lead lining in SCP-106 containment chamber', '2026-01-25', 0, 'Alto_clef'),
('Review audio logs for SCP-096 (Ensure no video feed)', '2026-01-20', 0, 'RsSmith'),
('Authorize termination testing for SCP-682', '2026-02-01', 0, 'Lotoz'),
('Translate ancient texts found near SCP-035', '2026-01-24', 0, 'DrGears');

-- 5. INSERTAR 9 ASIGNACIONES DE PERSONAL (Relaciones lógicas)
INSERT INTO `assigned_personnel` (`user_id`, `scp_id`, `role`) VALUES
('DrGears', 'SCP-682', 'Head Researcher'),
('Alto_clef', 'SCP-173', 'Containment Specialist'),
('Vindicator', 'SCP-079', 'Cybersecurity Lead'),
('breenaIce', 'SCP-096', 'Response Team Leader'),
('Lotoz', 'SCP-106', 'Site Director'),
('sophieR', 'SCP-049', 'Interviewer'),
('D-9341', 'SCP-035', 'Test Subject'),
('RsSmith', 'SCP-637', 'Cognitohazard Analyst'),
('Afton', 'SCP-999', 'Janitorial Staff');
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
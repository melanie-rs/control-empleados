-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2026 a las 19:24:52
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
-- Base de datos: `control_empleados`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `dni` varchar(15) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `dni`, `clave`, `nombre`, `apellido`, `activo`) VALUES
(1, '40111222', '$2y$10$oU0sY0DPaZQwWb43l1X3CO5iFSxq5QV1l657ZJt1MaAJ6GLSY3Qnu', 'Ana', 'Gomez', 1),
(2, '40222333', '$2y$10$ydSLMvvXpU0d.OglUGKIXuyZhzT6o0niyH29bc8MCCERxFKTv5zKC', 'Juan', 'Perez', 1),
(3, '40333444', '$2y$10$slht4SflTm7ddTozM45ED.3.vh.PTUyH9fZYZKCzMeSshAVwMDFue', 'Lucia', 'Martinez', 1),
(4, '40444555', '$2y$10$fA2WwtHS7PLveiD2MIQ57uSzcjrNB7Ky8tjh/o7U3Yx1coKKt0M/q', 'Miguel', 'Lopez', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `empleado_id`, `tipo`, `fecha_hora`) VALUES
(1, 1, 'ingreso', '2026-10-13 13:41:14'),
(2, 1, 'egreso', '2026-10-13 13:41:20'),
(3, 1, 'ingreso', '2026-10-13 13:42:22'),
(4, 1, 'egreso', '2026-10-13 13:42:56'),
(5, 2, 'ingreso', '2026-10-13 14:06:22'),
(6, 2, 'egreso', '2026-10-13 14:32:51'),
(7, 1, 'ingreso', '2026-10-14 12:01:05'),
(8, 1, 'egreso', '2026-10-14 12:01:11'),
(9, 1, 'ingreso', '2026-10-14 12:03:19'),
(10, 1, 'egreso', '2026-10-14 12:03:28'),
(11, 4, 'ingreso', '2026-10-14 13:24:13'),
(12, 4, 'egreso', '2026-10-14 13:24:15'),
(13, 4, 'ingreso', '2026-10-14 13:45:57'),
(14, 4, 'egreso', '2026-10-14 13:46:03'),
(15, 4, 'ingreso', '2026-10-14 14:02:03'),
(16, 4, 'egreso', '2026-10-14 14:02:37'),
(17, 4, 'ingreso', '2026-10-14 14:09:08'),
(18, 4, 'egreso', '2026-10-14 14:09:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-08-2021 a las 14:50:51
-- Versión del servidor: 5.7.24
-- Versión de PHP: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tasksdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbltasks`
--

CREATE TABLE `tbltasks` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext,
  `deadline` datetime DEFAULT NULL,
  `compleated` enum('N','Y') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Task Table';

--
-- Volcado de datos para la tabla `tbltasks`
--

INSERT INTO `tbltasks` (`id`, `title`, `description`, `deadline`, `compleated`) VALUES
(2, 'Limpiar el patio', 'Limpiar el patio y recoger la basura', '2021-06-21 17:00:00', 'N'),
(3, 'JUntar basura', 'Juntar basura y ubicar en su lugar', '2021-06-21 18:00:00', 'N'),
(4, 'Limpiar al perro', 'Limpiar al perro y secarlo con secador', '2021-06-21 13:00:00', 'Y');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbltasks`
--
ALTER TABLE `tbltasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbltasks`
--
ALTER TABLE `tbltasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

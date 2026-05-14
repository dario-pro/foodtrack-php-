-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-09-2025 a las 01:42:37
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
-- Base de datos: `sistema_alimentacion_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alimentos`
--

CREATE TABLE `alimentos` (
  `id` int(11) NOT NULL,
  `id_donante` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen_url` varchar(255) NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `porcentaje_descuento` tinyint(3) UNSIGNED DEFAULT 0,
  `estado` enum('fresco','apto_consumo','proximo_a_vencer','caducado') NOT NULL DEFAULT 'apto_consumo',
  `activo` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alimentos`
--

INSERT INTO `alimentos` (`id`, `id_donante`, `nombre`, `descripcion`, `precio`, `stock`, `imagen_url`, `fecha_caducidad`, `porcentaje_descuento`, `estado`, `activo`, `creado_en`, `actualizado_en`) VALUES
(27, 56, 'Manzanas', 'frescas mazandas', 0.20, 2, 'update/1758440658_Apples.jpg', '2025-09-27', 7, 'fresco', 1, '2025-09-21 07:44:18', '2025-09-21 18:56:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `activo`) VALUES
(1, 'Verduras', 1),
(2, 'Granos', 0),
(3, 'Comida Caliente', 1),
(4, 'Frutas', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `id_receptor` int(11) NOT NULL,
  `fecha_compra` date DEFAULT curdate(),
  `total` decimal(10,2) NOT NULL,
  `fecha_entrega` date NOT NULL,
  `direccion_entrega` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `id_receptor`, `fecha_compra`, `total`, `fecha_entrega`, `direccion_entrega`) VALUES
(3, 9, '2025-08-21', 0.58, '2025-08-29', 'asda'),
(4, 9, '2025-08-22', 0.58, '2025-08-28', 'Calle sin nombre'),
(5, 9, '2025-09-15', 2.07, '2025-09-17', 'Calle sin nombre'),
(6, 9, '2025-09-15', 1.15, '2025-09-17', 'Calle sin nombre'),
(7, 9, '2025-09-15', 1.61, '2025-09-17', 'Calle sin nombre'),
(8, 9, '2025-09-16', 1.55, '2025-09-24', 'Calle sin nombre'),
(9, 9, '2025-09-18', 0.52, '2025-09-26', 'Calle sin nombre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_alimento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locales`
--

CREATE TABLE `locales` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_tipo_local` int(11) NOT NULL,
  `nombre_local` varchar(100) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `sector` enum('norte','sur','este','oeste','centro','no_definido') NOT NULL DEFAULT 'no_definido',
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `locales`
--

INSERT INTO `locales` (`id`, `id_usuario`, `id_tipo_local`, `nombre_local`, `imagen_url`, `sector`, `direccion`, `telefono`, `creado_en`) VALUES
(15, 56, 3, 'La Merced', 'update/1758426819_fruteria1024x683.jpg', 'norte', 'sadasd', '0999999999', '2025-09-21 03:53:39'),
(16, 57, 1, 'Fruteria la 1', 'update/1758475805_fruteria.jpg', 'sur', 'Solanda', '0999999999', '2025-09-21 17:30:05'),
(17, 58, 3, 'Pasteleria j', 'update/1758480444_Panaderia.jpg', 'norte', 'Carolina N02-O5', '0889545757', '2025-09-21 18:47:24'),
(18, 59, 3, 'Pasteleria Cayambe', 'update/1758480577_Panaderia.jpg', 'norte', 'Carolina N02-O5', '0988255454', '2025-09-21 18:49:37'),
(19, 60, 2, 'kfc', 'update/1758493173_KFC_Cumbayá_(Quito,_Ecuador).png', 'norte', 'Quicentro norte', '0985645255', '2025-09-21 22:19:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moderaciones`
--

CREATE TABLE `moderaciones` (
  `id` int(11) NOT NULL,
  `id_moderador` int(11) NOT NULL,
  `id_alimento` int(11) NOT NULL,
  `fecha_revision` timestamp NOT NULL DEFAULT current_timestamp(),
  `accion` enum('aprobado','pendiente','rechazado') NOT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `moderaciones`
--

INSERT INTO `moderaciones` (`id`, `id_moderador`, `id_alimento`, `fecha_revision`, `accion`, `comentario`) VALUES
(199, 6, 27, '2025-09-21 07:44:28', 'aprobado', 'Producto aprobado por moderador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(3, 'donante'),
(2, 'moderador'),
(4, 'receptor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_local`
--

CREATE TABLE `tipo_local` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_local`
--

INSERT INTO `tipo_local` (`id`, `nombre`) VALUES
(2, 'Comida'),
(1, 'Frutas'),
(3, 'Panaderia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_rol`, `nombre`, `apellido`, `email`, `password`, `direccion`, `telefono`, `activo`, `creado_en`) VALUES
(6, 2, 'Moderador', 'M', 'moderador@gmail.com', '$2y$10$HAvP4Ac5htmEpw0ujcD7wOEINVJ0XtlE5WYzAiXqEm85/jgXeBd7S', 'sin info', '0999999999', 1, '2025-07-22 05:44:41'),
(9, 4, 'Jostin', 'Taco', 'consumidor@gmail.com', '$2y$10$a2hiHKO44P/b/AJwCuiwf.c/QfZkwCwIyox/B3iczDbxFSDHUbHee', 'Linea ferrea', '0999999999555', 1, '2025-08-17 22:58:49'),
(54, 4, 'Alexis', 'Quilca', 'alexis@gmail.com', '$2y$10$4KCdGkCx26XQAhTn5RIgt.SNsw.OvVVQzXO3KRCzZl2SAsI8H0j9e', 'Guamani', '0999999999', 1, '2025-09-21 03:49:30'),
(56, 3, 'Andrea', 'Guaman', 'merced@gmail.com', '$2y$10$LoA05upzXr1Ct8RPNKc9rOU.epplHr/ht/NSnP/tiKLpNgiWAIZgO', 'sadasd', '0999999999', 1, '2025-09-21 03:53:39'),
(57, 3, 'fruteria1', 'fruteria1 apellido', 'fruteria1@gmail.com', '$2y$10$U2Nt4jyl4Jtdmu0igJEoo.Fh/1v4rixVOFvAzHH/Ig7vY2P0nxd02', 'Solanda', '0999999999', 1, '2025-09-21 17:30:05'),
(58, 3, 'Ericka', 'Velez', 'pasteleriaj@gmail.com', '$2y$10$Xq573b6Pd9QWoMCggBVnfeN8aMfr5HSYKQ2h/PJk7ro.VOTvcAL0.', 'Carolina N02-O5', '0889545757', 1, '2025-09-21 18:47:24'),
(59, 3, 'Dayana', 'Yepez', 'pasteleriac@gmail.com', '$2y$10$WQ973p.cXJfbyTztPIzvEOUWVt16zHARb4FL8FQxyVoE7SoaCqmQe', 'Carolina N02-O5', '0988255454', 1, '2025-09-21 18:49:37'),
(60, 3, 'Jorge', 'Alvarez', 'kfc@gmail.com', '$2y$10$mEgBiwYVJmTK12GTQitqGeZpzwL0ChxQfmz1Q0sgsP8gk6VndKbWO', 'Quicentro norte', '0985645255', 1, '2025-09-21 22:19:33');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`id_donante`,`nombre`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_receptor` (`id_receptor`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_alimento` (`id_alimento`);

--
-- Indices de la tabla `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_local` (`id_tipo_local`);

--
-- Indices de la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_alimento` (`id_alimento`),
  ADD KEY `id_moderador` (`id_moderador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipo_local`
--
ALTER TABLE `tipo_local`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_local`
--
ALTER TABLE `tipo_local`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD CONSTRAINT `alimentos_ibfk_1` FOREIGN KEY (`id_donante`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_receptor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`id_alimento`) REFERENCES `alimentos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `locales`
--
ALTER TABLE `locales`
  ADD CONSTRAINT `locales_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `locales_ibfk_2` FOREIGN KEY (`id_tipo_local`) REFERENCES `tipo_local` (`id`);

--
-- Filtros para la tabla `moderaciones`
--
ALTER TABLE `moderaciones`
  ADD CONSTRAINT `moderaciones_ibfk_1` FOREIGN KEY (`id_alimento`) REFERENCES `alimentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderaciones_ibfk_2` FOREIGN KEY (`id_moderador`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

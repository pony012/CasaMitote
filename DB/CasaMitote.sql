-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 16, 2016 at 10:56 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `CasaMitote`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cuentas`
--

CREATE TABLE IF NOT EXISTS `Cuentas` (
  `idCuenta` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  `subTotal` float NOT NULL,
  `total` float NOT NULL,
  `pagada` int(11) NOT NULL DEFAULT '0',
  `pagoEfectivo` float DEFAULT '0',
  `pagoTarjeta` float DEFAULT '0',
  `pagoTotal` float DEFAULT '0',
  `sobra` float DEFAULT '0',
  `activa` int(11) NOT NULL DEFAULT '1',
  `nombre` varchar(63) DEFAULT NULL,
  `grupo` varchar(63) DEFAULT NULL,
  `comentario` varchar(127) DEFAULT NULL,
  PRIMARY KEY (`idCuenta`),
  KEY `idUsuario` (`idUsuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Table structure for table `Permisos`
--

CREATE TABLE IF NOT EXISTS `Permisos` (
  `idPermiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(63) NOT NULL,
  PRIMARY KEY (`idPermiso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `Permisos`
--

INSERT INTO `Permisos` (`idPermiso`, `nombre`) VALUES
(1, 'CrearCuenta'),
(3, 'CancelarCuenta'),
(4, 'ModificarCuenta'),
(5, 'PanelProductos'),
(6, 'PanelUsuarios'),
(7, 'EliminarProductoCuenta'),
(8, 'RecuperarCuenta');

-- --------------------------------------------------------

--
-- Table structure for table `PermisosCuentas`
--

CREATE TABLE IF NOT EXISTS `PermisosCuentas` (
  `idPermisoCuenta` int(11) NOT NULL AUTO_INCREMENT,
  `idTipoDeCuenta` int(11) NOT NULL,
  `idPermiso` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idPermisoCuenta`),
  KEY `idTipoDeCuenta` (`idTipoDeCuenta`),
  KEY `idPermiso` (`idPermiso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `PermisosCuentas`
--

INSERT INTO `PermisosCuentas` (`idPermisoCuenta`, `idTipoDeCuenta`, `idPermiso`, `activo`) VALUES
(1, 99, 1, 1),
(2, 99, 3, 1),
(3, 99, 4, 1),
(4, 99, 5, 1),
(5, 99, 6, 1),
(6, 2, 1, 1),
(7, 5, 1, 1),
(8, 5, 3, 1),
(9, 5, 4, 1),
(10, 99, 7, 1),
(11, 5, 7, 1),
(12, 99, 8, 1),
(13, 5, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Productos`
--

CREATE TABLE IF NOT EXISTS `Productos` (
  `idProducto` int(11) NOT NULL AUTO_INCREMENT,
  `idTipoProducto` int(11) NOT NULL,
  `nombre` varchar(63) NOT NULL,
  `comentario` varchar(511) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `precio` float NOT NULL,
  PRIMARY KEY (`idProducto`),
  KEY `idTipoProducto` (`idTipoProducto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;


--
-- Table structure for table `ProductosCuenta`
--

CREATE TABLE IF NOT EXISTS `ProductosCuenta` (
  `idProductoCuenta` int(11) NOT NULL AUTO_INCREMENT,
  `idCuenta` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subTotal` float NOT NULL,
  `comentario` varchar(127) DEFAULT NULL,
  PRIMARY KEY (`idProductoCuenta`),
  KEY `idProducto` (`idProducto`),
  KEY `idCuenta` (`idCuenta`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=564 ;

--
-- Table structure for table `TiposDeCuentas`
--

CREATE TABLE IF NOT EXISTS `TiposDeCuentas` (
  `idTipoDeCuenta` int(11) NOT NULL,
  `nombre` varchar(33) NOT NULL,
  `idTipoDeCuentaPadre` int(11) DEFAULT NULL,
  `idTipoDeCuentaHija` int(11) DEFAULT NULL,
  PRIMARY KEY (`idTipoDeCuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TiposDeCuentas`
--

INSERT INTO `TiposDeCuentas` (`idTipoDeCuenta`, `nombre`, `idTipoDeCuentaPadre`, `idTipoDeCuentaHija`) VALUES
(2, 'Empleado', 5, NULL),
(5, 'gerente', 99, 2),
(99, 'root', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `TiposProductos`
--

CREATE TABLE IF NOT EXISTS `TiposProductos` (
  `idTipoProducto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(63) NOT NULL,
  `area` varchar(63) NOT NULL,
  PRIMARY KEY (`idTipoProducto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `Usuario`
--

CREATE TABLE IF NOT EXISTS `Usuario` (
  `idUsuario` int(11) NOT NULL,
  `idTipoDeCuenta` int(11) NOT NULL,
  `nombres` varchar(127) NOT NULL,
  `apellidos` varchar(127) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `correo` varchar(155) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Usuario`
--

INSERT INTO `Usuario` (`idUsuario`, `idTipoDeCuenta`, `nombres`, `apellidos`, `telefono`, `password`, `correo`, `activo`) VALUES
(101, 2, 'Elizabeth', 'Murguia', NULL, 'passEli', NULL, '1'),
(100, 5, 'Jonathan', 'Aguayo', NULL, 'passJona', NULL, '1'),
(102, 2, 'Young Su', 'Kim', NULL, 'passSu', NULL, '1'),
(103, 2, 'Carlos', 'Sanchez', NULL, 'passLeche', NULL, '1'),
(9810, 99, 'Root', 'Root', NULL, 'testRoot', NULL, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Cuentas`
--
ALTER TABLE `Cuentas`
  ADD CONSTRAINT `Cuentas_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `Usuario` (`idUsuario`);

--
-- Constraints for table `PermisosCuentas`
--
ALTER TABLE `PermisosCuentas`
  ADD CONSTRAINT `PermisosCuentas_ibfk_1` FOREIGN KEY (`idTipoDeCuenta`) REFERENCES `TiposDeCuentas` (`idTipoDeCuenta`),
  ADD CONSTRAINT `PermisosCuentas_ibfk_2` FOREIGN KEY (`idPermiso`) REFERENCES `Permisos` (`idPermiso`);

--
-- Constraints for table `Productos`
--
ALTER TABLE `Productos`
  ADD CONSTRAINT `Productos_ibfk_1` FOREIGN KEY (`idTipoProducto`) REFERENCES `TiposProductos` (`idTipoProducto`);

--
-- Constraints for table `ProductosCuenta`
--
ALTER TABLE `ProductosCuenta`
  ADD CONSTRAINT `ProductosCuenta_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `Productos` (`idProducto`),
  ADD CONSTRAINT `ProductosCuenta_ibfk_2` FOREIGN KEY (`idCuenta`) REFERENCES `Cuentas` (`idCuenta`);

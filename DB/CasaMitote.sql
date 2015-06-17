-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 17, 2015 at 01:53 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CasaMitote`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cuentas`
--

CREATE TABLE IF NOT EXISTS `Cuentas` (
  `idCuenta` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  `subTotal` float NOT NULL,
  `total` float NOT NULL,
  `pagada` int(11) NOT NULL DEFAULT '0',
  `pago` int(11) DEFAULT NULL,
  `activa` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idCuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Permisos`
--

CREATE TABLE IF NOT EXISTS `Permisos` (
  `idPermiso` int(11) NOT NULL,
  `nombre` varchar(63) NOT NULL,
  PRIMARY KEY (`idPermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PermisosCuentas`
--

CREATE TABLE IF NOT EXISTS `PermisosCuentas` (
  `idTipoDeCuenta` int(11) NOT NULL,
  `idPermiso` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ProductosCuenta`
--

CREATE TABLE IF NOT EXISTS `ProductosCuenta` (
  `idCuenta` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subTotal` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TiposDeCuentas`
--

CREATE TABLE IF NOT EXISTS `TiposDeCuentas` (
  `idTipoDeCuenta` int(11) NOT NULL,
  `nombre` varchar(33) NOT NULL,
  `idTipoDeCuentaPadre` int(11) DEFAULT NULL,
  PRIMARY KEY (`idTipoDeCuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TiposDeCuentas`
--

INSERT INTO `TiposDeCuentas` (`idTipoDeCuenta`, `nombre`, `idTipoDeCuentaPadre`) VALUES
(5, 'gerente', 99),
(99, 'root', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `TiposProductos`
--

CREATE TABLE IF NOT EXISTS `TiposProductos` (
  `idTipoProducto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(63) NOT NULL,
  `area` varchar(63) NOT NULL,
  PRIMARY KEY (`idTipoProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(1000, 5, 'Alan', 'Sánchez', NULL, 'testGerente', NULL, 1),
(9810, 99, 'Root', 'Root', NULL, 'testRoot', NULL, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

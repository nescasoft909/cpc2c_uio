
CREATE schema hispana;
use hispana;

-- MySQL dump 10.11
--
-- Host: localhost    Database: hispana
-- ------------------------------------------------------
-- Server version	5.0.77

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary table structure for view `_view_campania_calltype`
--

DROP TABLE IF EXISTS `_view_campania_calltype`;
/*!50001 DROP VIEW IF EXISTS `_view_campania_calltype`*/;
/*!50001 CREATE TABLE `_view_campania_calltype` (
  `id` int(11),
  `id_campania` int(11),
  `campania` varchar(50),
  `clase` enum('Contactado','No contactado','Agendado'),
  `call_type` varchar(30),
  `peso` int(11),
  `status` enum('A','I')
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_cdr_hispana_callcenter`
--

DROP TABLE IF EXISTS `_view_cdr_hispana_callcenter`;
/*!50001 DROP VIEW IF EXISTS `_view_cdr_hispana_callcenter`*/;
/*!50001 CREATE TABLE `_view_cdr_hispana_callcenter` (
  `duration` int(11),
  `calldate` datetime,
  `src` varchar(80),
  `dst` varchar(80),
  `userfield` varchar(255),
  `dcontext` varchar(80),
  `audio_uniqueid` varchar(32),
  `time_uniqueid` varchar(32)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_clientes_agendados`
--

DROP TABLE IF EXISTS `_view_clientes_agendados`;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_agendados`*/;
/*!50001 CREATE TABLE `_view_clientes_agendados` (
  `apellido` varchar(50),
  `nombre` varchar(50),
  `ci` varchar(13),
  `nombre_campania` varchar(50),
  `fecha_agendamiento` datetime,
  `agente_agendado` varchar(30),
  `ultimo_calltype` varchar(46),
  `id_campania` int(11),
  `id_campania_cliente` int(11)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_clientes_base`
--

DROP TABLE IF EXISTS `_view_clientes_base`;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_base`*/;
/*!50001 CREATE TABLE `_view_clientes_base` (
  `base` varchar(50),
  `ci` varchar(13),
  `nombre` varchar(50),
  `apellido` varchar(50),
  `provincia` varchar(50),
  `ciudad` varchar(50),
  `nacimiento` date,
  `correo_personal` varchar(100),
  `correo_trabajo` varchar(100),
  `estado_civil` varchar(10)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_clientes_campania`
--

DROP TABLE IF EXISTS `_view_clientes_campania`;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_campania`*/;
/*!50001 CREATE TABLE `_view_clientes_campania` (
  `ci` varchar(13),
  `cliente` varchar(101),
  `id_campania` int(11),
  `id_campania_cliente` int(11),
  `fecha_agendamiento` datetime,
  `agente_agendado` varchar(30),
  `campania` varchar(50)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_clientes_campania_recargable`
--

DROP TABLE IF EXISTS `_view_clientes_campania_recargable`;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_campania_recargable`*/;
/*!50001 CREATE TABLE `_view_clientes_campania_recargable` (
  `id_cliente` int(11),
  `ci` varchar(13),
  `cliente` varchar(101),
  `id_campania` int(11),
  `id_campania_cliente` int(11),
  `fecha_agendamiento` datetime,
  `agente_agendado` varchar(30),
  `campania` varchar(50)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_gestion_general`
--

DROP TABLE IF EXISTS `_view_gestion_general`;
/*!50001 DROP VIEW IF EXISTS `_view_gestion_general`*/;
/*!50001 CREATE TABLE `_view_gestion_general` (
  `id_campania` int(11),
  `campania` varchar(50),
  `cedula` varchar(13),
  `cliente` varchar(101),
  `id_gestion_campania` int(11),
  `telefono` varchar(15),
  `calltype` varchar(46),
  `timestamp` varchar(32),
  `fecha` datetime,
  `agente` varchar(20)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_reporte_calltype`
--

DROP TABLE IF EXISTS `_view_reporte_calltype`;
/*!50001 DROP VIEW IF EXISTS `_view_reporte_calltype`*/;
/*!50001 CREATE TABLE `_view_reporte_calltype` (
  `id_campania` int(11),
  `id_campania_consolidada` int(11),
  `fecha` varbinary(19),
  `cliente` varchar(101),
  `id_campania_cliente` varbinary(11),
  `ci` varchar(13),
  `campania` varchar(50),
  `agente` varchar(20),
  `telefono` varchar(15),
  `contactabilidad` varchar(13),
  `mejor_calltype` varchar(30),
  `id_gestion_mejor_calltype` int(11),
  `observacion` text,
  `fecha_agendamiento` datetime,
  `agente_agendado` varchar(30),
  `origen` varchar(45),
  `peso` bigint(20),
  `id_campania_recargable_cliente` varbinary(11)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_reporte_calltype2`
--

DROP TABLE IF EXISTS `_view_reporte_calltype2`;
/*!50001 DROP VIEW IF EXISTS `_view_reporte_calltype2`*/;
/*!50001 CREATE TABLE `_view_reporte_calltype2` (
  `id_campania` int(11),
  `id_campania_consolidada` int(11),
  `fecha` datetime,
  `cliente` varchar(101),
  `id_campania_cliente` varbinary(11),
  `ci` varchar(13),
  `campania` varchar(50),
  `agente` varchar(20),
  `telefono` varchar(15),
  `contactabilidad` varchar(13),
  `mejor_calltype` varchar(30),
  `id_gestion_mejor_calltype` int(11),
  `observacion` text,
  `fecha_agendamiento` datetime,
  `agente_agendado` varchar(30),
  `origen` varchar(45),
  `peso` int(11),
  `id_campania_recargable_cliente` varbinary(11)
) ENGINE=MyISAM */;

--
-- Temporary table structure for view `_view_telefonos_inactivos`
--

DROP TABLE IF EXISTS `_view_telefonos_inactivos`;
/*!50001 DROP VIEW IF EXISTS `_view_telefonos_inactivos`*/;
/*!50001 CREATE TABLE `_view_telefonos_inactivos` (
  `id` int(11),
  `cliente` varchar(101),
  `ci` varchar(13),
  `descripcion` varchar(20),
  `telefono` varchar(12),
  `status` enum('A','E')
) ENGINE=MyISAM */;

--
-- Table structure for table `audit_actualizacion_clientes`
--

DROP TABLE IF EXISTS `audit_actualizacion_clientes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `audit_actualizacion_clientes` (
  `id` int(11) NOT NULL auto_increment,
  `ci` varchar(13) default NULL,
  `usuario` varchar(30) default NULL,
  `data` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2342 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `audit_gestion`
--

DROP TABLE IF EXISTS `audit_gestion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `audit_gestion` (
  `id` int(11) NOT NULL auto_increment,
  `id_gestion` int(11) default NULL,
  `usuario` varchar(45) default NULL,
  `data` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `base`
--

DROP TABLE IF EXISTS `base`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `base` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) default NULL,
  `fecha` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=508 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `base_cliente`
--

DROP TABLE IF EXISTS `base_cliente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `base_cliente` (
  `id_base` int(11) NOT NULL,
  `ci` varchar(13) default NULL,
  `prioridad` int(11) default '99',
  UNIQUE KEY `id_base` (`id_base`,`ci`),
  KEY `ci` (`ci`),
  CONSTRAINT `base_cliente_ibfk_1` FOREIGN KEY (`id_base`) REFERENCES `base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `break`
--

DROP TABLE IF EXISTS `break`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `break` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) default NULL,
  `status` varchar(1) NOT NULL default 'A',
  `tipo` enum('B','H') default 'B',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `break_agente`
--

DROP TABLE IF EXISTS `break_agente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `break_agente` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `id_agente` varchar(40) NOT NULL,
  `id_break` int(10) unsigned default NULL,
  `datetime_init` datetime NOT NULL,
  `datetime_end` datetime default NULL,
  `duration` time default NULL,
  `ext_parked` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_break` (`id_break`),
  CONSTRAINT `audit_ibfk_2` FOREIGN KEY (`id_break`) REFERENCES `break` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5352 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype`
--

DROP TABLE IF EXISTS `calltype`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype` (
  `id` int(11) NOT NULL auto_increment,
  `clase` enum('Contactado','No contactado','Agendado') default NULL,
  `descripcion` varchar(30) default NULL,
  `definicion` varchar(250) default NULL,
  `peso` int(11) default NULL,
  `status` enum('A','I') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=753 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_bck`
--

DROP TABLE IF EXISTS `calltype_bck`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_bck` (
  `id` int(11) NOT NULL default '0',
  `clase` enum('Contactado','No contactado','Agendado') default NULL,
  `descripcion` varchar(30) default NULL,
  `definicion` varchar(250) default NULL,
  `peso` int(11) default NULL,
  `status` enum('A','I') default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_bck_2`
--

DROP TABLE IF EXISTS `calltype_bck_2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_bck_2` (
  `id` int(11) NOT NULL default '0',
  `clase` enum('Contactado','No contactado','Agendado') default NULL,
  `descripcion` varchar(30) default NULL,
  `definicion` varchar(250) default NULL,
  `peso` int(11) default NULL,
  `status` enum('A','I') default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_campania`
--

DROP TABLE IF EXISTS `calltype_campania`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_campania` (
  `id_calltype` int(11) NOT NULL,
  `id_campania` int(11) NOT NULL,
  `peso` int(11) NOT NULL,
  `status` enum('A','I') default 'A',
  UNIQUE KEY `id_calltype` (`id_calltype`,`id_campania`),
  KEY `id_campania` (`id_campania`),
  CONSTRAINT `calltype_campania_ibfk_1` FOREIGN KEY (`id_campania`) REFERENCES `campania` (`id`),
  CONSTRAINT `calltype_campania_ibfk_2` FOREIGN KEY (`id_calltype`) REFERENCES `calltype` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_campania_bck`
--

DROP TABLE IF EXISTS `calltype_campania_bck`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_campania_bck` (
  `id_calltype` int(11) NOT NULL,
  `id_campania` int(11) NOT NULL,
  `peso` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_campania_bck_2`
--

DROP TABLE IF EXISTS `calltype_campania_bck_2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_campania_bck_2` (
  `id_calltype` int(11) NOT NULL,
  `id_campania` int(11) NOT NULL,
  `peso` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `calltype_tmp`
--

DROP TABLE IF EXISTS `calltype_tmp`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calltype_tmp` (
  `calltype` int(11) NOT NULL,
  `nuevo_calltype` bigint(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania`
--

DROP TABLE IF EXISTS `campania`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(50) character set latin1 NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_form` int(10) unsigned NOT NULL,
  `script` text character set latin1,
  `status` enum('A','I') character set latin1 default NULL,
  `tipo` enum('ORIGINAL','REGESTION','DERIVADA','RECARGABLE') default 'ORIGINAL',
  `campania_origen` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `campania_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_agente`
--

DROP TABLE IF EXISTS `campania_agente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_agente` (
  `id_campania` int(11) NOT NULL,
  `id_agente` varchar(40) default NULL,
  `status` enum('A','I') default NULL,
  UNIQUE KEY `id_campania` (`id_campania`,`id_agente`),
  CONSTRAINT `campania_agente_ibfk_1` FOREIGN KEY (`id_campania`) REFERENCES `campania` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_base`
--

DROP TABLE IF EXISTS `campania_base`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_base` (
  `id_campania` int(11) NOT NULL,
  `id_base` int(11) NOT NULL,
  `status` enum('A','I') default NULL,
  UNIQUE KEY `id_campania` (`id_campania`,`id_base`),
  KEY `id_base` (`id_base`),
  CONSTRAINT `campania_base_ibfk_1` FOREIGN KEY (`id_campania`) REFERENCES `campania` (`id`),
  CONSTRAINT `campania_base_ibfk_2` FOREIGN KEY (`id_base`) REFERENCES `base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_calltype`
--

DROP TABLE IF EXISTS `campania_calltype`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_calltype` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania` int(11) NOT NULL,
  `clase` enum('Contactado','No contactado','Agendado') default NULL,
  `descripcion` varchar(30) NOT NULL,
  `definicion` varchar(250) default NULL,
  `peso` int(11) NOT NULL,
  `status` enum('A','I') NOT NULL,
  `calltype_origen` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_campania` (`id_campania`),
  CONSTRAINT `campania_calltype_ibfk_1` FOREIGN KEY (`id_campania`) REFERENCES `campania` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_calltype_back`
--

DROP TABLE IF EXISTS `campania_calltype_back`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_calltype_back` (
  `id` int(11) NOT NULL default '0',
  `id_campania` int(11) NOT NULL,
  `clase` enum('Contactado','No contactado','Agendado') character set utf8 default NULL,
  `descripcion` varchar(30) character set utf8 NOT NULL,
  `definicion` varchar(250) character set utf8 default NULL,
  `peso` int(11) NOT NULL,
  `status` enum('A','I') character set utf8 NOT NULL,
  `calltype_origen` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_cliente`
--

DROP TABLE IF EXISTS `campania_cliente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_cliente` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania` int(11) NOT NULL,
  `id_campania_consolidada` int(11) default NULL,
  `ci` varchar(13) character set latin1 default NULL,
  `prioridad` int(11) default '99',
  `status` varchar(20) character set latin1 default NULL,
  `fecha_status` datetime default NULL,
  `agente_status` varchar(14) character set latin1 default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente_agendado` varchar(30) default NULL,
  `ultimo_calltype` int(11) default NULL,
  `id_gestion_mejor_calltype` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_campania` (`id_campania`,`ci`),
  KEY `campania_cliente_ci` (`ci`),
  KEY `campania_cliente_id_gestion_mejor_calltype` (`id_gestion_mejor_calltype`),
  KEY `campania_cliente_id_campania_consolidada` (`id_campania_consolidada`)
) ENGINE=InnoDB AUTO_INCREMENT=39793 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_cliente_backup`
--

DROP TABLE IF EXISTS `campania_cliente_backup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_cliente_backup` (
  `id` int(11) NOT NULL default '0',
  `id_campania` int(11) NOT NULL,
  `ci` varchar(13) default NULL,
  `prioridad` int(11) default '99',
  `status` varchar(20) default NULL,
  `fecha_status` datetime default NULL,
  `agente_status` varchar(14) default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente_agendado` varchar(30) character set utf8 default NULL,
  `ultimo_calltype` int(11) default NULL,
  `id_gestion_mejor_calltype` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_cliente_bck`
--

DROP TABLE IF EXISTS `campania_cliente_bck`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_cliente_bck` (
  `id` int(11) NOT NULL default '0',
  `id_campania` int(11) NOT NULL,
  `id_campania_consolidada` int(11) default NULL,
  `ci` varchar(13) default NULL,
  `prioridad` int(11) default '99',
  `status` varchar(20) default NULL,
  `fecha_status` datetime default NULL,
  `agente_status` varchar(14) default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente_agendado` varchar(30) character set utf8 default NULL,
  `ultimo_calltype` int(11) default NULL,
  `id_gestion_mejor_calltype` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_cliente_bck_2`
--

DROP TABLE IF EXISTS `campania_cliente_bck_2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_cliente_bck_2` (
  `id` int(11) NOT NULL default '0',
  `id_campania` int(11) NOT NULL,
  `id_campania_consolidada` int(11) default NULL,
  `ci` varchar(13) default NULL,
  `prioridad` int(11) default '99',
  `status` varchar(20) default NULL,
  `fecha_status` datetime default NULL,
  `agente_status` varchar(14) default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente_agendado` varchar(30) character set utf8 default NULL,
  `ultimo_calltype` int(11) default NULL,
  `id_gestion_mejor_calltype` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_recarga`
--

DROP TABLE IF EXISTS `campania_recarga`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_recarga` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania` int(11) default NULL,
  `id_base` int(11) default NULL,
  `fecha_inicio` date default NULL,
  `fecha_fin` date default NULL,
  `status` enum('A','E','I') default NULL,
  PRIMARY KEY  (`id`),
  KEY `campania_recarga_idx` (`id_campania`,`id_base`)
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `campania_recargable_cliente`
--

DROP TABLE IF EXISTS `campania_recargable_cliente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `campania_recargable_cliente` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania` int(11) NOT NULL,
  `id_base_cliente` int(11) default NULL,
  `id_cliente` int(11) default NULL,
  `prioridad` int(11) default '99',
  `status` varchar(20) default NULL,
  `fecha_status` datetime default NULL,
  `agente_status` varchar(14) default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente_agendado` varchar(30) default NULL,
  `ultimo_calltype` int(11) default NULL,
  `id_gestion_mejor_calltype` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ultimo_calltype` (`ultimo_calltype`),
  KEY `campania_cliente_id` (`id`),
  KEY `campania_cliente_id_gestion_mejor_calltype` (`id_gestion_mejor_calltype`),
  KEY `campania_recargable_cliente_ibfk_1` (`id_cliente`),
  CONSTRAINT `campania_recargable_cliente_ibfk_2` FOREIGN KEY (`ultimo_calltype`) REFERENCES `calltype` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44432 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente` (
  `id` int(11) NOT NULL auto_increment,
  `ci` varchar(13) character set latin1 NOT NULL,
  `nombre` varchar(50) character set latin1 NOT NULL,
  `apellido` varchar(50) character set latin1 NOT NULL,
  `provincia` varchar(50) character set latin1 default NULL,
  `ciudad` varchar(50) character set latin1 default NULL,
  `nacimiento` date default NULL,
  `correo_personal` varchar(100) default NULL,
  `correo_trabajo` varchar(100) default NULL,
  `estado_civil` varchar(10) character set latin1 default NULL,
  `id_base` int(11) NOT NULL,
  `origen` varchar(10) default 'base',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ci` (`ci`),
  KEY `cliente_ci` (`ci`)
) ENGINE=InnoDB AUTO_INCREMENT=35140 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente_adicional`
--

DROP TABLE IF EXISTS `cliente_adicional`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente_adicional` (
  `id` int(11) NOT NULL auto_increment,
  `adicional` varchar(80) default NULL,
  `ci` varchar(13) character set latin1 NOT NULL,
  `descripcion` varchar(20) character set latin1 default NULL,
  `id_base` int(11) NOT NULL,
  `status` enum('A','E') NOT NULL default 'A',
  PRIMARY KEY  (`id`),
  KEY `id_base` (`id_base`),
  KEY `cliente_adicional_ci` (`ci`),
  CONSTRAINT `base_adicional_ibfk_1` FOREIGN KEY (`id_base`) REFERENCES `base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=308133 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente_direccion`
--

DROP TABLE IF EXISTS `cliente_direccion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente_direccion` (
  `id` int(11) NOT NULL auto_increment,
  `direccion` varchar(80) default NULL,
  `ci` varchar(13) NOT NULL,
  `descripcion` varchar(20) default NULL,
  `id_base` int(11) NOT NULL,
  `status` enum('A','E') NOT NULL default 'A',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `direccion_ci` (`direccion`,`ci`),
  KEY `id_base` (`id_base`),
  CONSTRAINT `base_direccion_ibfk_1` FOREIGN KEY (`id_base`) REFERENCES `base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42005 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente_gestion`
--

DROP TABLE IF EXISTS `cliente_gestion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente_gestion` (
  `id` int(11) NOT NULL auto_increment,
  `ci` varchar(13) NOT NULL,
  `nombre` varchar(50) default NULL,
  `apellido` varchar(50) default NULL,
  `provincia` varchar(50) default NULL,
  `ciudad` varchar(50) default NULL,
  `nacimiento` date default NULL,
  `correo_personal` varchar(100) default NULL,
  `correo_trabajo` varchar(100) default NULL,
  `estado_civil` varchar(10) default NULL,
  `id_base` int(11) default NULL,
  `origen` varchar(45) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ci` (`ci`),
  KEY `id_base` (`id_base`)
) ENGINE=InnoDB AUTO_INCREMENT=39950 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente_gestion_adicionales`
--

DROP TABLE IF EXISTS `cliente_gestion_adicionales`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente_gestion_adicionales` (
  `id` int(11) NOT NULL auto_increment,
  `id_cliente` int(11) default NULL,
  `id_base` int(11) default NULL,
  `tipo` varchar(20) default NULL,
  `descripcion` varchar(80) default NULL,
  `adicional` varchar(80) default NULL,
  `status` enum('A','E') default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=575898 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cliente_telefono`
--

DROP TABLE IF EXISTS `cliente_telefono`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cliente_telefono` (
  `id` int(11) NOT NULL auto_increment,
  `telefono` varchar(12) NOT NULL,
  `ci` varchar(13) NOT NULL,
  `descripcion` varchar(20) default NULL,
  `id_base` int(11) NOT NULL,
  `status` enum('A','E') NOT NULL default 'A',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `telefono_ci` (`telefono`,`ci`),
  KEY `id_base` (`id_base`),
  CONSTRAINT `base_telefono_ibfk_1` FOREIGN KEY (`id_base`) REFERENCES `base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91635 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `form` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(40) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `estatus` varchar(1) NOT NULL default 'A',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `form_data_recolected`
--

DROP TABLE IF EXISTS `form_data_recolected`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `form_data_recolected` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_calls` int(10) unsigned NOT NULL,
  `id_form_field` int(10) unsigned NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_form_field` (`id_form_field`),
  KEY `id_calls` (`id_calls`),
  CONSTRAINT `form_data_recolected_ibfk_1` FOREIGN KEY (`id_form_field`) REFERENCES `form_field` (`id`),
  CONSTRAINT `form_data_recolected_ibfk_2` FOREIGN KEY (`id_calls`) REFERENCES `calls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `form_field`
--

DROP TABLE IF EXISTS `form_field`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `form_field` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_form` int(10) unsigned NOT NULL,
  `etiqueta` varchar(150) default NULL,
  `value` text,
  `tipo` varchar(25) NOT NULL,
  `orden` int(10) unsigned NOT NULL,
  `status` enum('A','I') default 'A',
  PRIMARY KEY  (`id`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `form_field_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `form_field_backup`
--

DROP TABLE IF EXISTS `form_field_backup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `form_field_backup` (
  `id` int(10) unsigned NOT NULL default '0',
  `id_form` int(10) unsigned NOT NULL,
  `etiqueta` varchar(60) character set utf8 default NULL,
  `value` varchar(250) character set utf8 NOT NULL,
  `tipo` varchar(25) character set utf8 NOT NULL,
  `orden` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania`
--

DROP TABLE IF EXISTS `gestion_campania`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania_cliente` int(11) NOT NULL,
  `calltype` int(11) NOT NULL,
  `timestamp` varchar(32) default NULL,
  `telefono` varchar(15) default NULL,
  `fecha` datetime default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente` varchar(20) default NULL,
  `observacion` text,
  `id_campania_recargable_cliente` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `gestion_campania_ibfk_1` (`id_campania_cliente`),
  KEY `gestion_campania_calltype` (`calltype`)
) ENGINE=InnoDB AUTO_INCREMENT=128848 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania_bck`
--

DROP TABLE IF EXISTS `gestion_campania_bck`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania_bck` (
  `id` int(11) NOT NULL default '0',
  `id_campania_cliente` int(11) NOT NULL,
  `calltype` int(11) NOT NULL,
  `timestamp` varchar(32) character set utf8 default NULL,
  `telefono` varchar(15) character set utf8 default NULL,
  `fecha` datetime default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente` varchar(20) character set utf8 default NULL,
  `observacion` text character set utf8,
  `id_campania_recargable_cliente` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania_bck_2`
--

DROP TABLE IF EXISTS `gestion_campania_bck_2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania_bck_2` (
  `id` int(11) NOT NULL default '0',
  `id_campania_cliente` int(11) NOT NULL,
  `calltype` int(11) NOT NULL,
  `timestamp` varchar(32) character set utf8 default NULL,
  `telefono` varchar(15) character set utf8 default NULL,
  `fecha` datetime default NULL,
  `fecha_agendamiento` datetime default NULL,
  `agente` varchar(20) character set utf8 default NULL,
  `observacion` text character set utf8,
  `id_campania_recargable_cliente` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania_detalle`
--

DROP TABLE IF EXISTS `gestion_campania_detalle`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania_detalle` (
  `id` int(11) NOT NULL auto_increment,
  `id_gestion_campania` int(11) NOT NULL,
  `id_form_field` int(10) unsigned NOT NULL,
  `valor` varchar(500) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_gestion_campania` (`id_gestion_campania`),
  KEY `id_form_field` (`id_form_field`),
  CONSTRAINT `gestion_campania_detalle_ibfk_1` FOREIGN KEY (`id_gestion_campania`) REFERENCES `gestion_campania` (`id`),
  CONSTRAINT `gestion_campania_detalle_ibfk_2` FOREIGN KEY (`id_form_field`) REFERENCES `form_field` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1849482 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania_detalle_bck`
--

DROP TABLE IF EXISTS `gestion_campania_detalle_bck`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania_detalle_bck` (
  `id` int(11) NOT NULL default '0',
  `id_gestion_campania` int(11) NOT NULL,
  `id_form_field` int(10) unsigned NOT NULL,
  `valor` varchar(200) character set utf8 default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `gestion_campania_detalle_bck_2`
--

DROP TABLE IF EXISTS `gestion_campania_detalle_bck_2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gestion_campania_detalle_bck_2` (
  `id` int(11) NOT NULL default '0',
  `id_gestion_campania` int(11) NOT NULL,
  `id_form_field` int(10) unsigned NOT NULL,
  `valor` varchar(200) character set utf8 default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `readme`
--

DROP TABLE IF EXISTS `readme`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `readme` (
  `readme` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `reportes_offline`
--

DROP TABLE IF EXISTS `reportes_offline`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `reportes_offline` (
  `id` int(11) NOT NULL auto_increment,
  `id_campania` int(11) default NULL,
  `tiempo_unix` varchar(45) default NULL,
  `ruta` varchar(200) default NULL,
  `filtro` varchar(200) default NULL,
  `status` varchar(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `_view_campania_calltype`
--

/*!50001 DROP TABLE `_view_campania_calltype`*/;
/*!50001 DROP VIEW IF EXISTS `_view_campania_calltype`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_campania_calltype` AS select `a`.`id` AS `id`,`c`.`id` AS `id_campania`,`c`.`nombre` AS `campania`,`a`.`clase` AS `clase`,`a`.`descripcion` AS `call_type`,`b`.`peso` AS `peso`,`b`.`status` AS `status` from ((`calltype` `a` join `calltype_campania` `b`) join `campania` `c`) where ((`a`.`id` = `b`.`id_calltype`) and (`b`.`id_campania` = `c`.`id`)) */;

--
-- Final view structure for view `_view_cdr_hispana_callcenter`
--

/*!50001 DROP TABLE `_view_cdr_hispana_callcenter`*/;
/*!50001 DROP VIEW IF EXISTS `_view_cdr_hispana_callcenter`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `hispana`.`_view_cdr_hispana_callcenter` AS select `a`.`duration` AS `duration`,`a`.`calldate` AS `calldate`,`b`.`src` AS `src`,`b`.`dst` AS `dst`,`b`.`userfield` AS `userfield`,`a`.`dcontext` AS `dcontext`,`b`.`uniqueid` AS `audio_uniqueid`,`a`.`uniqueid` AS `time_uniqueid` from (`asteriskcdrdb`.`cdr` `a` join `asteriskcdrdb`.`cdr` `b`) where ((`a`.`dcontext` = _latin1'hispana-callcenter') and (substr(`a`.`dstchannel`,1,(locate(_latin1';',`a`.`dstchannel`) - 1)) = substr(`b`.`channel`,1,(locate(_latin1';',`b`.`channel`) - 1)))) */;

--
-- Final view structure for view `_view_clientes_agendados`
--

/*!50001 DROP TABLE `_view_clientes_agendados`*/;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_agendados`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_clientes_agendados` AS select `c`.`apellido` AS `apellido`,`c`.`nombre` AS `nombre`,`a`.`ci` AS `ci`,`b`.`nombre` AS `nombre_campania`,`a`.`fecha_agendamiento` AS `fecha_agendamiento`,`a`.`agente_agendado` AS `agente_agendado`,concat(`e`.`clase`,_latin1' - ',`e`.`descripcion`) AS `ultimo_calltype`,`b`.`id` AS `id_campania`,`a`.`id` AS `id_campania_cliente` from ((((`campania_cliente` `a` join `campania` `b`) join `cliente` `c`) join `calltype_campania` `d`) join `calltype` `e`) where ((`a`.`id_campania` = `b`.`id`) and (`a`.`ultimo_calltype` = `d`.`id_calltype`) and (`a`.`ci` = `c`.`ci`) and (`a`.`fecha_agendamiento` is not null) and (`a`.`id_campania` = `d`.`id_campania`) and (`d`.`id_calltype` = `e`.`id`)) order by `c`.`apellido`,`b`.`nombre` */;

--
-- Final view structure for view `_view_clientes_base`
--

/*!50001 DROP TABLE `_view_clientes_base`*/;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_base`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_clientes_base` AS select `b`.`nombre` AS `base`,`c`.`ci` AS `ci`,`c`.`nombre` AS `nombre`,`c`.`apellido` AS `apellido`,`c`.`provincia` AS `provincia`,`c`.`ciudad` AS `ciudad`,`c`.`nacimiento` AS `nacimiento`,`c`.`correo_personal` AS `correo_personal`,`c`.`correo_trabajo` AS `correo_trabajo`,`c`.`estado_civil` AS `estado_civil` from ((`base` `b` join `base_cliente` `bc`) join `cliente` `c`) where ((`b`.`id` = `bc`.`id_base`) and (`bc`.`ci` = `c`.`ci`)) */;

--
-- Final view structure for view `_view_clientes_campania`
--

/*!50001 DROP TABLE `_view_clientes_campania`*/;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_campania`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_clientes_campania` AS select `a`.`ci` AS `ci`,concat(`c`.`nombre`,_latin1' ',`c`.`apellido`) AS `cliente`,`a`.`id_campania` AS `id_campania`,`a`.`id` AS `id_campania_cliente`,`a`.`fecha_agendamiento` AS `fecha_agendamiento`,`a`.`agente_agendado` AS `agente_agendado`,`b`.`nombre` AS `campania` from ((`campania_cliente` `a` join `campania` `b`) join `cliente` `c`) where ((`a`.`id_campania` = `b`.`id`) and (`a`.`ci` = `c`.`ci`)) order by concat(`c`.`nombre`,_latin1' ',`c`.`apellido`) */;

--
-- Final view structure for view `_view_clientes_campania_recargable`
--

/*!50001 DROP TABLE `_view_clientes_campania_recargable`*/;
/*!50001 DROP VIEW IF EXISTS `_view_clientes_campania_recargable`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_clientes_campania_recargable` AS select `c`.`id` AS `id_cliente`,`c`.`ci` AS `ci`,concat(`c`.`nombre`,_utf8' ',`c`.`apellido`) AS `cliente`,`a`.`id_campania` AS `id_campania`,`a`.`id` AS `id_campania_cliente`,`a`.`fecha_agendamiento` AS `fecha_agendamiento`,`a`.`agente_agendado` AS `agente_agendado`,`b`.`nombre` AS `campania` from ((`campania_recargable_cliente` `a` join `campania` `b`) join `cliente_gestion` `c`) where ((`a`.`id_campania` = `b`.`id`) and (`a`.`id_cliente` = `c`.`id`)) order by concat(`c`.`nombre`,_utf8' ',`c`.`apellido`) */;

--
-- Final view structure for view `_view_gestion_general`
--

/*!50001 DROP TABLE `_view_gestion_general`*/;
/*!50001 DROP VIEW IF EXISTS `_view_gestion_general`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_gestion_general` AS select `b`.`id` AS `id_campania`,`b`.`nombre` AS `campania`,`c`.`ci` AS `cedula`,concat(`c`.`nombre`,_latin1' ',`c`.`apellido`) AS `cliente`,`d`.`id` AS `id_gestion_campania`,`d`.`telefono` AS `telefono`,concat(`f`.`clase`,_latin1' - ',`f`.`descripcion`) AS `calltype`,`d`.`timestamp` AS `timestamp`,`d`.`fecha` AS `fecha`,`d`.`agente` AS `agente` from (((((`campania_cliente` `a` join `campania` `b`) join `cliente` `c`) join `gestion_campania` `d`) join `calltype_campania` `e`) join `calltype` `f`) where ((`a`.`id_campania` = `b`.`id`) and (`a`.`ci` = `c`.`ci`) and (`d`.`id_campania_cliente` = `a`.`id`) and (`d`.`calltype` = `e`.`id_calltype`) and (`e`.`id_campania` = `a`.`id_campania`) and (`f`.`id` = `e`.`id_calltype`)) */;

--
-- Final view structure for view `_view_reporte_calltype`
--

/*!50001 DROP TABLE `_view_reporte_calltype`*/;
/*!50001 DROP VIEW IF EXISTS `_view_reporte_calltype`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_reporte_calltype` AS select distinct `e`.`id` AS `id_campania`,`b`.`id_campania_consolidada` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_latin1' ',`a`.`apellido`) AS `cliente`,`b`.`id` AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,_utf8'' AS `id_campania_recargable_cliente` from (((((`cliente` `a` join `campania_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`ci` = `b`.`ci`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania_consolidada` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` <> _utf8'RECARGABLE')) union select distinct `e`.`id` AS `id_campania`,`b`.`id_campania_consolidada` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_latin1' ',`a`.`apellido`) AS `cliente`,`b`.`id` AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,_utf8'' AS `id_campania_recargable_cliente` from (((((`cliente` `a` join `campania_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`ci` = `b`.`ci`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` <> _utf8'RECARGABLE')) union select distinct `e`.`id` AS `id_campania`,`b`.`id_campania_consolidada` AS `id_campania_consolidada`,_utf8'' AS `fecha`,concat(`a`.`nombre`,_latin1' ',`a`.`apellido`) AS `cliente`,`b`.`id` AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`b`.`agente_status` AS `agente`,_utf8'' AS `telefono`,_utf8'' AS `contactabilidad`,_utf8'' AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,_utf8'' AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,0 AS `peso`,_utf8'' AS `id_campania_recargable_cliente` from (((`cliente` `a` join `campania_cliente` `b`) join `campania` `e`) join `campania_agente` `f`) where ((`a`.`ci` = `b`.`ci`) and isnull(`b`.`id_gestion_mejor_calltype`) and (`b`.`id_campania` = `e`.`id`) and (`e`.`tipo` <> _utf8'RECARGABLE') and (`b`.`id_campania` = `f`.`id_campania`) and (`b`.`agente_agendado` = convert(`f`.`id_agente` using utf8)) and (`a`.`origen` <> _utf8'base')) union select distinct `e`.`id` AS `id_campania`,`b`.`id_campania` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_utf8' ',`a`.`apellido`) AS `cliente`,_utf8'' AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,`b`.`id` AS `id_campania_recargable_cliente` from (((((`cliente_gestion` `a` join `campania_recargable_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`id` = `b`.`id_cliente`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` = _utf8'RECARGABLE')) order by `fecha` desc */;

--
-- Final view structure for view `_view_reporte_calltype2`
--

/*!50001 DROP TABLE `_view_reporte_calltype2`*/;
/*!50001 DROP VIEW IF EXISTS `_view_reporte_calltype2`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_reporte_calltype2` AS select distinct `e`.`id` AS `id_campania`,`b`.`id_campania_consolidada` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_latin1' ',`a`.`apellido`) AS `cliente`,`b`.`id` AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,_utf8'' AS `id_campania_recargable_cliente` from (((((`cliente` `a` join `campania_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`ci` = `b`.`ci`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania_consolidada` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` <> _utf8'RECARGABLE')) union select distinct `e`.`id` AS `id_campania`,`b`.`id_campania_consolidada` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_latin1' ',`a`.`apellido`) AS `cliente`,`b`.`id` AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,_utf8'' AS `id_campania_recargable_cliente` from (((((`cliente` `a` join `campania_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`ci` = `b`.`ci`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` <> _utf8'RECARGABLE')) union select distinct `e`.`id` AS `id_campania`,`b`.`id_campania` AS `id_campania_consolidada`,`c`.`fecha` AS `fecha`,concat(`a`.`nombre`,_utf8' ',`a`.`apellido`) AS `cliente`,_utf8'' AS `id_campania_cliente`,`a`.`ci` AS `ci`,`e`.`nombre` AS `campania`,`c`.`agente` AS `agente`,`c`.`telefono` AS `telefono`,`f`.`clase` AS `contactabilidad`,`f`.`descripcion` AS `mejor_calltype`,`b`.`id_gestion_mejor_calltype` AS `id_gestion_mejor_calltype`,`c`.`observacion` AS `observacion`,`b`.`fecha_agendamiento` AS `fecha_agendamiento`,`b`.`agente_agendado` AS `agente_agendado`,`a`.`origen` AS `origen`,`f`.`peso` AS `peso`,`b`.`id` AS `id_campania_recargable_cliente` from (((((`cliente_gestion` `a` join `campania_recargable_cliente` `b`) join `gestion_campania` `c`) join `calltype_campania` `d`) join `calltype` `f`) join `campania` `e`) where ((`a`.`id` = `b`.`id_cliente`) and (`b`.`id_gestion_mejor_calltype` = `c`.`id`) and (`c`.`calltype` = `d`.`id_calltype`) and (`b`.`id_campania` = `e`.`id`) and (`d`.`id_calltype` = `f`.`id`) and (`e`.`tipo` = _utf8'RECARGABLE')) order by `fecha` desc */;

--
-- Final view structure for view `_view_telefonos_inactivos`
--

/*!50001 DROP TABLE `_view_telefonos_inactivos`*/;
/*!50001 DROP VIEW IF EXISTS `_view_telefonos_inactivos`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `_view_telefonos_inactivos` AS select `a`.`id` AS `id`,concat(`b`.`nombre`,_latin1' ',`b`.`apellido`) AS `cliente`,`b`.`ci` AS `ci`,`a`.`descripcion` AS `descripcion`,`a`.`telefono` AS `telefono`,`a`.`status` AS `status` from (`cliente_telefono` `a` join `cliente` `b`) where ((`a`.`status` = _utf8'E') and (`a`.`ci` = convert(`b`.`ci` using utf8))) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-03 15:12:25


CREATE INDEX gestion_campania_id_campania_recargable ON gestion_campania (id_campania_recargable_cliente);
CREATE INDEX cliente_gestion_adicionales_tipo ON cliente_gestion_adicionales (tipo);
CREATE INDEX cliente_telefono_ci ON cliente_telefono (ci);
CREATE INDEX gestion_campania_agente ON gestion_campania (agente); 

CREATE or replace
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `_view_gestion_detallada` AS
    select 
        `b`.`id` AS `id_campania`,
        `b`.`nombre` AS `campania`,
        `c`.`ci` AS `cedula`,
        concat(`c`.`nombre`, _latin1' ', `c`.`apellido`) AS `cliente`,
        `d`.`id` AS `id_gestion_campania`,
        `d`.`telefono` AS `telefono`,
        concat(`f`.`clase`,
                _latin1' - ',
                `f`.`descripcion`) AS `mejor_calltype`,
        `d`.`timestamp` AS `timestamp`,
        `d`.`fecha` AS `fecha`,
        `d`.`agente` AS `agente`,
		`a`.`id_gestion_mejor_calltype`
    from
        (((((`campania_cliente` `a`
        join `campania` `b`)
        join `cliente` `c`)
        join `gestion_campania` `d`)
        join `calltype_campania` `e`)
        join `calltype` `f`)
    where
        ((`a`.`id_campania` = `b`.`id`)
            and (`a`.`ci` = `c`.`ci`)
            and (`d`.`id` = `a`.`id_gestion_mejor_calltype`)
            and (`d`.`calltype` = `e`.`id_calltype`)
            and (`e`.`id_campania` = `a`.`id_campania`)
            and (`f`.`id` = `e`.`id_calltype`))
group by cedula,id_campania;
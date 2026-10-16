-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.7.31 - MySQL Community Server (GPL)
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para eleicoes_presidenciais
CREATE DATABASE IF NOT EXISTS `eleicoes_presidenciais` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `eleicoes_presidenciais`;

-- Copiando estrutura para tabela eleicoes_presidenciais.eleitor
CREATE TABLE IF NOT EXISTS `eleitor` (
  `idEleitor` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `data` date NOT NULL,
  `zona` varchar(45) NOT NULL,
  `municipio` varchar(45) NOT NULL,
  `cpf` varchar(45) NOT NULL,
  `secao` varchar(45) NOT NULL,
  `filiacao` varchar(45) NOT NULL,
  `votou` int(1) NOT NULL,
  PRIMARY KEY (`idEleitor`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela eleicoes_presidenciais.legenda
CREATE TABLE IF NOT EXISTS `legenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partido_coligacao` varchar(45) NOT NULL,
  `numero_voto` int(2) NOT NULL,
  `nome_candidato` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partido_UNIQUE` (`partido_coligacao`),
  UNIQUE KEY `numero_UNIQUE` (`numero_voto`),
  UNIQUE KEY `nome_UNIQUE` (`nome_candidato`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela eleicoes_presidenciais.votacao
CREATE TABLE IF NOT EXISTS `votacao` (
  `idVotacao` int(11) NOT NULL AUTO_INCREMENT,
  `eleitor_idEleitor` int(11) NOT NULL,
  `legenda_id` int(11) DEFAULT NULL,
  `votos_validos` int(11) NOT NULL,
  `votos_brancos` int(11) NOT NULL,
  `votos_nulos` int(11) NOT NULL,
  PRIMARY KEY (`idVotacao`),
  UNIQUE KEY `eleitor_idEleitor_UNIQUE` (`eleitor_idEleitor`),
  KEY `fk_votacao_eleitor1_idx` (`eleitor_idEleitor`),
  KEY `fk_votacao_legenda1_idx` (`legenda_id`),
  CONSTRAINT `fk_votacao_eleitor1` FOREIGN KEY (`eleitor_idEleitor`) REFERENCES `eleitor` (`idEleitor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_votacao_legenda1` FOREIGN KEY (`legenda_id`) REFERENCES `legenda` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

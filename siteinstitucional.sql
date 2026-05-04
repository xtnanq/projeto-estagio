-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 04-Maio-2026 às 13:48
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `siteinstitucional`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresas`
--

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `nome_empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `morada` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_postal` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_empresa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome_contato` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefone_contato` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_contato` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `empresas`
--

INSERT INTO `empresas` (`id`, `usuario_id`, `nome_empresa`, `morada`, `codigo_postal`, `telefone`, `email_empresa`, `nome_contato`, `telefone_contato`, `email_contato`) VALUES
(35, 36, 'empresa 2', 'morada 2', 'postal2', 'AQSDD', 'ad@adad', 'adarwerwt ghg', 'dadfrwewe', 'adad@ASadsdfdgddsf'),
(36, 37, 'IS4 - Informática e Serviços, Lda', 'Rua Acácio lino, 354', '4600-045 Amarante', '255431324', 'geral@is4.pt', 'Francisco Silva', '932537560', 'is4.francisco@gmail.com'),
(39, 40, 'teste2', 'porto', '23348394920', '23282932032', 'goncalo.dinis.cs@gmail.com', 'teste2', '23282932032', 'goncalo.dinis.cs@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `portfolio`
--

DROP TABLE IF EXISTS `portfolio`;
CREATE TABLE IF NOT EXISTS `portfolio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int DEFAULT NULL,
  `imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao_imagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `portfolio`
--

INSERT INTO `portfolio` (`id`, `empresa_id`, `imagem`, `descricao_imagem`) VALUES
(30, 36, 'imagens/IS4 - Informática e Serviços, Lda/Imagem_Assist_tecnica.jpg', ''),
(34, 36, 'imagens/IS4 - Informática e Serviços, Lda/279_800x800.png', ''),
(36, 35, 'imagens/empresa 2/281_800x800.png', 'gege'),
(32, 35, 'imagens/empresa 2/281_800x800.png', 'awedar'),
(33, 36, 'imagens/IS4 - Informática e Serviços, Lda/275_800x800.png', 'pos'),
(26, 35, 'imagens/empresa 2/azeitonas.jpg', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `servicos`
--

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `nome_servico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `titulo_servico` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao_servico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `servicos`
--

INSERT INTO `servicos` (`id`, `empresa_id`, `nome_servico`, `titulo_servico`, `descricao_servico`) VALUES
(65, 35, 'sfsf', 'sdgfsdg', 'dgdg'),
(59, 36, 'sadsf', 'asfdsff', 'sfsfdsf'),
(62, 35, 'sdgf', 'dgdg', 'dgdg'),
(61, 35, 'Sobre Nós', 'Sobre Nós', 'dgdhdfhf\r\ndhdh\r\ndg\r\ndg'),
(64, 36, 'qwqe', 'qweqwe', 'qweq'),
(66, 39, 'teste1', 'Serviço de teste', 'teste');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('admin','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'cliente',
  `data_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `data_registro`) VALUES
(1, 'Estagiario', 'is4.estagio@gmail.com', '$2y$10$tmdSpIgdD8enIfe7T326Ae/F.4XLe7CfSrnxgTzTaH4rsSc7ujkee', 'admin', '2025-05-02 15:12:36'),
(36, 'francisco', 'empresa@gmail.com', '$2y$10$c4A6ef23XVvSmS5camvXq./Xs5P/7yMviJTL/Ix5H3Ss6R4T/i2WS', 'cliente', '2025-06-06 14:39:56'),
(37, 'Francisco Silva', 'geral@is4.pt', '$2y$10$V8tjG0nsh.BSzxHuPDzTNOWlsyuQvUP8cQalgiQhvtQWIGnqS4rqG', 'cliente', '2025-06-06 14:47:18'),
(40, 'teste2', 'goncalo.dinis.cs@gmail.com', '$2y$10$GN6POOnTcL3G3xolZQAMZ.tFRUxIDIkRQ0wb1u2p9N3qIpESvY3mm', 'cliente', '2026-04-15 09:06:09');

-- --------------------------------------------------------

--
-- Estrutura da tabela `website_config`
--

DROP TABLE IF EXISTS `website_config`;
CREATE TABLE IF NOT EXISTS `website_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `logotipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `capa_empresa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao_empresa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `link_facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_instagram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_x` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

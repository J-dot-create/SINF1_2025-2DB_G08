-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20-Maio-2026 às 17:59
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sinf1_queima`
--
DROP DATABASE IF EXISTS `sinf1_queima`;
CREATE DATABASE IF NOT EXISTS `sinf1_queima` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sinf1_queima`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `artist`
--

DROP TABLE IF EXISTS `artist`;
CREATE TABLE `artist` (
  `id_artist` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `musical_genre` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `biography` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `artist`
--

INSERT INTO `artist` (`id_artist`, `name`, `musical_genre`, `country`, `biography`) VALUES
(1, 'Dillaz', 'Hip-Hop', 'Portugal', 'Rapper e produtor português, associado ao hip-hop nacional e presente na noite de 2 de maio da Queima das Fitas do Porto 2026.'),
(2, 'Bárbara Tinoco', 'Pop', 'Portugal', 'Cantora e compositora portuguesa.'),
(3, 'Papillon', 'Hip-Hop', 'Portugal', 'Artista português conhecido pela energia em palco e por uma sonoridade ligada ao rap, soul e música urbana.'),
(4, 'ÁTOA', 'Pop', 'Portugal', 'Banda portuguesa de Évora, conhecida por temas como Distância, Falar a Dois, Hoje e Ritinha.'),
(5, 'Slow J', 'Hip-Hop', 'Portugal', 'João Coelho, conhecido como Slow J, é cantor, produtor e rapper português com uma sonoridade singular no hip-hop nacional.'),
(6, 'Deejay Telio', 'Afrobeat / Hip-Hop', 'Angola', 'Músico, produtor e compositor angolano, conhecido por temas de música urbana e afrobeat.'),
(7, 'Wet Bed Gang', 'Hip-Hop / Rap', 'Portugal', 'Coletivo português de rap formado por Kroa, Gson, Zizzy e Zara G, com forte presença na música urbana portuguesa.'),
(8, 'Quim Barreiros', 'Música popular portuguesa', 'Portugal', 'Cantor e compositor português, figura habitual das festas académicas e conhecido pela música popular portuguesa.'),
(9, 'Pimbamix', 'Música popular portuguesa', 'Portugal', 'Projeto dos Insert Coin dedicado à música popular portuguesa.'),
(10, 'Yasmine', 'Kizomba / Zouk', 'Portugal', 'Cantora portuguesa ligada ao kizomba e ao zouk, conhecida por temas como Apaixona, Esquece o Mundo e Perfume.'),
(11, 'Morad', 'Rap / Drill', 'Espanha', 'Rapper e cantor marroquino-espanhol, associado ao drill e à música urbana.'),
(12, 'Bispo', 'Hip-Hop', 'Portugal', 'Pedro Bispo, rapper português natural de Sintra, reconhecido pela escrita ligada à realidade social.'),
(13, 'Calum Scott', 'Pop', 'Reino Unido', 'Cantor e compositor britânico conhecido internacionalmente por Dancing On My Own, You Are The Reason e Biblical.'),
(14, 'Veigh', 'Trap', 'Brasil', 'Artista brasileiro de trap, cofundador da Supernova e um dos nomes de destaque da música urbana brasileira.'),
(15, 'Chico da Tina', 'Trap / Música popular', 'Portugal', 'Artista português de Viana do Castelo que mistura trap, concertina e referências populares minhotas.'),
(16, 'Richie Campbell', 'Reggae / Dancehall / R&B', 'Portugal', 'Artista português conhecido por cruzar reggae, dancehall e R&B, com uma carreira nacional e internacional.'),
(17, 'Xutos & Pontapés', 'Rock', 'Portugal', 'Banda portuguesa de rock formada em 1978, uma das maiores referências da música portuguesa.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(100) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `id_tent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `event`
--

INSERT INTO `event` (`id_event`, `name`, `description`, `event_date`, `location`, `event_type`, `id_tent`) VALUES
(1, 'Noite da Queima: Quim Barreiros e Pimbamix', 'Noite tradicional de música popular da Queima das Fitas do Porto 2026, com Quim Barreiros e Pimbamix.', '2026-05-05 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(2, 'Monumental Serenata', 'A Monumental Serenata marca o início dos festejos da semana da Queima das Fitas do Porto, com grupos de fado da Academia.', '2026-05-03 00:01:00', 'Avenida dos Aliados', 'Cerimónia académica', NULL),
(3, 'Noite da Queima: Dillaz e Papillon', 'Noite de hip-hop nacional com Dillaz e Papillon.', '2026-05-02 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(4, 'Noite da Queima: ÁTOA e Slow J', 'Noite com ÁTOA e Slow J no Queimódromo do Porto.', '2026-05-03 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(5, 'Noite da Queima: Wet Bed Gang e Deejay Telio', 'Noite de música urbana com Wet Bed Gang e Deejay Telio.', '2026-05-04 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(6, 'Noite da Queima: Yasmine e Morad', 'Noite com Yasmine e Morad na Queima das Fitas do Porto 2026.', '2026-05-06 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(7, 'Noite da Queima: Bispo e Calum Scott', 'Noite com Bispo e Calum Scott no Queimódromo do Porto.', '2026-05-07 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(8, 'Noite da Queima: Chico da Tina e Veigh', 'Noite com Chico da Tina e Veigh na Queima das Fitas do Porto 2026.', '2026-05-08 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(9, 'Noite da Queima: Richie Campbell e Xutos & Pontapés', 'Noite de encerramento com Richie Campbell e Xutos & Pontapés.', '2026-05-09 20:00:00', 'Queimódromo do Porto', 'Concerto', NULL),
(10, 'Missa Bênção das Pastas', 'Celebração religiosa tradicionalmente dedicada aos finalistas.', '2026-05-03 00:00:00', 'Avenida dos Aliados', 'Cerimónia académica', NULL),
(11, 'ECAP XXVII - Encontro de Coros da Academia do Porto', 'Encontro de coros académicos integrado nas atividades da Queima das Fitas do Porto.', '2026-05-03 00:00:00', 'Teatro Sá da Bandeira', 'Atividade académica', NULL),
(12, 'Dia da Beneficência', 'Atividade solidária de angariação de fundos nas ruas da cidade.', '2026-05-04 00:00:00', 'Porto', 'Beneficência', NULL),
(13, 'Concerto Promenade', 'Concerto de música erudita da Queima das Fitas do Porto.', '2026-05-04 00:00:00', 'Casa da Música', 'Concerto', NULL),
(14, 'Cortejo Académico', 'Cortejo académico pelas ruas da cidade do Porto, um dos pontos altos da semana.', '2026-05-05 00:00:00', 'Rua de Camões / Aliados', 'Cortejo', NULL),
(15, 'FITA XXXVII - Festival Ibérico de Tunas Académicas', 'Festival ibérico de tunas académicas integrado na Queima das Fitas do Porto.', '2026-05-06 00:00:00', 'Coliseu do Porto', 'Festival académico', NULL),
(16, 'Sarau Cultural', 'Evento cultural onde estudantes da Academia do Porto apresentam música, teatro, dança e comédia.', '2026-05-07 00:00:00', 'Auditório Oporto Vilar', 'Cultural', NULL),
(17, 'Baile de Gala', 'Baile formal associado ao culminar do percurso académico.', '2026-05-08 00:00:00', 'Casa dos Arcos', 'Baile de gala', NULL),
(18, 'Rally Paper', 'Atividade de convívio e descoberta cultural da cidade do Porto.', '2026-05-09 00:00:00', 'Porto', 'Atividade académica', NULL),
(19, 'Chá dançante', 'Evento social destinado especialmente aos estudantes em penúltimo ano.', '2026-05-09 00:00:00', 'Ordo BH Concept', 'Evento social', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_artist`
--

DROP TABLE IF EXISTS `event_artist`;
CREATE TABLE `event_artist` (
  `id_event` int(11) NOT NULL,
  `id_artist` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `event_artist`
--

INSERT INTO `event_artist` (`id_event`, `id_artist`) VALUES
(1, 8),
(1, 9),
(2, 2),
(3, 1),
(3, 3),
(4, 3),
(4, 4),
(4, 5),
(5, 6),
(5, 7),
(6, 10),
(6, 11),
(7, 12),
(7, 13),
(8, 14),
(8, 15),
(9, 16),
(9, 17);

-- --------------------------------------------------------

--
-- Estrutura da tabela `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE `faculty` (
  `id_faculty` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `acronym` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `faculty`
--

INSERT INTO `faculty` (`id_faculty`, `name`, `acronym`, `description`, `color`) VALUES
(1, 'Faculdade de Engenharia', 'FEUP', 'Faculdade de Engenharia da Universidade do Porto', '#883a3a'),
(2, 'Faculdade de Economia da Universidade do Porto', 'FEP', 'Faculdade de Economia da Universidade do Porto', '#ff0000'),
(3, 'Instituto Superior de Engenharia do Porto', 'ISEP', 'Instituto Superior de Engenharia do Porto', '#e83b3b'),
(101, 'Instituto Português de Administração de Marketing', 'IPAM', 'Instituto Português de Administração de Marketing', '#0057a8'),
(102, 'Escola Superior de Educação de Paula Frassinetti', 'ESEPF', 'Escola Superior de Educação de Paula Frassinetti', '#5b8c5a'),
(103, 'Instituto Superior de Serviço Social do Porto', 'ISSSP', 'Instituto Superior de Serviço Social do Porto', '#6c5ce7'),
(104, 'Instituto Superior de Línguas e Administração de Gaia', 'ISLA Gaia', 'Instituto Superior de Línguas e Administração de Gaia', '#2d98da'),
(105, 'Escola Superior de Artes e Design', 'ESAD', 'Escola Superior de Artes e Design', '#222222'),
(106, 'Faculdade de Direito da Universidade Católica Portuguesa', 'Católica Direito', 'Faculdade de Direito da Universidade Católica Portuguesa - Porto', '#8b0000'),
(107, 'Instituto de Ciências da Saúde da Universidade Católica Portuguesa', 'ICS Católica', 'Instituto de Ciências da Saúde da Universidade Católica Portuguesa - Porto', '#b22222'),
(108, 'Faculdade de Arquitectura da Universidade do Porto', 'FAUP', 'Faculdade de Arquitectura da Universidade do Porto', '#111111'),
(109, 'Instituto Superior de Contabilidade e Administração do Porto', 'ISCAP', 'Instituto Superior de Contabilidade e Administração do Porto', '#1f77b4'),
(110, 'Faculdade de Ciências da Universidade do Porto', 'FCUP', 'Faculdade de Ciências da Universidade do Porto', '#1b9e77'),
(111, 'Faculdade de Desporto da Universidade do Porto', 'FADEUP', 'Faculdade de Desporto da Universidade do Porto', '#2ca02c'),
(112, 'Universidade da Maia', 'UMAIA', 'Universidade da Maia', '#9467bd'),
(113, 'Faculdade de Direito da Universidade do Porto', 'FDUP', 'Faculdade de Direito da Universidade do Porto', '#003f8f'),
(114, 'Universidade Portucalense', 'UPT', 'Universidade Portucalense', '#d62728'),
(115, 'Universidade Lusófona', 'Lusófona', 'Universidade Lusófona', '#ff7f0e'),
(116, 'Instituto Superior de Administração e Gestão', 'ISAG', 'Instituto Superior de Administração e Gestão', '#17becf'),
(117, 'Universidade Lusíada - Norte', 'ULN', 'Universidade Lusíada - Norte', '#bcbd22'),
(118, 'Escola Superior de Tecnologia e Gestão', 'ESTG', 'Escola Superior de Tecnologia e Gestão', '#7f7f7f'),
(119, 'Escola Superior de Biotecnologia da Universidade Católica Portuguesa', 'ESB', 'Escola Superior de Biotecnologia da Universidade Católica Portuguesa', '#8c564b'),
(120, 'Escola Superior de Saúde do Politécnico do Porto', 'ESS-P.PORTO', 'Escola Superior de Saúde do Politécnico do Porto', '#e377c2'),
(121, 'Universidade Fernando Pessoa', 'UFP', 'Universidade Fernando Pessoa', '#4c78a8');

-- --------------------------------------------------------

--
-- Estrutura da tabela `personalagenda`
--

DROP TABLE IF EXISTS `personalagenda`;
CREATE TABLE `personalagenda` (
  `id_user` int(11) NOT NULL,
  `id_event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `personalagenda`
--

INSERT INTO `personalagenda` (`id_user`, `id_event`) VALUES
(1, 2),
(3, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `rating`
--

DROP TABLE IF EXISTS `rating`;
CREATE TABLE `rating` (
  `id_rating` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_event` int(11) DEFAULT NULL,
  `id_tent` int(11) DEFAULT NULL,
  `score` int(11) NOT NULL CHECK (`score` >= 1 and `score` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `role`
--

INSERT INTO `role` (`id_role`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Student');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tent`
--

DROP TABLE IF EXISTS `tent`;
CREATE TABLE `tent` (
  `id_tent` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_faculty` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tent`
--

INSERT INTO `tent` (`id_tent`, `name`, `id_faculty`, `location`, `open_time`, `close_time`, `description`) VALUES
(1, 'Barraca da FEUP', 1, 'Rua Principal, Lugar 1', '20:00:00', '04:00:00', 'A melhor barraca do recinto!'),
(2, 'Barraca da FEP', 2, 'Rua Principal, Lugar 2', '20:00:00', '04:00:00', 'Sempre a faturar.'),
(3, 'Turbinada', 3, 'A4', '20:00:00', '04:00:00', ''),
(101, 'AE ESTG', 118, 'Lugar 4', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(102, 'ESB\'EMBOA', 119, 'Lugar 20', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(103, 'Hardshot IPAM', 101, 'Lugar 13', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(104, 'AEIPAM', 101, 'Lugar 30', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(105, 'Papaias - ESEPF', 102, 'Lugar 48', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(106, 'ISSPONJA-TE', 103, 'Lugar 49', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(107, 'AE ISLA GAIA', 104, 'Lugar 64', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(108, 'ESTEPVS BARRACVS ACADEMICVS', 120, 'Lugar 73', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(109, 'AE ESAD', 105, 'Lugar 76', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(110, 'Barraquinha Católica Direito', 106, 'Lugar 77', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(111, 'Instituto de Ciências da Saúde - Católica', 107, 'Lugar 78', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(112, 'AEFAUP', 108, 'Lugar 79', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(113, 'ISCAPUS BARRACUS', 109, 'Lugar 80', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(114, 'ISEP Informática', 3, 'Lugar 81', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(115, 'AAFP', 121, 'Lugar 82', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(116, 'AE ISAG', 116, 'Lugar 84', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(117, 'AEFCUP', 110, 'Lugar 86', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(118, 'AEFADEUP', 111, 'Lugar 87', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(119, 'AEFEP', 2, 'Lugar 88', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(120, 'AEISCAP', 109, 'Lugar 89', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(121, 'AAULN', 117, 'Lugar 90', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(122, 'AEUMAIA', 112, 'Lugar 92', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(123, 'AEFDUP Mortis Causa', 113, 'Lugar 93', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(124, 'AEPortucalense', 114, 'Lugar 94', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.'),
(125, 'Lusófona', 115, 'Lugar 95', '20:00:00', '04:00:00', 'Barraquinha oficial da Queima das Fitas do Porto 2026.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id_user`, `name`, `email`, `password_hash`, `id_role`) VALUES
(1, 'Admin Queima', 'admin@queimaporto.pt', '$2y$10$ye.ygY7nyx1HUkyuXibaPuQBgyZF0ARO22vk4hQr.s7WecB4IdPAK', 1),
(2, 'João Estudante', 'joao@estudante.pt', '$2y$10$JFGR9arQTDKR4URdIVAZNufVTle4Izf8NS2MkZznBIbfltxyyqAea', 2),
(3, 'João Paulo Brazeta Bastos', 'joaobrazeta@gmail.com', '$2y$10$X.5fR1JYQikqoXbLACniweOOyJyig0G5yF04BE.RFf8UFzp8Z6N7S', 1),
(4, 'José Antonio', 'Jose@estudante.pt', '$2y$10$/pAzd47HHtIQcPgqBeiiOuDQ7Q4N7dqfk9xkFJ06/hfPDCrDmepgG', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`id_artist`);

--
-- Índices para tabela `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `id_tent` (`id_tent`);

--
-- Índices para tabela `event_artist`
--
ALTER TABLE `event_artist`
  ADD PRIMARY KEY (`id_event`,`id_artist`),
  ADD KEY `id_artist` (`id_artist`);

--
-- Índices para tabela `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id_faculty`);

--
-- Índices para tabela `personalagenda`
--
ALTER TABLE `personalagenda`
  ADD PRIMARY KEY (`id_user`,`id_event`),
  ADD KEY `id_event` (`id_event`);

--
-- Índices para tabela `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `id_tent` (`id_tent`);

--
-- Índices para tabela `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Índices para tabela `tent`
--
ALTER TABLE `tent`
  ADD PRIMARY KEY (`id_tent`),
  ADD KEY `id_faculty` (`id_faculty`);

--
-- Índices para tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `artist`
--
ALTER TABLE `artist`
  MODIFY `id_artist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `event`
--
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id_faculty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT de tabela `rating`
--
ALTER TABLE `rating`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tent`
--
ALTER TABLE `tent`
  MODIFY `id_tent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`id_tent`) REFERENCES `tent` (`id_tent`);

--
-- Limitadores para a tabela `event_artist`
--
ALTER TABLE `event_artist`
  ADD CONSTRAINT `event_artist_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_artist_ibfk_2` FOREIGN KEY (`id_artist`) REFERENCES `artist` (`id_artist`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `personalagenda`
--
ALTER TABLE `personalagenda`
  ADD CONSTRAINT `personalagenda_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `personalagenda_ibfk_2` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE,
  ADD CONSTRAINT `rating_ibfk_3` FOREIGN KEY (`id_tent`) REFERENCES `tent` (`id_tent`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tent`
--
ALTER TABLE `tent`
  ADD CONSTRAINT `tent_ibfk_1` FOREIGN KEY (`id_faculty`) REFERENCES `faculty` (`id_faculty`);

--
-- Limitadores para a tabela `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

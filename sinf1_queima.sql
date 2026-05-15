-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15-Abr-2026 às 09:33
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sinf1_queima`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `artist`
--

CREATE TABLE `artist` (
  `id_artist` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `musical_genre` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `biography` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `event`
--

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
(1, 'Noite de Quim Barreiros', 'O clássico concerto da Queima.', '2026-05-05 22:00:00', 'Palco Principal', 'Concert', NULL),
(2, 'Serenata Monumental', 'Cerimónia de abertura da Queima das Fitas.', '2026-05-03 00:01:00', 'Cordoaria', 'Academic ceremony', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `event_artist`
--

CREATE TABLE `event_artist` (
  `id_event` int(11) NOT NULL,
  `id_artist` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `faculty`
--

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
(1, 'Faculdade de Engenharia', 'FEUP', 'Faculdade de Engenharia da Universidade do Porto', 'Castanho'),
(2, 'Faculdade de Economia', 'FEP', 'Faculdade de Economia da Universidade do Porto', 'Vermelho');

-- --------------------------------------------------------

--
-- Estrutura da tabela `personalagenda`
--

CREATE TABLE `personalagenda` (
  `id_user` int(11) NOT NULL,
  `id_event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rating`
--

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
(2, 'Barraca da FEP', 2, 'Rua Principal, Lugar 2', '20:00:00', '04:00:00', 'Sempre a faturar.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

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
(1, 'Admin Queima', 'admin@queimaporto.pt', 'admin123', 1),
(2, 'João Estudante', 'joao@estudante.pt', 'joao123', 2);

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
  MODIFY `id_artist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `event`
--
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id_faculty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_tent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

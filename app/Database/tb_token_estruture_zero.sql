-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/12/2025 às 22:55
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tb_token`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `gtoken`
--

CREATE TABLE `gtoken` (
  `id_token` int(10) UNSIGNED NOT NULL,
  `token` varchar(16) DEFAULT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_prof` int(10) UNSIGNED NOT NULL,
  `paciente` varchar(120) DEFAULT NULL,
  `cpf` varchar(16) DEFAULT NULL,
  `nome_resp` varchar(120) DEFAULT NULL,
  `responsavel_f` varchar(120) DEFAULT NULL,
  `nome_banco` varchar(80) DEFAULT NULL,
  `modalidadepag` varchar(40) DEFAULT NULL,
  `formapag` varchar(30) DEFAULT NULL,
  `mes_ref` varchar(30) DEFAULT NULL,
  `ano_ref` varchar(10) DEFAULT NULL,
  `data_sessoes` varchar(150) DEFAULT NULL,
  `statuspag` varchar(20) NOT NULL,
  `valorpag` double NOT NULL,
  `vencimento` datetime DEFAULT NULL,
  `obs` varchar(200) DEFAULT NULL,
  `datacad` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `listagem`
--

CREATE TABLE `listagem` (
  `id_list` int(10) UNSIGNED NOT NULL,
  `nome_li` varchar(80) DEFAULT NULL,
  `valor_li` varchar(80) DEFAULT NULL,
  `tipo_li` varchar(80) DEFAULT NULL,
  `status_li` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `nome_responsavel` varchar(120) DEFAULT NULL,
  `responsavel_financeiro` varchar(120) DEFAULT NULL,
  `id_prof_referencia` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id_prof` int(10) UNSIGNED NOT NULL,
  `nome_p` varchar(100) DEFAULT NULL,
  `email_p` varchar(200) DEFAULT NULL,
  `senha_p` varchar(70) DEFAULT NULL,
  `user_tipo` int(11) NOT NULL DEFAULT 0,
  `id_status` int(11) NOT NULL DEFAULT 1,
  `crp` varchar(40) DEFAULT NULL,
  `porcento` int(11) DEFAULT NULL,
  `code_reset` varchar(40) DEFAULT NULL,
  `data_cad_p` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes`
--

CREATE TABLE `sessoes` (
  `id_sessao` int(11) NOT NULL,
  `id_token` int(10) UNSIGNED NOT NULL,
  `data_sessao` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'efetuado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_a`
--

CREATE TABLE `usuarios_a` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `nome` varchar(40) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `senha` varchar(70) DEFAULT NULL,
  `nivel_acesso` int(11) DEFAULT NULL,
  `user_tipo` int(11) NOT NULL DEFAULT 0,
  `id_status` int(11) NOT NULL DEFAULT 1,
  `crp` varchar(40) DEFAULT NULL,
  `data_cad` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `gtoken`
--
ALTER TABLE `gtoken`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_prof` (`id_prof`);

--
-- Índices de tabela `listagem`
--
ALTER TABLE `listagem`
  ADD PRIMARY KEY (`id_list`);

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id_prof`);

--
-- Índices de tabela `sessoes`
--
ALTER TABLE `sessoes`
  ADD PRIMARY KEY (`id_sessao`),
  ADD KEY `id_token` (`id_token`);

--
-- Índices de tabela `usuarios_a`
--
ALTER TABLE `usuarios_a`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `gtoken`
--
ALTER TABLE `gtoken`
  MODIFY `id_token` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `listagem`
--
ALTER TABLE `listagem`
  MODIFY `id_list` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id_prof` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sessoes`
--
ALTER TABLE `sessoes`
  MODIFY `id_sessao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios_a`
--
ALTER TABLE `usuarios_a`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `gtoken`
--
ALTER TABLE `gtoken`
  ADD CONSTRAINT `gtoken_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios_a` (`id_user`),
  ADD CONSTRAINT `gtoken_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `profissionais` (`id_prof`);

--
-- Restrições para tabelas `sessoes`
--
ALTER TABLE `sessoes`
  ADD CONSTRAINT `fk_sessoes_token` FOREIGN KEY (`id_token`) REFERENCES `gtoken` (`id_token`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 10/12/2025 às 19:22
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
  `statuspag` varchar(20) NOT NULL,
  `valorpag` double NOT NULL,
  `vencimento` datetime DEFAULT NULL,
  `obs` varchar(200) DEFAULT NULL,
  `datacad` datetime DEFAULT NULL,
  `origem` varchar(50) DEFAULT NULL
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
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `origem` varchar(50) DEFAULT NULL
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

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id_prof`, `nome_p`, `email_p`, `senha_p`, `user_tipo`, `id_status`, `crp`, `porcento`, `code_reset`, `data_cad_p`) VALUES
(9, 'Alexciane Beatriz Vieira Paixão', 'alexcianebeatriz@hotmail.com', '$2y$10$/L7swDryXxeSAcCDI9cqa.ULYb1yrT5HFopF8Engj8QHCrkozOaNq', 1, 1, NULL, 80, NULL, '2022-11-09'),
(10, 'Adriano Correia da Silva', 'Euadrianocorreia@gmail.com', '$2y$10$2vIZ8MNdlSy3W.pKdDpj1uhKXbk8y97Km8QE/fI4mBO3NoH2/el1O', 1, 0, NULL, NULL, NULL, '2022-11-09'),
(11, 'Daniella Paes Barreto Bezerra', 'daniellapaesbarreto1011@gmail.com', '$2y$10$4nkS3FF5SqoKlOw/tRYLNO2.arCXpYqte6KHzJLXKs4j/lz9u..Sy', 1, 1, NULL, 70, 'tgomkavlEdZg', '2022-11-09'),
(12, 'MAXWELL JONES DO MONTE BRITO', 'maxwellmontebrito@gmail.com', '$2y$10$WUHNi7xL0v/7inHtI6l7.e/bcVAT/YUSnBcuGd.BZNzhHfpiisj2u', 1, 0, NULL, NULL, NULL, '2022-11-09'),
(14, 'Talita Pacheco dos Santos', 'talitapacheco.psi@gmail.com', '$2y$10$Ulb7prOKrztxc8TQ8gCbcOUWZzHGJwQN.vqBxKP0h2KYNStDVOckO', 1, 1, NULL, 70, NULL, '2022-11-09'),
(15, 'Thamyris da silva souza', 'thamyris.souza@hotmail.com', '$2y$10$bvH4DibibdbAu5j2LgmMR.IQq.seTSMj8/D4iuy52EKyRw9794eOO', 1, 0, NULL, NULL, NULL, '2022-11-09'),
(16, 'Karla Rodrigues', 'karlinharo08@gmail.com', '$2y$10$.2Y1BaYm0zBSlvZtSLXM8.xyF/gp3XtABwW7T/IKg4nkt31Uvjetu', 1, 1, NULL, 70, NULL, '2022-11-09'),
(17, 'Dayane Souza Silva', 'dayane.souzapsi@gmail.com', '$2y$10$niZ7u919ICbFBSMlmnU03.g4QrX1zZpnWUZchqkm/ibrj6wcsbFdy', 1, 1, NULL, 80, NULL, '2022-11-09'),
(18, 'Thaís Leão', 'thaispatricia_2008@hotmail.com', '$2y$10$ePJendGm7WAbd7PzbrAX/OFWZuq6T9VWOinoDwhASSPah9cHH5CQ6', 1, 1, NULL, 80, NULL, '2022-11-09'),
(19, 'Luan Henrique da Silva Arruda', 'luanhenriquepe@gmail.com', '$2y$10$wNG2ywiJQhmbOFsSOHBNKu0oW1V91ttyRtxbe36PydZyDw.402fVK', 1, 1, NULL, 70, 'okRJN43SjulcmU', '2022-11-09'),
(20, 'Valdiégia Ferreira', 'valdiegia@hotmail.com', '$2y$10$8dMQbvKxQoYpKSgy13sty./NyhZe8sMK5fmssnxOXo2kaGiJ9e.sy', 1, 0, NULL, 70, 'Xo1zbAPe4EQJig', '2022-11-09'),
(21, 'Erik Natan Morais Santos', 'erikpsimorais@gmail.com', '$2y$10$vwbtPfukDFl20wLMqmt2yeaN3S8b4kjiSR5RYr3VG6YfWpkNVGULi', 1, 0, NULL, NULL, NULL, '2022-11-10'),
(22, 'Maria Socorro Alves de Souza', 'mariasocorroa754@gmail.com', '$2y$10$LRflGM8r4thVH.QBA9aWQuZejEaCRU0baJ5fsH5EKCcEl92YKk7se', 1, 0, NULL, NULL, NULL, '2022-11-10'),
(24, 'Lais Soares', 'psi.laissoares@gmail.com', '$2y$10$UDjzizKVO7iSGpaA0W1trePb9c6CoSw0qMA/NF4bRyCp.EGA/n0aW', 1, 0, NULL, NULL, NULL, '2022-11-10'),
(26, 'Tatiane Silva de Moura', 'mouratatiane11@gmail.com', '$2y$10$QDPLqXTzSAOlsNhqhW6oseSEnLYkrhOhdmE6P/uiMqZD3sc/GI6Xi', 1, 1, NULL, 70, NULL, '2022-11-10'),
(27, 'Jocelma Maria Andrade Marins', 'psicojocelmamarins@gmail.com', '$2y$10$8K0.Hv2FenNXNfMYOKwu5ekk6xD00LttBOWHtqInj0IWSn9RXMu5q', 1, 1, NULL, 70, NULL, '2022-11-10'),
(28, 'Giselle Mendonça de Medeiros', 'gisellemdias.psi@gmail.com', '$2y$10$ng314I0e7MLta2cOs6lhdeOQi6U4/nM24ckB9cxCiQxn8jjW4sXX2', 1, 1, NULL, 70, NULL, '2022-11-10'),
(29, 'Iara Cysneiros Silva', 'iaracysneirospsi@gmail.com', '$2y$10$GpNtRNH0nhLO1vfP/DIaT.TOLEvHzmnoTqHhpo1vyrA8BiBEbypYy', 1, 1, NULL, 80, 'XhycS9GQvxFPzk', '2022-11-11'),
(31, 'Andreza Patricia Machado Pontes', 'andrezapontesfono@gmail.com', '$2y$10$vTg8WgOC7O73n60ba5uVr.nbbJzKs0cmxsQiPr4ibIEVX61UVvwra', 1, 1, 'CRFa4-11099', 80, NULL, '2023-01-09'),
(32, 'Paulo de Tarso Melo', 'paulopsimelo@gmail.com', '$2y$10$dOP2U3jV5WYk27lsfam9eulueBteoENO/lMiHpUXoJ6zyLSIuRKry', 1, 1, '0213928', 80, 'd3WNgeQMIOHr2C', '2023-01-11'),
(33, 'Beatriz Costa Praxedes', 'beatrizpraxedes@hotmail.com', '$2y$10$2NMMYpCf8cgP2q9j7DcJPu/FZADOYV3rECZSQBcO.GGd.76y/erSW', 1, 1, 'CREFITO 1937-6 TO', 70, NULL, '2023-01-12'),
(35, 'Ana Cristina Cavalcante Belfort', 'cristinabelfort.psi@gmail.com', '$2y$10$y9qly/RCTbMzvvbYsBgaye56gYTKcCHOI9XLEsdGvXfqfmmRYi.se', 1, 1, 'CRP/02-27328', 70, 'I9rj3lxq6gpW1f', '2023-03-07'),
(36, 'Adriana Bezerra', 'psi.adriana.bezerra@gmail.com', '$2y$10$3I2RlS6rf5kDM.JryuadKeDzgrS7sMQD2bqWd05MovF5C.CcL/VFO', 1, 1, '02/27302', 75, NULL, '2023-03-07'),
(37, 'Priscila de Melo', 'priscilademmelo@gmail.com', '$2y$10$BPKrOAu/aoJh4yviVZ6SuebkwpMwYabQPNcKi8oxjgncxJeptD/jq', 1, 0, '02/27672', NULL, NULL, '2023-04-24'),
(38, 'Gabriela Agra', 'gabrielaagra@gmail.com', '$2y$10$3Ywo.NKahxQY7e5mhUx6w.wmnEn6zKV3HJSRsaqIf27mGQG6cxUTy', 1, 1, '02/26670', 70, NULL, '2023-08-22'),
(39, 'Stephanny Tavares Ferreira', 'psi.stephannytavares@gmail.com', '$2y$10$p/st.no6w0pFaiLk3GCM2uOBl/I1Dnrxh6/VvWnzK00NOKQ44/Pie', 1, 1, '02/25667', 70, '1osYJdbcBeHlMp', '2023-09-26'),
(40, 'Gabriela Grangeiro Dias', 'psi.gabrielagrangeiro@gmail.com', '$2y$10$h0.5g3AvfXiOwSx6tloca.QxlK0wIgxfdpfjEM.kcqXOVKw7d5kZ2', 1, 1, '02/20360', 75, 'BjFm6OsnQTyMXk', '2023-09-26'),
(41, 'Dalila Dos Reis Gomes', 'psicologadalilareis@gmail.com', '$2y$10$NUZ7dswOwJJDZFkxcTYUVOVWnpWxjtUtyT6IM/ba6iuE0vguKUPvi', 1, 1, '02/26111', 70, 'yPdRVFmgXkMGLI', '2023-09-26'),
(42, 'Vanessa Rodrigues Barbosa', 'vanessa.rbarbosa@outlook.com.br', '$2y$10$MfEj0Qq50z2IrhtU1WEwl.jK5zMVxLRKMzPoK3xrjX7lyGlB/RfFC', 1, 1, '02/26563', 70, NULL, '2024-02-01'),
(43, 'Rochanne Sonely de Lima Farias', 'psirochannesonely@gmail.com', NULL, 1, 1, '02/26032', 80, 'Mia3SJY5lWGh4D', '2024-02-01'),
(44, 'Monike Maciel Barros Pontes', 'psimonikepontes@gmail.com', '$2y$10$QPvnovorEXOWy3AehIj1Ge5bkFDLI6bN2qhn7vfuSr88nL6y6ZdQC', 1, 1, '0228888', 70, NULL, '2024-07-23'),
(45, 'Fernanda Gouveia da Fonte', 'fernandagouveiadafonte@gmail.com', '$2y$10$mbINsPOrsQVbNyQMNJXxD.LO5MY6OMNVBhX/pdrNO8hkUb.3rMWqi', 1, 0, '02/29283', 70, NULL, '2024-07-26'),
(46, 'Pedro Cerqueira Russo', 'pedro.crusso@gmail.com', '$2y$10$AwS5A9G1O2jj2qqBjcXr9./is.Yr/O941P/xEiUtJ1ZmaJN.G2SsO', 1, 1, 'CRM 22086', 92, NULL, '2025-02-03'),
(47, 'Aricia Medeiros', 'aricia.nut@gmail.com\r\n\r\n', '$2y$10$8ovEoJHSd2IJ78eDsK1WqOCvBJ5ji/eteGqvHn8sQ9qbP2/MBg4dS', 1, 1, NULL, 80, NULL, '2025-02-03'),
(48, 'Amanda Morais Rodrigues\r\n\r\n', 'amandamoraisnutricionista@gmail.com', '$2y$10$teh2FNEF50G8W0DG28NMP.dZdv2UImiuQ6GGNunbbqLGgY6mQKrsG\r\n', 1, 1, NULL, 80, NULL, '2025-03-31'),
(49, 'Raissa Guerra de Magalhães Melo', 'psi.raissaguerra@gmail.com', '$2y$10$TSvtsWNWVOimEnZrqdjP8.jRSXyzh2xhPo7SzvYb6/TOQBX6pGGNK', 1, 1, '02/30050', 70, NULL, '2025-04-15'),
(50, 'Augusto César Cordeiro Galindo', 'psiaugustocordeiro@gmail.com', 'testepsi', 1, 1, '02/22179', 70, NULL, '2025-04-15'),
(51, 'Nathália Karla Souza Cavalcanti', 'nathaliacavpsicologia@gmail.com', 'testepsi', 1, 1, '02/23103', 70, NULL, '2025-04-08'),
(52, 'Rodolfo Cunha', 'psirodolfocunha@gmail.com', '$2y$10$.eX7JCbcuGI70QmSpJj40Oh0Ilzvl3VFA6UlatDmWT5Lx.NK2.15u', 1, 1, NULL, 70, NULL, '2025-06-30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes`
--

CREATE TABLE `sessoes` (
  `id_sessao` int(11) NOT NULL,
  `id_token` int(10) UNSIGNED NOT NULL,
  `data_sessao` datetime NOT NULL
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
-- Despejando dados para a tabela `usuarios_a`
--

INSERT INTO `usuarios_a` (`id_user`, `nome`, `email`, `senha`, `nivel_acesso`, `user_tipo`, `id_status`, `crp`, `data_cad`) VALUES
(1, 'Assista', 'assistacentro@gmail.com', '$2y$10$AVzSTZyQSVuIVgQG5E9/muCuhvQKB8BCfdM5SlPST56YZjIVpZPhO', 1, 2, 1, NULL, '2022-10-25'),
(2, 'Priscila Holanda', 'priscilahn@gmail.com', '$2y$10$Po9KCx2C5JcOXqBvN3NMZ.DlEUgZ.2./yv2/10mmoNoPKOLj8Vrd2', 3, 2, 0, NULL, '2022-11-03'),
(3, 'Bruno Silva', 'brunojsilvasuporte@gmail.com', '$2y$10$yCmqRlvApJKWOysAldubW.KqyEB8HnrkRji9Vb17XvltZbomcRpgC', 3, 2, 1, NULL, '2022-11-09'),
(4, 'Filipe Oliveira', 'oliveira.lipe1806@gmail.com', '$2y$10$QPHHkm889rkNh4O3NEUsS.msvfIultmgwtZwsKyamZzessTZkaIxm', 2, 0, 0, NULL, '2022-11-07'),
(6, 'Cecilia Campos', 'ceciduda1999@gmail.com', '$2y$10$yhsbkh0DW0ba.AgDie9xfe/eLdhBRhy02KoCB6ir4mi0QsWN1kFMe', 2, 2, 1, NULL, '2023-08-15'),
(7, 'Paulo de Tarso', 'paulopsimelo@gmail.com', '$2y$10$iCqD6v1sWFs66VPcOldLG./1hOaqaAx0KTdCogReTh3O3W5b0GW4u', 2, 2, 1, NULL, '2024-04-12'),
(8, 'Karen Araújo', 'andradekaren94@gmail.com', '$2y$10$rzhq.wjn0L6Kxu4WSeJ4/.mjK6oYV4aP7DTF09kvBOcEhl/sz1Su6', 2, 2, 1, NULL, '2024-05-13');

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
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id_prof` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `sessoes`
--
ALTER TABLE `sessoes`
  MODIFY `id_sessao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios_a`
--
ALTER TABLE `usuarios_a`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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

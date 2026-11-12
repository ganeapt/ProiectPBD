-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 12, 2026 at 11:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proiectpbd`
--

-- --------------------------------------------------------

--
-- Table structure for table `activitate`
--

CREATE TABLE `activitate` (
  `id_act` int(11) NOT NULL,
  `id_proiect` int(11) NOT NULL,
  `id_linie` int(11) NOT NULL,
  `numar_ordine` varchar(10) NOT NULL,
  `titlu` varchar(200) NOT NULL,
  `data_inceput` date NOT NULL,
  `data_sfarsit` date NOT NULL,
  `procent_realizat` int(11) DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `apel_proiect`
--

CREATE TABLE `apel_proiect` (
  `id_apel` int(11) NOT NULL,
  `cod_apel` varchar(50) NOT NULL,
  `titlu` varchar(200) NOT NULL,
  `buget_total` decimal(15,2) NOT NULL,
  `procent_cofinantare` decimal(5,2) NOT NULL,
  `data_start` date NOT NULL,
  `data_limita` date NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `cerere_rambursare`
--

CREATE TABLE `cerere_rambursare` (
  `id_cr` int(11) NOT NULL,
  `id_proiect` int(11) NOT NULL,
  `id_ofiter` int(11) DEFAULT NULL,
  `numar_cerere` varchar(20) NOT NULL,
  `data_depunere` date NOT NULL,
  `suma_solicitata` decimal(15,2) NOT NULL,
  `suma_aprobata` decimal(15,2) DEFAULT NULL,
  `stare_cr` enum('Depusa','In_Verificare','Aprobata','Respinsa') DEFAULT 'Depusa'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `document_justificativ`
--

CREATE TABLE `document_justificativ` (
  `id_doc` int(11) NOT NULL,
  `id_cr` int(11) NOT NULL,
  `id_linie` int(11) NOT NULL,
  `tip_document` enum('Factura','Ordin_Plata','Stat_Salarii','Bon_Fiscal','Raport_Audit') NOT NULL,
  `numar_document` varchar(50) NOT NULL,
  `data_emiterii` date NOT NULL,
  `valoare_totala` decimal(15,2) NOT NULL,
  `valoare_eligibila_solicitata` decimal(15,2) NOT NULL,
  `valoare_aprobata` decimal(15,2) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `evaluare`
--

CREATE TABLE `evaluare` (
  `id_eval` int(11) NOT NULL,
  `id_proiect` int(11) NOT NULL,
  `id_evaluator` int(11) NOT NULL,
  `punctaj_total` decimal(5,2) NOT NULL,
  `decizie` enum('Admis','Respins','Clarificari') NOT NULL,
  `observatii` text DEFAULT NULL,
  `data_evaluare` date NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `linie_bugetara`
--

CREATE TABLE `linie_bugetara` (
  `id_linie` int(11) NOT NULL,
  `id_proiect` int(11) NOT NULL,
  `categorie_cheltuiala` enum('Personal','Echipamente','Servicii_Subcontractate','Deplasari','Regie') NOT NULL,
  `suma_eligibila` decimal(15,2) NOT NULL,
  `suma_neeligibila` decimal(15,2) DEFAULT 0.00
) ;

-- --------------------------------------------------------

--
-- Table structure for table `livrabil`
--

CREATE TABLE `livrabil` (
  `id_livrabil` int(11) NOT NULL,
  `id_act` int(11) NOT NULL,
  `id_cr` int(11) DEFAULT NULL,
  `denumire` varchar(200) NOT NULL,
  `termen_asumat` date NOT NULL,
  `stare` enum('In_Lucru','Livrat','Aprobat','Respins') DEFAULT 'In_Lucru'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizatie`
--

CREATE TABLE `organizatie` (
  `id_org` int(11) NOT NULL,
  `denumire` varchar(150) NOT NULL,
  `cui` varchar(20) NOT NULL,
  `iban` varchar(34) NOT NULL,
  `adresa` varchar(255) NOT NULL,
  `tip_organizatie` enum('IMM','Universitate','ONG','Institut_Cercetare','Companie_Mare') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proiect`
--

CREATE TABLE `proiect` (
  `id_proiect` int(11) NOT NULL,
  `id_apel` int(11) NOT NULL,
  `id_org_lider` int(11) NOT NULL,
  `titlu` varchar(255) NOT NULL,
  `acronim` varchar(30) NOT NULL,
  `durata_luni` int(11) NOT NULL,
  `stare` enum('Draft','Depus','In_Evaluare','Contractat','Implementare','Finalizat','Respins') DEFAULT 'Draft',
  `data_depunere` datetime DEFAULT NULL,
  `valoare_totala` decimal(15,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `proiect_partener`
--

CREATE TABLE `proiect_partener` (
  `id_proiect` int(11) NOT NULL,
  `id_org` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilizator`
--

CREATE TABLE `utilizator` (
  `id_user` int(11) NOT NULL,
  `id_org` int(11) NOT NULL,
  `nume` varchar(50) NOT NULL,
  `prenume` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `parola_hash` varchar(255) NOT NULL,
  `rol` enum('Solicitant','Evaluator','Ofiter_AM','Administrator') NOT NULL,
  `activ` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activitate`
--
ALTER TABLE `activitate`
  ADD PRIMARY KEY (`id_act`),
  ADD UNIQUE KEY `uq_act_proiect_nr` (`id_proiect`,`numar_ordine`),
  ADD KEY `fk_act_linie` (`id_linie`);

--
-- Indexes for table `apel_proiect`
--
ALTER TABLE `apel_proiect`
  ADD PRIMARY KEY (`id_apel`),
  ADD UNIQUE KEY `cod_apel` (`cod_apel`);

--
-- Indexes for table `cerere_rambursare`
--
ALTER TABLE `cerere_rambursare`
  ADD PRIMARY KEY (`id_cr`),
  ADD UNIQUE KEY `uq_cr_proiect_nr` (`id_proiect`,`numar_cerere`),
  ADD KEY `fk_cr_ofiter` (`id_ofiter`);

--
-- Indexes for table `document_justificativ`
--
ALTER TABLE `document_justificativ`
  ADD PRIMARY KEY (`id_doc`),
  ADD UNIQUE KEY `uq_doc_cr_numar` (`id_cr`,`numar_document`),
  ADD KEY `fk_doc_linie` (`id_linie`);

--
-- Indexes for table `evaluare`
--
ALTER TABLE `evaluare`
  ADD PRIMARY KEY (`id_eval`),
  ADD UNIQUE KEY `uq_eval_proiect_evaluator` (`id_proiect`,`id_evaluator`),
  ADD KEY `fk_eval_evaluator` (`id_evaluator`);

--
-- Indexes for table `linie_bugetara`
--
ALTER TABLE `linie_bugetara`
  ADD PRIMARY KEY (`id_linie`),
  ADD UNIQUE KEY `uq_proiect_categorie` (`id_proiect`,`categorie_cheltuiala`);

--
-- Indexes for table `livrabil`
--
ALTER TABLE `livrabil`
  ADD PRIMARY KEY (`id_livrabil`),
  ADD KEY `fk_livrabil_act` (`id_act`),
  ADD KEY `fk_livrabil_cr` (`id_cr`);

--
-- Indexes for table `organizatie`
--
ALTER TABLE `organizatie`
  ADD PRIMARY KEY (`id_org`),
  ADD UNIQUE KEY `cui` (`cui`),
  ADD UNIQUE KEY `iban` (`iban`);

--
-- Indexes for table `proiect`
--
ALTER TABLE `proiect`
  ADD PRIMARY KEY (`id_proiect`),
  ADD KEY `fk_proiect_apel` (`id_apel`),
  ADD KEY `fk_proiect_lider` (`id_org_lider`);

--
-- Indexes for table `proiect_partener`
--
ALTER TABLE `proiect_partener`
  ADD PRIMARY KEY (`id_proiect`,`id_org`),
  ADD KEY `fk_partener_org` (`id_org`);

--
-- Indexes for table `utilizator`
--
ALTER TABLE `utilizator`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_org` (`id_org`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activitate`
--
ALTER TABLE `activitate`
  MODIFY `id_act` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apel_proiect`
--
ALTER TABLE `apel_proiect`
  MODIFY `id_apel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cerere_rambursare`
--
ALTER TABLE `cerere_rambursare`
  MODIFY `id_cr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_justificativ`
--
ALTER TABLE `document_justificativ`
  MODIFY `id_doc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluare`
--
ALTER TABLE `evaluare`
  MODIFY `id_eval` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `linie_bugetara`
--
ALTER TABLE `linie_bugetara`
  MODIFY `id_linie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `livrabil`
--
ALTER TABLE `livrabil`
  MODIFY `id_livrabil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizatie`
--
ALTER TABLE `organizatie`
  MODIFY `id_org` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proiect`
--
ALTER TABLE `proiect`
  MODIFY `id_proiect` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilizator`
--
ALTER TABLE `utilizator`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activitate`
--
ALTER TABLE `activitate`
  ADD CONSTRAINT `fk_act_linie` FOREIGN KEY (`id_linie`) REFERENCES `linie_bugetara` (`id_linie`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_act_proiect` FOREIGN KEY (`id_proiect`) REFERENCES `proiect` (`id_proiect`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cerere_rambursare`
--
ALTER TABLE `cerere_rambursare`
  ADD CONSTRAINT `fk_cr_ofiter` FOREIGN KEY (`id_ofiter`) REFERENCES `utilizator` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cr_proiect` FOREIGN KEY (`id_proiect`) REFERENCES `proiect` (`id_proiect`) ON UPDATE CASCADE;

--
-- Constraints for table `document_justificativ`
--
ALTER TABLE `document_justificativ`
  ADD CONSTRAINT `fk_doc_cr` FOREIGN KEY (`id_cr`) REFERENCES `cerere_rambursare` (`id_cr`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_linie` FOREIGN KEY (`id_linie`) REFERENCES `linie_bugetara` (`id_linie`) ON UPDATE CASCADE;

--
-- Constraints for table `evaluare`
--
ALTER TABLE `evaluare`
  ADD CONSTRAINT `fk_eval_evaluator` FOREIGN KEY (`id_evaluator`) REFERENCES `utilizator` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_proiect` FOREIGN KEY (`id_proiect`) REFERENCES `proiect` (`id_proiect`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `linie_bugetara`
--
ALTER TABLE `linie_bugetara`
  ADD CONSTRAINT `fk_linie_proiect` FOREIGN KEY (`id_proiect`) REFERENCES `proiect` (`id_proiect`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `livrabil`
--
ALTER TABLE `livrabil`
  ADD CONSTRAINT `fk_livrabil_act` FOREIGN KEY (`id_act`) REFERENCES `activitate` (`id_act`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_livrabil_cr` FOREIGN KEY (`id_cr`) REFERENCES `cerere_rambursare` (`id_cr`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `proiect`
--
ALTER TABLE `proiect`
  ADD CONSTRAINT `fk_proiect_apel` FOREIGN KEY (`id_apel`) REFERENCES `apel_proiect` (`id_apel`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_proiect_lider` FOREIGN KEY (`id_org_lider`) REFERENCES `organizatie` (`id_org`) ON UPDATE CASCADE;

--
-- Constraints for table `proiect_partener`
--
ALTER TABLE `proiect_partener`
  ADD CONSTRAINT `fk_partener_org` FOREIGN KEY (`id_org`) REFERENCES `organizatie` (`id_org`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_partener_proiect` FOREIGN KEY (`id_proiect`) REFERENCES `proiect` (`id_proiect`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `utilizator`
--
ALTER TABLE `utilizator`
  ADD CONSTRAINT `fk_user_org` FOREIGN KEY (`id_org`) REFERENCES `organizatie` (`id_org`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

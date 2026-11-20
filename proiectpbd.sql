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

-- =============================================================================
-- SECTIUNEA 4: DATE DE TEST PENTRU VALIDARE SI RAPORTARE (SEED DATA)
-- =============================================================================

-- Populare tabela ORGANIZATIE
INSERT INTO organizatie (id_org, denumire, cui, iban, dresa, 	ip_organizatie) VALUES
(1, 'Universitatea Tehnica din Cluj-Napoca - CUNBM', 'RO14587211', 'RO49TREZ06120F330500XXXX', 'Str. Victor Babes 62A, Baia Mare', 'Universitate'),
(2, 'SC SoftTech Solutions Innovation SRL', 'RO28994120', 'RO12BTRL02401202889941XX', 'Str. Memorandumului 21, Cluj-Napoca', 'IMM'),
(3, 'Institutul National de Cercetare in Informatica ICI', 'RO15904423', 'RO88TREZ7005069XXX001234', 'Bd. Maresal Averescu 8-10, Bucuresti', 'Institut_Cercetare'),
(4, 'Asociatia Centrul de Inovare Digitala Transilvania', 'RO34001928', 'RO33INGB0000999901452288', 'Str. Teodor Mihali 56, Cluj-Napoca', 'ONG'),
(5, 'SC CloudEnterprise Systems SA', 'RO18442091', 'RO77RZBR0000060011223344', 'Str. Barbu Vacarescu 102, Bucuresti', 'Companie_Mare');

-- Populare tabela UTILIZATOR
INSERT INTO utilizator (id_user, id_org, 
ume, prenume, email, parola_hash, 
ol, ctiv) VALUES
(1, 1, 'Popescu', 'Mihai', 'admin.sys@mysmis-demo.ro', '.K2gE6K7R9yKx3QvM2.kL9z9q0X3uP4aO7bN6vC1mQ8yU5iZ2W', 'Administrator', 1),
(2, 2, 'Ionescu', 'Elena', 'elena.ionescu@softtech.ro', '.L3hF7L8S0zLy4RwN3.lM0a0r1Y4vQ5bP8cO7wD2nR9zV6jA3X', 'Solicitant', 1),
(3, 1, 'Radu', 'Cristian', 'c.radu@cunbm.utcluj.ro', '.M4iG8M9T1aMz5SxO4.mN1b1s2Z5wR6cQ9dP8xE3oS0aW7kB4Y', 'Solicitant', 1),
(4, 3, 'Dumitrescu', 'Adrian', 'a.dumitrescu@ici.ro', '.N5jH9N0U2bNa6TyP5.nO2c2t3A6xS7dR0eQ9yF4pT1bX8lC5Z', 'Evaluator', 1),
(5, 4, 'Vasilescu', 'Andreea', 'andreea.v@transilvania-hub.ro', '.O6kI0O1V3cOb7UzQ6.oP3d3u4B7yT8eS1fR0zG5qU2cY9mD6A', 'Evaluator', 1),
(6, 1, 'Marinescu', 'Dan', 'dan.marinescu@oipoc.ro', '.P7lJ1P2W4dPc8VaR7.pQ4e4v5C8zU9fT2gS1aH6rV3dZ0nE7B', 'Ofiter_AM', 1);

-- Populare tabela APEL_PROIECT
INSERT INTO pel_proiect (id_apel, cod_apel, 	itlu, uget_total, procent_cofinantare, data_start, data_limita) VALUES
(1, 'PNRR/2026/C9/I8', 'Digitalizarea IMM-urilor prin Tehnologii Avansate AI si Cloud Computing', 50000000.00, 10.00, '2026-01-15', '2026-06-30'),
(2, 'POCIDIF/2026/1.1/A', 'Centre de Excelenta in Cercetare Aplicata si Transfer Tehnologic', 80000000.00, 15.00, '2026-02-01', '2026-08-31'),
(3, 'HORIZON-EUROPE-2026-AI', 'Sisteme Inteligente pentru Securitatea Datelor Industriale Transfrontaliere', 120000000.00, 0.00, '2026-03-01', '2026-10-15');

-- Populare tabela PROIECT
INSERT INTO proiect (id_proiect, id_apel, id_org_lider, 	itlu, cronim, durata_luni, stare, data_depunere, aloare_totala) VALUES
(1, 1, 2, 'Platforma Nationala de Analiza si Automatizare a Fluxurilor de Afaceri bazata pe Inteligenta Artificiala', 'SMART-BIZ-AI', 24, 'Implementare', '2026-03-10 14:30:00', 4500000.00),
(2, 2, 1, 'Centru Regional de Cercetare Avansata in Securitate Cibernetica si Internet of Things', 'CYBER-SEC-IOT', 36, 'Contractat', '2026-04-12 11:15:00', 8200000.00),
(3, 1, 5, 'Modernizarea Sistemelor ERP de Productie prin Solutii Cloud Hibrid', 'CLOUD-PROD', 18, 'In_Evaluare', '2026-05-20 09:45:00', 3100000.00),
(4, 3, 3, 'Consortiu Transnational pentru Transparenta si Etica Datelor Digitale Industriale', 'ETHIC-DATA', 30, 'Draft', NULL, 6500000.00);

-- Populare tabela PROIECT_PARTENER
INSERT INTO proiect_partener (id_proiect, id_org) VALUES
(1, 1),
(1, 4),
(2, 2),
(2, 3);

-- Populare tabela LINIE_BUGETARA
INSERT INTO linie_bugetara (id_linie, id_proiect, categorie_cheltuiala, suma_eligibila, suma_neeligibila) VALUES
(1, 1, 'Personal', 2200000.00, 0.00),
(2, 1, 'Echipamente', 1100000.00, 50000.00),
(3, 1, 'Servicii_Subcontractate', 600000.00, 20000.00),
(4, 1, 'Deplasari', 150000.00, 10000.00),
(5, 1, 'Regie', 450000.00, 0.00),
(6, 2, 'Personal', 4000000.00, 0.00),
(7, 2, 'Echipamente', 2800000.00, 100000.00),
(8, 2, 'Servicii_Subcontractate', 800000.00, 50000.00),
(9, 2, 'Deplasari', 250000.00, 20000.00),
(10, 2, 'Regie', 350000.00, 0.00),
(11, 3, 'Personal', 1500000.00, 0.00),
(12, 3, 'Echipamente', 1200000.00, 30000.00),
(13, 3, 'Servicii_Subcontractate', 400000.00, 10000.00);

-- Populare tabela ACTIVITATE
INSERT INTO ctivitate (id_act, id_proiect, id_linie, 
umar_ordine, 	itlu, data_inceput, data_sfarsit, procent_realizat) VALUES
(1, 1, 1, 'A1.1', 'Analiza cerintelor operationale si proiectarea arhitecturii AI', '2026-04-01', '2026-07-31', 100),
(2, 1, 2, 'A1.2', 'Achizitia infrastructurii hardware si configurarea clusterului de calcul', '2026-06-01', '2026-09-30', 100),
(3, 1, 1, 'A2.1', 'Dezvoltarea modulelor de procesare a limbajului natural si viziune computerizata', '2026-08-01', '2027-01-31', 65),
(4, 1, 3, 'A2.2', 'Subcontractare servicii specializate de audit securitate cibernetica si penetrare', '2026-11-01', '2027-03-31', 20),
(5, 1, 4, 'A3.1', 'Participare la conferinte internationale si diseminarea rezultatelor cercetarii', '2026-09-01', '2027-09-30', 40),
(6, 2, 6, 'A1.1', 'Recrutarea echipei de cercetare si definirea metodologiei experimentale', '2026-05-01', '2026-08-31', 90),
(7, 2, 7, 'A1.2', 'Amenajarea laboratorului IoT si instalarea bancurilor de testare senzoriala', '2026-07-01', '2026-11-30', 45),
(8, 2, 8, 'A2.1', 'Dezvoltarea protocoalelor criptografice de comunicatie sigura pentru senzori', '2026-10-01', '2027-04-30', 10);

-- Populare tabela CERERE_RAMBURSARE
INSERT INTO cerere_rambursare (id_cr, id_proiect, id_ofiter, 
umar_cerere, data_depunere, suma_solicitata, suma_aprobata, stare_cr) VALUES
(1, 1, 6, 'CR-01', '2026-08-05', 450000.00, 445000.00, 'Aprobata'),
(2, 1, 6, 'CR-02', '2026-11-10', 820000.00, 795000.00, 'Aprobata'),
(3, 1, 6, 'CR-03', '2027-02-15', 620000.00, NULL, 'In_Verificare'),
(4, 2, 6, 'CR-01', '2026-09-15', 380000.00, 380000.00, 'Aprobata');

-- Populare tabela LIVRABIL
INSERT INTO livrabil (id_livrabil, id_act, id_cr, denumire, 	ermen_asumat, stare) VALUES
(1, 1, 1, 'Raport de analiza a cerintelor de business si specificatii tehnice arhitecturale', '2026-07-15', 'Aprobat'),
(2, 1, 1, 'Document de arhitectura software si model conceptual al datelor', '2026-07-30', 'Aprobat'),
(3, 2, 2, 'Proces-verbal de receptie calitativa si cantitativa a nodurilor de calcul GPU', '2026-09-15', 'Aprobat'),
(4, 3, 3, 'Modul software prototip NLP v1.0 integrat in repository-ul securizat al proiectului', '2027-01-20', 'Livrat'),
(5, 3, NULL, 'Modul software prototip Computer Vision v1.0 cu acuratete de recunoastere > 92%', '2027-02-15', 'In_Lucru'),
(6, 4, NULL, 'Raport preliminar de conformitate cu cerintele ISO 27001 si analiza de risc', '2027-03-15', 'In_Lucru'),
(7, 5, 2, 'Lucrare stiintifica publicata si prezentata in cadrul conferintei IEEE SmartSystems', '2026-10-10', 'Aprobat'),
(8, 6, 4, 'Metodologie experimentala validata si planul operational al laboratorului de cercetare', '2026-08-20', 'Aprobat'),
(9, 7, NULL, 'Raport tehnic de instalare a senzorilor si masuratori preliminare in camp IoT', '2026-11-20', 'In_Lucru'),
(10, 8, NULL, 'Specificatie tehnica a algoritmului de criptare optimizat pentru noduri ultra-low-power', '2027-04-15', 'In_Lucru');

-- Populare tabela EVALUARE
INSERT INTO evaluare (id_eval, id_proiect, id_evaluator, punctaj_total, decizie, observatii, data_evaluare) VALUES
(1, 1, 4, 94.50, 'Admis', 'Propunere matura, excelenta stiintifica bine articulata, impact economic ridicat, buget corect dimensionat.', '2026-03-25'),
(2, 1, 5, 91.00, 'Admis', 'Consortiu echilibrat, colaborare solida intre mediul universitar si privat, obiective realiste.', '2026-03-26'),
(3, 2, 4, 88.00, 'Admis', 'Infrastructura de cercetare relevanta, capacitate institutionala dovedita.', '2026-04-28'),
(4, 3, 5, 68.50, 'Clarificari', 'Se solicita clarificari privind licentierea drepturilor de proprietate intelectuala si devizul echipamentelor.', '2026-06-02');

-- Populare tabela DOCUMENT_JUSTIFICATIV
INSERT INTO document_justificativ (id_doc, id_cr, id_linie, 	ip_document, 
umar_document, data_emiterii, aloare_totala, aloare_eligibila_solicitata, aloare_aprobata) VALUES
(1, 1, 1, 'Stat_Salarii', 'STAT-2026-04-06', '2026-07-05', 180000.00, 180000.00, 180000.00),
(2, 1, 1, 'Ordin_Plata', 'OP-SAL-2026-07', '2026-07-10', 180000.00, 180000.00, 180000.00),
(3, 1, 4, 'Factura', 'FACT-DEP-9941', '2026-06-25', 90000.00, 85000.00, 85000.00),
(4, 2, 2, 'Factura', 'FACT-SRV-2026-88', '2026-09-02', 650000.00, 620000.00, 620000.00),
(5, 2, 2, 'Ordin_Plata', 'OP-FURN-2026-14', '2026-09-05', 650000.00, 620000.00, 620000.00),
(6, 2, 3, 'Factura', 'FACT-AUD-104', '2026-10-15', 180000.00, 175000.00, 150000.00),
(7, 3, 1, 'Stat_Salarii', 'STAT-2026-10-12', '2027-01-08', 350000.00, 350000.00, NULL),
(8, 3, 3, 'Factura', 'FACT-IT-449', '2027-01-18', 270000.00, 270000.00, NULL),
(9, 4, 6, 'Stat_Salarii', 'STAT-UTCN-2026-07', '2026-08-05', 220000.00, 220000.00, 220000.00),
(10, 4, 7, 'Factura', 'FACT-SENZ-332', '2026-08-25', 160000.00, 160000.00, 160000.00);


-- =============================================================================
-- SECTIUNEA 5: VEDERI (VIEWS) PENTRU MONITORIZARE SI RAPORTARE MYSMIS
-- =============================================================================

-- VIEW 1: Centralizatorul general al proiectelor
CREATE OR REPLACE VIEW _raport_general_proiecte AS
SELECT 
    p.id_proiect,
    p.acronim,
    p.titlu AS titlu_proiect,
    a.cod_apel,
    a.titlu AS titlu_apel,
    o.denumire AS lider_consortiu,
    o.cui AS cui_lider,
    o.tip_organizatie AS tip_lider,
    COUNT(pp.id_org) AS numar_parteneri,
    p.durata_luni,
    p.valoare_totala,
    p.stare AS stare_proiect,
    p.data_depunere
FROM proiect p
JOIN pel_proiect a ON p.id_apel = a.id_apel
JOIN organizatie o ON p.id_org_lider = o.id_org
LEFT JOIN proiect_partener pp ON p.id_proiect = pp.id_proiect
GROUP BY p.id_proiect;

-- VIEW 2: Situatia financiara si gradul de absorbtie pe proiect
CREATE OR REPLACE VIEW _executie_bugetara_proiect AS
SELECT 
    p.id_proiect,
    p.acronim,
    p.valoare_totala AS buget_contractat,
    COALESCE(SUM(DISTINCT lb_sum.total_eligibil_linii), 0) AS total_eligibil_bugetat,
    COALESCE(cr_stat.total_solicitat, 0) AS total_solicitat_decontare,
    COALESCE(cr_stat.total_aprobat, 0) AS total_aprobat_plata,
    ROUND(COALESCE(cr_stat.total_aprobat, 0) / p.valoare_totala * 100, 2) AS grad_absorbtie_procent,
    (p.valoare_totala - COALESCE(cr_stat.total_aprobat, 0)) AS sold_ramas_neabsorbit,
    p.stare
FROM proiect p
LEFT JOIN (
    SELECT id_proiect, SUM(suma_eligibila) AS total_eligibil_linii
    FROM linie_bugetara
    GROUP BY id_proiect
) lb_sum ON p.id_proiect = lb_sum.id_proiect
LEFT JOIN (
    SELECT 
        id_proiect,
        SUM(suma_solicitata) AS total_solicitat,
        SUM(COALESCE(suma_aprobata, 0)) AS total_aprobat
    FROM cerere_rambursare
    GROUP BY id_proiect
) cr_stat ON p.id_proiect = cr_stat.id_proiect
GROUP BY p.id_proiect;

-- VIEW 3: Balanta de executie pe categorii de cheltuieli (linii bugetare)
CREATE OR REPLACE VIEW _balanta_linii_bugetare AS
SELECT 
    lb.id_linie,
    p.id_proiect,
    p.acronim,
    lb.categorie_cheltuiala,
    lb.suma_eligibila AS buget_eligibil_linie,
    lb.suma_neeligibila AS cofinantare_linie,
    COALESCE(SUM(dj.valoare_aprobata), 0) AS total_decontat_aprobat,
    (lb.suma_eligibila - COALESCE(SUM(dj.valoare_aprobata), 0)) AS disponibil_ramas,
    ROUND(COALESCE(SUM(dj.valoare_aprobata), 0) / lb.suma_eligibila * 100, 2) AS procent_consum_linie
FROM linie_bugetara lb
JOIN proiect p ON lb.id_proiect = p.id_proiect
LEFT JOIN document_justificativ dj ON lb.id_linie = dj.id_linie
GROUP BY lb.id_linie;

-- VIEW 4: Progresul fizic al activitatilor si evidenta livrabilelor
CREATE OR REPLACE VIEW _progres_activitati_livrabile AS
SELECT 
    a.id_act,
    p.acronim AS acronim_proiect,
    a.numar_ordine,
    a.titlu AS titlu_activitate,
    a.data_inceput,
    a.data_sfarsit,
    a.procent_realizat,
    COUNT(l.id_livrabil) AS total_livrabile_asumate,
    SUM(CASE WHEN l.stare = 'Aprobat' THEN 1 ELSE 0 END) AS livrabile_aprobate,
    SUM(CASE WHEN l.stare = 'Livrat' THEN 1 ELSE 0 END) AS livrabile_livrate,
    SUM(CASE WHEN l.stare = 'In_Lucru' THEN 1 ELSE 0 END) AS livrabile_in_lucru
FROM ctivitate a
JOIN proiect p ON a.id_proiect = p.id_proiect
LEFT JOIN livrabil l ON a.id_act = l.id_act
GROUP BY a.id_act;

-- VIEW 5: Centralizatorul sesiunilor de evaluare
CREATE OR REPLACE VIEW _clasament_evaluari AS
SELECT 
    e.id_eval,
    ap.cod_apel,
    p.id_proiect,
    p.acronim,
    p.titlu AS titlu_proiect,
    CONCAT(u.nume, ' ', u.prenume) AS nume_evaluator,
    org_ev.denumire AS institutie_evaluator,
    e.punctaj_total,
    e.decizie,
    e.data_evaluare,
    e.observatii
FROM evaluare e
JOIN proiect p ON e.id_proiect = p.id_proiect
JOIN pel_proiect ap ON p.id_apel = ap.id_apel
JOIN utilizator u ON e.id_evaluator = u.id_user
JOIN organizatie org_ev ON u.id_org = org_ev.id_org;

-- VIEW 6: Registrul de control al cererilor de rambursare
CREATE OR REPLACE VIEW _status_cereri_rambursare AS
SELECT 
    cr.id_cr,
    p.acronim AS acronim_proiect,
    cr.numar_cerere,
    cr.data_depunere,
    CONCAT(u.nume, ' ', u.prenume) AS ofiter_am_responsabil,
    cr.suma_solicitata,
    cr.suma_aprobata,
    (cr.suma_solicitata - COALESCE(cr.suma_aprobata, 0)) AS corectie_sau_reducere,
    ROUND(COALESCE(cr.suma_aprobata, 0) / cr.suma_solicitata * 100, 2) AS procent_validat,
    cr.stare_cr
FROM cerere_rambursare cr
JOIN proiect p ON cr.id_proiect = p.id_proiect
LEFT JOIN utilizator u ON cr.id_ofiter = u.id_user;

-- VIEW 7: Profilul organizatiilor partenere si implicarea in consortii
CREATE OR REPLACE VIEW _centralizator_parteneri AS
SELECT 
    o.id_org,
    o.denumire,
    o.cui,
    o.tip_organizatie,
    COUNT(DISTINCT p_lider.id_proiect) AS numar_proiecte_lider,
    COUNT(DISTINCT pp.id_proiect) AS numar_proiecte_partener,
    (COUNT(DISTINCT p_lider.id_proiect) + COUNT(DISTINCT pp.id_proiect)) AS total_implicari_consortii
FROM organizatie o
LEFT JOIN proiect p_lider ON o.id_org = p_lider.id_org_lider
LEFT JOIN proiect_partener pp ON o.id_org = pp.id_org
GROUP BY o.id_org;

-- VIEW 8: Pista de audit - Documente justificative ajustate la verificare
CREATE OR REPLACE VIEW _documente_ajustate_audit AS
SELECT 
    dj.id_doc,
    p.acronim AS acronim_proiect,
    cr.numar_cerere,
    dj.tip_document,
    dj.numar_document,
    dj.data_emiterii,
    lb.categorie_cheltuiala,
    dj.valoare_totala,
    dj.valoare_eligibila_solicitata,
    dj.valoare_aprobata,
    (dj.valoare_eligibila_solicitata - dj.valoare_aprobata) AS cheltuiala_taiata_neeligibila
FROM document_justificativ dj
JOIN cerere_rambursare cr ON dj.id_cr = cr.id_cr
JOIN proiect p ON cr.id_proiect = p.id_proiect
JOIN linie_bugetara lb ON dj.id_linie = lb.id_linie
WHERE dj.valoare_aprobata IS NOT NULL 
  AND dj.valoare_aprobata < dj.valoare_eligibila_solicitata;

-- VIEW 9: Sistem de alerta timpurie pentru termene depasite
CREATE OR REPLACE VIEW _alerte_termene_depasite AS
SELECT 
    'Activitate' AS tip_element,
    p.acronim AS acronim_proiect,
    a.numar_ordine AS identificator,
    a.titlu AS denumire,
    a.data_sfarsit AS data_scadenta,
    CONCAT(a.procent_realizat, '% realizat') AS stare_curenta,
    DATEDIFF(CURRENT_DATE(), a.data_sfarsit) AS zile_depasire
FROM ctivitate a
JOIN proiect p ON a.id_proiect = p.id_proiect
WHERE a.procent_realizat < 100 AND a.data_sfarsit < CURRENT_DATE()
UNION ALL
SELECT 
    'Livrabil' AS tip_element,
    p.acronim AS acronim_proiect,
    CONCAT('L-', l.id_livrabil) AS identificator,
    l.denumire AS denumire,
    l.termen_asumat AS data_scadenta,
    l.stare AS stare_curenta,
    DATEDIFF(CURRENT_DATE(), l.termen_asumat) AS zile_depasire
FROM livrabil l
JOIN ctivitate a ON l.id_act = a.id_act
JOIN proiect p ON a.id_proiect = p.id_proiect
WHERE l.stare IN ('In_Lucru', 'Respins') AND l.termen_asumat < CURRENT_DATE();

-- VIEW 10: Raport de incarcare si performanta operativa ofiteri AM
CREATE OR REPLACE VIEW _incarcare_ofiteri_am AS
SELECT 
    u.id_user AS id_ofiter,
    CONCAT(u.nume, ' ', u.prenume) AS nume_ofiter,
    u.email,
    COUNT(cr.id_cr) AS total_cereri_alocate,
    SUM(CASE WHEN cr.stare_cr = 'Aprobata' THEN 1 ELSE 0 END) AS cereri_finalizate_aprobate,
    SUM(CASE WHEN cr.stare_cr = 'In_Verificare' THEN 1 ELSE 0 END) AS cereri_in_lucru,
    COALESCE(SUM(cr.suma_aprobata), 0) AS volum_financiar_aprobat
FROM utilizator u
LEFT JOIN cerere_rambursare cr ON u.id_user = cr.id_ofiter
WHERE u.rol = 'Ofiter_AM'
GROUP BY u.id_user;


-- =============================================================================
-- SECTIUNEA 6: SELECT-URI COMPLEXE (RAPOARTE ANALITICE SI LOGICA DE BUSINESS)
-- =============================================================================

-- SELECT 1: Raport avansat de risc - Proiecte cu ritm scazut de implementare
-- Interogare pentru comisia de monitorizare: filtreaza proiectele aflate in implementare
-- cu progres fizic mediu al activitatilor sub 75%.
SELECT 
    p.id_proiect,
    p.acronim,
    p.durata_luni,
    ROUND(AVG(a.procent_realizat), 2) AS progres_fizic_mediu,
    COUNT(a.id_act) AS numar_activitati,
    SUM(CASE WHEN a.procent_realizat = 100 THEN 1 ELSE 0 END) AS activitati_finalizate
FROM proiect p
JOIN ctivitate a ON p.id_proiect = a.id_proiect
WHERE p.stare = 'Implementare'
GROUP BY p.id_proiect, p.acronim, p.durata_luni
HAVING progres_fizic_mediu < 75.00
ORDER BY progres_fizic_mediu ASC;

-- SELECT 2: Clasament oficial per apel prin functii analitice de clasare (Window Functions)
-- Ierarhizeaza proiectele evaluate in functie de punctajul mediu obtinut, grupate pe fiecare apel.
SELECT 
    ap.cod_apel,
    p.acronim,
    p.titlu AS titlu_proiect,
    ROUND(AVG(e.punctaj_total), 2) AS medie_punctaj_evaluare,
    DENSE_RANK() OVER (PARTITION BY ap.id_apel ORDER BY AVG(e.punctaj_total) DESC) AS rang_clasament,
    CASE 
        WHEN AVG(e.punctaj_total) >= 85.00 THEN 'Propunere finantabila'
        WHEN AVG(e.punctaj_total) >= 70.00 THEN 'Lista de rezerva'
        ELSE 'Propunere respinsa'
    END AS status_selectie
FROM pel_proiect ap
JOIN proiect p ON ap.id_apel = p.id_apel
JOIN evaluare e ON p.id_proiect = e.id_proiect
GROUP BY ap.id_apel, ap.cod_apel, p.id_proiect, p.acronim, p.titlu
ORDER BY ap.cod_apel, rang_clasament;

-- SELECT 3: Matrice Pivot a structurii bugetare pe categorii de cheltuieli per proiect
-- Transpune liniile de cheltuieli pe coloane individuale (Personal, Echipamente, Servicii, Deplasari, Regie).
SELECT 
    p.acronim,
    p.valoare_totala AS buget_total_proiect,
    COALESCE(SUM(CASE WHEN lb.categorie_cheltuiala = 'Personal' THEN lb.suma_eligibila END), 0) AS buget_personal,
    COALESCE(SUM(CASE WHEN lb.categorie_cheltuiala = 'Echipamente' THEN lb.suma_eligibila END), 0) AS buget_echipamente,
    COALESCE(SUM(CASE WHEN lb.categorie_cheltuiala = 'Servicii_Subcontractate' THEN lb.suma_eligibila END), 0) AS buget_servicii,
    COALESCE(SUM(CASE WHEN lb.categorie_cheltuiala = 'Deplasari' THEN lb.suma_eligibila END), 0) AS buget_deplasari,
    COALESCE(SUM(CASE WHEN lb.categorie_cheltuiala = 'Regie' THEN lb.suma_eligibila END), 0) AS buget_regie
FROM proiect p
JOIN linie_bugetara lb ON p.id_proiect = lb.id_proiect
GROUP BY p.id_proiect, p.acronim, p.valoare_totala;

-- SELECT 4: Identificarea entitatilor partenere exclusiv pasive (subcerere NOT EXISTS)
-- Selecteaza organizatiile care participa doar ca parteneri, dar nu au depus niciun proiect ca lider.
SELECT 
    o.id_org,
    o.denumire,
    o.cui,
    o.tip_organizatie
FROM organizatie o
WHERE EXISTS (
    SELECT 1 FROM proiect_partener pp WHERE pp.id_org = o.id_org
)
AND NOT EXISTS (
    SELECT 1 FROM proiect p WHERE p.id_org_lider = o.id_org
);

-- SELECT 5: Statistica ponderii financiare a tipurilor de documente justificative
-- Calculeaza ponderea fiecarui tip de act justificativ in totalul cheltuielilor eligibile validate.
SELECT 
    dj.tip_document,
    COUNT(dj.id_doc) AS numar_documente,
    SUM(dj.valoare_totala) AS valoare_bruta_totala,
    SUM(COALESCE(dj.valoare_aprobata, 0)) AS valoare_efectiv_aprobata,
    ROUND(SUM(COALESCE(dj.valoare_aprobata, 0)) / (SELECT SUM(COALESCE(valoare_aprobata, 0)) FROM document_justificativ) * 100, 2) AS pondere_procentuala
FROM document_justificativ dj
GROUP BY dj.tip_document
ORDER BY valoare_efectiv_aprobata DESC;

-- SELECT 6: Verificare de integritate financiara MySMIS (Audit Buget Proiect vs Linii Bugetare)
-- Verifica daca suma liniilor bugetare eligibile corespunde cu valoarea totala contractata a proiectului.
SELECT 
    p.id_proiect,
    p.acronim,
    p.valoare_totala AS valoare_declarata_proiect,
    COALESCE(SUM(lb.suma_eligibila), 0) AS suma_totala_linii_eligibile,
    (p.valoare_totala - COALESCE(SUM(lb.suma_eligibila), 0)) AS diferenta_nealocata,
    CASE 
        WHEN p.valoare_totala = COALESCE(SUM(lb.suma_eligibila), 0) THEN 'Buget corelat perfect (OK)'
        ELSE 'Eroare: Liniile nu coincid cu valoarea contractata!'
    END AS status_concordanta
FROM proiect p
LEFT JOIN linie_bugetara lb ON p.id_proiect = lb.id_proiect
GROUP BY p.id_proiect, p.acronim, p.valoare_totala;

-- SELECT 7: Distributia fondurilor atrase pe tipuri de organizatii beneficiare
-- Evalueaza volumul de finantare atras in functie de forma juridica a beneficiarului lider.
SELECT 
    o.tip_organizatie,
    COUNT(DISTINCT p.id_proiect) AS proiecte_castigate_lider,
    SUM(p.valoare_totala) AS volum_financiar_gestionat,
    ROUND(AVG(p.valoare_totala), 2) AS dimensiune_medie_proiect
FROM organizatie o
JOIN proiect p ON o.id_org = p.id_org_lider
WHERE p.stare IN ('Contractat', 'Implementare', 'Finalizat')
GROUP BY o.tip_organizatie
ORDER BY volum_financiar_gestionat DESC;

-- SELECT 8: Proiectie Cash-Flow pe apeluri de finantare (agregare cronologica pe ani si luni)
-- Sintetizeaza platile aprobate pe luni calendaristice pentru monitorizarea lichiditatilor la nivel de program.
SELECT 
    ap.cod_apel,
    YEAR(cr.data_depunere) AS an_plata,
    MONTH(cr.data_depunere) AS luna_plata,
    COUNT(cr.id_cr) AS numar_cereri_platite,
    SUM(cr.suma_aprobata) AS total_fonduri_eliberate
FROM pel_proiect ap
JOIN proiect p ON ap.id_apel = p.id_apel
JOIN cerere_rambursare cr ON p.id_proiect = cr.id_proiect
WHERE cr.stare_cr = 'Aprobata'
GROUP BY ap.cod_apel, YEAR(cr.data_depunere), MONTH(cr.data_depunere)
ORDER BY an_plata, luna_plata;

-- SELECT 9: Dosarul integrat de audit al unei cereri de rambursare (Jonctiune pe 6 tabele)
-- Ofera trasabilitatea completa: Proiect -> Lider -> Cerere -> Ofiter AM -> Document -> Linie Buget.
SELECT 
    p.acronim AS acronim_proiect,
    org.denumire AS beneficiar_lider,
    cr.numar_cerere,
    cr.data_depunere,
    CONCAT(u.nume, ' ', u.prenume) AS ofiter_verificator,
    dj.tip_document,
    dj.numar_document,
    lb.categorie_cheltuiala,
    dj.valoare_eligibila_solicitata,
    dj.valoare_aprobata
FROM cerere_rambursare cr
JOIN proiect p ON cr.id_proiect = p.id_proiect
JOIN organizatie org ON p.id_org_lider = org.id_org
JOIN utilizator u ON cr.id_ofiter = u.id_user
JOIN document_justificativ dj ON cr.id_cr = dj.id_cr
JOIN linie_bugetara lb ON dj.id_linie = lb.id_linie
WHERE cr.id_cr = 1
ORDER BY dj.valoare_aprobata DESC;

-- SELECT 10: Raport statistic privind exigenta si performanta evaluatorilor
-- Calculeaza media notelor, deviatia minim/maxim si rata de promovabilitate per evaluator independent.
SELECT 
    u.id_user,
    CONCAT(u.nume, ' ', u.prenume) AS evaluator,
    org.denumire AS institutie,
    COUNT(e.id_eval) AS dosare_evaluate,
    ROUND(AVG(e.punctaj_total), 2) AS punctaj_mediu_acordat,
    MIN(e.punctaj_total) AS punctaj_minim,
    MAX(e.punctaj_total) AS punctaj_maxim,
    SUM(CASE WHEN e.decizie = 'Admis' THEN 1 ELSE 0 END) AS propuneri_admise,
    SUM(CASE WHEN e.decizie = 'Respins' THEN 1 ELSE 0 END) AS propuneri_respinse,
    SUM(CASE WHEN e.decizie = 'Clarificari' THEN 1 ELSE 0 END) AS propuneri_clarificari
FROM evaluare e
JOIN utilizator u ON e.id_evaluator = u.id_user
JOIN organizatie org ON u.id_org = org.id_org
GROUP BY u.id_user, u.nume, u.prenume, org.denumire;

COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

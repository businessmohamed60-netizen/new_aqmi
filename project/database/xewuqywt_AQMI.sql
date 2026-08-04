-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 31 juil. 2026 à 12:14
-- Version du serveur : 10.11.18-MariaDB-cll-lve
-- Version de PHP : 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `xewuqywt_AQMI`
--

-- --------------------------------------------------------

--
-- Structure de la table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `status` enum('in_progress','completed') DEFAULT 'in_progress',
  `current_step` int(11) DEFAULT 0,
  `total_score` decimal(10,2) DEFAULT NULL,
  `maturity_level` varchar(50) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `assessments`
--

INSERT INTO `assessments` (`id`, `user_id`, `session_id`, `status`, `current_step`, `total_score`, `maturity_level`, `started_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 3, '0223c28cd734429b0fac7027c6605318', 'in_progress', 0, 0.00, 'Beginner', '2026-07-24 16:02:26', NULL, '2026-07-24 16:02:26', '2026-07-24 20:12:21'),
(2, 3, '85ccc96431573c179297bb016fc1d3de', 'in_progress', 0, NULL, NULL, '2026-07-24 17:45:47', NULL, '2026-07-24 17:45:47', '2026-07-24 17:45:47'),
(3, 3, '2c5033861275c11cbb24bae153c61d88', 'in_progress', 0, NULL, NULL, '2026-07-24 19:28:35', NULL, '2026-07-24 19:28:35', '2026-07-24 19:28:35'),
(4, NULL, 'f94a3383738f0473a807839e0c30f778', 'in_progress', 0, NULL, NULL, '2026-07-24 20:44:43', NULL, '2026-07-24 20:44:43', '2026-07-24 20:44:43'),
(5, NULL, '40644ea796d685af10a896366bd12f73', 'in_progress', 0, NULL, NULL, '2026-07-27 01:02:04', NULL, '2026-07-27 01:02:04', '2026-07-27 01:02:04'),
(6, NULL, 'b65bdc49a49590aae6397b0b695fd9b8', 'in_progress', 0, NULL, NULL, '2026-07-27 14:39:06', NULL, '2026-07-27 14:39:06', '2026-07-27 14:39:06'),
(7, 3, '0bd7fa52391d8a94a0df14291ae74268', 'completed', 0, 79.60, 'Performing', '2026-07-27 19:15:49', '2026-07-27 20:42:40', '2026-07-27 19:15:49', '2026-07-27 20:42:40'),
(8, NULL, '294efd2fb9ca9c0ffe3ad3eb3932a943', 'in_progress', 0, NULL, NULL, '2026-07-27 21:32:27', NULL, '2026-07-27 21:32:27', '2026-07-27 21:32:27'),
(9, NULL, '550cd9dd2aff421010d274ace93db756', 'in_progress', 0, NULL, NULL, '2026-07-28 03:22:44', NULL, '2026-07-28 03:22:44', '2026-07-28 03:22:44'),
(10, NULL, 'abd497c4eac2e01029f7829b249b210c', 'in_progress', 0, NULL, NULL, '2026-07-28 20:13:21', NULL, '2026-07-28 20:13:21', '2026-07-28 20:13:21'),
(11, 3, '6808bfdc8f466f3cac6da7f9dcd8bd03', 'in_progress', 0, NULL, NULL, '2026-07-28 21:36:02', NULL, '2026-07-28 21:36:02', '2026-07-28 21:36:02'),
(12, 3, '33c8f07040828b66c25b40734ec3f4df', 'in_progress', 0, NULL, NULL, '2026-07-29 08:29:37', NULL, '2026-07-29 08:29:37', '2026-07-29 08:29:37'),
(13, NULL, 'd1d7b092998390bfc0673765312317ae', 'in_progress', 0, NULL, NULL, '2026-07-30 14:41:47', NULL, '2026-07-30 14:41:47', '2026-07-30 14:41:47'),
(14, NULL, 'f654c0f57108fd9dbebe2bd2c59ca7ea', 'in_progress', 0, NULL, NULL, '2026-07-30 16:35:45', NULL, '2026-07-30 16:35:45', '2026-07-30 16:35:45');

-- --------------------------------------------------------

--
-- Structure de la table `assessment_answers`
--

CREATE TABLE `assessment_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `score` int(11) DEFAULT NULL CHECK (`score` >= 0 and `score` <= 5),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `assessment_answers`
--

INSERT INTO `assessment_answers` (`id`, `assessment_id`, `question_id`, `score`, `created_at`) VALUES
(1, 7, 1, 4, '2026-07-27 20:40:10'),
(2, 7, 2, 4, '2026-07-27 20:40:13'),
(3, 7, 3, 4, '2026-07-27 20:40:15'),
(4, 7, 4, 4, '2026-07-27 20:40:18'),
(5, 7, 5, 4, '2026-07-27 20:40:20'),
(6, 7, 6, 4, '2026-07-27 20:40:23'),
(7, 7, 7, 4, '2026-07-27 20:40:26'),
(8, 7, 8, 4, '2026-07-27 20:40:29'),
(9, 7, 9, 5, '2026-07-27 20:40:32'),
(10, 7, 10, 4, '2026-07-27 20:40:34'),
(11, 7, 11, 4, '2026-07-27 20:40:37'),
(12, 7, 12, 4, '2026-07-27 20:40:41'),
(13, 7, 13, 4, '2026-07-27 20:40:44'),
(14, 7, 14, 3, '2026-07-27 20:40:47'),
(15, 7, 15, 4, '2026-07-27 20:40:50'),
(16, 7, 16, 4, '2026-07-27 20:40:54'),
(17, 7, 17, 4, '2026-07-27 20:40:57'),
(18, 7, 18, 2, '2026-07-27 20:41:00'),
(19, 7, 19, 4, '2026-07-27 20:41:03'),
(20, 7, 20, 4, '2026-07-27 20:41:05'),
(21, 7, 21, 5, '2026-07-27 20:41:10'),
(22, 7, 22, 4, '2026-07-27 20:41:13'),
(23, 7, 23, 4, '2026-07-27 20:41:16'),
(24, 7, 24, 5, '2026-07-27 20:41:18'),
(25, 7, 25, 1, '2026-07-27 20:41:21'),
(26, 7, 26, 4, '2026-07-27 20:41:24'),
(27, 7, 27, 4, '2026-07-27 20:41:27'),
(28, 7, 28, 4, '2026-07-27 20:41:30'),
(29, 7, 29, 4, '2026-07-27 20:41:32'),
(30, 7, 30, 4, '2026-07-27 20:41:35'),
(31, 7, 31, 5, '2026-07-27 20:41:39'),
(32, 7, 32, 4, '2026-07-27 20:41:41'),
(33, 7, 33, 4, '2026-07-27 20:41:44'),
(34, 7, 34, 4, '2026-07-27 20:41:47'),
(35, 7, 35, 4, '2026-07-27 20:41:49'),
(36, 7, 36, 4, '2026-07-27 20:41:53'),
(37, 7, 37, 2, '2026-07-27 20:41:56'),
(38, 7, 38, 4, '2026-07-27 20:41:59'),
(39, 7, 39, 5, '2026-07-27 20:42:02'),
(40, 7, 40, 4, '2026-07-27 20:42:04'),
(41, 7, 41, 4, '2026-07-27 20:42:08'),
(42, 7, 42, 4, '2026-07-27 20:42:11'),
(43, 7, 43, 4, '2026-07-27 20:42:14'),
(44, 7, 44, 5, '2026-07-27 20:42:17'),
(45, 7, 45, 4, '2026-07-27 20:42:19'),
(46, 7, 46, 4, '2026-07-27 20:42:23'),
(47, 7, 47, 4, '2026-07-27 20:42:27'),
(48, 7, 48, 4, '2026-07-27 20:42:30'),
(49, 7, 49, 4, '2026-07-27 20:42:33'),
(50, 7, 50, 5, '2026-07-27 20:42:36'),
(51, 12, 1, 5, '2026-07-29 09:21:26'),
(52, 12, 2, 4, '2026-07-29 09:21:36');

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `domains`
--

CREATE TABLE `domains` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_fr` varchar(100) DEFAULT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_fr` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `weight` decimal(5,2) DEFAULT 1.00,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `domains`
--

INSERT INTO `domains` (`id`, `name`, `name_fr`, `name_ar`, `description`, `description_fr`, `description_ar`, `icon`, `weight`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Quality Governance', 'Gouvernance Qualité', 'حوكمة الجودة', 'Management commitment, quality policy, objectives, and quality management system', 'Engagement de la direction, politique qualité, objectifs et système de management de la qualité', 'التزام الإدارة، سياسة الجودة، الأهداف ونظام إدارة الجودة', 'fa-shield-alt', 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'Risk Management', 'Gestion des Risques', 'إدارة المخاطر', 'Risk identification, analysis, evaluation and treatment processes', 'Processus d\'identification, d\'analyse, d\'évaluation et de traitement des risques', 'عمليات تحديد وتحليل وتقييم ومعالجة المخاطر', 'fa-exclamation-triangle', 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'Non-Conformities and CAPA', 'Non-Conformités et CAPA', 'عدم المطابقة والإجراءات التصحيحية', 'Non-conformity management, root cause analysis, corrective and preventive actions', 'Gestion des non-conformités, analyse des causes racines, actions correctives et préventives', 'إدارة عدم المطابقة، تحليل الأسباب الجذرية، الإجراءات التصحيحية والوقائية', 'fa-clipboard-check', 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 'Audit and Compliance', 'Audit et Conformité', 'التدقيق والامتثال', 'Internal audit program, audit planning, execution, reporting and follow-up', 'Programme d\'audit interne, planification, réalisation, rapport et suivi des audits', 'برنامج التدقيق الداخلي، التخطيط والتنفيذ وإعداد التقارير والمتابعة', 'fa-search', 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 'Production', 'Production', 'الإنتاج', 'Production process control, quality at source, process capability and performance', 'Maîtrise des processus de production, qualité à la source, capabilité et performance', 'التحكم في عمليات الإنتاج، الجودة في المصدر، قدرة وأداء العملية', 'fa-industry', 1.20, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(6, 'Maintenance', 'Maintenance', 'الصيانة', 'Preventive and predictive maintenance, spare parts management, equipment effectiveness', 'Maintenance préventive et prédictive, gestion des pièces de rechange, efficacité des équipements', 'الصيانة الوقائية والتنبؤية، إدارة قطع الغيار، فعالية المعدات', 'fa-wrench', 1.00, 6, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(7, 'Supply Chain', 'Supply Chain', 'سلسلة التوريد', 'Supplier management, logistics, inventory management and supply chain performance', 'Gestion des fournisseurs, logistique, gestion des stocks et performance supply chain', 'إدارة الموردين، اللوجستيات، إدارة المخزون وأداء سلسلة التوريد', 'fa-truck', 1.00, 7, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(8, 'Human Resources', 'Ressources Humaines', 'الموارد البشرية', 'Competence management, training, awareness, and organizational culture', 'Gestion des compétences, formation, sensibilisation et culture organisationnelle', 'إدارة الكفاءات، التدريب، التوعية والثقافة التنظيمية', 'fa-users', 0.80, 8, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(9, 'Continuous Improvement', 'Amélioration Continue', 'التحسين المستمر', 'Kaizen, Lean, Six Sigma, problem-solving methodologies and innovation', 'Kaizen, Lean, Six Sigma, méthodologies de résolution de problèmes et innovation', 'كايزن، لين، ستة سيغما، منهجيات حل المشكلات والابتكار', 'fa-chart-line', 1.00, 9, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(10, 'Digitalization', 'Digitalisation', 'الرقمنة', 'Digital tools, Industry 4.0, data analytics and connected systems', 'Outils digitaux, Industrie 4.0, analyse de données et systèmes connectés', 'الأدوات الرقمية، الصناعة 4.0، تحليل البيانات والأنظمة المتصلة', 'fa-laptop-code', 0.80, 10, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `evaluation_models`
--

CREATE TABLE `evaluation_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_fr` varchar(255) DEFAULT '',
  `name_ar` varchar(255) DEFAULT '',
  `description` text DEFAULT NULL,
  `description_fr` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-clipboard-check',
  `color` varchar(20) DEFAULT '#7367f0',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluation_models`
--

INSERT INTO `evaluation_models` (`id`, `name`, `name_fr`, `name_ar`, `description`, `description_fr`, `description_ar`, `icon`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'IATF 16949', 'IATF 16949', '', NULL, NULL, NULL, 'fa-industry', '#7367f0', 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'VDA 6.3', 'VDA 6.3', '', NULL, NULL, NULL, 'fa-cogs', '#28c76f', 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'ISO 9001', 'ISO 9001', '', NULL, NULL, NULL, 'fa-certificate', '#ff9f43', 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `assessment_id` int(10) UNSIGNED DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `company` varchar(255) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `company_size` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `certifications` varchar(255) DEFAULT NULL,
  `founded_year` varchar(4) DEFAULT NULL,
  `production_type` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `leads`
--

INSERT INTO `leads` (`id`, `assessment_id`, `firstname`, `lastname`, `company`, `sector`, `job_title`, `phone`, `email`, `country`, `company_size`, `website`, `certifications`, `founded_year`, `production_type`, `notes`, `created_at`, `updated_at`) VALUES
(1, 7, 'Your Talent visa', 'job', 'Your Talent Visa Ltd', 'Aéronautique', 'dg', '+213781358536', 'business.mohamed60@gmail.com', 'Royaume-Uni', '1-10', 'www.techcorp-solutions.fr', '', '2010', '', '', '2026-07-27 20:57:45', '2026-07-27 20:57:45');

-- --------------------------------------------------------

--
-- Structure de la table `lead_custom_fields`
--

CREATE TABLE `lead_custom_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `field_type` varchar(50) DEFAULT 'text',
  `is_required` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lead_documents`
--

CREATE TABLE `lead_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lead_field_values`
--

CREATE TABLE `lead_field_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login_date` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `operating_system` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `result` enum('success','failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `email`, `login_date`, `ip_address`, `browser`, `operating_system`, `country`, `city`, `result`) VALUES
(1, 3, 'gmove05@gmail.com', '2026-07-24 17:45:35', '105.97.19.214', 'Chrome', 'Windows', NULL, NULL, 'success'),
(2, 3, 'gmove05@gmail.com', '2026-07-24 19:25:02', '105.97.19.214', 'Chrome', 'Windows', NULL, NULL, 'success'),
(3, 3, 'gmove05@gmail.com', '2026-07-25 13:46:33', '105.102.227.199', NULL, NULL, NULL, NULL, 'success'),
(4, 3, 'gmove05@gmail.com', '2026-07-25 14:28:30', '105.102.227.199', 'Chrome', 'Windows', NULL, NULL, 'success'),
(5, 3, 'gmove05@gmail.com', '2026-07-26 08:32:55', '105.96.142.38', 'Chrome', 'Windows', NULL, NULL, 'success'),
(6, 3, 'gmove05@gmail.com', '2026-07-26 17:34:45', '105.97.82.0', 'Chrome', 'Windows', NULL, NULL, 'success'),
(7, 3, 'gmove05@gmail.com', '2026-07-27 07:31:31', '129.45.66.88', 'Chrome', 'Linux', NULL, NULL, 'success'),
(8, 3, 'gmove05@gmail.com', '2026-07-27 19:15:35', '105.98.204.116', 'Chrome', 'Windows', NULL, NULL, 'success'),
(9, 3, 'gmove05@gmail.com', '2026-07-27 19:54:39', '105.98.204.116', 'Chrome', 'Windows', NULL, NULL, 'success'),
(10, 3, 'gmove05@gmail.com', '2026-07-27 20:39:40', '105.98.204.116', 'Chrome', 'Windows', NULL, NULL, 'success'),
(11, 3, 'gmove05@gmail.com', '2026-07-28 21:35:25', '105.102.91.2', 'Chrome', 'Linux', NULL, NULL, 'success'),
(12, 3, 'gmove05@gmail.com', '2026-07-29 08:18:20', '105.100.131.41', 'Chrome', 'Windows', NULL, NULL, 'success'),
(13, 4, 'business.mohamed60@gmail.com', '2026-07-29 19:41:32', '105.98.234.85', 'Chrome', 'Windows', NULL, NULL, 'success'),
(14, 4, 'business.mohamed60@gmail.com', '2026-07-29 21:12:37', '105.98.234.85', 'Chrome', 'Windows', NULL, NULL, 'success'),
(15, 4, 'business.mohamed60@gmail.com', '2026-07-30 07:28:49', '129.45.28.19', 'Chrome', 'Linux', NULL, NULL, 'success'),
(16, 3, 'gmove05@gmail.com', '2026-07-30 11:06:57', '154.252.236.214', 'Firefox', 'Windows', NULL, NULL, 'success'),
(17, 4, 'business.mohamed60@gmail.com', '2026-07-30 11:25:11', '154.252.236.214', 'Chrome', 'Linux', NULL, NULL, 'success');

-- --------------------------------------------------------

--
-- Structure de la table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `email`, `ip_address`, `status`, `created_at`) VALUES
(1, NULL, 'admin@aqmi.com', '105.97.19.214', 'success', '2026-07-24 15:31:33'),
(2, 3, 'gmove05@gmail.com', '105.97.19.214', 'success', '2026-07-24 16:02:16'),
(3, NULL, 'admin@aqmi.com', '105.97.19.214', 'success', '2026-07-24 19:29:26'),
(4, 3, 'gmove05@gmail.com', '105.102.227.199', 'success', '2026-07-25 13:46:33'),
(5, NULL, 'admin@aqmi.com', '105.98.204.116', 'success', '2026-07-27 21:05:48'),
(6, NULL, 'admin@aqmi.com', '105.98.204.116', 'success', '2026-07-27 21:25:11'),
(7, NULL, 'admin@aqmi.com', '105.98.204.116', 'success', '2026-07-27 21:48:50');

-- --------------------------------------------------------

--
-- Structure de la table `model_domains`
--

CREATE TABLE `model_domains` (
  `model_id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expire_at` datetime NOT NULL,
  `attempts` tinyint(3) UNSIGNED DEFAULT 0,
  `used` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `otp_code`, `created_at`, `expire_at`, `attempts`, `used`, `ip_address`, `browser`, `device`) VALUES
(4, 3, '391890', '2026-07-24 17:11:21', '2026-07-24 18:16:21', 0, 1, '105.97.19.214', 'Chrome', ''),
(5, 3, '941271', '2026-07-24 17:11:49', '2026-07-24 18:16:49', 0, 1, '105.97.19.214', 'Chrome', ''),
(6, 3, '085892', '2026-07-24 17:13:15', '2026-07-24 18:18:15', 0, 1, '105.97.19.214', 'Chrome', ''),
(7, 3, '036473', '2026-07-24 17:28:26', '2026-07-24 18:33:26', 0, 1, '105.97.19.214', 'Chrome', ''),
(8, 3, '191543', '2026-07-24 17:32:33', '2026-07-24 18:37:33', 0, 1, '105.97.19.214', 'Chrome', ''),
(9, 3, '665129', '2026-07-24 17:45:13', '2026-07-24 18:50:13', 0, 1, '105.97.19.214', 'Chrome', ''),
(10, 3, '956636', '2026-07-24 17:45:19', '2026-07-24 18:50:19', 2, 1, '105.97.19.214', 'Chrome', ''),
(11, 3, '372497', '2026-07-24 19:24:41', '2026-07-24 20:29:41', 2, 1, '105.97.19.214', 'Chrome', ''),
(12, 3, '283494', '2026-07-25 14:26:51', '2026-07-25 15:31:51', 0, 1, '105.102.227.199', 'Chrome', ''),
(13, 3, '586883', '2026-07-25 14:28:01', '2026-07-25 15:33:01', 2, 1, '105.102.227.199', 'Chrome', ''),
(14, 3, '596564', '2026-07-26 08:32:34', '2026-07-26 09:37:34', 2, 1, '105.96.142.38', 'Chrome', ''),
(15, 3, '025122', '2026-07-26 17:33:59', '2026-07-26 18:38:59', 0, 1, '105.97.82.0', 'Chrome', ''),
(16, 3, '021099', '2026-07-26 17:34:31', '2026-07-26 18:39:31', 2, 1, '105.97.82.0', 'Chrome', ''),
(17, 3, '598467', '2026-07-27 07:30:45', '2026-07-27 08:35:45', 2, 1, '129.45.66.88', 'Chrome', ''),
(18, 3, '985909', '2026-07-27 19:14:57', '2026-07-27 20:19:57', 0, 1, '105.98.204.116', 'Chrome', ''),
(19, 3, '894940', '2026-07-27 19:15:18', '2026-07-27 20:20:18', 2, 1, '105.98.204.116', 'Chrome', ''),
(20, 3, '041442', '2026-07-27 19:49:04', '2026-07-27 20:54:04', 0, 1, '105.98.204.116', 'Chrome', ''),
(21, 3, '677622', '2026-07-27 19:49:38', '2026-07-27 20:54:38', 0, 1, '105.98.204.116', 'Chrome', ''),
(22, 3, '158709', '2026-07-27 19:50:00', '2026-07-27 20:55:00', 0, 1, '105.98.204.116', 'Chrome', ''),
(23, 3, '183332', '2026-07-27 19:50:36', '2026-07-27 20:55:36', 0, 1, '105.98.204.116', 'Chrome', ''),
(24, 3, '167754', '2026-07-27 19:52:36', '2026-07-27 20:57:36', 0, 1, '105.98.204.116', 'Chrome', ''),
(25, 3, '559946', '2026-07-27 19:54:21', '2026-07-27 20:59:21', 2, 1, '105.98.204.116', 'Chrome', ''),
(26, 3, '143742', '2026-07-27 20:39:02', '2026-07-27 21:44:02', 0, 1, '105.98.204.116', 'Chrome', ''),
(27, 3, '515246', '2026-07-27 20:39:21', '2026-07-27 21:44:21', 2, 1, '105.98.204.116', 'Chrome', ''),
(28, 3, '941725', '2026-07-28 21:34:56', '2026-07-28 22:39:56', 2, 1, '105.102.91.2', 'Chrome', ''),
(29, 3, '077001', '2026-07-29 08:17:43', '2026-07-29 09:22:43', 0, 1, '105.100.131.41', 'Chrome', ''),
(30, 3, '572647', '2026-07-29 08:18:04', '2026-07-29 09:23:04', 2, 1, '105.100.131.41', 'Chrome', ''),
(31, 4, '035538', '2026-07-29 19:41:12', '2026-07-29 20:46:12', 2, 1, '105.98.234.85', 'Chrome', ''),
(32, 4, '718707', '2026-07-29 21:12:17', '2026-07-29 22:17:17', 2, 1, '105.98.234.85', 'Chrome', ''),
(33, 4, '287235', '2026-07-30 07:28:12', '2026-07-30 08:33:12', 2, 1, '129.45.28.19', 'Chrome', ''),
(34, 3, '699765', '2026-07-30 11:06:36', '2026-07-30 12:11:36', 2, 1, '154.252.236.214', 'Firefox', ''),
(35, 4, '841732', '2026-07-30 11:24:40', '2026-07-30 12:29:40', 2, 1, '154.252.236.214', 'Chrome', '');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `expire_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Gérer les questions', 'manage_questions', 'Créer, modifier, supprimer des questions', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'Gérer les domaines', 'manage_domains', 'Créer, modifier, supprimer des domaines', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'Gérer les utilisateurs', 'manage_users', 'Créer, modifier, supprimer des utilisateurs', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 'Voir les leads', 'view_leads', 'Consulter la liste des leads', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 'Exporter les données', 'export_data', 'Exporter les données en CSV', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(6, 'Voir les rapports', 'view_reports', 'Consulter les rapports', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(7, 'Gérer les paramètres', 'manage_settings', 'Modifier les paramètres de l\'application', '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED NOT NULL,
  `model_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_fr` varchar(255) DEFAULT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_fr` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT 1.00,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `domain_id`, `model_id`, `title`, `title_fr`, `title_ar`, `description`, `description_fr`, `description_ar`, `weight`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Quality Policy', 'Politique Qualité', 'سياسة الجودة', NULL, 'La politique qualité est-elle définie, communiquée et comprise par tous ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 1, NULL, 'Quality Objectives', 'Objectifs Qualité', 'أهداف الجودة', NULL, 'Des objectifs qualité mesurables sont-ils définis et suivis régulièrement ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 1, NULL, 'Management Review', 'Revue de Direction', 'مراجعة الإدارة', NULL, 'La direction réalise-t-elle des revues périodiques du système de management ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 1, NULL, 'Documentation', 'Documentation', 'التوثيق', NULL, 'Le système documentaire est-il maîtrisé et accessible aux collaborateurs ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 1, NULL, 'Quality Culture', 'Culture Qualité', 'ثقافة الجودة', NULL, 'La culture qualité est-elle ancrée dans l\'organisation et promue par le management ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(6, 2, NULL, 'Risk Identification', 'Identification des Risques', 'تحديد المخاطر', NULL, 'Les risques qualité et opérationnels sont-ils identifiés systématiquement ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(7, 2, NULL, 'Risk Analysis', 'Analyse des Risques', 'تحليل المخاطر', NULL, 'Les risques sont-ils analysés et évalués selon leur criticité ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(8, 2, NULL, 'Risk Treatment', 'Traitement des Risques', 'معالجة المخاطر', NULL, 'Des plans de traitement des risques sont-ils définis et mis en œuvre ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(9, 2, NULL, 'Risk Monitoring', 'Surveillance des Risques', 'مراقبة المخاطر', NULL, 'Les risques sont-ils suivis et réévalués périodiquement ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(10, 2, NULL, 'Opportunity Management', 'Gestion des Opportunités', 'إدارة الفرص', NULL, 'Les opportunités d\'amélioration sont-elles identifiées et exploitées ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(11, 3, NULL, 'NC Detection', 'Détection des NC', 'كشف عدم المطابقة', NULL, 'Les non-conformités sont-elles détectées et enregistrées systématiquement ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(12, 3, NULL, 'Root Cause Analysis', 'Analyse des Causes Racines', 'تحليل الأسباب الجذرية', NULL, 'Les causes racines des non-conformités sont-elles analysées en profondeur ?', NULL, 1.20, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(13, 3, NULL, 'Corrective Actions', 'Actions Correctives', 'الإجراءات التصحيحية', NULL, 'Des actions correctives sont-elles définies et suivies jusqu\'à leur clôture ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(14, 3, NULL, 'Effectiveness Verification', 'Vérification d\'Efficacité', 'التحقق من الفعالية', NULL, 'L\'efficacité des actions correctives est-elle vérifiée ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(15, 3, NULL, 'Preventive Actions', 'Actions Préventives', 'الإجراءات الوقائية', NULL, 'Des actions préventives sont-elles déployées à partir des retours d\'expérience ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(16, 4, NULL, 'Audit Program', 'Programme d\'Audit', 'برنامج التدقيق', NULL, 'Un programme d\'audit interne annuel est-il défini et planifié ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(17, 4, NULL, 'Audit Execution', 'Réalisation des Audits', 'تنفيذ التدقيق', NULL, 'Les audits sont-ils réalisés selon le planning par des auditeurs qualifiés ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(18, 4, NULL, 'Audit Reports', 'Rapports d\'Audit', 'تقارير التدقيق', NULL, 'Les rapports d\'audit sont-ils complets et diffusés aux parties concernées ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(19, 4, NULL, 'Regulatory Compliance', 'Conformité Réglementaire', 'الامتثال التنظيمي', NULL, 'La veille réglementaire et la conformité aux exigences légales sont-elles assurées ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(20, 4, NULL, 'Auditor Competence', 'Compétence des Auditeurs', 'كفاءة المدققين', NULL, 'Les auditeurs sont-ils formés et leurs compétences maintenues à jour ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(21, 5, NULL, 'Process Control', 'Contrôle des Processus', 'التحكم في العمليات', NULL, 'Les processus de production sont-ils maîtrisés avec des paramètres définis ?', NULL, 1.20, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(22, 5, NULL, 'Quality at Source', 'Qualité à la Source', 'الجودة في المصدر', NULL, 'L\'auto-contrôle et la responsabilité qualité des opérateurs sont-ils en place ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(23, 5, NULL, 'Process Capability', 'Capabilité Processus', 'قدرة العملية', NULL, 'La capabilité des processus est-elle mesurée et améliorée ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(24, 5, NULL, 'Control Plan', 'Plan de Contrôle', 'خطة التحكم', NULL, 'Un plan de contrôle est-il défini et appliqué pour chaque produit ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(25, 5, NULL, 'Traceability', 'Traçabilité', 'قابلية التتبع', NULL, 'La traçabilité des produits et des lots est-elle assurée tout au long de la production ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(26, 6, NULL, 'Preventive Maintenance', 'Maintenance Préventive', 'الصيانة الوقائية', NULL, 'Un plan de maintenance préventive est-il défini et exécuté ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(27, 6, NULL, 'Predictive Maintenance', 'Maintenance Prédictive', 'الصيانة التنبؤية', NULL, 'Des techniques de maintenance prédictive sont-elles utilisées ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(28, 6, NULL, 'Spare Parts', 'Pièces de Rechange', 'قطع الغيار', NULL, 'La gestion des pièces de rechange est-elle optimisée ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(29, 6, NULL, 'OEE', 'TRS - Efficacité', 'الفعالية الكلية للمعدات', NULL, 'Le TRS (Taux de Rendement Synthétique) est-il mesuré et suivi ?', NULL, 1.20, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(30, 6, NULL, 'Maintenance KPIs', 'Indicateurs Maintenance', 'مؤشرات الصيانة', NULL, 'Des indicateurs de performance maintenance sont-ils suivis ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(31, 7, NULL, 'Supplier Evaluation', 'Évaluation Fournisseurs', 'تقييم الموردين', NULL, 'Les fournisseurs sont-ils évalués et qualifiés selon des critères objectifs ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(32, 7, NULL, 'Supplier Development', 'Développement Fournisseurs', 'تطوير الموردين', NULL, 'Des actions de développement fournisseurs sont-elles menées ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(33, 7, NULL, 'Inventory Management', 'Gestion des Stocks', 'إدارة المخزون', NULL, 'La gestion des stocks est-elle optimisée (rotation, just-in-time, etc.) ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(34, 7, NULL, 'Logistics', 'Logistique', 'اللوجستيات', NULL, 'Les opérations logistiques sont-elles maîtrisées et optimisées ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(35, 7, NULL, 'Supply Chain Risk', 'Risque Supply Chain', 'مخاطر سلسلة التوريد', NULL, 'Les risques supply chain sont-ils identifiés et gérés ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(36, 8, NULL, 'Competence Matrix', 'Matrice de Compétences', 'مصفوفة الكفاءات', NULL, 'Une matrice de compétences est-elle maintenue à jour pour chaque poste ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(37, 8, NULL, 'Training Plan', 'Plan de Formation', 'خطة التدريب', NULL, 'Un plan de formation annuel est-il défini et suivi ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(38, 8, NULL, 'Training Effectiveness', 'Efficacité Formation', 'فعالية التدريب', NULL, 'L\'efficacité des formations est-elle évaluée ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(39, 8, NULL, 'Employee Awareness', 'Sensibilisation', 'توعية الموظفين', NULL, 'Les collaborateurs sont-ils sensibilisés à la qualité et à l\'amélioration continue ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(40, 8, NULL, 'Communication', 'Communication', 'التواصل', NULL, 'La communication interne sur les enjeux qualité est-elle efficace ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(41, 9, NULL, 'Kaizen Culture', 'Culture Kaizen', 'ثقافة كايزن', NULL, 'Une culture d\'amélioration continue est-elle déployée dans l\'organisation ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(42, 9, NULL, 'Problem Solving', 'Résolution de Problèmes', 'حل المشكلات', NULL, 'Des méthodes structurées de résolution de problèmes sont-elles utilisées (8D, PDCA, DMAIC) ?', NULL, 1.20, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(43, 9, NULL, 'Lean Tools', 'Outils Lean', 'أدوات اللين', NULL, 'Les outils Lean sont-ils maîtrisés et appliqués (5S, SMED, TPM, VSM) ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(44, 9, NULL, 'Suggestions System', 'Système de Suggestions', 'نظام الاقتراحات', NULL, 'Un système de suggestions des employés est-il en place et encouragé ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(45, 9, NULL, 'Innovation', 'Innovation', 'الابتكار', NULL, 'L\'innovation est-elle encouragée et les bonnes pratiques sont-elles partagées ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(46, 10, NULL, 'Digital Strategy', 'Stratégie Digitale', 'الاستراتيجية الرقمية', NULL, 'Une stratégie de digitalisation est-elle définie et déployée ?', NULL, 1.00, 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(47, 10, NULL, 'Digital Tools', 'Outils Digitaux', 'الأدوات الرقمية', NULL, 'Les outils digitaux de gestion de la qualité sont-ils utilisés ?', NULL, 1.00, 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(48, 10, NULL, 'Data Analytics', 'Analyse de Données', 'تحليل البيانات', NULL, 'Les données sont-elles analysées pour piloter la performance ?', NULL, 1.00, 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(49, 10, NULL, 'Connected Systems', 'Systèmes Connectés', 'الأنظمة المتصلة', NULL, 'Les systèmes de production sont-ils connectés et les données exploitées en temps réel ?', NULL, 1.00, 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(50, 10, NULL, 'Cybersecurity', 'Cybersécurité', 'الأمن السيبراني', NULL, 'La sécurité des systèmes d\'information est-elle assurée ?', NULL, 1.00, 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED DEFAULT NULL,
  `condition_field` varchar(100) DEFAULT NULL,
  `condition_operator` enum('<','>','<=','>=','==') DEFAULT '<',
  `condition_value` decimal(5,2) DEFAULT NULL,
  `recommendation_text` text DEFAULT NULL,
  `recommendation_text_fr` text DEFAULT NULL,
  `recommendation_text_ar` text DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recommendations`
--

INSERT INTO `recommendations` (`id`, `domain_id`, `condition_field`, `condition_operator`, `condition_value`, `recommendation_text`, `recommendation_text_fr`, `recommendation_text_ar`, `priority`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'global_score', '<', 50.00, 'Define and deploy a formal quality policy with measurable objectives and regular management reviews.', 'Définir et déployer une politique qualité formelle avec des objectifs mesurables et des revues de direction régulières.', NULL, 'critical', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 2, 'global_score', '<', 40.00, 'Implement a structured risk management process including identification, analysis, treatment, and monitoring.', 'Mettre en place un processus structuré de gestion des risques incluant identification, analyse, traitement et surveillance.', NULL, 'critical', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 3, 'global_score', '<', 50.00, 'Establish a formal CAPA process with root cause analysis and effectiveness verification.', 'Mettre en place un processus CAPA formel avec analyse des causes racines et vérification d\'efficacité.', NULL, 'high', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 4, 'global_score', '<', 50.00, 'Strengthen the internal audit program with qualified auditors and complete audit reporting.', 'Renforcer le programme d\'audit interne avec des auditeurs qualifiés et des rapports d\'audit complets.', NULL, 'high', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 5, 'global_score', '<', 60.00, 'Implement process control plans and develop operator self-control culture.', 'Mettre en place des plans de contrôle processus et développer l\'auto-contrôle opérateur.', NULL, 'high', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(6, 6, 'global_score', '<', 50.00, 'Deploy a preventive maintenance plan and start measuring OEE to improve equipment effectiveness.', 'Déployer un plan de maintenance préventive et commencer à mesurer le TRS pour améliorer l\'efficacité des équipements.', NULL, 'high', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(7, 7, 'global_score', '<', 50.00, 'Structure supplier evaluation and qualification process with performance monitoring.', 'Structurer le processus d\'évaluation et de qualification des fournisseurs avec suivi de performance.', NULL, 'medium', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(8, 8, 'global_score', '<', 50.00, 'Develop a competence management system with training plans and effectiveness evaluation.', 'Développer un système de gestion des compétences avec plans de formation et évaluation d\'efficacité.', NULL, 'medium', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(9, 9, 'global_score', '<', 60.00, 'Deploy continuous improvement methodologies (Kaizen, 5S, problem-solving) across the organization.', 'Déployer les méthodologies d\'amélioration continue (Kaizen, 5S, résolution de problèmes) dans l\'organisation.', NULL, 'medium', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(10, 10, 'global_score', '<', 40.00, 'Define a digital transformation roadmap and start implementing quality management digital tools.', 'Définir une feuille de route de transformation digitale et commencer à implémenter des outils qualité digitaux.', NULL, 'medium', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(11, NULL, 'global_score', '>', 85.00, 'Maintain excellence level by sharing best practices and innovating in quality management approaches.', 'Maintenir le niveau d\'excellence en partageant les bonnes pratiques et en innovant dans les approches qualité.', NULL, 'low', 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_number` varchar(50) DEFAULT NULL,
  `assessment_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `status` enum('certification_requested','under_review','approved','rejected','certified') NOT NULL DEFAULT 'certification_requested',
  `admin_comment` text DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `action_plan` text DEFAULT NULL,
  `aqmi_level_assigned` varchar(100) DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `validated_by` varchar(255) DEFAULT NULL,
  `admin_signature` varchar(255) DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT current_timestamp(),
  `certification_requested_at` timestamp NULL DEFAULT NULL,
  `certified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `report_number`, `assessment_id`, `lead_id`, `file_path`, `qr_code_path`, `status`, `admin_comment`, `observations`, `action_plan`, `aqmi_level_assigned`, `validated_at`, `validated_by`, `admin_signature`, `generated_at`, `certification_requested_at`, `certified_at`, `created_at`, `updated_at`) VALUES
(1, 'AQMI-2026-000001', 7, 1, 'certificat_AQMI_AQMI-2026-000001_20260730_112533.pdf', NULL, 'certified', '', '', '', 'Performant', '2026-07-30 11:25:33', 'Mohamed BENSAFI', 'Mohamed BENSAFI', '2026-07-27 20:57:45', '2026-07-27 20:57:45', '2026-07-30 11:25:33', '2026-07-27 20:57:45', '2026-07-30 11:25:33');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrateur', 'super_admin', 'Accès complet à toutes les fonctionnalités', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'Administrateur', 'admin', 'Accès à la gestion des contenus', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'Consultant', 'consultant', 'Accès aux évaluations et rapports', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 'Lecteur', 'reader', 'Accès en lecture seule', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 'Client', 'client', 'Espace client - accès à ses évaluations', '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `score_levels`
--

CREATE TABLE `score_levels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_fr` varchar(100) DEFAULT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `min_percent` decimal(5,2) NOT NULL,
  `max_percent` decimal(5,2) NOT NULL,
  `color` varchar(20) DEFAULT '#6c757d',
  `icon` varchar(50) DEFAULT 'fa-chart-bar',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `score_levels`
--

INSERT INTO `score_levels` (`id`, `name`, `name_fr`, `name_ar`, `min_percent`, `max_percent`, `color`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Beginner', 'Débutant', 'مبتدئ', 0.00, 30.00, '#6c757d', 'fa-flag', 1, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'Developing', 'En Développement', 'قيد التطوير', 31.00, 50.00, '#fd7e14', 'fa-chart-bar', 2, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'Structured', 'Structuré', 'منظم', 51.00, 70.00, '#1a56db', 'fa-layer-group', 3, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 'Performing', 'Performant', 'متميز', 71.00, 85.00, '#059669', 'fa-trophy', 4, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 'Excellence', 'Excellence', 'امتياز', 86.00, 100.00, '#d97706', 'fa-crown', 5, 1, '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'AQMI', 'string', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(2, 'app_description', 'Automotive Quality Maturity Index - Plateforme d\'évaluation de la maturité qualité', 'string', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 'admin_email', 'admin@aqmi.com', 'string', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(4, 'items_per_page', '20', 'integer', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(5, 'default_language', 'fr', 'string', '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(6, 'score_calculation_method', 'weighted', 'string', '2026-07-24 15:30:13', '2026-07-24 15:30:13');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `role_id`, `firstname`, `lastname`, `email`, `password`, `phone`, `avatar`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(2, 5, 'Jean', 'Dupont', 'client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 1, NULL, '2026-07-24 15:30:13', '2026-07-24 15:30:13'),
(3, 5, 'Mohammed', 'BENSAFI', 'gmove05@gmail.com', '$2y$12$IHKJMPEvlQKT98DqhlkHRu4egQTyebVgW4y3PnVJJu//b4ZxWLsXa', '+213781358536', NULL, 1, '2026-07-30 11:06:57', '2026-07-24 16:02:16', '2026-07-30 11:06:57'),
(4, 1, 'Mohamed', 'BENSAFI', 'business.mohamed60@gmail.com', '$2y$10$nB45sceyb0mO9.5JFxhy4et4Z3cr2ITq6fcle/7lqeWOELkB6U0Fa', NULL, NULL, 1, '2026-07-30 11:25:11', '2026-07-29 19:10:26', '2026-07-30 11:25:11');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_assessments_session` (`session_id`),
  ADD KEY `idx_assessments_status` (`status`);

--
-- Index pour la table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_assessment_question` (`assessment_id`,`question_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `idx_answers_assessment` (`assessment_id`);

--
-- Index pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_logs_user` (`user_id`),
  ADD KEY `idx_audit_logs_action` (`action`);

--
-- Index pour la table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evaluation_models`
--
ALTER TABLE `evaluation_models`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assessment_id` (`assessment_id`),
  ADD KEY `idx_leads_email` (`email`),
  ADD KEY `idx_leads_company` (`company`);

--
-- Index pour la table `lead_custom_fields`
--
ALTER TABLE `lead_custom_fields`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `lead_documents`
--
ALTER TABLE `lead_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Index pour la table `lead_field_values`
--
ALTER TABLE `lead_field_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_lead_field` (`lead_id`,`field_id`),
  ADD KEY `idx_lead` (`lead_id`),
  ADD KEY `idx_field` (`field_id`);

--
-- Index pour la table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_history_user` (`user_id`),
  ADD KEY `idx_history_date` (`login_date`),
  ADD KEY `idx_history_result` (`result`);

--
-- Index pour la table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `model_domains`
--
ALTER TABLE `model_domains`
  ADD PRIMARY KEY (`model_id`,`domain_id`),
  ADD KEY `fk_model_domains_domain` (`domain_id`);

--
-- Index pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_otp_user` (`user_id`),
  ADD KEY `idx_otp_code` (`otp_code`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reset_user` (`user_id`),
  ADD KEY `idx_reset_token` (`token`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_questions_domain` (`domain_id`),
  ADD KEY `idx_model_id` (`model_id`);

--
-- Index pour la table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recommendations_domain` (`domain_id`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_number` (`report_number`),
  ADD KEY `assessment_id` (`assessment_id`),
  ADD KEY `lead_id` (`lead_id`),
  ADD KEY `idx_reports_status` (`status`),
  ADD KEY `idx_reports_number` (`report_number`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Index pour la table `score_levels`
--
ALTER TABLE `score_levels`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `idx_users_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `evaluation_models`
--
ALTER TABLE `evaluation_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `lead_custom_fields`
--
ALTER TABLE `lead_custom_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `lead_documents`
--
ALTER TABLE `lead_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `lead_field_values`
--
ALTER TABLE `lead_field_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `score_levels`
--
ALTER TABLE `score_levels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  ADD CONSTRAINT `assessment_answers_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `lead_documents`
--
ALTER TABLE `lead_documents`
  ADD CONSTRAINT `lead_documents_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `lead_field_values`
--
ALTER TABLE `lead_field_values`
  ADD CONSTRAINT `fk_lfv_field` FOREIGN KEY (`field_id`) REFERENCES `lead_custom_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lfv_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `model_domains`
--
ALTER TABLE `model_domains`
  ADD CONSTRAINT `fk_model_domains_domain` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_model_domains_model` FOREIGN KEY (`model_id`) REFERENCES `evaluation_models` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_model` FOREIGN KEY (`model_id`) REFERENCES `evaluation_models` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 04 août 2026 à 20:06
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
(1, 3, 'fad1d438f1544fa752338f6d64a4ca2c', 'completed', 0, 0.00, 'Beginner', '2026-07-31 22:29:57', '2026-07-31 22:33:11', '2026-07-31 22:29:57', '2026-07-31 22:33:11'),
(2, 3, '22a9fd96eb04fd87f334e1ebd3da8ee8', 'completed', 0, 0.00, 'Beginner', '2026-08-04 16:22:05', '2026-08-04 17:22:43', '2026-08-04 16:22:05', '2026-08-04 17:22:43');

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
(1, 'Quality Governance', 'Gouvernance Qualité', 'حوكمة الجودة', 'Management commitment, quality policy, objectives, and quality management system', 'Engagement de la direction, politique qualité, objectifs et système de management de la qualité', 'التزام الإدارة، سياسة الجودة، الأهداف ونظام إدارة الجودة', 'fa-shield-alt', 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'Risk Management', 'Gestion des Risques', 'إدارة المخاطر', 'Risk identification, analysis, evaluation and treatment processes', 'Processus d\'identification, d\'analyse, d\'évaluation et de traitement des risques', 'عمليات تحديد وتحليل وتقييم ومعالجة المخاطر', 'fa-exclamation-triangle', 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'Non-Conformities and CAPA', 'Non-Conformités et CAPA', 'عدم المطابقة والإجراءات التصحيحية', 'Non-conformity management, root cause analysis, corrective and preventive actions', 'Gestion des non-conformités, analyse des causes racines, actions correctives et préventives', 'إدارة عدم المطابقة، تحليل الأسباب الجذرية، الإجراءات التصحيحية والوقائية', 'fa-clipboard-check', 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 'Audit and Compliance', 'Audit et Conformité', 'التدقيق والامتثال', 'Internal audit program, audit planning, execution, reporting and follow-up', 'Programme d\'audit interne, planification, réalisation, rapport et suivi des audits', 'برنامج التدقيق الداخلي، التخطيط والتنفيذ وإعداد التقارير والمتابعة', 'fa-search', 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 'Production', 'Production', 'الإنتاج', 'Production process control, quality at source, process capability and performance', 'Maîtrise des processus de production, qualité à la source, capabilité et performance', 'التحكم في عمليات الإنتاج، الجودة في المصدر، قدرة وأداء العملية', 'fa-industry', 1.20, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(6, 'Maintenance', 'Maintenance', 'الصيانة', 'Preventive and predictive maintenance, spare parts management, equipment effectiveness', 'Maintenance préventive et prédictive, gestion des pièces de rechange, efficacité des équipements', 'الصيانة الوقائية والتنبؤية، إدارة قطع الغيار، فعالية المعدات', 'fa-wrench', 1.00, 6, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(7, 'Supply Chain', 'Supply Chain', 'سلسلة التوريد', 'Supplier management, logistics, inventory management and supply chain performance', 'Gestion des fournisseurs, logistique, gestion des stocks et performance supply chain', 'إدارة الموردين، اللوجستيات، إدارة المخزون وأداء سلسلة التوريد', 'fa-truck', 1.00, 7, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(8, 'Human Resources', 'Ressources Humaines', 'الموارد البشرية', 'Competence management, training, awareness, and organizational culture', 'Gestion des compétences, formation, sensibilisation et culture organisationnelle', 'إدارة الكفاءات، التدريب، التوعية والثقافة التنظيمية', 'fa-users', 0.80, 8, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(9, 'Continuous Improvement', 'Amélioration Continue', 'التحسين المستمر', 'Kaizen, Lean, Six Sigma, problem-solving methodologies and innovation', 'Kaizen, Lean, Six Sigma, méthodologies de résolution de problèmes et innovation', 'كايزن، لين، ستة سيغما، منهجيات حل المشكلات والابتكار', 'fa-chart-line', 1.00, 9, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(10, 'Digitalization', 'Digitalisation', 'الرقمنة', 'Digital tools, Industry 4.0, data analytics and connected systems', 'Outils digitaux, Industrie 4.0, analyse de données et systèmes connectés', 'الأدوات الرقمية، الصناعة 4.0، تحليل البيانات والأنظمة المتصلة', 'fa-laptop-code', 0.80, 10, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16');

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
(1, 'IATF 16949', 'IATF 16949', '', NULL, NULL, NULL, 'fa-industry', '#7367f0', 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'VDA 6.3', 'VDA 6.3', '', NULL, NULL, NULL, 'fa-cogs', '#28c76f', 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'ISO 9001', 'ISO 9001', '', NULL, NULL, NULL, 'fa-certificate', '#ff9f43', 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16');

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
  `consent_contact` tinyint(1) NOT NULL DEFAULT 0,
  `consent_share_industry` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 4, 'business.mohamed60@gmail.com', '2026-07-31 19:52:57', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(2, 4, 'business.mohamed60@gmail.com', '2026-07-31 21:29:47', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(3, 4, 'business.mohamed60@gmail.com', '2026-07-31 22:25:28', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(4, 3, 'gmove05@gmail.com', '2026-07-31 22:29:47', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(5, 4, 'business.mohamed60@gmail.com', '2026-08-01 15:36:08', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(6, 4, 'business.mohamed60@gmail.com', '2026-08-01 15:55:55', '105.97.93.50', 'Chrome', 'Windows', NULL, NULL, 'success'),
(7, 4, 'business.mohamed60@gmail.com', '2026-08-01 18:55:11', '105.102.89.184', 'Chrome', 'Windows', NULL, NULL, 'success'),
(8, 4, 'business.mohamed60@gmail.com', '2026-08-01 19:08:40', '105.102.89.184', 'Chrome', 'Windows', NULL, NULL, 'success'),
(9, 4, 'business.mohamed60@gmail.com', '2026-08-01 19:57:16', '105.102.89.184', 'Chrome', 'Windows', NULL, NULL, 'success'),
(10, 4, 'business.mohamed60@gmail.com', '2026-08-01 20:33:18', '105.102.89.184', 'Chrome', 'Windows', NULL, NULL, 'success'),
(11, 4, 'business.mohamed60@gmail.com', '2026-08-01 20:38:10', '105.102.89.184', 'Chrome', 'Windows', NULL, NULL, 'success'),
(12, 4, 'business.mohamed60@gmail.com', '2026-08-02 07:30:24', '129.45.68.71', 'Chrome', 'Linux', NULL, NULL, 'success'),
(13, 4, 'business.mohamed60@gmail.com', '2026-08-04 11:46:49', '41.111.38.219', 'Chrome', 'Windows', NULL, NULL, 'success'),
(14, 4, 'business.mohamed60@gmail.com', '2026-08-04 12:39:22', '41.96.96.59', 'Chrome', 'Windows', NULL, NULL, 'success'),
(15, 4, 'business.mohamed60@gmail.com', '2026-08-04 16:17:46', '41.96.96.59', 'Chrome', 'Windows', NULL, NULL, 'success'),
(16, 3, 'gmove05@gmail.com', '2026-08-04 16:21:54', '41.96.96.59', 'Chrome', 'Windows', NULL, NULL, 'success');

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

-- --------------------------------------------------------

--
-- Structure de la table `model_domains`
--

CREATE TABLE `model_domains` (
  `id` int(10) UNSIGNED NOT NULL,
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
(1, 4, '071544', '2026-07-31 19:52:38', '2026-07-31 20:57:38', 2, 1, '105.97.93.50', 'Chrome', ''),
(2, 4, '392642', '2026-07-31 21:29:22', '2026-07-31 22:34:22', 2, 1, '105.97.93.50', 'Chrome', ''),
(3, 4, '225112', '2026-07-31 22:25:07', '2026-07-31 23:30:07', 2, 1, '105.97.93.50', 'Chrome', ''),
(4, 3, '580283', '2026-07-31 22:29:26', '2026-07-31 23:34:26', 2, 1, '105.97.93.50', 'Chrome', ''),
(5, 4, '326353', '2026-08-01 15:35:50', '2026-08-01 16:40:50', 2, 1, '105.97.93.50', 'Chrome', ''),
(6, 4, '398180', '2026-08-01 15:55:33', '2026-08-01 17:00:33', 2, 1, '105.97.93.50', 'Chrome', ''),
(7, 4, '691039', '2026-08-01 18:54:54', '2026-08-01 19:59:54', 2, 1, '105.102.89.184', 'Chrome', ''),
(8, 4, '940537', '2026-08-01 19:08:12', '2026-08-01 20:13:12', 2, 1, '105.102.89.184', 'Chrome', ''),
(9, 4, '251500', '2026-08-01 19:56:57', '2026-08-01 21:01:57', 2, 1, '105.102.89.184', 'Chrome', ''),
(10, 4, '441261', '2026-08-01 20:32:59', '2026-08-01 21:37:59', 2, 1, '105.102.89.184', 'Chrome', ''),
(11, 4, '316213', '2026-08-01 20:37:56', '2026-08-01 21:42:56', 2, 1, '105.102.89.184', 'Chrome', ''),
(12, 4, '440613', '2026-08-02 07:30:00', '2026-08-02 08:35:00', 2, 1, '129.45.68.71', 'Chrome', ''),
(13, 4, '857581', '2026-08-04 11:46:30', '2026-08-04 12:51:30', 2, 1, '41.111.38.219', 'Chrome', ''),
(14, 4, '589183', '2026-08-04 12:39:02', '2026-08-04 13:44:02', 2, 1, '41.96.96.59', 'Chrome', ''),
(15, 4, '088861', '2026-08-04 16:16:30', '2026-08-04 17:21:30', 0, 1, '41.96.96.59', 'Chrome', ''),
(16, 4, '031359', '2026-08-04 16:17:28', '2026-08-04 17:22:28', 2, 1, '41.96.96.59', 'Chrome', ''),
(17, 3, '522173', '2026-08-04 16:21:20', '2026-08-04 17:26:20', 2, 1, '41.96.96.59', 'Chrome', '');

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
(1, 'Gérer les questions', 'manage_questions', 'Créer, modifier, supprimer des questions', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'Gérer les domaines', 'manage_domains', 'Créer, modifier, supprimer des domaines', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'Gérer les utilisateurs', 'manage_users', 'Créer, modifier, supprimer des utilisateurs', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 'Voir les leads', 'view_leads', 'Consulter la liste des leads', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 'Exporter les données', 'export_data', 'Exporter les données en CSV', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(6, 'Voir les rapports', 'view_reports', 'Consulter les rapports', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(7, 'Gérer les paramètres', 'manage_settings', 'Modifier les paramètres de l\'application', '2026-07-31 19:12:16', '2026-07-31 19:12:16');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(10) UNSIGNED NOT NULL,
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

INSERT INTO `questions` (`id`, `domain_id`, `title`, `title_fr`, `title_ar`, `description`, `description_fr`, `description_ar`, `weight`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Quality Policy', 'Politique Qualité', 'سياسة الجودة', NULL, 'La politique qualité est-elle définie, communiquée et comprise par tous ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 1, 'Quality Objectives', 'Objectifs Qualité', 'أهداف الجودة', NULL, 'Des objectifs qualité mesurables sont-ils définis et suivis régulièrement ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 1, 'Management Review', 'Revue de Direction', 'مراجعة الإدارة', NULL, 'La direction réalise-t-elle des revues périodiques du système de management ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 1, 'Documentation', 'Documentation', 'التوثيق', NULL, 'Le système documentaire est-il maîtrisé et accessible aux collaborateurs ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 1, 'Quality Culture', 'Culture Qualité', 'ثقافة الجودة', NULL, 'La culture qualité est-elle ancrée dans l\'organisation et promue par le management ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(6, 2, 'Risk Identification', 'Identification des Risques', 'تحديد المخاطر', NULL, 'Les risques qualité et opérationnels sont-ils identifiés systématiquement ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(7, 2, 'Risk Analysis', 'Analyse des Risques', 'تحليل المخاطر', NULL, 'Les risques sont-ils analysés et évalués selon leur criticité ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(8, 2, 'Risk Treatment', 'Traitement des Risques', 'معالجة المخاطر', NULL, 'Des plans de traitement des risques sont-ils définis et mis en œuvre ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(9, 2, 'Risk Monitoring', 'Surveillance des Risques', 'مراقبة المخاطر', NULL, 'Les risques sont-ils suivis et réévalués périodiquement ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(10, 2, 'Opportunity Management', 'Gestion des Opportunités', 'إدارة الفرص', NULL, 'Les opportunités d\'amélioration sont-elles identifiées et exploitées ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(11, 3, 'NC Detection', 'Détection des NC', 'كشف عدم المطابقة', NULL, 'Les non-conformités sont-elles détectées et enregistrées systématiquement ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(12, 3, 'Root Cause Analysis', 'Analyse des Causes Racines', 'تحليل الأسباب الجذرية', NULL, 'Les causes racines des non-conformités sont-elles analysées en profondeur ?', NULL, 1.20, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(13, 3, 'Corrective Actions', 'Actions Correctives', 'الإجراءات التصحيحية', NULL, 'Des actions correctives sont-elles définies et suivies jusqu\'à leur clôture ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(14, 3, 'Effectiveness Verification', 'Vérification d\'Efficacité', 'التحقق من الفعالية', NULL, 'L\'efficacité des actions correctives est-elle vérifiée ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(15, 3, 'Preventive Actions', 'Actions Préventives', 'الإجراءات الوقائية', NULL, 'Des actions préventives sont-elles déployées à partir des retours d\'expérience ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(16, 4, 'Audit Program', 'Programme d\'Audit', 'برنامج التدقيق', NULL, 'Un programme d\'audit interne annuel est-il défini et planifié ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(17, 4, 'Audit Execution', 'Réalisation des Audits', 'تنفيذ التدقيق', NULL, 'Les audits sont-ils réalisés selon le planning par des auditeurs qualifiés ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(18, 4, 'Audit Reports', 'Rapports d\'Audit', 'تقارير التدقيق', NULL, 'Les rapports d\'audit sont-ils complets et diffusés aux parties concernées ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(19, 4, 'Regulatory Compliance', 'Conformité Réglementaire', 'الامتثال التنظيمي', NULL, 'La veille réglementaire et la conformité aux exigences légales sont-elles assurées ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(20, 4, 'Auditor Competence', 'Compétence des Auditeurs', 'كفاءة المدققين', NULL, 'Les auditeurs sont-ils formés et leurs compétences maintenues à jour ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(21, 5, 'Process Control', 'Contrôle des Processus', 'التحكم في العمليات', NULL, 'Les processus de production sont-ils maîtrisés avec des paramètres définis ?', NULL, 1.20, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(22, 5, 'Quality at Source', 'Qualité à la Source', 'الجودة في المصدر', NULL, 'L\'auto-contrôle et la responsabilité qualité des opérateurs sont-ils en place ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(23, 5, 'Process Capability', 'Capabilité Processus', 'قدرة العملية', NULL, 'La capabilité des processus est-elle mesurée et améliorée ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(24, 5, 'Control Plan', 'Plan de Contrôle', 'خطة التحكم', NULL, 'Un plan de contrôle est-il défini et appliqué pour chaque produit ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(25, 5, 'Traceability', 'Traçabilité', 'قابلية التتبع', NULL, 'La traçabilité des produits et des lots est-elle assurée tout au long de la production ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(26, 6, 'Preventive Maintenance', 'Maintenance Préventive', 'الصيانة الوقائية', NULL, 'Un plan de maintenance préventive est-il défini et exécuté ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(27, 6, 'Predictive Maintenance', 'Maintenance Prédictive', 'الصيانة التنبؤية', NULL, 'Des techniques de maintenance prédictive sont-elles utilisées ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(28, 6, 'Spare Parts', 'Pièces de Rechange', 'قطع الغيار', NULL, 'La gestion des pièces de rechange est-elle optimisée ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(29, 6, 'OEE', 'TRS - Efficacité', 'الفعالية الكلية للمعدات', NULL, 'Le TRS (Taux de Rendement Synthétique) est-il mesuré et suivi ?', NULL, 1.20, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(30, 6, 'Maintenance KPIs', 'Indicateurs Maintenance', 'مؤشرات الصيانة', NULL, 'Des indicateurs de performance maintenance sont-ils suivis ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(31, 7, 'Supplier Evaluation', 'Évaluation Fournisseurs', 'تقييم الموردين', NULL, 'Les fournisseurs sont-ils évalués et qualifiés selon des critères objectifs ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(32, 7, 'Supplier Development', 'Développement Fournisseurs', 'تطوير الموردين', NULL, 'Des actions de développement fournisseurs sont-elles menées ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(33, 7, 'Inventory Management', 'Gestion des Stocks', 'إدارة المخزون', NULL, 'La gestion des stocks est-elle optimisée (rotation, just-in-time, etc.) ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(34, 7, 'Logistics', 'Logistique', 'اللوجستيات', NULL, 'Les opérations logistiques sont-elles maîtrisées et optimisées ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(35, 7, 'Supply Chain Risk', 'Risque Supply Chain', 'مخاطر سلسلة التوريد', NULL, 'Les risques supply chain sont-ils identifiés et gérés ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(36, 8, 'Competence Matrix', 'Matrice de Compétences', 'مصفوفة الكفاءات', NULL, 'Une matrice de compétences est-elle maintenue à jour pour chaque poste ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(37, 8, 'Training Plan', 'Plan de Formation', 'خطة التدريب', NULL, 'Un plan de formation annuel est-il défini et suivi ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(38, 8, 'Training Effectiveness', 'Efficacité Formation', 'فعالية التدريب', NULL, 'L\'efficacité des formations est-elle évaluée ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(39, 8, 'Employee Awareness', 'Sensibilisation', 'توعية الموظفين', NULL, 'Les collaborateurs sont-ils sensibilisés à la qualité et à l\'amélioration continue ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(40, 8, 'Communication', 'Communication', 'التواصل', NULL, 'La communication interne sur les enjeux qualité est-elle efficace ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(41, 9, 'Kaizen Culture', 'Culture Kaizen', 'ثقافة كايزن', NULL, 'Une culture d\'amélioration continue est-elle déployée dans l\'organisation ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(42, 9, 'Problem Solving', 'Résolution de Problèmes', 'حل المشكلات', NULL, 'Des méthodes structurées de résolution de problèmes sont-elles utilisées (8D, PDCA, DMAIC) ?', NULL, 1.20, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(43, 9, 'Lean Tools', 'Outils Lean', 'أدوات اللين', NULL, 'Les outils Lean sont-ils maîtrisés et appliqués (5S, SMED, TPM, VSM) ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(44, 9, 'Suggestions System', 'Système de Suggestions', 'نظام الاقتراحات', NULL, 'Un système de suggestions des employés est-il en place et encouragé ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(45, 9, 'Innovation', 'Innovation', 'الابتكار', NULL, 'L\'innovation est-elle encouragée et les bonnes pratiques sont-elles partagées ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(46, 10, 'Digital Strategy', 'Stratégie Digitale', 'الاستراتيجية الرقمية', NULL, 'Une stratégie de digitalisation est-elle définie et déployée ?', NULL, 1.00, 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(47, 10, 'Digital Tools', 'Outils Digitaux', 'الأدوات الرقمية', NULL, 'Les outils digitaux de gestion de la qualité sont-ils utilisés ?', NULL, 1.00, 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(48, 10, 'Data Analytics', 'Analyse de Données', 'تحليل البيانات', NULL, 'Les données sont-elles analysées pour piloter la performance ?', NULL, 1.00, 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(49, 10, 'Connected Systems', 'Systèmes Connectés', 'الأنظمة المتصلة', NULL, 'Les systèmes de production sont-ils connectés et les données exploitées en temps réel ?', NULL, 1.00, 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(50, 10, 'Cybersecurity', 'Cybersécurité', 'الأمن السيبراني', NULL, 'La sécurité des systèmes d\'information est-elle assurée ?', NULL, 1.00, 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16');

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

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_number` varchar(50) DEFAULT NULL,
  `assessment_id` int(10) UNSIGNED DEFAULT NULL,
  `lead_id` int(10) UNSIGNED DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
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

-- --------------------------------------------------------

--
-- Structure de la table `report_blocks`
--

CREATE TABLE `report_blocks` (
  `id` int(11) NOT NULL,
  `block_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `default_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `report_blocks`
--

INSERT INTO `report_blocks` (`id`, `block_key`, `name`, `category`, `icon`, `description`, `default_config`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'global_score', 'Global Score', 'metrics', 'bi-speedometer', 'Overall AQMI score with rating', '{\"score\": 0, \"max\": 100, \"show_rating\": true}', 1, 10, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(2, 'radar_chart', 'Radar Chart', 'charts', 'bi-graph-up', 'Multi-axis radar chart', '{\"axes\": [], \"legend\": true}', 1, 20, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(3, 'gauge', 'Gauge', 'metrics', 'bi-dial', 'Single-value gauge indicator', '{\"value\": 0, \"min\": 0, \"max\": 100}', 1, 30, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(4, 'recommendations', 'Recommendations', 'content', 'bi-list-check', 'List of recommendations', '{\"items\": []}', 1, 40, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(5, 'company_info', 'Company Information', 'content', 'bi-building', 'Company details block', '{\"fields\": []}', 1, 50, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(6, 'aqmi_logo', 'AQMI Logo', 'branding', 'bi-award', 'Official AQMI logo', '{\"size\": \"md\", \"align\": \"left\"}', 1, 60, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(7, 'company_logo', 'Company Logo', 'branding', 'bi-image', 'Client company logo', '{\"size\": \"md\", \"align\": \"left\"}', 1, 70, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(8, 'qr_code', 'QR Code', 'utility', 'bi-qr-code', 'Generated QR code', '{\"value\": \"\", \"size\": 120}', 1, 80, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(9, 'signature', 'Signature', 'utility', 'bi-pen', 'Signature line block', '{\"label\": \"\", \"role\": \"\", \"show_date\": true, \"show_stamp\": false}', 1, 90, '2026-07-31 20:09:40', '2026-07-31 20:10:00'),
(10, 'header', 'Header', 'structure', 'bi-text-left', 'Page header block', '{\"text\": \"\", \"align\": \"left\", \"show_page_number\": false, \"show_report_number\": false, \"show_date\": false}', 1, 100, '2026-07-31 20:09:40', '2026-07-31 20:10:00'),
(11, 'footer', 'Footer', 'structure', 'bi-text-right', 'Page footer block', '{\"text\": \"\", \"align\": \"center\", \"show_page_number\": true, \"show_report_number\": false, \"show_date\": false}', 1, 110, '2026-07-31 20:09:40', '2026-07-31 20:10:00'),
(12, 'rich_text', 'Rich Text', 'content', 'bi-fonts', 'Editable rich text content', '{\"html\": \"\"}', 1, 120, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(13, 'image', 'Image', 'media', 'bi-card-image', 'Image block', '{\"url\": \"\", \"alt\": \"\", \"width\": \"100%\"}', 1, 130, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(14, 'official_stamp', 'Official Stamp', 'branding', 'bi-patch-check-fill', 'Official AQMI certification stamp/seal', '{\"style\": \"circular\", \"text\": \"CERTIFIÉ\", \"subtext\": \"AQMI\", \"color\": \"#0d47a1\", \"size\": 100, \"align\": \"right\"}', 1, 65, '2026-07-31 20:10:00', '2026-07-31 20:10:00'),
(28, 'bar_chart', 'Bar Chart', 'charts', 'bi-bar-chart', 'Vertical or horizontal bar chart', '{\"series\": [{\"label\": \"Série 1\", \"data\": [{\"label\": \"A\", \"value\": 0}]}], \"horizontal\": false, \"legend\": true}', 1, 25, '2026-08-01 20:54:43', '2026-08-01 20:54:43'),
(29, 'line_chart', 'Line Chart', 'charts', 'bi-graph-up-arrow', 'Trend line chart with multiple series', '{\"series\": [{\"label\": \"Série 1\", \"data\": [{\"label\": \"Jan\", \"value\": 0}]}], \"legend\": true, \"smooth\": true}', 1, 26, '2026-08-01 20:54:43', '2026-08-01 20:54:43'),
(30, 'donut_chart', 'Donut Chart', 'charts', 'bi-pie-chart', 'Donut/pie chart for proportional data', '{\"series\": [{\"label\": \"A\", \"value\": 1}], \"legend\": true}', 1, 27, '2026-08-01 20:54:43', '2026-08-01 20:54:43'),
(31, 'area_chart', 'Area Chart', 'charts', 'bi-graph-up-arrow', 'Stacked area chart for trends', '{\"series\": [{\"label\": \"Série 1\", \"data\": [{\"label\": \"Jan\", \"value\": 0}]}], \"legend\": true, \"smooth\": true}', 1, 28, '2026-08-01 20:54:43', '2026-08-01 20:54:43');

-- --------------------------------------------------------

--
-- Structure de la table `report_templates`
--

CREATE TABLE `report_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `report_number_prefix` varchar(20) DEFAULT 'AQMI-RPT-',
  `orientation` enum('portrait','landscape') NOT NULL DEFAULT 'portrait',
  `watermark_text` varchar(100) DEFAULT NULL,
  `watermark_opacity` decimal(3,2) NOT NULL DEFAULT 0.08,
  `certification_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `thumbnail` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `report_templates`
--

INSERT INTO `report_templates` (`id`, `name`, `description`, `theme_id`, `category`, `report_number_prefix`, `orientation`, `watermark_text`, `watermark_opacity`, `certification_date`, `expiration_date`, `status`, `thumbnail`, `settings`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'test', '', 1, 'audit', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 16:58:58', '2026-08-01 16:58:58'),
(2, 'test2', '', 2, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 17:06:30', '2026-08-01 17:06:30'),
(3, 'test2', '', 4, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 17:29:44', '2026-08-01 17:29:44'),
(4, 'tes44', '', 1, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 22:02:26', '2026-08-01 22:02:26'),
(5, 'tes44', '', 1, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 22:10:40', '2026-08-01 22:10:40'),
(6, 'tes44', '', 1, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 23:04:37', '2026-08-01 23:04:37'),
(7, 'hhhh', '', NULL, '', 'AQMI-RPT-', 'portrait', NULL, 0.08, NULL, NULL, 'draft', NULL, NULL, 0, '2026-08-01 23:20:27', '2026-08-01 23:20:27');

-- --------------------------------------------------------

--
-- Structure de la table `report_template_blocks`
--

CREATE TABLE `report_template_blocks` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `block_id` int(11) DEFAULT NULL,
  `block_key` varchar(50) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `block_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`block_config`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `visibility` enum('web_pdf','web_only','pdf_only') NOT NULL DEFAULT 'web_pdf',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `column_span` int(11) DEFAULT 12,
  `row_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `report_template_blocks`
--

INSERT INTO `report_template_blocks` (`id`, `template_id`, `block_id`, `block_key`, `title`, `block_config`, `sort_order`, `is_enabled`, `visibility`, `created_at`, `updated_at`, `column_span`, `row_id`) VALUES
(1, 1, 3, 'gauge', 'Gauge', '{\"label\":\"Indicateur\",\"value\":0,\"min\":0,\"max\":100,\"unit\":\"%\"}', 0, 1, 'web_pdf', '2026-08-01 23:07:03', '2026-08-01 23:07:03', 12, 0);

-- --------------------------------------------------------

--
-- Structure de la table `report_themes`
--

CREATE TABLE `report_themes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `primary_color` varchar(20) NOT NULL DEFAULT '#0d47a1',
  `secondary_color` varchar(20) NOT NULL DEFAULT '#546e7a',
  `accent_color` varchar(20) NOT NULL DEFAULT '#00897b',
  `heading_color` varchar(20) DEFAULT NULL,
  `body_color` varchar(20) DEFAULT NULL,
  `background_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `font_family` varchar(100) NOT NULL DEFAULT 'Inter, Arial, sans-serif',
  `css_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`css_variables`)),
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `report_themes`
--

INSERT INTO `report_themes` (`id`, `name`, `description`, `primary_color`, `secondary_color`, `accent_color`, `heading_color`, `body_color`, `background_color`, `font_family`, `css_variables`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(2, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(3, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-07-31 20:09:40', '2026-07-31 20:09:40'),
(4, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-08-01 17:28:35', '2026-08-01 17:28:35'),
(5, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-01 17:28:35', '2026-08-01 17:28:35'),
(6, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-01 17:28:35', '2026-08-01 17:28:35'),
(7, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-08-04 13:08:06', '2026-08-04 13:08:06'),
(8, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 13:08:06', '2026-08-04 13:08:06'),
(9, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 13:08:06', '2026-08-04 13:08:06'),
(10, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-08-04 17:19:38', '2026-08-04 17:19:38'),
(11, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 17:19:38', '2026-08-04 17:19:38'),
(12, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 17:19:38', '2026-08-04 17:19:38'),
(13, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-08-04 17:48:58', '2026-08-04 17:48:58'),
(14, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 17:48:58', '2026-08-04 17:48:58'),
(15, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 17:48:58', '2026-08-04 17:48:58'),
(16, 'AQMI Corporate', 'Default AQMI brand theme', '#0d47a1', '#546e7a', '#00897b', '#1a237e', '#37474f', '#ffffff', 'Inter, Arial, sans-serif', NULL, 1, 1, '2026-08-04 18:25:00', '2026-08-04 18:25:00'),
(17, 'Ocean Blue', 'Calm blue corporate theme', '#1565c0', '#42a5f5', '#26c6da', '#0d47a1', '#455a64', '#f5f9fc', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 18:25:00', '2026-08-04 18:25:00'),
(18, 'Monochrome', 'Minimal monochrome theme', '#212121', '#616161', '#9e9e9e', '#000000', '#424242', '#ffffff', 'Inter, Arial, sans-serif', NULL, 0, 1, '2026-08-04 18:25:00', '2026-08-04 18:25:00');

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
(1, 'Super Administrateur', 'super_admin', 'Accès complet à toutes les fonctionnalités', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'Administrateur', 'admin', 'Accès à la gestion des contenus', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'Consultant', 'consultant', 'Accès aux évaluations et rapports', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 'Lecteur', 'reader', 'Accès en lecture seule', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 'Client', 'client', 'Espace client - accès à ses évaluations', '2026-07-31 19:12:16', '2026-07-31 19:12:16');

-- --------------------------------------------------------

--
-- Structure de la table `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_permission`
--

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(2, 2),
(2, 4),
(2, 5),
(2, 6),
(3, 4),
(3, 6),
(4, 6),
(5, 4);

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
(1, 'Beginner', 'Débutant', 'مبتدئ', 0.00, 30.00, '#6c757d', 'fa-flag', 1, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'Developing', 'En Développement', 'قيد التطوير', 31.00, 50.00, '#fd7e14', 'fa-chart-bar', 2, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'Structured', 'Structuré', 'منظم', 51.00, 70.00, '#1a56db', 'fa-layer-group', 3, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 'Performing', 'Performant', 'متميز', 71.00, 85.00, '#059669', 'fa-trophy', 4, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 'Excellence', 'Excellence', 'امتياز', 86.00, 100.00, '#d97706', 'fa-crown', 5, 1, '2026-07-31 19:12:16', '2026-07-31 19:12:16');

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
(1, 'app_name', 'AQMI', 'string', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(2, 'app_description', 'Automotive Quality Maturity Index - Plateforme d\'évaluation de la maturité qualité', 'string', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(3, 'admin_email', 'admin@aqmi.com', 'string', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(4, 'items_per_page', '20', 'integer', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(5, 'default_language', 'fr', 'string', '2026-07-31 19:12:16', '2026-07-31 19:12:16'),
(6, 'score_calculation_method', 'weighted', 'string', '2026-07-31 19:12:16', '2026-07-31 19:12:16');

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
(3, 5, 'Mohammed', 'BENSAFI', 'gmove05@gmail.com', '$2y$12$IHKJMPEvlQKT98DqhlkHRu4egQTyebVgW4y3PnVJJu//b4ZxWLsXa', '+213781358536', NULL, 1, '2026-08-04 16:21:54', '2026-07-31 19:46:29', '2026-08-04 16:21:54'),
(4, 1, 'Mohamed', 'BENSAFI', 'business.mohamed60@gmail.com', '$2y$10$nB45sceyb0mO9.5JFxhy4et4Z3cr2ITq6fcle/7lqeWOELkB6U0Fa', NULL, NULL, 1, '2026-08-04 16:17:46', '2026-07-31 19:46:29', '2026-08-04 16:17:46');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `domain_id` (`domain_id`);

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
  ADD KEY `idx_questions_domain` (`domain_id`);

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
  ADD KEY `idx_reports_number` (`report_number`),
  ADD KEY `fk_reports_template` (`template_id`);

--
-- Index pour la table `report_blocks`
--
ALTER TABLE `report_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_report_blocks_key` (`block_key`),
  ADD KEY `idx_report_blocks_category` (`category`),
  ADD KEY `idx_report_blocks_active` (`is_active`);

--
-- Index pour la table `report_templates`
--
ALTER TABLE `report_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_templates_theme` (`theme_id`),
  ADD KEY `idx_report_templates_status` (`status`),
  ADD KEY `idx_report_templates_category` (`category`),
  ADD KEY `idx_report_templates_number` (`report_number_prefix`);

--
-- Index pour la table `report_template_blocks`
--
ALTER TABLE `report_template_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_tblocks_template` (`template_id`),
  ADD KEY `idx_report_tblocks_sort` (`template_id`,`sort_order`),
  ADD KEY `fk_tblocks_block` (`block_id`);

--
-- Index pour la table `report_themes`
--
ALTER TABLE `report_themes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_themes_active` (`is_active`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `assessment_answers`
--
ALTER TABLE `assessment_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `model_domains`
--
ALTER TABLE `model_domains`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `report_blocks`
--
ALTER TABLE `report_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT pour la table `report_templates`
--
ALTER TABLE `report_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `report_template_blocks`
--
ALTER TABLE `report_template_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `report_themes`
--
ALTER TABLE `report_themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  ADD CONSTRAINT `model_domains_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `evaluation_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_domains_ibfk_2` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_reports_template` FOREIGN KEY (`template_id`) REFERENCES `report_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `report_templates`
--
ALTER TABLE `report_templates`
  ADD CONSTRAINT `fk_templates_theme` FOREIGN KEY (`theme_id`) REFERENCES `report_themes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `report_template_blocks`
--
ALTER TABLE `report_template_blocks`
  ADD CONSTRAINT `fk_tblocks_block` FOREIGN KEY (`block_id`) REFERENCES `report_blocks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tblocks_template` FOREIGN KEY (`template_id`) REFERENCES `report_templates` (`id`) ON DELETE CASCADE;

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

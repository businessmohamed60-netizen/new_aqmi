-- AQMI Demo Data Seeder

-- Roles
INSERT INTO `roles` (`name`, `slug`, `description`) VALUES
('Super Administrateur', 'super_admin', 'Accès complet à toutes les fonctionnalités'),
('Administrateur', 'admin', 'Accès à la gestion des contenus'),
('Consultant', 'consultant', 'Accès aux évaluations et rapports'),
('Lecteur', 'reader', 'Accès en lecture seule'),
('Client', 'client', 'Espace client - accès à ses évaluations');

-- Permissions
INSERT INTO `permissions` (`name`, `slug`, `description`) VALUES
('Gérer les questions', 'manage_questions', 'Créer, modifier, supprimer des questions'),
('Gérer les domaines', 'manage_domains', 'Créer, modifier, supprimer des domaines'),
('Gérer les utilisateurs', 'manage_users', 'Créer, modifier, supprimer des utilisateurs'),
('Voir les leads', 'view_leads', 'Consulter la liste des leads'),
('Exporter les données', 'export_data', 'Exporter les données en CSV'),
('Voir les rapports', 'view_reports', 'Consulter les rapports'),
('Gérer les paramètres', 'manage_settings', 'Modifier les paramètres de l\'application');

-- Super Admin user (password: Admin@2024#)
INSERT INTO `users` (`role_id`, `firstname`, `lastname`, `email`, `password`, `is_active`) VALUES
(1, 'Admin', 'AQMI', 'admin@aqmi.com', '$2y$10$Wvfw.iqpLOi78o3uqDhtcere567qzE9ibKIzV3MydWVvRu2oPRNhu', 1);

-- Client user (password: password)
INSERT INTO `users` (`role_id`, `firstname`, `lastname`, `email`, `password`, `is_active`) VALUES
(5, 'Jean', 'Dupont', 'client@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Evaluation Models
INSERT INTO `evaluation_models` (`name`, `name_fr`, `icon`, `color`, `sort_order`, `is_active`) VALUES
('IATF 16949', 'IATF 16949', 'fa-industry', '#7367f0', 1, 1),
('VDA 6.3', 'VDA 6.3', 'fa-cogs', '#28c76f', 2, 1),
('ISO 9001', 'ISO 9001', 'fa-certificate', '#ff9f43', 3, 1);

-- Domains
INSERT INTO `domains` (`name`, `name_fr`, `name_ar`, `description`, `description_fr`, `description_ar`, `icon`, `weight`, `sort_order`, `is_active`) VALUES
('Quality Governance', 'Gouvernance Qualité', 'حوكمة الجودة', 'Management commitment, quality policy, objectives, and quality management system', 'Engagement de la direction, politique qualité, objectifs et système de management de la qualité', 'التزام الإدارة، سياسة الجودة، الأهداف ونظام إدارة الجودة', 'fa-shield-alt', 1.0, 1, 1),
('Risk Management', 'Gestion des Risques', 'إدارة المخاطر', 'Risk identification, analysis, evaluation and treatment processes', 'Processus d\'identification, d\'analyse, d\'évaluation et de traitement des risques', 'عمليات تحديد وتحليل وتقييم ومعالجة المخاطر', 'fa-exclamation-triangle', 1.0, 2, 1),
('Non-Conformities and CAPA', 'Non-Conformités et CAPA', 'عدم المطابقة والإجراءات التصحيحية', 'Non-conformity management, root cause analysis, corrective and preventive actions', 'Gestion des non-conformités, analyse des causes racines, actions correctives et préventives', 'إدارة عدم المطابقة، تحليل الأسباب الجذرية، الإجراءات التصحيحية والوقائية', 'fa-clipboard-check', 1.0, 3, 1),
('Audit and Compliance', 'Audit et Conformité', 'التدقيق والامتثال', 'Internal audit program, audit planning, execution, reporting and follow-up', 'Programme d\'audit interne, planification, réalisation, rapport et suivi des audits', 'برنامج التدقيق الداخلي، التخطيط والتنفيذ وإعداد التقارير والمتابعة', 'fa-search', 1.0, 4, 1),
('Production', 'Production', 'الإنتاج', 'Production process control, quality at source, process capability and performance', 'Maîtrise des processus de production, qualité à la source, capabilité et performance', 'التحكم في عمليات الإنتاج، الجودة في المصدر، قدرة وأداء العملية', 'fa-industry', 1.2, 5, 1),
('Maintenance', 'Maintenance', 'الصيانة', 'Preventive and predictive maintenance, spare parts management, equipment effectiveness', 'Maintenance préventive et prédictive, gestion des pièces de rechange, efficacité des équipements', 'الصيانة الوقائية والتنبؤية، إدارة قطع الغيار، فعالية المعدات', 'fa-wrench', 1.0, 6, 1),
('Supply Chain', 'Supply Chain', 'سلسلة التوريد', 'Supplier management, logistics, inventory management and supply chain performance', 'Gestion des fournisseurs, logistique, gestion des stocks et performance supply chain', 'إدارة الموردين، اللوجستيات، إدارة المخزون وأداء سلسلة التوريد', 'fa-truck', 1.0, 7, 1),
('Human Resources', 'Ressources Humaines', 'الموارد البشرية', 'Competence management, training, awareness, and organizational culture', 'Gestion des compétences, formation, sensibilisation et culture organisationnelle', 'إدارة الكفاءات، التدريب، التوعية والثقافة التنظيمية', 'fa-users', 0.8, 8, 1),
('Continuous Improvement', 'Amélioration Continue', 'التحسين المستمر', 'Kaizen, Lean, Six Sigma, problem-solving methodologies and innovation', 'Kaizen, Lean, Six Sigma, méthodologies de résolution de problèmes et innovation', 'كايزن، لين، ستة سيغما، منهجيات حل المشكلات والابتكار', 'fa-chart-line', 1.0, 9, 1),
('Digitalization', 'Digitalisation', 'الرقمنة', 'Digital tools, Industry 4.0, data analytics and connected systems', 'Outils digitaux, Industrie 4.0, analyse de données et systèmes connectés', 'الأدوات الرقمية، الصناعة 4.0، تحليل البيانات والأنظمة المتصلة', 'fa-laptop-code', 0.8, 10, 1);

-- Questions (5 per domain = 50 total)
-- Domain 1: Quality Governance
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(1, 'Quality Policy', 'Politique Qualité', 'سياسة الجودة', 'La politique qualité est-elle définie, communiquée et comprise par tous ?', 1.0, 1, 1),
(1, 'Quality Objectives', 'Objectifs Qualité', 'أهداف الجودة', 'Des objectifs qualité mesurables sont-ils définis et suivis régulièrement ?', 1.0, 2, 1),
(1, 'Management Review', 'Revue de Direction', 'مراجعة الإدارة', 'La direction réalise-t-elle des revues périodiques du système de management ?', 1.0, 3, 1),
(1, 'Documentation', 'Documentation', 'التوثيق', 'Le système documentaire est-il maîtrisé et accessible aux collaborateurs ?', 1.0, 4, 1),
(1, 'Quality Culture', 'Culture Qualité', 'ثقافة الجودة', 'La culture qualité est-elle ancrée dans l''organisation et promue par le management ?', 1.0, 5, 1);

-- Domain 2: Risk Management
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(2, 'Risk Identification', 'Identification des Risques', 'تحديد المخاطر', 'Les risques qualité et opérationnels sont-ils identifiés systématiquement ?', 1.0, 1, 1),
(2, 'Risk Analysis', 'Analyse des Risques', 'تحليل المخاطر', 'Les risques sont-ils analysés et évalués selon leur criticité ?', 1.0, 2, 1),
(2, 'Risk Treatment', 'Traitement des Risques', 'معالجة المخاطر', 'Des plans de traitement des risques sont-ils définis et mis en œuvre ?', 1.0, 3, 1),
(2, 'Risk Monitoring', 'Surveillance des Risques', 'مراقبة المخاطر', 'Les risques sont-ils suivis et réévalués périodiquement ?', 1.0, 4, 1),
(2, 'Opportunity Management', 'Gestion des Opportunités', 'إدارة الفرص', 'Les opportunités d''amélioration sont-elles identifiées et exploitées ?', 1.0, 5, 1);

-- Domain 3: Non-Conformities and CAPA
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(3, 'NC Detection', 'Détection des NC', 'كشف عدم المطابقة', 'Les non-conformités sont-elles détectées et enregistrées systématiquement ?', 1.0, 1, 1),
(3, 'Root Cause Analysis', 'Analyse des Causes Racines', 'تحليل الأسباب الجذرية', 'Les causes racines des non-conformités sont-elles analysées en profondeur ?', 1.2, 2, 1),
(3, 'Corrective Actions', 'Actions Correctives', 'الإجراءات التصحيحية', 'Des actions correctives sont-elles définies et suivies jusqu''à leur clôture ?', 1.0, 3, 1),
(3, 'Effectiveness Verification', 'Vérification d''Efficacité', 'التحقق من الفعالية', 'L''efficacité des actions correctives est-elle vérifiée ?', 1.0, 4, 1),
(3, 'Preventive Actions', 'Actions Préventives', 'الإجراءات الوقائية', 'Des actions préventives sont-elles déployées à partir des retours d''expérience ?', 1.0, 5, 1);

-- Domain 4: Audit and Compliance
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(4, 'Audit Program', 'Programme d''Audit', 'برنامج التدقيق', 'Un programme d''audit interne annuel est-il défini et planifié ?', 1.0, 1, 1),
(4, 'Audit Execution', 'Réalisation des Audits', 'تنفيذ التدقيق', 'Les audits sont-ils réalisés selon le planning par des auditeurs qualifiés ?', 1.0, 2, 1),
(4, 'Audit Reports', 'Rapports d''Audit', 'تقارير التدقيق', 'Les rapports d''audit sont-ils complets et diffusés aux parties concernées ?', 1.0, 3, 1),
(4, 'Regulatory Compliance', 'Conformité Réglementaire', 'الامتثال التنظيمي', 'La veille réglementaire et la conformité aux exigences légales sont-elles assurées ?', 1.0, 4, 1),
(4, 'Auditor Competence', 'Compétence des Auditeurs', 'كفاءة المدققين', 'Les auditeurs sont-ils formés et leurs compétences maintenues à jour ?', 1.0, 5, 1);

-- Domain 5: Production
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(5, 'Process Control', 'Contrôle des Processus', 'التحكم في العمليات', 'Les processus de production sont-ils maîtrisés avec des paramètres définis ?', 1.2, 1, 1),
(5, 'Quality at Source', 'Qualité à la Source', 'الجودة في المصدر', 'L''auto-contrôle et la responsabilité qualité des opérateurs sont-ils en place ?', 1.0, 2, 1),
(5, 'Process Capability', 'Capabilité Processus', 'قدرة العملية', 'La capabilité des processus est-elle mesurée et améliorée ?', 1.0, 3, 1),
(5, 'Control Plan', 'Plan de Contrôle', 'خطة التحكم', 'Un plan de contrôle est-il défini et appliqué pour chaque produit ?', 1.0, 4, 1),
(5, 'Traceability', 'Traçabilité', 'قابلية التتبع', 'La traçabilité des produits et des lots est-elle assurée tout au long de la production ?', 1.0, 5, 1);

-- Domain 6: Maintenance
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(6, 'Preventive Maintenance', 'Maintenance Préventive', 'الصيانة الوقائية', 'Un plan de maintenance préventive est-il défini et exécuté ?', 1.0, 1, 1),
(6, 'Predictive Maintenance', 'Maintenance Prédictive', 'الصيانة التنبؤية', 'Des techniques de maintenance prédictive sont-elles utilisées ?', 1.0, 2, 1),
(6, 'Spare Parts', 'Pièces de Rechange', 'قطع الغيار', 'La gestion des pièces de rechange est-elle optimisée ?', 1.0, 3, 1),
(6, 'OEE', 'TRS - Efficacité', 'الفعالية الكلية للمعدات', 'Le TRS (Taux de Rendement Synthétique) est-il mesuré et suivi ?', 1.2, 4, 1),
(6, 'Maintenance KPIs', 'Indicateurs Maintenance', 'مؤشرات الصيانة', 'Des indicateurs de performance maintenance sont-ils suivis ?', 1.0, 5, 1);

-- Domain 7: Supply Chain
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(7, 'Supplier Evaluation', 'Évaluation Fournisseurs', 'تقييم الموردين', 'Les fournisseurs sont-ils évalués et qualifiés selon des critères objectifs ?', 1.0, 1, 1),
(7, 'Supplier Development', 'Développement Fournisseurs', 'تطوير الموردين', 'Des actions de développement fournisseurs sont-elles menées ?', 1.0, 2, 1),
(7, 'Inventory Management', 'Gestion des Stocks', 'إدارة المخزون', 'La gestion des stocks est-elle optimisée (rotation, just-in-time, etc.) ?', 1.0, 3, 1),
(7, 'Logistics', 'Logistique', 'اللوجستيات', 'Les opérations logistiques sont-elles maîtrisées et optimisées ?', 1.0, 4, 1),
(7, 'Supply Chain Risk', 'Risque Supply Chain', 'مخاطر سلسلة التوريد', 'Les risques supply chain sont-ils identifiés et gérés ?', 1.0, 5, 1);

-- Domain 8: Human Resources
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(8, 'Competence Matrix', 'Matrice de Compétences', 'مصفوفة الكفاءات', 'Une matrice de compétences est-elle maintenue à jour pour chaque poste ?', 1.0, 1, 1),
(8, 'Training Plan', 'Plan de Formation', 'خطة التدريب', 'Un plan de formation annuel est-il défini et suivi ?', 1.0, 2, 1),
(8, 'Training Effectiveness', 'Efficacité Formation', 'فعالية التدريب', 'L''efficacité des formations est-elle évaluée ?', 1.0, 3, 1),
(8, 'Employee Awareness', 'Sensibilisation', 'توعية الموظفين', 'Les collaborateurs sont-ils sensibilisés à la qualité et à l''amélioration continue ?', 1.0, 4, 1),
(8, 'Communication', 'Communication', 'التواصل', 'La communication interne sur les enjeux qualité est-elle efficace ?', 1.0, 5, 1);

-- Domain 9: Continuous Improvement
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(9, 'Kaizen Culture', 'Culture Kaizen', 'ثقافة كايزن', 'Une culture d''amélioration continue est-elle déployée dans l''organisation ?', 1.0, 1, 1),
(9, 'Problem Solving', 'Résolution de Problèmes', 'حل المشكلات', 'Des méthodes structurées de résolution de problèmes sont-elles utilisées (8D, PDCA, DMAIC) ?', 1.2, 2, 1),
(9, 'Lean Tools', 'Outils Lean', 'أدوات اللين', 'Les outils Lean sont-ils maîtrisés et appliqués (5S, SMED, TPM, VSM) ?', 1.0, 3, 1),
(9, 'Suggestions System', 'Système de Suggestions', 'نظام الاقتراحات', 'Un système de suggestions des employés est-il en place et encouragé ?', 1.0, 4, 1),
(9, 'Innovation', 'Innovation', 'الابتكار', 'L''innovation est-elle encouragée et les bonnes pratiques sont-elles partagées ?', 1.0, 5, 1);

-- Domain 10: Digitalization
INSERT INTO `questions` (`domain_id`, `title`, `title_fr`, `title_ar`, `description_fr`, `weight`, `sort_order`, `is_active`) VALUES
(10, 'Digital Strategy', 'Stratégie Digitale', 'الاستراتيجية الرقمية', 'Une stratégie de digitalisation est-elle définie et déployée ?', 1.0, 1, 1),
(10, 'Digital Tools', 'Outils Digitaux', 'الأدوات الرقمية', 'Les outils digitaux de gestion de la qualité sont-ils utilisés ?', 1.0, 2, 1),
(10, 'Data Analytics', 'Analyse de Données', 'تحليل البيانات', 'Les données sont-elles analysées pour piloter la performance ?', 1.0, 3, 1),
(10, 'Connected Systems', 'Systèmes Connectés', 'الأنظمة المتصلة', 'Les systèmes de production sont-ils connectés et les données exploitées en temps réel ?', 1.0, 4, 1),
(10, 'Cybersecurity', 'Cybersécurité', 'الأمن السيبراني', 'La sécurité des systèmes d''information est-elle assurée ?', 1.0, 5, 1);

-- Score Levels
INSERT INTO `score_levels` (`name`, `name_fr`, `name_ar`, `min_percent`, `max_percent`, `color`, `icon`, `sort_order`, `is_active`) VALUES
('Beginner', 'Débutant', 'مبتدئ', 0, 30, '#6c757d', 'fa-flag', 1, 1),
('Developing', 'En Développement', 'قيد التطوير', 30, 50, '#fd7e14', 'fa-chart-bar', 2, 1),
('Structured', 'Structuré', 'منظم', 50, 70, '#1a56db', 'fa-layer-group', 3, 1),
('Performing', 'Performant', 'متميز', 70, 85, '#059669', 'fa-trophy', 4, 1),
('Excellence', 'Excellence', 'امتياز', 85, 100, '#d97706', 'fa-crown', 5, 1);

-- Recommendations
INSERT INTO `recommendations` (`domain_id`, `condition_field`, `condition_operator`, `condition_value`, `recommendation_text`, `recommendation_text_fr`, `priority`, `is_active`) VALUES
(1, 'global_score', '<', 50, 'Define and deploy a formal quality policy with measurable objectives and regular management reviews.', 'Définir et déployer une politique qualité formelle avec des objectifs mesurables et des revues de direction régulières.', 'critical', 1),
(2, 'global_score', '<', 40, 'Implement a structured risk management process including identification, analysis, treatment, and monitoring.', 'Mettre en place un processus structuré de gestion des risques incluant identification, analyse, traitement et surveillance.', 'critical', 1),
(3, 'global_score', '<', 50, 'Establish a formal CAPA process with root cause analysis and effectiveness verification.', 'Mettre en place un processus CAPA formel avec analyse des causes racines et vérification d''efficacité.', 'high', 1),
(4, 'global_score', '<', 50, 'Strengthen the internal audit program with qualified auditors and complete audit reporting.', 'Renforcer le programme d''audit interne avec des auditeurs qualifiés et des rapports d''audit complets.', 'high', 1),
(5, 'global_score', '<', 60, 'Implement process control plans and develop operator self-control culture.', 'Mettre en place des plans de contrôle processus et développer l''auto-contrôle opérateur.', 'high', 1),
(6, 'global_score', '<', 50, 'Deploy a preventive maintenance plan and start measuring OEE to improve equipment effectiveness.', 'Déployer un plan de maintenance préventive et commencer à mesurer le TRS pour améliorer l''efficacité des équipements.', 'high', 1),
(7, 'global_score', '<', 50, 'Structure supplier evaluation and qualification process with performance monitoring.', 'Structurer le processus d''évaluation et de qualification des fournisseurs avec suivi de performance.', 'medium', 1),
(8, 'global_score', '<', 50, 'Develop a competence management system with training plans and effectiveness evaluation.', 'Développer un système de gestion des compétences avec plans de formation et évaluation d''efficacité.', 'medium', 1),
(9, 'global_score', '<', 60, 'Deploy continuous improvement methodologies (Kaizen, 5S, problem-solving) across the organization.', 'Déployer les méthodologies d''amélioration continue (Kaizen, 5S, résolution de problèmes) dans l''organisation.', 'medium', 1),
(10, 'global_score', '<', 40, 'Define a digital transformation roadmap and start implementing quality management digital tools.', 'Définir une feuille de route de transformation digitale et commencer à implémenter des outils qualité digitaux.', 'medium', 1),
(NULL, 'global_score', '>', 85, 'Maintain excellence level by sharing best practices and innovating in quality management approaches.', 'Maintenir le niveau d''excellence en partageant les bonnes pratiques et en innovant dans les approches qualité.', 'low', 1);

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`) VALUES
('app_name', 'AQMI', 'string'),
('app_description', 'Automotive Quality Maturity Index - Plateforme d''évaluation de la maturité qualité', 'string'),
('admin_email', 'admin@aqmi.com', 'string'),
('items_per_page', '20', 'integer'),
('default_language', 'fr', 'string'),
('score_calculation_method', 'weighted', 'string');
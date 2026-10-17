-- Modernisation du système de consolidation :
-- 1. Poids configurable par item (par modèle d'évaluation)
-- 2. Méthode de calcul stockée sur le rapport consolidé
-- 3. Paramètres JSON avancés (pondération par domaine, stratégie)
-- 4. Score détaillé par domaine consolidé

ALTER TABLE consolidated_report_items
    ADD COLUMN IF NOT EXISTS weight DECIMAL(5,2) DEFAULT 1.00,
    ADD COLUMN IF NOT EXISTS domain_scores_json TEXT DEFAULT NULL;

ALTER TABLE consolidated_reports
    ADD COLUMN IF NOT EXISTS calculation_method VARCHAR(50) DEFAULT 'weighted_average',
    ADD COLUMN IF NOT EXISTS consolidation_params TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS domain_breakdown_json TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS trend_data_json TEXT DEFAULT NULL;

-- catalogue des méthodes de consolidation disponibles
CREATE TABLE IF NOT EXISTS consolidation_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    name_fr VARCHAR(100) NOT NULL,
    description TEXT,
    description_fr TEXT,
    icon VARCHAR(50) DEFAULT 'fa-calculator',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO consolidation_methods (code, name, name_fr, description, description_fr, icon, sort_order) VALUES
('weighted_average', 'Weighted Average', 'Moyenne pondérée',
 'Each evaluation model contributes proportionally to its assigned weight. Ideal when some models are more critical than others.',
 'Chaque modèle d''évaluation contribue proportionnellement au poids qui lui est attribué. Idéal quand certains modèles sont plus critiques que d''autres.',
 'fa-scale-balanced', 1),
('domain_fusion', 'Domain Fusion', 'Fusion par domaine',
 'Domaines communs à plusieurs modèles sont moyennés puis regroupés. Le score global est la moyenne pondérée des domaines fusionnés.',
 'Les domaines communs à plusieurs modèles sont moyennés puis regroupés. Le score global est la moyenne pondérée des domaines fusionnés.',
 'fa-object-group', 2),
('best_of', 'Best Performance', 'Meilleure performance',
 'Retains the highest score per domain across all evaluations. Highlights the company''s peak maturity.',
 'Retient le score le plus élevé par domaine à travers toutes les évaluations. Met en avant la maturité maximale de l''entreprise.',
 'fa-trophy', 3),
('latest_only', 'Latest Assessment', 'Dernière évaluation',
 'Only the most recent assessment per model is considered. Focuses on current maturity state.',
 'Seule l''évaluation la plus récente par modèle est prise en compte. Se concentre sur l''état de maturité actuel.',
 'fa-clock-rotate-left', 4),
('maturity_gap', 'Maturity Gap Index', 'Indice d''écart de maturité',
 'Calculates a composite index factoring the gap between current scores and target maturity. Penalizes inconsistency between models.',
 'Calcule un indice composite intégrant l''écart entre les scores actuels et la maturité cible. Pénalise l''inconsistance entre modèles.',
 'fa-chart-line', 5),
('consensus', 'Consensus Score', 'Score de consensus',
 'Uses the median score across evaluations to reduce outlier influence. Robust against extreme values.',
 'Utilise le score médian à travers les évaluations pour réduire l''influence des valeurs extrêmes. Robuste face aux outliers.',
 'fa-handshake', 6)
ON DUPLICATE KEY UPDATE name_fr = VALUES(name_fr);

-- Migration : Ajout de la colonne Espagnol (ES) pour les recommandations
-- Date : 2026-10-11
-- Description : Ajoute la colonne recommendation_text_es à la table recommendations
--               et traduit en espagnol les 50 recommandations existantes.

-- 1. Ajout de la colonne recommendation_text_es
ALTER TABLE `recommendations`
  ADD COLUMN IF NOT EXISTS `recommendation_text_es` TEXT AFTER `recommendation_text_ar`;

-- 2. Traduction des 50 recommandations en espagnol
--    Mise à jour par ID (1 à 50). Les textes espagnols correspondent
--    aux recommandations de maturité qualité automotive (AQMI).

-- === DOMAINE 1 : Leadership & Planification Stratégique ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer una visión de calidad clara y comunicarla a todos los niveles de la organización. La dirección debe demostrar su compromiso con la calidad.' WHERE `id` = 1;
UPDATE `recommendations` SET `recommendation_text_es` = 'Definir una política de calidad documentada y revisarla anualmente. Asegurar que sea comprendida por todo el personal.' WHERE `id` = 2;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar revisiones de dirección periódicas (al menos trimestrales) para evaluar el desempeño del sistema de gestión de calidad.' WHERE `id` = 3;
UPDATE `recommendations` SET `recommendation_text_es` = 'Asignar objetivos de calidad medibles (KPI) a cada departamento y seguir su cumplimiento mensualmente.' WHERE `id` = 4;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desarrollar un plan estratégico de calidad a 3-5 años con hitos intermedios y asignación de recursos.' WHERE `id` = 5;

-- === DOMAINE 2 : Système de Management de la Qualité ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un sistema de gestión de calidad conforme a la norma ISO 9001 como base mínima antes de aspirar a IATF 16949.' WHERE `id` = 6;
UPDATE `recommendations` SET `recommendation_text_es` = 'Documentar todos los procedimientos operativos clave (control de documentos, control de registros, no conformidades).' WHERE `id` = 7;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso de auditoría interna programado regularmente. Cubrir todos los procesos y sitios de producción.' WHERE `id` = 8;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar una gestión de no conformidades estructurada con análisis de causa raíz y acciones correctivas.' WHERE `id` = 9;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar revisiones de gestión documentadas con seguimiento de acciones, indicadores y resultados de auditorías.' WHERE `id` = 10;

-- === DOMAINE 3 : Conception & Développement Produit ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Formalizar un proceso de diseño y desarrollo producto con etapas, validaciones y aprobaciones documentadas.' WHERE `id` = 11;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar Analysis Failure Mode and Effects (AMDEC/FMEA) de producto en todas las nuevas concepciones.' WHERE `id` = 12;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un Plan de Control de Producción (PFC) derivado del AMDEC producto y de los planos de definición.' WHERE `id` = 13;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar ensayos de validación de producto (R&R, capacidad, fiabilidad) antes de la liberación para producción.' WHERE `id` = 14;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar la gestión de cambios de ingeniería (ECN) con análisis de impacto y aprobación multidisciplinar.' WHERE `id` = 15;

-- === DOMAINE 4 : Conception & Développement Processus ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar AMDEC de proceso para cada línea de producción. Identificar los modos de fallo y las acciones de mitigación.' WHERE `id` = 16;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar el Plan de Control de Proceso en cada puesto de trabajo con parámetros de control y frecuencias definidas.' WHERE `id` = 17;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar estudios de capacidad de proceso (Cp, Cpk) en las características críticas. Objetivo Cpk ≥ 1.33.' WHERE `id` = 18;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso PPAP (Production Part Approval Process) para la liberación de nuevas piezas y cambios.' WHERE `id` = 19;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar el flujo de producción (Flow Chart) y el layout optimizado para reducir desperdicios y mejorar la trazabilidad.' WHERE `id` = 20;

-- === DOMAINE 5 : Gestion de la Chaîne d'Approvisionnement ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso formal de cualificación de proveedores con criterios de calidad, coste y plazos.' WHERE `id` = 21;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar auditorías de proveedores periódicas basadas en el riesgo y el desempeño pasado.' WHERE `id` = 22;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desplegar indicadores de desempeña de proveedores (PPM, OTD, no conformidades) con revisiones mensuales.' WHERE `id` = 23;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un proceso de desarrollo de proveedores para los proveedores críticos con planes de mejora conjuntos.' WHERE `id` = 24;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer contratos de calidad con los proveedores estratégicos que incluyan cláusulas de garantía y recall.' WHERE `id` = 25;

-- === DOMAINE 6 : Production & Maîtrise des Processus ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar el control estadístico de procesos (SPC) en las características críticas con cartas de control X-R.' WHERE `id` = 26;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer la gestión visual en planta (5S, señalización, indicadores en tiempo real) para facilitar el control.' WHERE `id` = 27;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar la trazabilidad completa de producto (lote, fecha, operador, parámetros) mediante sistema informatizado.' WHERE `id` = 28;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar mantenimiento productivo total (TPM) con planes de mantenimiento preventivo y autónomo.' WHERE `id` = 29;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso de gestión de desviaciones temporales con análisis de riesgo y plazo de validez.' WHERE `id` = 30;

-- === DOMAINE 7 : Amélioration Continue ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un proceso CAPA (Corrective and Preventive Actions) con seguimiento de eficacia y cierre formal.' WHERE `id` = 31;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desplegar herramientas de resolución de problemas estructuradas (8D, 5 Porqués, Ishikawa) en todos los equipos.' WHERE `id` = 32;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un sistema de sugerencias de mejora con evaluación, implementación y reconocimiento.' WHERE `id` = 33;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar proyectos Kaizen periódicos enfocados en la reducción de desperdicios y mejora de productividad.' WHERE `id` = 34;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar análisis de benchmarking interno y externo para identificar mejores prácticas y oportunidades de mejora.' WHERE `id` = 35;

-- === DOMAINE 8 : Satisfaction Client ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso formal de medición de la satisfacción del cliente (encuestas, indicadores, revisiones).' WHERE `id` = 36;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un sistema de gestión de reclamaciones de cliente con análisis de causa raíz y respuesta en plazo.' WHERE `id` = 37;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desplegar indicadores de calidad producto orientados al cliente (PPM cliente, claims, warranty, field returns).' WHERE `id` = 38;
UPDATE `recommendations` SET `recommendation_text_es` = 'Realizar auditorías de producto (layout, función, apariencia) antes de cada expedición crítica.' WHERE `id` = 39;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso de comunicación proactiva con los clientes sobre incidencias, acciones y mejoras.' WHERE `id` = 40;

-- === DOMAINE 9 : Formation & Compétences ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer una matriz de competencias por puesto con evaluación inicial y periódica del personal.' WHERE `id` = 41;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un plan de formación anual cubriendo calidad, seguridad, procedimientos y herramientas específicas.' WHERE `id` = 42;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desplegar formación en herramientas calidad (AMDEC, AMDEC proceso, 8D, SPC, control estadístico) para los equipos clave.' WHERE `id` = 43;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un proceso de cualificación de operadores en puestos críticos con evaluación práctica y registro.' WHERE `id` = 44;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un sistema de gestión del conocimiento para capitalizar la experiencia y evitar la pérdida de saber.' WHERE `id` = 45;

-- === DOMAINE 10 : Gestion des Données & Digitalisation ===
UPDATE `recommendations` SET `recommendation_text_es` = 'Digitalizar los datos de calidad (control de recepción, control de proceso, no conformidades) con un sistema centralizado.' WHERE `id` = 46;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar un cuadro de mando de calidad en tiempo real (dashboard) con indicadores clave accesibles a la dirección.' WHERE `id` = 47;
UPDATE `recommendations` SET `recommendation_text_es` = 'Establecer un sistema MES (Manufacturing Execution System) para la trazabilidad y el control de producción en tiempo real.' WHERE `id` = 48;
UPDATE `recommendations` SET `recommendation_text_es` = 'Implementar el análisis de datos de calidad (Big Data) para identificar tendencias, correlaciones y causas raíz.' WHERE `id` = 49;
UPDATE `recommendations` SET `recommendation_text_es` = 'Desplegar la gestión electrónica de documentos (EDM) para los registros de calidad con control de versiones y conservación.' WHERE `id` = 50;

-- 3. Recommandations globales (sans domaine spécifique, basées sur le score global)
--    Ces recommandations s'appliquent si les IDs 51+ existent (recommandations globales)
UPDATE `recommendations` SET `recommendation_text_es` = 'Su puntuación global es muy baja. Es imperativo establecer un sistema de gestión de calidad básico (ISO 9001) como fundación.' WHERE `condition_field` = 'global_score' AND `condition_operator` = '<' AND `condition_value` = '30' AND `recommendation_text_es` IS NULL;
UPDATE `recommendations` SET `recommendation_text_es` = 'Su puntuación global indica una base de calidad en construcción. Priorice la documentación de procesos y la formación de los equipos.' WHERE `condition_field` = 'global_score' AND `condition_operator` = '<' AND `condition_value` = '50' AND `recommendation_text_es` IS NULL;
UPDATE `recommendations` SET `recommendation_text_es` = 'Su puntuación global muestra una estructura de calidad sólida. Concéntrese en la mejora continua y la auditoría interna.' WHERE `condition_field` = 'global_score' AND `condition_operator` = '<' AND `condition_value` = '70' AND `recommendation_text_es` IS NULL;
UPDATE `recommendations` SET `recommendation_text_es` = 'Su puntuación global es buena. Apunte a la certificación IATF 16949 y a la digitalización de los procesos de calidad.' WHERE `condition_field` = 'global_score' AND `condition_operator` = '<' AND `condition_value` = '85' AND `recommendation_text_es` IS NULL;
UPDATE `recommendations` SET `recommendation_text_es` = 'Excelente desempeño de calidad. Mantenga el nivel, comparta las mejores prácticas y desarrolle la cultura de excelencia operativa.' WHERE `condition_field` = 'global_score' AND `condition_operator` = '>=' AND `condition_value` = '85' AND `recommendation_text_es` IS NULL;

-- Migration: Add multi-type question support columns
-- This migration adds the missing columns that the application code already
-- references but the database schema lacks. Without these columns, only the
-- rating_scale (1-5) question type works; all other types silently fall back
-- to rating_scale.

-- 1. Add columns to the questions table
ALTER TABLE `questions`
  ADD COLUMN IF NOT EXISTS `question_type` VARCHAR(50) NOT NULL DEFAULT 'rating_scale' AFTER `model_id`,
  ADD COLUMN IF NOT EXISTS `options` TEXT DEFAULT NULL AFTER `question_type`,
  ADD COLUMN IF NOT EXISTS `is_required` TINYINT(1) NOT NULL DEFAULT 1 AFTER `options`,
  ADD COLUMN IF NOT EXISTS `help_text` TEXT DEFAULT NULL AFTER `is_required`,
  ADD COLUMN IF NOT EXISTS `help_text_fr` TEXT DEFAULT NULL AFTER `help_text`,
  ADD COLUMN IF NOT EXISTS `help_text_ar` TEXT DEFAULT NULL AFTER `help_text_fr`;

-- 2. Add columns to the assessment_answers table
ALTER TABLE `assessment_answers`
  ADD COLUMN IF NOT EXISTS `answer_text` TEXT DEFAULT NULL AFTER `score`,
  ADD COLUMN IF NOT EXISTS `answer_value` VARCHAR(255) DEFAULT NULL AFTER `answer_text`;

-- 3. Relax the score constraint to allow NULL (for text/numeric questions)
-- The existing CHECK constraint allows score 0-5; for text_input questions
-- the score may be NULL. We need to drop and re-add the constraint.
ALTER TABLE `assessment_answers` DROP CHECK IF EXISTS `assessment_answers_chk_1`;
ALTER TABLE `assessment_answers`
  ADD CONSTRAINT `chk_answer_score` CHECK (`score` IS NULL OR (`score` >= 0 AND `score` <= 5));

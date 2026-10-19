-- Add preferred_lang column to users table
-- Allows each user to choose their interface language from their profile.
-- Values: 'fr', 'en', 'ar', 'es'. Default is 'fr'.
ALTER TABLE users ADD COLUMN preferred_lang VARCHAR(5) DEFAULT 'fr';

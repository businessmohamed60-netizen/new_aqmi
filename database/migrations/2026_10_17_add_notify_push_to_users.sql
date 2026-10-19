-- Add notify_push column to users (in-app push notification preference, default 1)
-- Allows administrators to select which users receive push notifications.
ALTER TABLE users ADD COLUMN IF NOT EXISTS notify_push TINYINT(1) DEFAULT 1;

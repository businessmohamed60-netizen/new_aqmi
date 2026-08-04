-- Add consent columns to the leads table.
-- consent_contact: user accepts being contacted using their personal data.
-- consent_share_industry: user accepts their score being shared with
--   industrial partners who may be interested in their results.
-- Both are mandatory (NOT NULL DEFAULT 0) and validated server-side.
ALTER TABLE `leads`
  ADD COLUMN `consent_contact` TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN `consent_share_industry` TINYINT(1) NOT NULL DEFAULT 0;

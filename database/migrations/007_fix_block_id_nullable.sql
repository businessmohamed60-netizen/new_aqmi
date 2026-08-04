-- =============================================================
-- AQMI Report Studio — Fix block_id nullable constraint
-- The builder can save blocks whose block_key exists in the
-- BlockRegistry but not yet in the report_blocks table.
-- block_id must be nullable with ON DELETE SET NULL.
-- =============================================================

ALTER TABLE report_template_blocks
  DROP FOREIGN KEY fk_tblocks_block;

ALTER TABLE report_template_blocks
  MODIFY COLUMN block_id INT NULL;

ALTER TABLE report_template_blocks
  ADD CONSTRAINT fk_tblocks_block FOREIGN KEY (block_id)
      REFERENCES report_blocks(id) ON DELETE SET NULL;

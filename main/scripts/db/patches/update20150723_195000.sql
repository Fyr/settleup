CREATE TABLE _contractor_vendor LIKE contractor_vendor;
ALTER TABLE _contractor_vendor ADD UNIQUE INDEX(contractor_id, vendor_id);
INSERT IGNORE INTO _contractor_vendor SELECT * FROM contractor_vendor;
RENAME TABLE contractor_vendor TO contractor_vendor_old, _contractor_vendor TO contractor_vendor;
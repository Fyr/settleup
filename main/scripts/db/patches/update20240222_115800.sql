UPDATE carrier
SET short_code = LOWER(REPLACE(name, ' ', '_'))
WHERE short_code IS NULL;

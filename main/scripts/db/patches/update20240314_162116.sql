CREATE TABLE rate (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    rate DECIMAL(10,2),
    created_by INT(10) UNSIGNED,
    created_at DATETIME DEFAULT NOW(),
    KEY `fk_created_by` (`created_by`),
    CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);
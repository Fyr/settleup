SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`payments_temp` ADD COLUMN `error` VARCHAR(255) NULL DEFAULT NULL  AFTER `status_id` , CHANGE COLUMN `status_id` `status_id` INT(10) UNSIGNED NOT NULL  ,
  ADD CONSTRAINT `payment_temp_status_id`
  FOREIGN KEY (`status_id` )
  REFERENCES `pfleet`.`payment_temp_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `payment_temp_status_id` (`status_id` ASC) ;

CREATE  TABLE IF NOT EXISTS `pfleet`.`payment_temp_status` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `pfleet`.`payment_temp_status` (
`id` ,
`title`
)
VALUES (
'1', 'Valid'
), (
'2', 'Not Valid'
);
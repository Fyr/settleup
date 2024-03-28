SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `pfleet`.`carrier_vendor` DROP FOREIGN KEY `fk_carrier_vendor_status` ;

ALTER TABLE `pfleet`.`carrier_vendor` 
  ADD CONSTRAINT `fk_carrier_vendor_status`
  FOREIGN KEY (`status` )
  REFERENCES `pfleet`.`vendor_status` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE  TABLE IF NOT EXISTS `pfleet`.`contractor_code` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `carrier_id` INT(10) UNSIGNED NOT NULL ,
  `contractor_id` INT(10) UNSIGNED NOT NULL ,
  `code` INT(10) NOT NULL ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `fk_contractor_code_carrier_id` (`carrier_id` ASC) ,
  INDEX `fk_contractor_code_contractor_id` (`contractor_id` ASC) ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `carrier_id_code_UNIQUE` (`carrier_id` ASC, `code` ASC) ,
  CONSTRAINT `fk_contractor_code_carrier_id`
    FOREIGN KEY (`carrier_id` )
    REFERENCES `pfleet`.`carrier` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contractor_code_contractor_id`
    FOREIGN KEY (`contractor_id` )
    REFERENCES `pfleet`.`contractor` (`entity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


INSERT INTO `pfleet`.`contractor_status`(`id`, `title`) VALUES ( 4, 'Not configured');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

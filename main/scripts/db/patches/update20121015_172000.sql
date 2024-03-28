SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `pfleet`.`recurring_payment` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `payment_id` INT(10) UNSIGNED NOT NULL ,
  `recurring_date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_recurring_payment_id` (`payment_id` ASC) ,
  CONSTRAINT `fk_recurring_payment_id`
    FOREIGN KEY (`payment_id` )
    REFERENCES `pfleet`.`payments` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;

CREATE  TABLE IF NOT EXISTS `pfleet`.`recurring_deduction` (
  `id` INT(10) UNSIGNED NOT NULL ,
  `deduction_id` INT(10) UNSIGNED NOT NULL ,
  `recurring_date` DATE NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_recurring_deduction_id` (`deduction_id` ASC) ,
  CONSTRAINT `fk_recurring_deduction_id`
    FOREIGN KEY (`deduction_id` )
    REFERENCES `pfleet`.`deductions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_bin;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

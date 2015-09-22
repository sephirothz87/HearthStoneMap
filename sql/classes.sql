DROP TABLE IF EXISTS `classes`;

CREATE TABLE `classes` (
  `id` INT NOT NULL,
  `name_cn` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_cn_UNIQUE` (`name_cn` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

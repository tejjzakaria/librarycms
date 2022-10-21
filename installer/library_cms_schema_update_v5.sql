-- MySQL Workbench Synchronization

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `Bindings` 
CHANGE COLUMN `name` `name` VARCHAR(45) NOT NULL ,
ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);

--
-- Dumping data for table `Bindings`
--
LOCK TABLES `Bindings` WRITE;
/*!40000 ALTER TABLE `Bindings` DISABLE KEYS */;
INSERT INTO `Bindings` VALUES (1,'Hardcover'),(2,'Softcover');
/*!40000 ALTER TABLE `Bindings` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `BookSizes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

--
-- Dumping data for table `BookSizes`
--
LOCK TABLES `BookSizes` WRITE;
/*!40000 ALTER TABLE `BookSizes` DISABLE KEYS */;
INSERT INTO `BookSizes` VALUES (1,'Huge'),(2,'Large'),(3,'Medium'),(4,'Small'),(5,'Tiny');
/*!40000 ALTER TABLE `BookSizes` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `BookTypes` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;

--
-- Dumping data for table `BookTypes`
--
LOCK TABLES `BookTypes` WRITE;
/*!40000 ALTER TABLE `BookTypes` DISABLE KEYS */;
INSERT INTO `BookTypes` VALUES (1,'Digital'),(2,'Standard');
/*!40000 ALTER TABLE `BookTypes` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `PhysicalForms` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;
--
-- Dumping data for table `PhysicalForms`
--

LOCK TABLES `PhysicalForms` WRITE;
/*!40000 ALTER TABLE `PhysicalForms` DISABLE KEYS */;
INSERT INTO `PhysicalForms` VALUES (1,'Book'),(2,'CD/DVD'),(3,'Journal'),(4,'Manuscript'),(5,'Other');
/*!40000 ALTER TABLE `PhysicalForms` ENABLE KEYS */;
UNLOCK TABLES;

ALTER TABLE `Users` 
ADD COLUMN `isLdapUser` BIT(1) NOT NULL DEFAULT b'0' AFTER `isActive`;

--
-- Dumping data for table `Permissions`
--
LOCK TABLES `Permissions` WRITE;
/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
INSERT INTO `Permissions` VALUES (276,'ldapSettings','LDAP Settings','\0'),(277,'ldapTest','LDAP Test','\0'),(278,'bindingListView','Binding List View','\0'),(279,'bookSizeListView','Book Sizes List View','\0'),(280,'bookTypeListView','Book Type List View','\0'),(281,'physicalFormListView','Physical Form List View','\0');
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;
UNLOCK TABLES;

ALTER TABLE `Books` 
CHANGE COLUMN `binding` `binding` VARCHAR(20) DEFAULT 'Hardcover' COMMENT 'Book binding' ,
CHANGE COLUMN `physicalForm` `physicalForm` VARCHAR(20) DEFAULT 'Book' COMMENT 'Physical form of book' ,
CHANGE COLUMN `size` `size` VARCHAR(20) DEFAULT 'Medium' COMMENT 'Book size' ,
CHANGE COLUMN `type` `type` VARCHAR(20) DEFAULT 'Standard' COMMENT 'Type of book' ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `Languages` 
CHANGE COLUMN `name` `name` VARCHAR(25) NOT NULL ,
CHANGE COLUMN `code` `code` VARCHAR(10) NOT NULL ,
CHANGE COLUMN `isActive` `isActive` BIT(1) NOT NULL DEFAULT b'0' ,
CHANGE COLUMN `shortCode` `shortCode` VARCHAR(2) NOT NULL ,
ADD UNIQUE INDEX `code_UNIQUE` (`code` ASC);

--
-- Dumping data for table `Permissions`
--
LOCK TABLES `Permissions` WRITE;
/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
INSERT INTO `Permissions` VALUES (247,'internalServerError','InternalServerError',''),(248,'publicSessionReset','Session Reset',''),(249,'sessionReset','Session Reset','\0'),(250,'bookBulkDelete','Book Bulk Delete','\0'),(251,'storeBooksView','Store Books View','\0'),(252,'locationBooksView','Location Books View','\0'),(253,'storeSearchPublic','Store Search',''),(254,'locationSearchPublic','Location Search',''),(255,'electronicBookUpload','eBook Upload','\0'),(256,'electronicBookGet','eBook Get',''),(257,'electronicBookView','eBook View',''),(258,'electronicBookViewAdmin','eBook View','\0'),(259,'electronicBookDelete','eBook Delete','\0'),(260,'electronicBookListView','eBook List View','\0'),(261,'storeListView','Store List View','\0'),(262,'storeCreate','Store Create','\0'),(263,'storeEdit','Store Edit','\0'),(264,'storeDelete','Store Delete','\0'),(265,'locationListView','Location List View','\0'),(266,'locationCreate','Location Create','\0'),(267,'locationEdit','Location Edit','\0'),(268,'locationDelete','Location Delete','\0');
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `RolePermissions`
--

LOCK TABLES `RolePermissions` WRITE;
/*!40000 ALTER TABLE `RolePermissions` DISABLE KEYS */;
INSERT INTO `RolePermissions` VALUES (3,258),(2,259),(2,260),(2,255),(2,258),(2,266),(2,268),(2,267),(2,265),(2,251),(2,262),(2,263),(2,261),(2,250),(2,264),(2,252);
/*!40000 ALTER TABLE `RolePermissions` ENABLE KEYS */;
UNLOCK TABLES;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
-- MySQL Workbench Synchronization

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `Books` 
DROP FOREIGN KEY `BOOK_SERIES`,
DROP FOREIGN KEY `BOOK_PUBLISHER`;

CREATE TABLE IF NOT EXISTS `BookCopies` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bookId` INT(10) UNSIGNED NOT NULL,
  `bookSN` VARCHAR(50) NULL DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL,
  `issueStatus` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `bookSN_UNIQUE` (`bookSN` ASC),
  INDEX `BOOKCOPIES_BOOKS_idx` (`bookId` ASC),
  CONSTRAINT `BOOKCOPIES_BOOKS`
    FOREIGN KEY (`bookId`)
    REFERENCES `Books` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 11378
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `BookFields` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `control` VARCHAR(45) NOT NULL COMMENT 'INPUT, TEXTAREA, SELECT, CHECKBOX',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `BookImages` (
  `bookId` INT(10) UNSIGNED NOT NULL,
  `imageId` INT(10) UNSIGNED NOT NULL,
  INDEX `BOOKIMAGES_BOOK_idx` (`bookId` ASC),
  INDEX `BOOKIMAGES_IMAGE_idx` (`imageId` ASC),
  CONSTRAINT `BOOKIMAGES_BOOK`
    FOREIGN KEY (`bookId`)
    REFERENCES `Books` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `BOOKIMAGES_IMAGE`
    FOREIGN KEY (`imageId`)
    REFERENCES `Images` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `BookTags` (
  `bookId` INT(10) UNSIGNED NOT NULL,
  `tagId` INT(10) UNSIGNED NOT NULL,
  INDEX `BOOKTAGS_BOOK_idx` (`bookId` ASC),
  INDEX `BOOKTAGS_TAG_idx` (`tagId` ASC),
  CONSTRAINT `BOOKTAGS_BOOK`
    FOREIGN KEY (`bookId`)
    REFERENCES `Books` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `BOOKTAGS_TAG`
    FOREIGN KEY (`tagId`)
    REFERENCES `Tags` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `Books` 
CHANGE COLUMN `metaDescription` `metaDescription` LONGTEXT NULL DEFAULT NULL AFTER `metaKeywords`,
CHANGE COLUMN `quantity` `quantity` INT(3) NULL DEFAULT '0' COMMENT 'Number of copies' ,
CHANGE COLUMN `actualQuantity` `actualQuantity` INT(3) NULL DEFAULT '0' COMMENT 'Actual number of book copies that can be issued right now' ,
ADD COLUMN `url` VARCHAR(255) NULL DEFAULT NULL AFTER `rating`,
ADD COLUMN `test` VARCHAR(45) NULL DEFAULT NULL AFTER `creationDateTime`,
ADD COLUMN `test2` VARCHAR(255) NULL DEFAULT NULL AFTER `test`,
ADD COLUMN `CheckBox` VARCHAR(255) NULL DEFAULT NULL AFTER `test2`,
ADD COLUMN `textarea` LONGTEXT NULL DEFAULT NULL AFTER `CheckBox`,
ADD UNIQUE INDEX `url_UNIQUE` (`url` ASC),
DROP INDEX `bookSN_UNIQUE` ;

ALTER TABLE `Images` 
CHANGE COLUMN `isExternalLink` `isExternalLink` BIT(1) NULL DEFAULT b'0' ,
ADD COLUMN `width` INT(10) NULL DEFAULT NULL COMMENT 'Image width' AFTER `isExternalLink`,
ADD COLUMN `height` INT(10) NULL DEFAULT NULL COMMENT 'Image height' AFTER `width`,
ADD COLUMN `size` INT(10) NULL DEFAULT NULL COMMENT 'Image size in bytes' AFTER `height`;

CREATE TABLE IF NOT EXISTS `IssueLogs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Order ID',
  `userId` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'User that created order',
  `userFullName` VARCHAR(100) NULL DEFAULT NULL,
  `bookId` INT(10) UNSIGNED NULL DEFAULT NULL,
  `bookCopyId` INT(10) UNSIGNED NULL DEFAULT NULL,
  `bookTitle` VARCHAR(255) NULL DEFAULT NULL,
  `bookISBN` VARCHAR(13) NULL DEFAULT NULL,
  `bookSN` VARCHAR(50) NULL DEFAULT NULL,
  `requestId` INT(10) UNSIGNED NULL DEFAULT NULL,
  `requestStatus` ENUM('Pending','Accepted','Rejected') NULL DEFAULT NULL,
  `requestNotes` LONGTEXT NULL DEFAULT NULL,
  `requestDateTime` DATETIME NULL DEFAULT NULL,
  `requestAcceptRejectDateTime` DATETIME NULL DEFAULT NULL,
  `issueId` INT(10) UNSIGNED NULL DEFAULT NULL,
  `issueDate` DATE NULL DEFAULT NULL COMMENT 'Date of issue of the book',
  `expiryDate` DATE NULL DEFAULT NULL COMMENT 'Expiration date - date when book should be returned',
  `returnDate` DATE NULL DEFAULT NULL COMMENT 'Date of return of the book - real return date',
  `isLost` BIT(1) NULL DEFAULT b'0',
  `penalty` DECIMAL(10,2) NULL DEFAULT '0.00',
  `updateDateTime` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `requestId_UNIQUE` (`requestId` ASC),
  UNIQUE INDEX `issueId_UNIQUE` (`issueId` ASC),
  INDEX `BOOK_ID_idx` (`bookId` ASC),
  INDEX `UPDATE_DATE_TIME` (`updateDateTime` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 26
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `Issues` 
CHANGE COLUMN `penalty` `penalty` DECIMAL(10,2) NULL DEFAULT '0.00' ,
ADD COLUMN `bookCopyId` INT(10) UNSIGNED NOT NULL AFTER `bookId`,
ADD INDEX `ISSUE_BOOKCOPY_idx` (`bookCopyId` ASC);

CREATE TABLE IF NOT EXISTS `ListValues` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fieldId` INT(10) UNSIGNED NOT NULL,
  `value` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `LISTVALUES_BOOKFIELDS_idx` (`fieldId` ASC),
  CONSTRAINT `LISTVALUES_BOOKFIELDS`
    FOREIGN KEY (`fieldId`)
    REFERENCES `BookFields` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 24
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `Tags` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  `description` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `Users` 
CHANGE COLUMN `photoId` `photoId` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Id of user\'s photo' AFTER `roleId`,
CHANGE COLUMN `email` `email` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `password` `password` VARCHAR(200) NULL DEFAULT NULL ,
ADD COLUMN `birthday` DATE NULL DEFAULT NULL AFTER `gender`,
ADD COLUMN `provider` VARCHAR(15) NULL DEFAULT NULL AFTER `birthday`,
ADD COLUMN `socialId` VARCHAR(50) NULL DEFAULT NULL AFTER `provider`,
ADD COLUMN `socialPage` VARCHAR(255) NULL DEFAULT NULL AFTER `socialId`,
ADD UNIQUE INDEX `email_socialId_provider_UNIQUE` (`email` ASC, `provider` ASC, `socialId` ASC),
DROP INDEX `email_UNIQUE`;


DROP PROCEDURE IF EXISTS migrateBooksToCopies;

CREATE PROCEDURE migrateBooksToCopies(OUT bookCopyCounter INT)
BEGIN
	DECLARE done BIT(1);
	DECLARE bookSN VARCHAR(50);
    DECLARE bookId INTEGER;
    DECLARE bookQuantity INTEGER;
    DECLARE i INTEGER;
	
    DECLARE bookCursor CURSOR FOR SELECT Books.id,Books.quantity,Books.bookSN FROM Books; 
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	SET bookCopyCounter = 0;
	
	OPEN bookCursor;
    SET done = FALSE;
	WHILE NOT done  DO 
		FETCH bookCursor INTO bookId, bookQuantity, bookSN;
		SET i = 0;

		IF bookId <> 0 THEN
			WHILE i < bookQuantity DO
				IF i <> 0 OR bookSN is NULL THEN
					SET bookSN = CONCAT("Book #",bookId," Copy #", i);
				END IF;
				
				INSERT INTO `BookCopies` (`bookId`, `bookSN`, `status`, `issueStatus`) VALUES (bookId,bookSN, "NEW", "AVAILABLE");
				SET bookCopyCounter = bookCopyCounter + 1;
				SET i = i + 1; 
						
			END WHILE;
			SET bookId = NULL;
            
        END IF;
		
    END WHILE; 
    
	CLOSE bookCursor; 
END;

DROP PROCEDURE IF EXISTS migrateIssuesToCopies;

CREATE PROCEDURE migrateIssuesToCopies(OUT issueCounter INT)
BEGIN
	DECLARE issueDone BIT(1);
    DECLARE bookCopyDone BIT(1);
    DECLARE lastBookId INTEGER;
	DECLARE currentBookId INTEGER;
	DECLARE currentBookCounter INTEGER;
    DECLARE currentIssueId INTEGER;
    DECLARE curBookCopyId INTEGER;
	DECLARE i INTEGER;
	
    DECLARE issueCursor CURSOR FOR SELECT id, bookId FROM Issues WHERE returnDate is null ORDER BY bookId ASC;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET issueDone = TRUE;
	SET issueCounter = -1;
	SET lastBookId = -1;
    
	OPEN issueCursor;
    SET issueDone = FALSE;
	WHILE NOT issueDone  DO 
		FETCH issueCursor INTO currentIssueId, currentBookId;

        IF lastBookId <> currentBookId THEN
			SET lastBookId = currentBookId;
            SET currentBookCounter = 0;
		END IF;

		BLOCK1: BEGIN
			DECLARE bookCopyCursor CURSOR FOR SELECT id FROM BookCopies WHERE bookId = lastBookId ORDER BY id ASC;
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET bookCopyDone = TRUE;
			OPEN bookCopyCursor;

            SET i = 0;   
            SET bookCopyDone = FALSE;
			
            REPEAT
				FETCH bookCopyCursor INTO curBookCopyId;
				SET i = i + 1;
			UNTIL i >= currentBookCounter OR bookCopyDone END REPEAT;

            SET currentBookCounter = currentBookCounter + 1;
            UPDATE `Issues` SET `bookCopyId` = curBookCopyId WHERE id = currentIssueId;
            UPDATE `BookCopies` SET `issueStatus` = 'ISSUED' WHERE id = curBookCopyId;
			CLOSE bookCopyCursor; 
		END BLOCK1;

        SET issueCounter = issueCounter + 1;
    END WHILE; 
    
	CLOSE issueCursor; 
END;


CALL `migrateBooksToCopies`(@out_value);
SELECT @out_value;

UPDATE `Issues`
	JOIN BookCopies ON BookCopies.bookId = Issues.bookId
    SET `bookCopyId` = BookCopies.id;
 
CALL `migrateIssuesToCopies`(@out_value);
SELECT @out_value;


LOCK TABLES `Permissions` WRITE;
/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
INSERT INTO `Permissions` VALUES (282,'userProfile','Public User Profile',''),(283,'socialAuth','Social Auth',''),(284,'bookUrlGenerate','Book Url Generate','\0'),(285,'bookUrlCheck','Book Url Check',''),(286,'bookLayout','Book Layout','\0'),(287,'bookVisibleFieldsForPublic','Book Visible Fields For Public','\0'),(288,'bookCopyCreate','Book Copy Create','\0'),(289,'bookCopyEdit','Book Copy Edit','\0'),(290,'bookCopyDelete','Book Copy Delete','\0'),(291,'bookSNCheck','BookSN Check','\0'),(292,'bookFieldListView','Book Field List View','\0'),(293,'bookFieldCreate','Book Field Create','\0'),(294,'bookFieldEdit','Book Field Edit','\0'),(295,'bookFieldDelete','Book Field Delete','\0'),(296,'bookFieldCheck','Book Field Check','\0'),(297,'issueLogListView','Issue Log List View','\0'),(298,'bookIssueLogListView','Book Issue Log List View','\0'),(299,'issueCreatePublic','Issue Create Public',''),(300,'bookSetLostPublic','Issued Book Set Lost Public',''),(301,'bookSetReturnedPublic','Issued Book Set Returned Public',''),(302,'bookImageUpload','Book Image Upload','\0'),(303,'imageDeletePublic','Image Delete Public',''),(304,'colorSchemaActivate','Color Schema Activate','\0'),(305,'socialNetworkSettings','Social Network Settings','\0'),(306,'smsSettings','SMS Settings','\0'),(307,'smsSend','SMS Send','\0'),(308,'smsGetBalance','SMS Get Balance','\0'),(309,'bookGoogleSearchByIsbnPublic','Google Search Book By ISBN',''),(310,'bookByGoogleDataGet','Book By Google Data Get',''),(311,'tagBooksPublic','Tag Books View Public',''),(312,'tagSearchPublic','Tag Search',''),(313,'envatoLicenseVerificationPublic','Envato License Verification',''),(314,'tagListView','Tag List View','\0'),(315,'tagCreate','Tag Create','\0'),(316,'tagEdit','Tag Edit','\0'),(317,'tagDelete','Tag Delete','\0'),(318,'bookViewViaUrlPublic','Book View Public via URL','');
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `RolePermissions` WRITE;
/*!40000 ALTER TABLE `RolePermissions` DISABLE KEYS */;
DELETE FROM `RolePermissions` WHERE `roleId` = 3 AND `permissionId` = 71;
DELETE FROM `RolePermissions` WHERE `roleId` = 3 AND `permissionId` = 72;
DELETE FROM `RolePermissions` WHERE `roleId` = 3 AND `permissionId` = 132;
DELETE FROM `RolePermissions` WHERE `roleId` = 3 AND `permissionId` = 237;
DELETE FROM `RolePermissions` WHERE `roleId` = 3 AND `permissionId` = 258;
INSERT INTO `RolePermissions` VALUES (2,288),(2,290),(2,289),(2,291),(2,297),(2,307),(2,315),(2,317),(2,316),(2,314);
/*!40000 ALTER TABLE `RolePermissions` ENABLE KEYS */;
UNLOCK TABLES;


UPDATE `Posts`
SET `url` = IF(LEFT(`url`, 1) = '/', SUBSTR(`url`, 2), `url`);
    
UPDATE `Pages`
SET `url` = IF(LEFT(`url`, 1) = '/', SUBSTR(`url`, 2), `url`),
    `partialUrl` = IF(LEFT(`partialUrl`, 1) = '/', SUBSTR(`partialUrl`, 2), `partialUrl`);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

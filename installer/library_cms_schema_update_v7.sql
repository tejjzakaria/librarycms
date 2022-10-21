-- MySQL Workbench Synchronization

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `Authors` 
ADD INDEX `AUTHOR_FIRSTNAME` USING BTREE (`firstName` ASC),
ADD INDEX `AUTHOR_LASTNAME` USING BTREE (`lastName` ASC),
ADD INDEX `AUTHOR_MIDDLENAME` USING BTREE (`middleName` ASC);

ALTER TABLE `BookAuthors` 
ADD INDEX `BOOK_AUTHOR_idx` (`authorId` ASC, `bookId` ASC);

ALTER TABLE `BookFields` 
ADD COLUMN `isFilterable` BIT(1) NULL DEFAULT b'0' AFTER `control`;

ALTER TABLE `Books` 
ADD INDEX `BOOK_ISBN10` USING BTREE (`ISBN10` ASC),
ADD INDEX `BOOK_RATING` USING BTREE (`rating` ASC),
ADD INDEX `BOOK_UPDATE` USING BTREE (`updateDateTime` ASC),
ADD INDEX `BOOK_CREATE` USING BTREE (`creationDateTime` ASC),
ADD INDEX `BOOK_TITLE` USING BTREE (`title` ASC),
ADD INDEX `BOOK_ISBN13` USING BTREE (`ISBN13` ASC);

ALTER TABLE `IssueLogs` 
ADD COLUMN `isIssueDeleted` BIT(1) NULL DEFAULT b'0' AFTER `isLost`;

ALTER TABLE `Publishers` 
ADD INDEX `PUBLISHER_NAME` USING BTREE (`name` ASC);

CREATE PROCEDURE `getRandomAuthors`(IN AUTHOR_LIMIT INT(10))
BEGIN
	
	DECLARE TEMP_TABLE_NAME VARCHAR(50);
    DECLARE MAX_ID INT;
    DECLARE RAND_SIZE INT;
    DECLARE RAND_ID INT;
	DECLARE COUNTER INT;
    DECLARE ITERATOR INT;
	SET TEMP_TABLE_NAME = CONCAT("rands",NOW() + 0);
    
    DROP TEMPORARY TABLE IF EXISTS TEMP_TABLE_NAME;
    CREATE TEMPORARY TABLE TEMP_TABLE_NAME ( randId INT unique );
    
    SET MAX_ID = (SELECT MAX(id) FROM Authors);
    SET COUNTER = 0;
    SET ITERATOR = 0;

	loop_me: LOOP
		-- check if found required number of rercord or max of iterations reached
		IF (AUTHOR_LIMIT <= COUNTER OR ITERATOR > 20) THEN
		  LEAVE loop_me;
		END IF;
        
        -- get max table id
        SET RAND_ID = (RAND() * MAX_ID);

		-- insert random id in temp table
		INSERT IGNORE  INTO TEMP_TABLE_NAME
        SELECT Authors.id FROM Authors
		WHERE Authors.id >= RAND_ID  	
        ORDER BY Authors.id
		LIMIT 1;

		SET RAND_SIZE = (SELECT count( distinct randId) FROM TEMP_TABLE_NAME);
		-- if random id found
		IF RAND_SIZE > COUNTER THEN
			SET COUNTER = COUNTER + 1;
        END IF;
       
        SET ITERATOR = ITERATOR + 1;
	END LOOP loop_me;

	-- get data by random ids
	SELECT 	Authors.*,  
			Images.title as imageTitle,
            Images.path,
            Images.uploadingDateTime
	FROM Authors 
    LEFT JOIN Images ON Images.id = Authors.photoId
    WHERE Authors.id IN (SELECT distinct randId FROM TEMP_TABLE_NAME)
    ORDER BY photoId DESC;
   -- DROP TEMPORARY TABLE IF EXISTS TEMP_TABLE_NAME;
END;

CREATE PROCEDURE `getRandomBooks`(IN BOOK_LIMIT INT(10))
BEGIN
	
	DECLARE TEMP_TABLE_NAME VARCHAR(50);
    DECLARE MAX_ID INT;
    DECLARE RAND_SIZE INT;
    DECLARE RAND_ID INT;
	DECLARE COUNTER INT;
    DECLARE ITERATOR INT;
	SET TEMP_TABLE_NAME = CONCAT("rands",NOW() + 0);
    
    DROP TEMPORARY TABLE IF EXISTS TEMP_TABLE_NAME;
    CREATE TEMPORARY TABLE TEMP_TABLE_NAME ( randId INT unique );
    
    SET MAX_ID = (SELECT MAX(id) FROM Books);
    SET COUNTER = 0;
    SET ITERATOR = 0;

	loop_me: LOOP
		-- check if found required number of rercord or max of iterations reached
		IF (BOOK_LIMIT <= COUNTER OR ITERATOR > 20) THEN
		  LEAVE loop_me;
		END IF;
        
        -- get max table id
        SET RAND_ID = (RAND() * MAX_ID);

		-- insert random id in temp table
		INSERT IGNORE  INTO TEMP_TABLE_NAME
        SELECT Books.id FROM Books
		WHERE Books.id >= RAND_ID  	
        ORDER BY Books.id
		LIMIT 1;

		SET RAND_SIZE = (SELECT count( distinct randId) FROM TEMP_TABLE_NAME);
		-- if random id found
		IF RAND_SIZE > COUNTER THEN
			SET COUNTER = COUNTER + 1;
        END IF;
       
        SET ITERATOR = ITERATOR + 1;
	END LOOP loop_me;

	-- get data by random ids
	SELECT 	Books.*,  
			Images.title as imageTitle,
            Images.path,
            Images.uploadingDateTime
	FROM Books 
    LEFT JOIN Images ON Images.id = Books.coverId
    WHERE Books.id IN (SELECT distinct randId FROM TEMP_TABLE_NAME)
    ORDER BY coverId DESC;
   -- DROP TEMPORARY TABLE IF EXISTS TEMP_TABLE_NAME;
END;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

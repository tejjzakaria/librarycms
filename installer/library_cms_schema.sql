/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Authors`
--

DROP TABLE IF EXISTS `Authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Authors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lastName` varchar(45) DEFAULT NULL,
  `middleName` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `description` longtext,
  `photoId` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `AUTHOR_PHOTO_idx` (`photoId`),
  CONSTRAINT `AUTHOR_PHOTO` FOREIGN KEY (`photoId`) REFERENCES `Images` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bindings`
--

DROP TABLE IF EXISTS `Bindings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Bindings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BookAuthors`
--

DROP TABLE IF EXISTS `BookAuthors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookAuthors` (
  `bookId` int(11) unsigned NOT NULL,
  `authorId` int(11) unsigned NOT NULL,
  KEY `BOOKAUTHOR_BOOK_idx` (`bookId`),
  KEY `BOOKAUTHOR_AUTHOR_idx` (`authorId`),
  CONSTRAINT `BOOKAUTHOR_AUTHOR` FOREIGN KEY (`authorId`) REFERENCES `Authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `BOOKAUTHOR_BOOK` FOREIGN KEY (`bookId`) REFERENCES `Books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BookGenres`
--

DROP TABLE IF EXISTS `BookGenres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookGenres` (
  `bookId` int(11) unsigned NOT NULL,
  `genreId` int(11) unsigned NOT NULL,
  KEY `BOOKGENRE_BOOK_idx` (`bookId`),
  KEY `BOOKGENRE_GENRE_idx` (`genreId`),
  CONSTRAINT `BOOKGENRE_BOOK` FOREIGN KEY (`bookId`) REFERENCES `Books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `BOOKGENRE_GENRE` FOREIGN KEY (`genreId`) REFERENCES `Genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BookReviews`
--

DROP TABLE IF EXISTS `BookReviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BookReviews` (
  `bookId` int(10) unsigned NOT NULL,
  `reviewId` int(10) unsigned NOT NULL,
  KEY `BOOKREWIES_BOOK_idx` (`bookId`),
  KEY `BOOKREVIEWS_REVIEW_idx` (`reviewId`),
  CONSTRAINT `BOOKREVIEWS_BOOK` FOREIGN KEY (`bookId`) REFERENCES `Books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `BOOKREVIEWS_REVIEW` FOREIGN KEY (`reviewId`) REFERENCES `Reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Books`
--

DROP TABLE IF EXISTS `Books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Books` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `seriesId` int(11) unsigned DEFAULT NULL COMMENT 'серия книг',
  `publisherId` int(11) unsigned DEFAULT NULL COMMENT 'издательство',
  `coverId` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'название на русском',
  `subtitle` varchar(150) DEFAULT NULL COMMENT 'Book subtitle',
  `ISBN10` varchar(10) DEFAULT NULL,
  `ISBN13` varchar(13) DEFAULT NULL,
  `publishingYear` int(4) DEFAULT NULL COMMENT 'Publishing year and month',
  `pages` int(4) DEFAULT NULL COMMENT 'страницы',
  `description` longtext COMMENT 'описание',
  `notes` varchar(500) DEFAULT NULL COMMENT 'Book notes',
  `quantity` int(3) NOT NULL DEFAULT '0' COMMENT 'Number of copies',
  `actualQuantity` int(3) NOT NULL DEFAULT '0' COMMENT 'Actual number of book copies that can be issued right now',
  `edition` varchar(150) DEFAULT NULL COMMENT 'Edition of the book',
  `binding` enum('Hardcover','Softcover') DEFAULT 'Hardcover' COMMENT 'переплет',
  `physicalForm` enum('Book','Manuscript','Journal','CD/DVD','Other') DEFAULT 'Book' COMMENT 'Physical form of book',
  `size` enum('Medium','Large','Huge','Small','Tiny') DEFAULT 'Medium' COMMENT 'Book size',
  `type` enum('Standard','Digital') DEFAULT 'Standard' COMMENT 'Type of book',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Book price',
  `language` varchar(45) DEFAULT NULL COMMENT 'Language of the book',
  `updateDateTime` datetime NOT NULL COMMENT 'Last update time',
  `creationDateTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `BOOK_IMAGE_idx` (`coverId`),
  KEY `BOOK_SERIES_idx` (`seriesId`),
  KEY `BOOK_PUBLISHER_idx` (`publisherId`),
  CONSTRAINT `BOOK_IMAGE` FOREIGN KEY (`coverId`) REFERENCES `Images` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `BOOK_PUBLISHER` FOREIGN KEY (`publisherId`) REFERENCES `Publishers` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `BOOK_SERIES` FOREIGN KEY (`seriesId`) REFERENCES `Series` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Categories`
--

DROP TABLE IF EXISTS `Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT 'Noname category',
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(250) NOT NULL,
  `metaTitle` varchar(100) DEFAULT NULL,
  `metaKeywords` varchar(255) DEFAULT NULL,
  `metaDescription` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `url_UNIQUE` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DynamicShortCodes`
--

DROP TABLE IF EXISTS `DynamicShortCodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DynamicShortCodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) DEFAULT NULL,
  `columnName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EmailNotifications`
--

DROP TABLE IF EXISTS `EmailNotifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailNotifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(100) NOT NULL COMMENT 'should be routeName',
  `userId` int(11) unsigned NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` longtext,
  `templateName` varchar(255) DEFAULT NULL,
  `from` varchar(255) NOT NULL COMMENT 'JSON object {email:Name}',
  `to` longtext NOT NULL COMMENT 'JSON array of objects {email1:Name1;email2:Name2;...}',
  `creationDateTime` datetime DEFAULT NULL,
  `updateDateTime` datetime DEFAULT NULL,
  `isEnabled` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `routeName_UNIQUE` (`route`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Genres`
--

DROP TABLE IF EXISTS `Genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Genres` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(300) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `uploadingDateTime` datetime DEFAULT NULL,
  `isGallery` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Issues`
--

DROP TABLE IF EXISTS `Issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Issues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Order ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'User that created order',
  `bookId` int(10) unsigned NOT NULL,
  `requestId` int(10) unsigned DEFAULT NULL,
  `issueDate` date NOT NULL COMMENT 'Date of issue of the book',
  `expiryDate` date NOT NULL COMMENT 'Expiration date - date when book should be returned',
  `returnDate` date DEFAULT NULL COMMENT 'Date of return of the book - real return date',
  `isLost` bit(1) NOT NULL DEFAULT b'0',
  `penalty` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `ISSUE_USER_idx` (`userId`),
  KEY `ISSUE_BOOK_idx` (`bookId`),
  KEY `ISSUE_REQUEST_idx` (`requestId`),
  CONSTRAINT `ISSUE_BOOK` FOREIGN KEY (`bookId`) REFERENCES `Books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ISSUE_REQUEST` FOREIGN KEY (`requestId`) REFERENCES `Requests` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `ISSUE_USER` FOREIGN KEY (`userId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `isActive` bit(1) DEFAULT NULL,
  `shortCode` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MenuItems`
--

DROP TABLE IF EXISTS `MenuItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MenuItems` (
  `id` int(11) unsigned NOT NULL,
  `menuId` int(11) unsigned NOT NULL COMMENT 'ID of menu: 1 - main menu, 2 - left menu, etc.',
  `parentId` int(11) unsigned NOT NULL COMMENT 'Parent menu item (0 - no parent)',
  `title` varchar(150) NOT NULL COMMENT 'Text to display on menu item',
  `pageId` int(11) unsigned DEFAULT NULL COMMENT 'Use specified page as menu item (by pageId)',
  `postId` int(11) unsigned DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'Link as menu item',
  `order` int(11) unsigned DEFAULT NULL COMMENT 'Order number of menu item',
  `class` varchar(50) DEFAULT NULL COMMENT 'CSS class',
  PRIMARY KEY (`id`,`menuId`),
  KEY `MENUITEM_MENU_idx` (`menuId`),
  CONSTRAINT `MENUITEM_MENU` FOREIGN KEY (`menuId`) REFERENCES `Menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Menus`
--

DROP TABLE IF EXISTS `Menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Menu name',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pages`
--

DROP TABLE IF EXISTS `Pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(10) unsigned DEFAULT NULL,
  `userId` int(11) unsigned NOT NULL,
  `url` varchar(300) DEFAULT NULL,
  `partialUrl` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` longtext,
  `metaTitle` varchar(200) DEFAULT NULL,
  `metaKeywords` varchar(250) DEFAULT NULL,
  `metaDescription` longtext,
  `imageId` int(11) unsigned DEFAULT NULL,
  `status` enum('Publish','Future','Pending') DEFAULT 'Pending',
  `publishDateTime` datetime DEFAULT NULL,
  `shortDescription` varchar(100) DEFAULT NULL,
  `creationDateTime` datetime DEFAULT NULL,
  `updateDateTime` datetime DEFAULT NULL,
  `customTemplate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `url_UNIQUE` (`url`(255)),
  KEY `PAGE_USER_idx` (`userId`),
  CONSTRAINT `PAGE_USER` FOREIGN KEY (`userId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PasswordConfirmation`
--

DROP TABLE IF EXISTS `PasswordConfirmation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PasswordConfirmation` (
  `email` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `validDateTime` datetime NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `hash_UNIQUE` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `routeName` varchar(50) NOT NULL,
  `routeTitle` varchar(100) DEFAULT NULL,
  `isPublic` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `routeName_UNIQUE` (`routeName`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PostCategories`
--

DROP TABLE IF EXISTS `PostCategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PostCategories` (
  `postId` int(10) unsigned NOT NULL,
  `categoryId` int(10) unsigned NOT NULL,
  KEY `POSTCATEGORIES_POST_idx` (`postId`),
  KEY `POSTCATEGORIES_CATEGORY___idx` (`categoryId`),
  CONSTRAINT `POSTCATEGORIES_CATEGORY__` FOREIGN KEY (`categoryId`) REFERENCES `Categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `POSTCATEGORIES_POST` FOREIGN KEY (`postId`) REFERENCES `Posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Posts`
--

DROP TABLE IF EXISTS `Posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `url` varchar(300) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `secondTitle` varchar(100) DEFAULT NULL,
  `shortDescription` varchar(250) DEFAULT NULL,
  `content` longtext,
  `metaTitle` varchar(200) DEFAULT NULL,
  `metaKeywords` varchar(250) DEFAULT NULL,
  `metaDescription` longtext,
  `imageId` int(11) unsigned DEFAULT NULL,
  `status` enum('Publish','Future','Pending') DEFAULT 'Pending',
  `publishDateTime` datetime DEFAULT NULL,
  `creationDateTime` datetime DEFAULT NULL,
  `updateDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `url_UNIQUE` (`url`(255)),
  KEY `POST_USER_idx` (`userId`),
  CONSTRAINT `POST_USER` FOREIGN KEY (`userId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Publishers`
--

DROP TABLE IF EXISTS `Publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publishers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Requests`
--

DROP TABLE IF EXISTS `Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Request ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'User that create request',
  `bookId` int(10) unsigned NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending' COMMENT 'Current request status',
  `notes` longtext COMMENT 'Request notes',
  `creationDate` date NOT NULL COMMENT 'Request creation date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `REQUEST_USER_idx` (`userId`),
  KEY `REQUEST_BOOK_idx` (`bookId`),
  CONSTRAINT `REQUEST_BOOK` FOREIGN KEY (`bookId`) REFERENCES `Books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `REQUEST_USER` FOREIGN KEY (`userId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Reviews`
--

DROP TABLE IF EXISTS `Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Review ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'User that created this review',
  `text` longtext COMMENT 'Content of review',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Id_UNIQUE` (`id`),
  KEY `REVIEW_USER_idx` (`userId`),
  CONSTRAINT `REVIEW_USER` FOREIGN KEY (`userId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RolePermissions`
--

DROP TABLE IF EXISTS `RolePermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RolePermissions` (
  `roleId` int(10) unsigned NOT NULL,
  `permissionId` int(10) unsigned NOT NULL,
  KEY `ROLEPERMISSION_PERMISSION` (`permissionId`),
  KEY `ROLEPERMISSION_ROLE` (`roleId`),
  CONSTRAINT `ROLEPERMISSION_PERMISSION` FOREIGN KEY (`permissionId`) REFERENCES `Permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ROLEPERMISSION_ROLE` FOREIGN KEY (`roleId`) REFERENCES `Roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Name of role',
  `issueDayLimit` int(3) NOT NULL COMMENT 'Issue day limitation',
  `issueBookLimit` int(3) NOT NULL COMMENT 'Issue book limitation',
  `finePerDay` decimal(10,2) NOT NULL COMMENT 'Fine per day after late book return',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series`
--

DROP TABLE IF EXISTS `Series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `isComplete` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `StaticShortCodes`
--

DROP TABLE IF EXISTS `StaticShortCodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StaticShortCodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) DEFAULT NULL,
  `value` mediumtext,
  `isLongText` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserMessages`
--

DROP TABLE IF EXISTS `UserMessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserMessages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` longtext,
  `url` varchar(250) DEFAULT NULL,
  `creationDate` datetime DEFAULT NULL,
  `isViewed` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roleId` int(11) unsigned NOT NULL COMMENT 'User''s role ID',
  `email` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `isActive` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Is user active or not?',
  `phone` varchar(45) DEFAULT NULL COMMENT 'User''s phone',
  `address` varchar(500) DEFAULT NULL COMMENT 'User''s address',
  `gender` enum('Male','Female') DEFAULT 'Male' COMMENT 'User''s gender',
  `photoId` int(10) unsigned DEFAULT NULL COMMENT 'Id of user''s photo',
  `creationDateTime` datetime NOT NULL,
  `updateDateTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `UserID` (`id`),
  KEY `USER_ROLE_idx` (`roleId`),
  CONSTRAINT `LIBRARYCMS_USER_ROLE` FOREIGN KEY (`roleId`) REFERENCES `Roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


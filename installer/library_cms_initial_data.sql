-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 192.168.1.55    Database: LibraryCMS
-- ------------------------------------------------------
-- Server version	5.5.57-MariaDB

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
-- Dumping data for table `Authors`
--

LOCK TABLES `Authors` WRITE;
/*!40000 ALTER TABLE `Authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `Authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Bindings`
--

LOCK TABLES `Bindings` WRITE;
/*!40000 ALTER TABLE `Bindings` DISABLE KEYS */;
/*!40000 ALTER TABLE `Bindings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `BookAuthors`
--

LOCK TABLES `BookAuthors` WRITE;
/*!40000 ALTER TABLE `BookAuthors` DISABLE KEYS */;
/*!40000 ALTER TABLE `BookAuthors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `BookGenres`
--

LOCK TABLES `BookGenres` WRITE;
/*!40000 ALTER TABLE `BookGenres` DISABLE KEYS */;
/*!40000 ALTER TABLE `BookGenres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `BookReviews`
--

LOCK TABLES `BookReviews` WRITE;
/*!40000 ALTER TABLE `BookReviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `BookReviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Books`
--

LOCK TABLES `Books` WRITE;
/*!40000 ALTER TABLE `Books` DISABLE KEYS */;
/*!40000 ALTER TABLE `Books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Categories`
--

LOCK TABLES `Categories` WRITE;
/*!40000 ALTER TABLE `Categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `Categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `DynamicShortCodes`
--

LOCK TABLES `DynamicShortCodes` WRITE;
/*!40000 ALTER TABLE `DynamicShortCodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `DynamicShortCodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `EmailNotifications`
--

LOCK TABLES `EmailNotifications` WRITE;
/*!40000 ALTER TABLE `EmailNotifications` DISABLE KEYS */;
INSERT INTO `EmailNotifications` VALUES (1,'userRegistration',2,'Confirmation of Registration','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n    <head>\r\n        <meta name=\"viewport\" content=\"width=device-width\"/>\r\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>\r\n        [STYLES_1]\r\n    </head>\r\n    <body>\r\n        <table class=\"body-wrap\">\r\n            <tr>\r\n                <td></td>\r\n                <td class=\"container\" width=\"600\">\r\n                    <div class=\"content\">\r\n                        <table class=\"main\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tr>\r\n                                <td class=\"alert alert-info\"><img src=\"[LOGO]\" alt=\"LibraryCMS\"></td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td class=\"content-wrap\">\r\n                                    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\">\r\n                                                <h1 class=\"\">Hello, [FIRST_NAME] [LAST_NAME]!</h1></td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\"> Thank you for registering on the site [SITE_NAME].\r\n                                            </td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\"> In order to enter your account, you need to activate it.\r\n                                            </td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\"> To activate your account, please follow this link:\r\n                                            </td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\">\r\n                                                <a href=\"[CONFIRMATION_LINK]\" class=\"btn-primary\">Confirm Email</a></td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\"> Sincerely, [SITE_NAME].\r\n                                            </td>\r\n                                        </tr>\r\n                                    </table>\r\n                                </td>\r\n                            </tr>\r\n                        </table>\r\n                        <div class=\"footer\">\r\n                            <table width=\"100%\">\r\n                                <tr>\r\n                                    <td class=\"aligncenter content-block\"><a href=\"[SITE_LINK]\">[SITE_NAME]</a></td>\r\n                                </tr>\r\n                            </table>\r\n                        </div>\r\n                    </div>\r\n                </td>\r\n                <td></td>\r\n            </tr>\r\n        </table>\r\n    </body>\r\n</html>','userRegistration.tpl','','','2017-10-29 13:33:20','2017-12-02 20:35:32',''),(2,'requestCreate',1,'User Request Books','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n    <head>\r\n        <meta name=\"viewport\" content=\"width=device-width\"/>\r\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>\r\n        [STYLES_1]\r\n    </head>\r\n\r\n    <body>\r\n        <table class=\"body-wrap\">\r\n            <tr>\r\n                <td></td>\r\n                <td class=\"container\" width=\"600\">\r\n                    <div class=\"content\">\r\n                        <table class=\"main\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tr>\r\n                                <td class=\"alert alert-info\">\r\n                                    <img src=\"[LOGO]\" alt=\"LibraryCMS\">\r\n                                </td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td class=\"content-wrap\">\r\n                                    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter pb-0\">\r\n                                                <h2>[FIRST_NAME] [LAST_NAME] Requested Books</h2>\r\n                                                <p>[REQUEST_DATE]</p>\r\n                                            </td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block\">\r\n                                                [BOOKS]\r\n                                            </td>\r\n                                        </tr>\r\n                                    </table>\r\n                                </td>\r\n                            </tr>\r\n                        </table>\r\n                        <div class=\"footer\">\r\n                            <table width=\"100%\">\r\n                                <tr>\r\n                                    <td class=\"aligncenter content-block\"><a href=\"[SITE_LINK]\">[SITE_NAME]</a></td>\r\n                                </tr>\r\n                            </table>\r\n                        </div>\r\n                    </div>\r\n                </td>\r\n                <td></td>\r\n            </tr>\r\n        </table>\r\n    </body>\r\n</html>','requestCreate.tpl','','','2017-10-31 13:08:05','2017-11-16 11:37:11',''),(3,'requestSetStatus',1,'Request Books [STATUS]','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n    <head>\r\n        <meta name=\"viewport\" content=\"width=device-width\"/>\r\n        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>\r\n        [STYLES_1]\r\n    </head>\r\n    <body>\r\n        <table class=\"body-wrap\">\r\n            <tr>\r\n                <td></td>\r\n                <td class=\"container\" width=\"600\">\r\n                    <div class=\"content\">\r\n                        <table class=\"main\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tr>\r\n                                <td class=\"alert alert-info\">\r\n                                    <img src=\"[LOGO]\" alt=\"LibraryCMS\">\r\n                                </td>\r\n                            </tr>\r\n                            <tr>\r\n                                <td class=\"content-wrap\">\r\n                                    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\">\r\n                                                <h2>[FIRST_NAME] [LAST_NAME]</h2>\r\n                                            </td>\r\n                                        </tr>\r\n                                        <tr>\r\n                                            <td class=\"content-block aligncenter\">\r\n                                                Your request ([REQUEST_DATE]) <strong>[STATUS]</strong>.\r\n                                            </td>\r\n                                        </tr>\r\n                                    </table>\r\n                                    [BOOKS]\r\n                                </td>\r\n                            </tr>\r\n                        </table>\r\n                        <div class=\"footer\">\r\n                            <table width=\"100%\">\r\n                                <tr>\r\n                                    <td class=\"aligncenter content-block\"><a href=\"[SITE_LINK]\">[SITE_NAME]</a></td>\r\n                                </tr>\r\n                            </table>\r\n                        </div>\r\n                    </div>\r\n                </td>\r\n                <td></td>\r\n            </tr>\r\n        </table>\r\n    </body>\r\n</html>','requestSetStatus.tpl','','','2017-10-31 14:17:13','2017-11-16 11:59:44','');
/*!40000 ALTER TABLE `EmailNotifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Genres`
--

LOCK TABLES `Genres` WRITE;
/*!40000 ALTER TABLE `Genres` DISABLE KEYS */;
/*!40000 ALTER TABLE `Genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Images`
--

LOCK TABLES `Images` WRITE;
/*!40000 ALTER TABLE `Images` DISABLE KEYS */;
/*!40000 ALTER TABLE `Images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Issues`
--

LOCK TABLES `Issues` WRITE;
/*!40000 ALTER TABLE `Issues` DISABLE KEYS */;
/*!40000 ALTER TABLE `Issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Languages`
--

LOCK TABLES `Languages` WRITE;
/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
INSERT INTO `Languages` VALUES (1,'English','en_US','','us'),(2,'Russian','ru_RU','','ru'),(3,'Spanish','es_ES','\0','es'),(4,'Italian','it_IT','\0','it'),(5,'French','fr_FR','\0','fr'),(6,'German','de_DE','\0','de'),(7,'Danish','da_DK','\0','dk'),(8,'Portuguese','pt_PT','\0','pt'),(9,'Japanese','ja_JP','\0','jp'),(10,'Chinese','zh_CN','\0','cn');
/*!40000 ALTER TABLE `Languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `MenuItems`
--

LOCK TABLES `MenuItems` WRITE;
/*!40000 ALTER TABLE `MenuItems` DISABLE KEYS */;
INSERT INTO `MenuItems` VALUES (1,1,0,'Books',NULL,NULL,'/books',1,''),(2,1,0,'Authors',NULL,NULL,'/authors',2,''),(3,1,0,'Contact Us',1,NULL,NULL,3,''),(4,1,0,'Blog',NULL,NULL,'/blog',4,'');
/*!40000 ALTER TABLE `MenuItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Menus`
--

LOCK TABLES `Menus` WRITE;
/*!40000 ALTER TABLE `Menus` DISABLE KEYS */;
INSERT INTO `Menus` VALUES (1,'Header Menu');
/*!40000 ALTER TABLE `Menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Pages`
--

LOCK TABLES `Pages` WRITE;
/*!40000 ALTER TABLE `Pages` DISABLE KEYS */;
INSERT INTO `Pages` VALUES (0,0,1,'/','/','Main Page',NULL,'Main Page',NULL,'',NULL,'Publish',NULL,NULL,'2017-09-01 00:00:00','2017-11-24 20:52:11',NULL),(1,0,1,'/contact-us','/contact-us','Contact Us','<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>','Contact Us','','',NULL,'Publish','2017-11-09 11:33:00',NULL,'2017-10-31 18:32:04','2017-11-10 11:33:17','custom/pages/contacts.tpl');
/*!40000 ALTER TABLE `Pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `PasswordConfirmation`
--

LOCK TABLES `PasswordConfirmation` WRITE;
/*!40000 ALTER TABLE `PasswordConfirmation` DISABLE KEYS */;
/*!40000 ALTER TABLE `PasswordConfirmation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Permissions`
--

LOCK TABLES `Permissions` WRITE;
/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
INSERT INTO `Permissions` VALUES (63,'pageIsNotFound','Page Is Not Found',''),(64,'pageIsForbidden','Page Is Forbidden',''),(65,'epicFail','Epic Fail',''),(66,'imageGet','Image Get',''),(71,'adminLogin','Admin Login','\0'),(72,'adminLogout','Admin Logout','\0'),(73,'passwordRecovery','Password Recovery',''),(74,'passwordChange','Password Change',''),(81,'imageResolutionCreate','Image Resolution Create','\0'),(82,'imageResolutionEdit','Image Resolution Edit','\0'),(83,'imageResolutionDelete','Image Resolution Delete','\0'),(84,'imageDelete','Image Delete','\0'),(85,'avatarUpload','Avatar Upload','\0'),(86,'coverUpload','Cover Upload','\0'),(93,'languageChange','Language Change',''),(100,'authorPhotoUpload','Author Photo Upload','\0'),(115,'bookSearchPublic','Search Book',''),(116,'publisherBooksPublic','Publisher Books View Public Page',''),(117,'publisherSearchPublic','Search Publisher',''),(118,'seriesBooksPublic','Series Books View Public Page',''),(119,'seriesSearchPublic','Search Series',''),(121,'authorBooksPublic','Author Books View Public Page',''),(122,'authorSearchPublic','Search Author',''),(123,'genreBooksPublic','Genre Books View Public Page',''),(124,'genreSearchPublic','Search Genre',''),(129,'publicIndex','Public Index',''),(130,'publicLogin','Public Login',''),(131,'publicLogout','Public Logout',''),(132,'adminIndex','Admin Index','\0'),(133,'bookListView','Books List View','\0'),(134,'bookCreate','Book Create','\0'),(135,'bookEdit','Book Edit','\0'),(136,'bookClone','Book Clone','\0'),(137,'bookDelete','Book Delete','\0'),(138,'bookSearch','Book Search','\0'),(139,'userBooksView','User Books View','\0'),(140,'issueListView','Issue List View','\0'),(141,'issueCreate','Issue Create','\0'),(142,'issueEdit','Issue Edit','\0'),(143,'issueDelete','Issue Delete','\0'),(144,'bookSetLost','Issued Book Set Returned','\0'),(145,'bookSetReturned','Issued Book Set Returned','\0'),(146,'requestedBookIssue','Issue Requested Book','\0'),(147,'userIssuedBooksView','User Issued Books View','\0'),(148,'smtpSettings','SMTP Settings','\0'),(149,'emailNotificationListView','Email Notification List View','\0'),(150,'emailNotificationCreate','Email Notification Create','\0'),(151,'emailNotificationEdit','Email Notification Edit','\0'),(152,'emailNotificationTest','Email Notification Test','\0'),(153,'emailNotificationEnable','Email Notification Activate','\0'),(154,'emailSend','Email Send','\0'),(155,'requestListView','Request List View','\0'),(156,'userRequestListView','User\"s Requests View','\0'),(157,'requestCreate','Request Create','\0'),(158,'requestEdit','Request Edit','\0'),(159,'requestDelete','Request Delete','\0'),(160,'requestSetStatus','Request Set Status','\0'),(161,'userRequestedBooksView','User Requested Books View','\0'),(162,'reviewListView','Review List View','\0'),(163,'reviewCreate','Review Create','\0'),(164,'reviewEdit','Review Edit','\0'),(165,'reviewDelete','Review Delete','\0'),(166,'postImageUpload','Post Image Upload','\0'),(167,'pageImageUpload','Page Image Upload','\0'),(168,'imageListView','Image List View','\0'),(169,'imageOptionsView','Image Options View','\0'),(170,'userListView','User List View','\0'),(171,'userCreate','User Create','\0'),(172,'userEdit','User Edit','\0'),(173,'userDelete','User Delete','\0'),(174,'userEmailCheck','User Email Check','\0'),(175,'userPhotoUpload','User Photo Upload','\0'),(176,'userSearch','User Search','\0'),(177,'optionListView','Option List View','\0'),(178,'languageListView','Language List View','\0'),(179,'roleListView','Role List View','\0'),(180,'roleCreate','Role Create','\0'),(181,'roleEdit','Role Edit','\0'),(182,'roleDelete','Role Delete','\0'),(183,'permissionListUpdate','Permission List Update','\0'),(184,'importCSV','Import CSV','\0'),(185,'exportCSV','Export CSV','\0'),(186,'importExport','Import/Export','\0'),(187,'menuListView','Menu List View','\0'),(188,'menuCreate','Menu Create','\0'),(189,'menuEdit','Menu Edit','\0'),(190,'menuDelete','Menu Delete','\0'),(191,'menuItemsEdit','Menu Items Edit','\0'),(192,'staticShortCodeListView','Static Short Code List View','\0'),(193,'dynamicShortCodeListView','Dynamic Short Code List View','\0'),(194,'postListView','Post List View','\0'),(195,'postCreate','Post Create','\0'),(196,'postEdit','Post Edit','\0'),(197,'postDelete','Post Delete','\0'),(198,'pageListView','Page List View','\0'),(199,'pageCreate','Page Create','\0'),(200,'pageEdit','Page Edit','\0'),(201,'pageDelete','Page Delete','\0'),(202,'userMessageListView','User Message List View','\0'),(203,'userMessageDelete','User Message Delete','\0'),(204,'userMessageSetViewed','User Message Set Viewed','\0'),(205,'categoryListView','Category List View','\0'),(206,'categoryCreate','Category Create','\0'),(207,'categoryEdit','Post Edit','\0'),(208,'categoryDelete','Post Delete','\0'),(209,'authorListView','Author List View','\0'),(210,'authorCreate','Author Create','\0'),(211,'authorEdit','Author Edit','\0'),(212,'authorDelete','Author Delete','\0'),(213,'publisherListView','Publisher List View','\0'),(214,'publisherCreate','Publisher Create','\0'),(215,'publisherEdit','Publisher Edit','\0'),(216,'publisherDelete','Publisher Delete','\0'),(217,'seriesListView','Series List View','\0'),(218,'seriesCreate','Series Create','\0'),(219,'seriesEdit','Series Edit','\0'),(220,'seriesDelete','Series Delete','\0'),(221,'bookListViewPublic','Books List View Public',''),(222,'bookViewPublic','Book View Public',''),(223,'bookGoogleSearchPublic','Google Search Book',''),(224,'bookByGoogleDataCreate','Book By Google Data Create',''),(225,'authorListViewPublic','Author List View Public ',''),(226,'genreListView','Genre List View','\0'),(227,'genreCreate','Genre Create','\0'),(228,'genreEdit','Genre Edit','\0'),(229,'genreDelete','Genre Delete','\0'),(230,'postListViewPublic','Post List View Public',''),(231,'postListByCategoryViewPublic','Post List View By Category',''),(232,'postSearchPublic','Post Search Public',''),(233,'postAndPageSearchPublic','Posts And Pages Search Public',''),(234,'pageSearchPublic','Pages Search Public',''),(235,'postViewPublic','Post View Public',''),(236,'pageViewPublic','Page View Public',''),(237,'bookView','Book View','\0'),(238,'userRegistration','Public User Registration',''),(239,'emailConfirmation','Email Confirm',''),(240,'emailSettings','Email Settings','\0'),(241,'userSendEmail','User Email Send','\0'),(242,'googleSettings','Google Settings','\0'),(243,'siteViewOptionFileUpload','Site View Option File Upload','\0'),(244,'siteViewOptionFileDelete','Site View Option File Delete','\0'),(245,'userMessageCreate','User Message Create','\0'),(246,'databaseSettings','Database Settings','\0');
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `PostCategories`
--

LOCK TABLES `PostCategories` WRITE;
/*!40000 ALTER TABLE `PostCategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `PostCategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Posts`
--

LOCK TABLES `Posts` WRITE;
/*!40000 ALTER TABLE `Posts` DISABLE KEYS */;
INSERT INTO `Posts` VALUES (0,1,'','Blog','Library CMS Blog','','','Library CMS Blog','','',0,'Publish','2016-06-06 00:00:00','2016-06-06 00:00:00','2016-06-30 00:04:29');
/*!40000 ALTER TABLE `Posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Publishers`
--

LOCK TABLES `Publishers` WRITE;
/*!40000 ALTER TABLE `Publishers` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publishers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Requests`
--

LOCK TABLES `Requests` WRITE;
/*!40000 ALTER TABLE `Requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `Requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Reviews`
--

LOCK TABLES `Reviews` WRITE;
/*!40000 ALTER TABLE `Reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `RolePermissions`
--

LOCK TABLES `RolePermissions` WRITE;
/*!40000 ALTER TABLE `RolePermissions` DISABLE KEYS */;
INSERT INTO `RolePermissions` VALUES (3,71),(3,72),(3,132),(3,139),(3,147),(3,161),(3,157),(3,156),(2,132),(2,71),(2,72),(2,210),(2,212),(2,211),(2,209),(2,100),(2,85),(2,136),(2,134),(2,137),(2,135),(2,138),(2,133),(2,206),(2,205),(2,86),(2,227),(2,229),(2,228),(2,226),(2,84),(2,168),(2,141),(2,143),(2,142),(2,140),(2,146),(2,144),(2,145),(2,188),(2,190),(2,189),(2,191),(2,187),(2,199),(2,201),(2,200),(2,167),(2,198),(2,195),(2,197),(2,208),(2,196),(2,207),(2,166),(2,194),(2,214),(2,216),(2,215),(2,213),(2,157),(2,159),(2,158),(2,155),(2,160),(2,218),(2,220),(2,219),(2,217),(2,139),(2,171),(2,173),(2,172),(2,174),(2,147),(2,170),(2,203),(2,202),(2,204),(2,175),(2,161),(2,176),(2,156),(2,237),(3,237),(3,172),(3,175),(2,241),(2,245),(3,159);
/*!40000 ALTER TABLE `RolePermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Roles`
--

LOCK TABLES `Roles` WRITE;
/*!40000 ALTER TABLE `Roles` DISABLE KEYS */;
INSERT INTO `Roles` VALUES (1,'Admin',14,5,30.00,255),(2,'Librarian',30,30,35.00,200),(3,'Member',15,15,15.00,100);
/*!40000 ALTER TABLE `Roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Series`
--

LOCK TABLES `Series` WRITE;
/*!40000 ALTER TABLE `Series` DISABLE KEYS */;
/*!40000 ALTER TABLE `Series` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `StaticShortCodes`
--

LOCK TABLES `StaticShortCodes` WRITE;
/*!40000 ALTER TABLE `StaticShortCodes` DISABLE KEYS */;
INSERT INTO `StaticShortCodes` VALUES (1,'LOGO','http://library.cms/resources/images/logo-light.png','\0'),(2,'SITE_LINK','http://library.cms','\0'),(3,'SITE_NAME','Library CMS','\0'),(4,'STYLES_1','<style>\r\n* {\r\n	margin: 0;\r\n	font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif;\r\n	box-sizing: border-box;\r\n	font-size: 14px;\r\n}\r\nimg {\r\n	max-width: 100%;\r\n}\r\nbody {\r\n	-webkit-font-smoothing: antialiased;\r\n	-webkit-text-size-adjust: none;\r\n	width: 100% !important;\r\n	height: 100%;\r\n	line-height: 1.6em;\r\n}\r\ntable td {\r\n	vertical-align: top;\r\n}\r\n.table-bordered {\r\n}\r\n.table-bordered thead th, .table-bordered th {\r\n	font-size: 11px;\r\n	text-transform: uppercase;\r\n	font-weight: 700;\r\n	color: #17202D;\r\n	vertical-align: bottom;\r\n	text-align: left;\r\n}\r\n.table-bordered td, .table-bordered th {\r\n	border: 1px solid #e9ecef;\r\n	padding: 5px;\r\n}\r\nbody {\r\n	background-color: #f6f6f6;\r\n}\r\n.body-wrap {\r\n	background-color: #f6f6f6;\r\n	width: 100%;\r\n}\r\n.container {\r\n	display: block !important;\r\n	max-width: 600px !important;\r\n	margin: 0 auto !important;\r\n	clear: both !important;\r\n}\r\n.content {\r\n	max-width: 600px;\r\n	margin: 0 auto;\r\n	display: block;\r\n	padding: 20px 0;\r\n}\r\n.main {\r\n	background-color: #fff;\r\n	border: 1px solid #e9e9e9;\r\n	border-radius: 3px;\r\n	color: #a7b1b6;\r\n}\r\n.content-wrap {\r\n	padding: 20px;\r\n}\r\n.content-block {\r\n	padding: 0 0 20px;\r\n}\r\n.header {\r\n	width: 100%;\r\n	margin-bottom: 20px;\r\n}\r\n.footer {\r\n	width: 100%;\r\n	clear: both;\r\n	color: #999;\r\n	padding: 20px;\r\n}\r\n.footer p, .footer a, .footer td {\r\n	color: #999;\r\n	font-size: 12px;\r\n}\r\nh1, h2, h3 {\r\n	font-family: \"Helvetica Neue\", Helvetica, Arial, \"Lucida Grande\", sans-serif;\r\n	color: #000;\r\n	line-height: 1.2em;\r\n	font-weight: 400;\r\n}\r\nh1 {\r\n	font-size: 32px;\r\n	font-weight: 500;\r\n}\r\nh2 {\r\n	font-size: 24px;\r\n}\r\nh3 {\r\n	font-size: 18px;\r\n}\r\nh4 {\r\n	font-size: 14px;\r\n	font-weight: 600;\r\n}\r\np, ul, ol {\r\n	margin-bottom: 10px;\r\n	font-weight: normal;\r\n}\r\np li, ul li, ol li {\r\n	margin-left: 5px;\r\n	list-style-position: inside;\r\n}\r\na {\r\n	color: #008EEE;\r\n	text-decoration: underline;\r\n}\r\n.btn-primary {\r\n	text-decoration: none;\r\n	color: #FFF;\r\n	background-color: #008EEE;\r\n	border: solid #008EEE;\r\n	border-width: 10px 20px;\r\n	line-height: 2em;\r\n	font-weight: bold;\r\n	text-align: center;\r\n	cursor: pointer;\r\n	display: inline-block;\r\n	border-radius: 5px;\r\n	text-transform: capitalize;\r\n}\r\n.pb-0 {\r\n	padding-bottom: 0;\r\n}\r\n.last {\r\n	margin-bottom: 0;\r\n}\r\n.first {\r\n	margin-top: 0;\r\n}\r\n.aligncenter {\r\n	text-align: center !important;\r\n}\r\n.alignright {\r\n	text-align: right;\r\n}\r\n.alignleft {\r\n	text-align: left;\r\n}\r\n.clear {\r\n	clear: both;\r\n}\r\n.alert {\r\n	font-size: 16px;\r\n	color: #fff;\r\n	font-weight: 500;\r\n	padding: 20px;\r\n	text-align: center;\r\n	border-radius: 3px 3px 0 0;\r\n}\r\n.alert a {\r\n	color: #fff;\r\n	text-decoration: none;\r\n	font-weight: 500;\r\n	font-size: 16px;\r\n}\r\n.alert.alert-warning {\r\n	background-color: #FF9F00;\r\n}\r\n.alert.alert-bad {\r\n	background-color: #D0021B;\r\n}\r\n.alert.alert-good {\r\n	background-color: #68B90F;\r\n}\r\n.alert.alert-info {\r\n	background-color: #008EEE;\r\n}\r\n.invoice {\r\n	margin: 40px auto;\r\n	text-align: left;\r\n	width: 80%;\r\n}\r\n.invoice td {\r\n	padding: 5px 0;\r\n}\r\n.invoice .invoice-items {\r\n	width: 100%;\r\n}\r\n.invoice .invoice-items td {\r\n	border-top: #eee 1px solid;\r\n}\r\n.invoice .invoice-items .total td {\r\n	border-top: 2px solid #333;\r\n	border-bottom: 2px solid #333;\r\n	font-weight: 700;\r\n}\r\n@media only screen and (max-width: 640px) {\r\n	body {\r\n		padding: 0 !important;\r\n	}\r\n	h1, h2, h3, h4 {\r\n		font-weight: 800 !important;\r\n		margin: 20px 0 5px !important;\r\n	}\r\n	h1 {\r\n		font-size: 22px !important;\r\n	}\r\n	h2 {\r\n		font-size: 18px !important;\r\n	}\r\n	h3 {\r\n		font-size: 16px !important;\r\n	}\r\n	.container {\r\n		padding: 0 !important;\r\n		width: 100% !important;\r\n	}\r\n	.content {\r\n		padding: 0 !important;\r\n	}\r\n	.content-wrap {\r\n		padding: 10px !important;\r\n	}\r\n	.invoice {\r\n		width: 100% !important;\r\n	}\r\n}\r\n</style>','\0');
/*!40000 ALTER TABLE `StaticShortCodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `UserMessages`
--

LOCK TABLES `UserMessages` WRITE;
/*!40000 ALTER TABLE `UserMessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserMessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,1,'test','test',NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,'2017-01-01 00:00:00','2017-01-01 00:00:00');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-06  3:01:56

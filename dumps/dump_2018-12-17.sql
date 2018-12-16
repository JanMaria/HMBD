-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: ytcrud2
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.18.04.1

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
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_23A0E66A76ED395` (`user_id`),
  CONSTRAINT `FK_23A0E66A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` VALUES (1,1,'Nowy artykuł','admin@crud.pl','1989-10-11 00:00:00',0,'[...nie dodano jeszcze treści artykułu...]'),(2,1,'Nowy artykuł','admin@crud.pl','1989-10-11 00:00:00',0,'[...nie dodano jeszcze treści artykułu...]');
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` smallint(6) NOT NULL,
  `level_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `extra` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 17:15:25','a:0:{}'),(2,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 17:17:24','a:0:{}'),(3,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 17:22:33','a:0:{}'),(4,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 21:58:21','a:0:{}'),(5,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:06:15','a:0:{}'),(6,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:06:30','a:0:{}'),(7,'Authentication Failure',300,'WARNING','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:09:36','a:0:{}'),(8,'Authentication Success',200,'INFO','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:29:18','a:0:{}'),(9,'Authentication Success',200,'INFO','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:55:15','a:0:{}'),(10,'Logout success',200,'INFO','a:1:{s:8:\"username\";s:26:\"jan.maria.prokop@gmail.com\";}','User Events','2018-12-16 22:55:19','a:0:{}');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
INSERT INTO `migration_versions` VALUES ('20181030194901'),('20181030212040'),('20181109212207'),('20181111154454'),('20181124133845'),('20181209190659'),('20181210204055'),('20181210204551');
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@crud.pl','[\"ROLE_ADMIN\"]','$argon2i$v=19$m=1024,t=2,p=2$aThJd0lQdmc5M292VmdNMw$6+l/N44KaWCf8i73VkiGiFq70d5ONf13BfldoJnUsxE'),(2,'jan.maria.prokop@gmail.com','[\"ROLE_ADMIN\"]','$argon2i$v=19$m=1024,t=2,p=2$V1lwSncvOVNBOGNHbWVFRA$GQ+CNlCDhX3Bz9jkjd0yZkUDIj7qc8gcGdb6G20Hqyo'),(3,'noob@crud.pl','[]','$argon2i$v=19$m=1024,t=2,p=2$d0ROWW95YjZaLzc3eG1NVA$mzOMSEIbQ2GFGYlB80lUB3A0M3CA4+WYziDS8MhSUhE');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-17  0:31:52

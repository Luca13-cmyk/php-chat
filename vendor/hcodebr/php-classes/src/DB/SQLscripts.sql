
-- USE `db_dsclub`;


-- DELIMITER $$
-- --
-- -- Procedures
-- --
-- $$

-- $$

-- $$

-- $$

-- $$

-- CREATE  PROCEDURE `sp_userspasswordsrecoveries_create`(
-- piduser INT,
-- pdesip VARCHAR(45)
-- )
-- BEGIN
  
--   INSERT INTO tb_userspasswordsrecoveries (iduser, desip)
--     VALUES(piduser, pdesip);
    
--     SELECT * FROM tb_userspasswordsrecoveries
--     WHERE idrecovery = LAST_INSERT_ID();
    
-- END$$

-- CREATE  PROCEDURE `sp_usersupdate_save`(
-- piduser INT,
-- pdesperson VARCHAR(64), 
-- pdeslogin VARCHAR(64), 
-- pdespassword VARCHAR(256), 
-- pdesemail VARCHAR(128), 
-- pnrphone BIGINT, 
-- pinadmin TINYINT
-- )
-- BEGIN
  
--     DECLARE vidperson INT;
    
--   SELECT idperson INTO vidperson
--     FROM tb_users
--     WHERE iduser = piduser;
    
--     UPDATE tb_persons
--     SET 
--     desperson = pdesperson,
--         desemail = pdesemail,
--         nrphone = pnrphone
--   WHERE idperson = vidperson;
    
--     UPDATE tb_users
--     SET
--     deslogin = pdeslogin,
--         despassword = pdespassword,
--         inadmin = pinadmin
--   WHERE iduser = piduser;
    
--     SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = piduser;
    
-- END$$

-- $$

-- CREATE  PROCEDURE `sp_users_save`(
-- pdesperson VARCHAR(64), 
-- pdeslogin VARCHAR(64), 
-- pdespassword VARCHAR(256), 
-- pdesemail VARCHAR(128), 
-- pnrphone BIGINT, 
-- pinadmin TINYINT
-- )
-- BEGIN
  
--     DECLARE vidperson INT;
    
--   INSERT INTO tb_persons (desperson, desemail, nrphone)
--     VALUES(pdesperson, pdesemail, pnrphone);
    
--     SET vidperson = LAST_INSERT_ID();
    
--     INSERT INTO tb_users (idperson, deslogin, despassword, inadmin)
--     VALUES(vidperson, pdeslogin, pdespassword, pinadmin);
    
--     SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = LAST_INSERT_ID();
    
-- END$$



-- CREATE  PROCEDURE `sp_topics_save`(
-- pidtopic INT,
-- pdestopic VARCHAR(64),
-- pdesheader VARCHAR(7000),
-- pdescap VARCHAR(255)
-- )
-- BEGIN
	
-- 	IF pidtopic > 0 THEN
		
-- 		UPDATE tb_topics
--         SET 
--             destopic = pdestopic,
--             desheader = pdesheader,
--             descap = pdescap
--         WHERE idtopic = pidtopic;
        
--     ELSE
		
-- 		INSERT INTO tb_topics (destopic, desheader, descap) VALUES(pdestopic, pdesheader, pdescap);
        
--         SET pidtopic = LAST_INSERT_ID();
        
--     END IF;
    
--     SELECT * FROM tb_topics WHERE idtopic = pidtopic;
    
-- END$$

-- DROP TABLE IF EXISTS `db_dsclub`.`tb_persons`;

-- CREATE TABLE `db_dsclub`.`tb_persons` (
--   `idperson` int(11) NOT NULL AUTO_INCREMENT,
--   `desperson` varchar(64) NOT NULL,
--   `desemail` varchar(128) DEFAULT NULL,
--   `nrphone` bigint(20) DEFAULT NULL,
--   `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`idperson`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- INSERT INTO `db_dsclub`.`tb_persons` VALUES (1,'Luca Negrette','lucanegrette@hotmail.com',739880000,'2019-11-24 03:00:00');

-- DROP TABLE IF EXISTS `db_dsclub`.`tb_users`;

-- CREATE TABLE `db_dsclub`.`tb_users` (
--   `iduser` int(11) NOT NULL AUTO_INCREMENT,
--   `idperson` int(11) NOT NULL,
--   `deslogin` varchar(64) NOT NULL,
--   `despassword` varchar(256) NOT NULL,
--   `inadmin` tinyint(4) NOT NULL DEFAULT '0',
--   `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`iduser`),
--   KEY `FK_users_persons_idx` (`idperson`),
--   CONSTRAINT `fk_users_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
-- ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- INSERT INTO `db_dsclub`.`tb_users` VALUES (1,1,'luca13-cmyk','$2y$12$YlooCyNvyTji8bPRcrfNfOKnVMmZA9ViM2A3IpFjmrpIbp5ovNmga',1,'2019-11-24 03:00:00');

-- DROP TABLE IF EXISTS `db_dsclub`.`tb_userspasswordsrecoveries`;

-- CREATE TABLE `db_dsclub`.`tb_userspasswordsrecoveries` (
--   `idrecovery` int(11) NOT NULL AUTO_INCREMENT,
--   `iduser` int(11) NOT NULL,
--   `desip` varchar(45) NOT NULL,
--   `dtrecovery` datetime DEFAULT NULL,
--   `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`idrecovery`),
--   KEY `fk_userspasswordsrecoveries_users_idx` (`iduser`),
--   CONSTRAINT `fk_userspasswordsrecoveries_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
-- ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS `db_dsclub`.`tb_topics`;

-- CREATE TABLE `db_dsclub`.`tb_topics` (
--   `idtopic` int(11) NOT NULL AUTO_INCREMENT,
--   `destopic` varchar(32) NOT NULL,
--   `dtregister` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`idtopic`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
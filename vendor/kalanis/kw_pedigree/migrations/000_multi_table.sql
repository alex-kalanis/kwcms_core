-- variant of sql for separated pedigree data


SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `kal_pedigree_relate`;
CREATE TABLE `kal_pedigree_relate` (
       `kpr_id` int(64) NOT NULL AUTO_INCREMENT,
       `kp_id_child` int(64) DEFAULT NULL,
       `kp_id_parent` int(64) DEFAULT NULL,
       PRIMARY KEY (`kpr_id`),
       KEY `kp_id_child` (`kp_id_child`),
       KEY `kp_id_parent` (`kp_id_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Kennel relations';


DROP TABLE IF EXISTS `kal_pedigree_upd`;
CREATE TABLE `kal_pedigree_upd` (
    `kp_id` int(32) NOT NULL AUTO_INCREMENT,
    `kp_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_kennel` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_birth` date NOT NULL,
    `kp_address` varchar(1023) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_trials` varchar(1023) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_photo` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kp_photo_x` smallint(5) NOT NULL DEFAULT '0',
    `kp_photo_y` smallint(5) NOT NULL DEFAULT '0',
    `kp_breed` set('no','yes','died') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
    `kp_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `kp_blood` set('our','other') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'other',
    `kp_text` longtext COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`kp_id`),
    UNIQUE KEY `identifier` (`kp_key`),
    KEY `kp_name` (`kp_name`),
    KEY `kp_kennel` (`kp_kennel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Kennel table';

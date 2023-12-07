-- variant of sql for separated pedigree tables

SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `kw_pedigree_relate`;
DROP TABLE IF EXISTS `kw_pedigree_upd`;

CREATE TABLE `kw_pedigree_upd` (
    `kwp_id` INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `kwp_short` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_family` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_birth` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_death` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_successes` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `kwp_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `kwp_text` longtext COLLATE utf8_unicode_ci NOT NULL,
    UNIQUE KEY `identifier` (`kwp_short`),
    INDEX `birth` (`kwp_birth`),
    INDEX `death` (`kwp_death`),
    INDEX `sex` (`kwp_sex`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree table';

CREATE TABLE `kw_pedigree_relate` (
    `kwpr_id` INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `kwp_id_child` INTEGER NOT NULL,
    `kwp_id_parent` INTEGER NOT NULL,
    CONSTRAINT kwp_child FOREIGN KEY (`kwp_id_child`) REFERENCES `kw_pedigree_upd`(`kwp_id`),
    CONSTRAINT kwp_parent FOREIGN KEY (`kwp_id_parent`) REFERENCES `kw_pedigree_upd`(`kwp_id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree relations';

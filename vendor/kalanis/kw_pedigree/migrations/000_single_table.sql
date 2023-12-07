-- simple SQL table for kw_pedigree

SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `kw_pedigree`;

CREATE TABLE `kw_pedigree` (
    `pedigree_id` INTEGER AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `pedigree_short` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `pedigree_family` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_birth` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_death` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_father_id` INTEGER NULL,
    `pedigree_mother_id` INTEGER NULL,
    `pedigree_successes` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `pedigree_sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
    `pedigree_text` longtext COLLATE utf8_unicode_ci NOT NULL,
    CONSTRAINT fk_father FOREIGN KEY (`pedigree_father_id`) REFERENCES `kw_pedigree`(`pedigree_id`),
    CONSTRAINT fk_mother FOREIGN KEY (`pedigree_mother_id`) REFERENCES `kw_pedigree`(`pedigree_id`),
    UNIQUE KEY `identifier` (`pedigree_short`),
    INDEX `name` (`pedigree_name`),
    INDEX `family` (`pedigree_family`),
    INDEX `birth` (`pedigree_birth`),
    INDEX `death` (`pedigree_death`),
    INDEX `sex` (`pedigree_sex`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Pedigree table';

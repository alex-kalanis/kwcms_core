-- simple SQL table for kw_pedigree

SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `kal_pedigree`;
CREATE TABLE `kal_pedigree` (
    `id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
    `name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `kennel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `birth` date NOT NULL,
    `father` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `mother` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `father_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
    `mother_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
    `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `trials` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `photo` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
    `photo_x` smallint(5) NOT NULL,
    `photo_y` smallint(5) NOT NULL,
    `breed` set('no','yes','died') COLLATE utf8_unicode_ci NOT NULL,
    `sex` set('female','male') COLLATE utf8_unicode_ci NOT NULL,
    `blood` set('our','other') COLLATE utf8_unicode_ci NOT NULL,
    `text` longtext COLLATE utf8_unicode_ci NOT NULL,
    UNIQUE KEY `identifier` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Kennel table';

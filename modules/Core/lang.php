<?php

#####################################################
#       CORE/LANG.PHP                               #
#       translates for many situations on pages     #
#       from Kalanys@2010                           #
#       if you translate to new language,           #
#       please use UTF-8 charset entities           #
#       (for compatibility)                         #
#####################################################

$lang = [
    'eng' => [
        'noscript' => 'Disabling scripts make page somewhere unusable. Your problem.',
        'nostyle' => 'Maybe styles at your browser crashed. If is it true, click on this text.',
        'to_menu' => 'To menu',
        'style' => 'Style',
        'no_module' => 'Module doesn\'t exists',
        'dis_module' => 'Module disabled',
        'err_module' => 'Module error',
        'no_fullwin' => 'Need only module content',

        'core.images.no_gd_library' => 'GD2 library is not present!',
        'core.images.no_rotate_library' => 'Rotations library is not present!',
        'core.images.no_imagemagick' => 'ImageMagic not installed or too old!',
        'core.images.cannot_create_from_resource' => 'Cannot create image from resource!',
        'core.images.cannot_save_resource' => 'Cannot save image resource!',
        'core.images.unknown_mime_class' => 'Unknown mime class!',
        'core.images.unknown_type' => 'Unknown format *%s*',
        'core.images.bad_instance' => 'Wrong instance of *%s*, must be instance of \kalanis\kw_images\Graphics\Format\AFormat',
        'core.images.wrong_file_mime' => 'Wrong file mime type - got *%s*',
        'core.images.compare_size_not_set' => 'Sizes to compare are not set.',
        'core.images.image_cannot_be_resized' => 'Image cannot be resized!',
        'core.images.image_cannot_be_resampled' => 'Image cannot be resampled!',
        'core.images.image_cannot_orientate' => 'Image cannot be orientated by its metadata!',
        'core.images.image_cannot_create_empty' => 'Cannot create empty image!',
        'core.images.image_cannot_get_size' => 'Cannot get image size!',
        'core.images.image_load_first' => 'You must load image first.',
        'core.images.description_cannot_remove' => 'Cannot remove description!',
        'core.images.description_cannot_find' => 'Cannot find that description.',
        'core.images.description_already_exists' => 'Description with the same name already exists here.',
        'core.images.description_cannot_remove_old' => 'Cannot remove old description.',
        'core.images.description_cannot_copy' => 'Cannot copy base description.',
        'core.images.description_cannot_move' => 'Cannot move base description.',
        'core.images.description_cannot_rename' => 'Cannot rename base description.',
        'core.images.thumb_dir_cannot_remove' => 'Cannot remove dir thumb!',
        'core.images.thumb_cannot_remove_current' => 'Cannot remove current thumb!',
        'core.images.image_cannot_read_size' => 'Cannot read file size. It exists?',
        'core.images.image_too_large' => 'This image is too large to use.',
        'core.images.image_cannot_find' => 'Cannot find that image.',
        'core.images.image_cannot_remove' => 'Cannot remove image.',
        'core.images.image_already_exists' => 'Image with the same name already exists here.',
        'core.images.image_cannot_remove_old' => 'Cannot remove old image.',
        'core.images.image_cannot_copy' => 'Cannot copy base image.',
        'core.images.image_cannot_move' => 'Cannot move base image.',
        'core.images.image_cannot_rename' => 'Cannot rename base image.',
        'core.images.thumb_cannot_find' => 'Cannot find that thumb.',
        'core.images.thumb_cannot_remove' => 'Cannot remove thumb!',
        'core.images.thumb_already_exists' => 'Thumb with the same name already exists here.',
        'core.images.thumb_cannot_remove_old' => 'Cannot remove old thumb.',
        'core.images.image_cannot_get_base' => 'Cannot get base image.',
        'core.images.image_cannot_store_temp' => 'Cannot store temporary image.',
        'core.images.image_cannot_load_temp' => 'Cannot load temporary image.',
        'core.images.thumb_cannot_copy' => 'Cannot copy base thumb.',
        'core.images.thumb_cannot_move' => 'Cannot move base thumb.',
        'core.images.thumb_cannot_rename' => 'Cannot rename base thumb.',

        'core.files.cannot_process_path' => 'Cannot process wanted path. *%s*',
        'core.files.bad_mode' => 'Bad file access mode. *%s*',
        'core.files.cannot_load_file' => 'Cannot load wanted file. *%s*',
        'core.files.cannot_save_file' => 'Cannot save wanted file. *%s*',
        'core.files.cannot_open_file' => 'Cannot open wanted file. *%s*',
        'core.files.cannot_seek_file' => 'Cannot seek wanted file. *%s*',
        'core.files.cannot_write_file' => 'Cannot write wanted file. *%s*',
        'core.files.cannot_extract_content' => 'Cannot extract part of content. *%s*',
        'core.files.cannot_get_file_size' => 'Cannot get file size. *%s*',
        'core.files.cannot_copy_file' => 'Cannot copy file *%s* to destination *%s*.',
        'core.files.cannot_move_file' => 'Cannot move file *%s* to destination *%s*.',
        'core.files.cannot_remove_file' => 'Cannot remove file *%s*.',
        'core.files.cannot_create_dir' => 'Cannot create directory *%s*.',
        'core.files.cannot_read_dir' => 'Cannot read directory *%s*.',
        'core.files.cannot_copy_dir' => 'Cannot copy directory *%s* to destination *%s*.',
        'core.files.cannot_move_dir' => 'Cannot move directory *%s* to destination *%s*.',
        'core.files.cannot_remove_dir' => 'Cannot remove directory *%s*.',
        'core.files.empty_delimiter' => 'You set the empty directory delimiter!',
        'core.files.no_process_nodes' => 'No processing nodes library set!',
        'core.files.no_process_files' => 'No processing files library set!',
        'core.files.no_process_streams' => 'No processing streams library set!',
        'core.files.no_process_dirs' => 'No processing directories library set!',
        'core.files.no_available_classes' => 'No available classes for that settings!',
    ],
    'fra' => [
        'noscript' => 'D&#x00E9;sactiver les scripts faire la page quelque part inutilisable. Votre probl&#x00E8;me.',
        'nostyle' => 'Peut-&#x00EA;tre styles &#x00E0; votre navigateur plante. Si c\'est vrai, cliquez sur ce texte.',
        'to_menu' => 'Aller au menu',
        'style' => 'Style',
        'no_module' => 'Module n\'est pas exist&#x00E9;e.',
        'dis_module' => 'Module interdit',
        'err_module' => 'L\'erreur en module',
        'no_fullwin' => 'Il suffit de contenu du module.',

        'core.images.no_gd_library' => 'GD2 library is not present!',
        'core.images.no_rotate_library' => 'Rotations library is not present!',
        'core.images.no_imagemagick' => 'ImageMagic not installed or too old!',
        'core.images.cannot_create_from_resource' => 'Cannot create image from resource!',
        'core.images.cannot_save_resource' => 'Cannot save image resource!',
        'core.images.unknown_mime_class' => 'Unknown mime class!',
        'core.images.unknown_type' => 'Unknown format *%s*',
        'core.images.bad_instance' => 'Wrong instance of *%s*, must be instance of \kalanis\kw_images\Graphics\Format\AFormat',
        'core.images.wrong_file_mime' => 'Wrong file mime type - got *%s*',
        'core.images.compare_size_not_set' => 'Sizes to compare are not set.',
        'core.images.image_cannot_be_resized' => 'Image cannot be resized!',
        'core.images.image_cannot_be_resampled' => 'Image cannot be resampled!',
        'core.images.image_cannot_orientate' => 'Image cannot be orientated by its metadata!',
        'core.images.image_cannot_create_empty' => 'Cannot create empty image!',
        'core.images.image_cannot_get_size' => 'Cannot get image size!',
        'core.images.image_load_first' => 'You must load image first.',
        'core.images.description_cannot_remove' => 'Cannot remove description!',
        'core.images.description_cannot_find' => 'Cannot find that description.',
        'core.images.description_already_exists' => 'Description with the same name already exists here.',
        'core.images.description_cannot_remove_old' => 'Cannot remove old description.',
        'core.images.description_cannot_copy' => 'Cannot copy base description.',
        'core.images.description_cannot_move' => 'Cannot move base description.',
        'core.images.description_cannot_rename' => 'Cannot rename base description.',
        'core.images.thumb_dir_cannot_remove' => 'Cannot remove dir thumb!',
        'core.images.thumb_cannot_remove_current' => 'Cannot remove current thumb!',
        'core.images.image_cannot_read_size' => 'Cannot read file size. It exists?',
        'core.images.image_too_large' => 'This image is too large to use.',
        'core.images.image_cannot_find' => 'Cannot find that image.',
        'core.images.image_cannot_remove' => 'Cannot remove image.',
        'core.images.image_already_exists' => 'Image with the same name already exists here.',
        'core.images.image_cannot_remove_old' => 'Cannot remove old image.',
        'core.images.image_cannot_copy' => 'Cannot copy base image.',
        'core.images.image_cannot_move' => 'Cannot move base image.',
        'core.images.image_cannot_rename' => 'Cannot rename base image.',
        'core.images.thumb_cannot_find' => 'Cannot find that thumb.',
        'core.images.thumb_cannot_remove' => 'Cannot remove thumb!',
        'core.images.thumb_already_exists' => 'Thumb with the same name already exists here.',
        'core.images.thumb_cannot_remove_old' => 'Cannot remove old thumb.',
        'core.images.image_cannot_get_base' => 'Cannot get base image.',
        'core.images.image_cannot_store_temp' => 'Cannot store temporary image.',
        'core.images.image_cannot_load_temp' => 'Cannot load temporary image.',
        'core.images.thumb_cannot_copy' => 'Cannot copy base thumb.',
        'core.images.thumb_cannot_move' => 'Cannot move base thumb.',
        'core.images.thumb_cannot_rename' => 'Cannot rename base thumb.',

        'core.files.cannot_process_path' => 'Cannot process wanted path. *%s*',
        'core.files.bad_mode' => 'Bad file access mode. *%s*',
        'core.files.cannot_load_file' => 'Cannot load wanted file. *%s*',
        'core.files.cannot_save_file' => 'Cannot save wanted file. *%s*',
        'core.files.cannot_open_file' => 'Cannot open wanted file. *%s*',
        'core.files.cannot_seek_file' => 'Cannot seek wanted file. *%s*',
        'core.files.cannot_write_file' => 'Cannot write wanted file. *%s*',
        'core.files.cannot_extract_content' => 'Cannot extract part of content. *%s*',
        'core.files.cannot_get_file_size' => 'Cannot get file size. *%s*',
        'core.files.cannot_copy_file' => 'Cannot copy file *%s* to destination *%s*.',
        'core.files.cannot_move_file' => 'Cannot move file *%s* to destination *%s*.',
        'core.files.cannot_remove_file' => 'Cannot remove file *%s*.',
        'core.files.cannot_create_dir' => 'Cannot create directory *%s*.',
        'core.files.cannot_read_dir' => 'Cannot read directory *%s*.',
        'core.files.cannot_copy_dir' => 'Cannot copy directory *%s* to destination *%s*.',
        'core.files.cannot_move_dir' => 'Cannot move directory *%s* to destination *%s*.',
        'core.files.cannot_remove_dir' => 'Cannot remove directory *%s*.',
        'core.files.empty_delimiter' => 'You set the empty directory delimiter!',
        'core.files.no_process_nodes' => 'No processing nodes library set!',
        'core.files.no_process_files' => 'No processing files library set!',
        'core.files.no_process_streams' => 'No processing streams library set!',
        'core.files.no_process_dirs' => 'No processing directories library set!',
        'core.files.no_available_classes' => 'No available classes for that settings!',
    ],
    'cze' => [
        'noscript' => 'Z&#x00E1;kaz skript&#x016F; neumo&#x017E;n&#x00ED; u&#x017E;&#x00ED;t str&#x00E1;nku spr&#x00E1;vn&#x011B;. V&#x00E1;&#x0161; probl&#x00E9;m.',
        'nostyle' => 'Pravd&#x011B;podobn&#x011B; v&#x00E1;m krachlo ostylov&#x00E1;n&#x00ED; str&#x00E1;nky. Jestli tomu tak opravdu je, klikn&#x011B;te na tento text.',
        'to_menu' => 'Na nab&#x00ED;dku',
        'style' => 'Styl',
        'no_module' => 'Modul neexistuje',
        'dis_module' => 'Modul zak&#x00E1;z&#x00E1;n',
        'err_module' => 'Chyba v modulu',
        'no_fullwin' => 'Je t&#x0159;eba nechat jen obsah str&#x00E1;nky!',

        'core.images.no_gd_library' => 'Knihovna GD2 není k dispozici!',
        'core.images.no_rotate_library' => 'Knihovna k rotovani obrázků není k dispozici!',
        'core.images.no_imagemagick' => 'ImageMagic není k dispozici nebo je zastaralý!',
        'core.images.cannot_create_from_resource' => 'Z těchto zdrojových dat nejde načíst obrázek!',
        'core.images.cannot_save_resource' => 'Připravený obrázek nejde uložit do zdrojových dat!',
        'core.images.unknown_mime_class' => 'Neznámé nastavení třídy rozpoznávající mime typ!',
        'core.images.unknown_type' => 'Neznámý formát *%s*',
        'core.images.bad_instance' => 'Špatný objekt *%s*, musí to být instance třídy \kalanis\kw_images\Graphics\Format\AFormat',
        'core.images.wrong_file_mime' => 'Špatný typ souboru - přišel *%s*',
        'core.images.compare_size_not_set' => 'Nejsou nastavené velikosti k porovnání.',
        'core.images.image_cannot_be_resized' => 'Obrázek nejde oříznout!',
        'core.images.image_cannot_be_resampled' => 'Nejde změnit velikost obrázku!',
        'core.images.image_cannot_orientate' => 'Nelze orientovat obrázek na základně jeho metadat!',
        'core.images.image_cannot_create_empty' => 'Nemohu vytvořit prázdný obrázek!',
        'core.images.image_cannot_get_size' => 'Nelze načíst velikost obrázku!',
        'core.images.image_load_first' => 'Nejdříve je třeba načíst obrázek.',
        'core.images.description_cannot_remove' => 'Nelze odstranit popisek!',
        'core.images.description_cannot_find' => ' Popisek nelze načíst.',
        'core.images.description_already_exists' => 'Popisek s tímto jménem už tu je.',
        'core.images.description_cannot_remove_old' => 'Nemohu odstranit starý popisek.',
        'core.images.description_cannot_copy' => 'Nemohu zkopírovat popisek.',
        'core.images.description_cannot_move' => 'Nemohu přesunout popisek.',
        'core.images.description_cannot_rename' => 'Nehohu přejmenovat popisek.',
        'core.images.thumb_dir_cannot_remove' => 'Nemohu odstranit náhled složky!',
        'core.images.thumb_cannot_remove_current' => 'Nemohu odstranit aktuální náhled!',
        'core.images.image_cannot_read_size' => 'Nemohu načíst velikost obrázku. Existsuje vůbec?',
        'core.images.image_too_large' => 'Tento obrázek je pro místní užití až moc velký.',
        'core.images.image_cannot_find' => 'Nemohu najít obrázek.',
        'core.images.image_cannot_remove' => 'Nemohu odstranit obrázek.',
        'core.images.image_already_exists' => 'Obrázek s tímto jménem už tu je.',
        'core.images.image_cannot_remove_old' => 'Nemohu odstranit starý obrázek.',
        'core.images.image_cannot_copy' => 'Nemohu zkopírovat obrázek.',
        'core.images.image_cannot_move' => 'Nemohu přesunout obrázek.',
        'core.images.image_cannot_rename' => 'Nemohu přejmenovat obrázek.',
        'core.images.thumb_cannot_find' => 'Nemohu najít náhled.',
        'core.images.thumb_cannot_remove' => 'Nemohu odstranit náhled.',
        'core.images.thumb_already_exists' => 'Náhled s tímto jménem už tu je.',
        'core.images.thumb_cannot_remove_old' => 'Nemohu odstranit starý náhled.',
        'core.images.image_cannot_get_base' => 'Nemohu načíst obrázek z úložiště.',
        'core.images.image_cannot_store_temp' => 'Nemohu uložit dočasný obrázek.',
        'core.images.image_cannot_load_temp' => 'Nemohu načíst dočasný obrázek.',
        'core.images.thumb_cannot_copy' => 'Nemohu zkopírovat náhled.',
        'core.images.thumb_cannot_move' => 'Nemohu přesunout náhled.',
        'core.images.thumb_cannot_rename' => 'Nemohu přejmenovat náhled.',

        'core.files.cannot_process_path' => 'Nelze použít zvolenou cestu. *%s*',
        'core.files.bad_mode' => 'Špatný způsob přístupu k souboru. *%s*',
        'core.files.cannot_load_file' => 'Nelze načíst zvolený soubor. *%s*',
        'core.files.cannot_save_file' => 'Nelze uložit zvolený soubor. *%s*',
        'core.files.cannot_open_file' => 'Nelze otevřít zvolený soubor. *%s*',
        'core.files.cannot_seek_file' => 'Nelze skočit ve zvoleném souboru. *%s*',
        'core.files.cannot_write_file' => 'Nelze zapsat zvolený soubor. *%s*',
        'core.files.cannot_extract_content' => 'Nelze získat část obsahu souboru. *%s*',
        'core.files.cannot_get_file_size' => 'Nelze zjistit velikost souboru. *%s*',
        'core.files.cannot_copy_file' => 'Nelze zkopírovat soubor *%s* do složky *%s*.',
        'core.files.cannot_move_file' => 'Nelze zkopírovat soubor *%s* do složky *%s*.',
        'core.files.cannot_remove_file' => 'Nelze odstranit soubor *%s*.',
        'core.files.cannot_create_dir' => 'Nelze vytvořit složku *%s*.',
        'core.files.cannot_read_dir' => 'Nelze přečíst složku *%s*.',
        'core.files.cannot_copy_dir' => 'Nelze zkopírovat složku *%s* do složky *%s*.',
        'core.files.cannot_move_dir' => 'Nelze přesunout složku *%s* do složky *%s*.',
        'core.files.cannot_remove_dir' => 'Nelze odstranit složku *%s*.',
        'core.files.empty_delimiter' => 'Není nastaven oddělovač složek!',
        'core.files.no_process_nodes' => 'Není nastavena knihovna na zpracování nodů!',
        'core.files.no_process_files' => 'Není nastavena knihovna na zpracování souborů!',
        'core.files.no_process_streams' => 'Není nastavena knihovna na zpracování proudů!',
        'core.files.no_process_dirs' => 'Není nastavena knihovna na zpracování složek!',
        'core.files.no_available_classes' => 'Žádná kombinace knihoven není dostupná!',
    ],
];

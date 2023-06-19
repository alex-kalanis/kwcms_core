<?php

############################################################
#       IMAGES/LANGS.PHP                                   #
#       names and contents of languages                    #
#       first shown on pages with selection                #
############################################################
#       if you translate to new language,                  #
#       please use UTF-8 charset entities                  #
#       (for compatibility)                                #
############################################################

$lang = [
    'eng' => [
        'images.list_dir' => 'Directory list',
        'images.dir_props' => 'Directory properties',
        'images.create_dir' => 'Create directory',
        'images.upload_image' => 'Upload image',

        'images.dir.props' => 'Directory properties',
        'images.dir_props.short' => 'Dir props',
        'images.dir.allow_extra_data' => 'Creation of necessary subdirs',
        'images.current_desc' => 'Description',
        'images.props_updated' => 'The description has been updated',
        'images.dirs_created' => 'The necessary subdirs has been created',

        'images.upload.short' => 'Upload',
        'images.upload.file' => 'Upload image',
        'images.file.select' => 'Select image',
        'images.description' => 'Image description',
        'images.upload.note' => 'Details - max 1024 x 1024 px, 10MB',
        'images.error.must_contain_file' => 'The entry must contains a file.',
        'images.must_be_sent' => 'The entry must be sent.',
        'images.upload.cannot_move' => 'The image cannot be moved from temporary storage.',
        'images.uploaded' => 'The image has been uploaded.',

        'images.thumb' => 'Thumbnail',
        'images.name' => 'Name',
        'images.size' => 'Size',
        'images.desc' => 'Description',
        'images.actions' => 'Operations',

        'images.filter.from' => 'From',
        'images.filter.to' => 'To',
        'images.update_item' => 'Edit',

        'images.dir_create.short' => 'Create dir',
        'images.dir.new' => 'Create directory',
        'images.dir.name' => 'Create new directory',
        'images.dir.select' => 'In which existing dir',
        'images.dir.move_into' => 'And then move there',
        'images.dir_created' => 'The directory has been created.',

        'images.files_props.short' => 'Properties',
        'images.file.current_name' => 'Name',
        'images.file.target' => 'Where',
        'images.file.desc' => 'Description',

        'images.single.thumb' => 'Thumbnail',
        'images.single.desc' => 'Description',
        'images.single.rename' => 'Rename',
        'images.single.copy' => 'Copy',
        'images.single.move' => 'Move',
        'images.single.primary_thumb' => 'Thumbnail for the whole directory',
        'images.single.delete' => 'Remove',

        'images.copied' => 'The image has been copied.',
        'images.moved' => 'The image has been moved.',
        'images.renamed' => 'The image has been renamed.',
        'images.removed' => 'The image has been removed.',
        'images.desc_updated' => 'The description has been updated.',
        'images.thumb_recreated' => 'The thumbnail has been re-generated.',
        'images.set_as_primary' => 'The thumbnail has been set for the whole directory.',

        'images.file_name.invalid' => 'The name of file *%s* is invalid!',
        'images.file_name.not_found' => 'The file with name *%s* does not exists or it is not an image!',

        'images.images.no_gd_library' => 'GD2 library is not present!',
        'images.images.no_imagemagick' => 'ImageMagic not installed or too old!',
        'images.images.cannot_create_from_resource' => 'Cannot create image from resource!',
        'images.images.cannot_save_resource' => 'Cannot save image resource!',
        'images.images.unknown_type' => 'Unknown format *%s*',
        'images.images.bad_instance' => 'Wrong instance of *%s*, must be instance of \kalanis\kw_images\Graphics\Format\AFormat',
        'images.images.wrong_file_mime' => 'Wrong file mime type - got *%s*',
        'images.images.compare_size_not_set' => 'Sizes to compare are not set.',
        'images.images.image_cannot_be_resized' => 'Image cannot be resized!',
        'images.images.image_cannot_be_resampled' => 'Image cannot be resampled!',
        'images.images.image_cannot_create_empty' => 'Cannot create empty image!',
        'images.images.image_cannot_get_size' => 'Cannot get image size!',
        'images.images.image_load_first' => 'You must load image first.',
        'images.images.description_cannot_remove' => 'Cannot remove description!',
        'images.images.description_cannot_find' => 'Cannot find that description.',
        'images.images.description_already_exists' => 'Description with the same name already exists here.',
        'images.images.description_cannot_remove_old' => 'Cannot remove old description.',
        'images.images.description_cannot_copy' => 'Cannot copy base description.',
        'images.images.description_cannot_move' => 'Cannot move base description.',
        'images.images.description_cannot_rename' => 'Cannot rename base description.',
        'images.images.thumb_dir_cannot_remove' => 'Cannot remove dir thumb!',
        'images.images.thumb_cannot_remove_current' => 'Cannot remove current thumb!',
        'images.images.image_cannot_read_size' => 'Cannot read file size. It exists?',
        'images.images.image_too_large' => 'This image is too large to use.',
        'images.images.image_cannot_find' => 'Cannot find that image.',
        'images.images.image_cannot_remove' => 'Cannot remove image.',
        'images.images.image_already_exists' => 'Image with the same name already exists here.',
        'images.images.image_cannot_remove_old' => 'Cannot remove old image.',
        'images.images.image_cannot_copy' => 'Cannot copy base image.',
        'images.images.image_cannot_move' => 'Cannot move base image.',
        'images.images.image_cannot_rename' => 'Cannot rename base image.',
        'images.images.thumb_cannot_find' => 'Cannot find that thumb.',
        'images.images.thumb_cannot_remove' => 'Cannot remove thumb!',
        'images.images.thumb_already_exists' => 'Thumb with the same name already exists here.',
        'images.images.thumb_cannot_remove_old' => 'Cannot remove old thumb.',
        'images.images.image_cannot_get_base' => 'Cannot get base image.',
        'images.images.image_cannot_store_temp' => 'Cannot store temporary image.',
        'images.images.image_cannot_load_temp' => 'Cannot load temporary image.',
        'images.images.thumb_cannot_copy' => 'Cannot copy base thumb.',
        'images.images.thumb_cannot_move' => 'Cannot move base thumb.',
        'images.images.thumb_cannot_rename' => 'Cannot rename base thumb.',

        'images.page' => 'Images',
    ],
    'fra' => [

        'images.list_dir' => 'Directory list',
        'images.dir_props' => 'Directory properties',
        'images.create_dir' => 'Create directory',
        'images.upload_image' => 'Upload image',

        'images.dir.props' => 'Directory properties',
        'images.dir_props.short' => 'Dir props',
        'images.dir.allow_extra_data' => 'Creation of necessary subdirs',
        'images.current_desc' => 'Titre',
        'images.props_updated' => 'The description has been updated',
        'images.dirs_created' => 'The necessary subdirs has been created',

        'images.upload.short' => 'T&#x00E9;l&#x00E9;charger',
        'images.upload.file' => 'T&#x00E9;l&#x00E9;charger la photo',
        'images.file.select' => 'Selectez un photo',
        'images.description' => 'Le titre de photo',
        'images.upload.note' => 'D&#x00E9;tails - max 1024 x 1024 px, 10MB',
        'images.error.must_contain_file' => 'The entry must contains a file.',
        'images.must_be_sent' => 'The entry must be sent.',
        'images.upload.cannot_move' => 'The image cannot be moved from temporary storage.',
        'images.uploaded' => 'The image has been uploaded.',

        'images.thumb' => 'Thumbnail',
        'images.name' => 'Nom',
        'images.size' => 'Size',
        'images.desc' => 'Titre',
        'images.actions' => 'Operations',

        'images.filter.from' => 'From',
        'images.filter.to' => 'To',
        'images.update_item' => 'Edit',

        'images.dir_create.short' => 'Noveau comp',
        'images.dir.new' => 'Le composant noveau',
        'images.dir.name' => 'Cr&#x00E9;e un composant noveau',
        'images.dir.select' => 'In which existing dir',
        'images.dir.move_into' => 'And then move there',
        'images.dir_created' => 'The directory has been created.',

        'images.files_props.short' => 'Properties',
        'images.file.current_name' => 'Name',
        'images.file.target' => 'Where',
        'images.file.desc' => 'Titre',

        'images.single.thumb' => 'Thumbnail',
        'images.single.desc' => 'Titre',
        'images.single.rename' => 'Rename',
        'images.single.copy' => 'Copie &#x00E0;',
        'images.single.move' => 'D&#x00E9;placer &#x00E0;',
        'images.single.primary_thumb' => 'Thumbnail for the whole directory',
        'images.single.delete' => 'Retrirer',

        'images.copied' => 'The image has been copied.',
        'images.moved' => 'The image has been moved.',
        'images.renamed' => 'The image has been renamed.',
        'images.removed' => 'The image has been removed.',
        'images.desc_updated' => 'Le titre est actualis&#x00E9;.',
        'images.thumb_recreated' => 'The thumbnail has been re-generated.',
        'images.set_as_primary' => 'The thumbnail has been set for the whole directory.',

        'images.file_name.invalid' => 'Le nom de fichier *%s* est invalid!',
        'images.file_name.not_found' => 'Le fichier avec le nom *%s* n\'existe pas o_ ce n\'est pas un photo!',

        'images.images.no_gd_library' => 'GD2 library is not present!',
        'images.images.no_imagemagick' => 'ImageMagic not installed or too old!',
        'images.images.cannot_create_from_resource' => 'Cannot create image from resource!',
        'images.images.cannot_save_resource' => 'Cannot save image resource!',
        'images.images.unknown_type' => 'Unknown format *%s*',
        'images.images.bad_instance' => 'Wrong instance of *%s*, must be instance of \kalanis\kw_images\Graphics\Format\AFormat',
        'images.images.wrong_file_mime' => 'Wrong file mime type - got *%s*',
        'images.images.compare_size_not_set' => 'Sizes to compare are not set.',
        'images.images.image_cannot_be_resized' => 'Image cannot be resized!',
        'images.images.image_cannot_be_resampled' => 'Image cannot be resampled!',
        'images.images.image_cannot_create_empty' => 'Cannot create empty image!',
        'images.images.image_cannot_get_size' => 'Cannot get image size!',
        'images.images.image_load_first' => 'You must load image first.',
        'images.images.description_cannot_remove' => 'Cannot remove description!',
        'images.images.description_cannot_find' => 'Cannot find that description.',
        'images.images.description_already_exists' => 'Description with the same name already exists here.',
        'images.images.description_cannot_remove_old' => 'Cannot remove old description.',
        'images.images.description_cannot_copy' => 'Cannot copy base description.',
        'images.images.description_cannot_move' => 'Cannot move base description.',
        'images.images.description_cannot_rename' => 'Cannot rename base description.',
        'images.images.thumb_dir_cannot_remove' => 'Cannot remove dir thumb!',
        'images.images.thumb_cannot_remove_current' => 'Cannot remove current thumb!',
        'images.images.image_cannot_read_size' => 'Cannot read file size. It exists?',
        'images.images.image_too_large' => 'This image is too large to use.',
        'images.images.image_cannot_find' => 'Cannot find that image.',
        'images.images.image_cannot_remove' => 'Cannot remove image.',
        'images.images.image_already_exists' => 'Image with the same name already exists here.',
        'images.images.image_cannot_remove_old' => 'Cannot remove old image.',
        'images.images.image_cannot_copy' => 'Cannot copy base image.',
        'images.images.image_cannot_move' => 'Cannot move base image.',
        'images.images.image_cannot_rename' => 'Cannot rename base image.',
        'images.images.thumb_cannot_find' => 'Cannot find that thumb.',
        'images.images.thumb_cannot_remove' => 'Cannot remove thumb!',
        'images.images.thumb_already_exists' => 'Thumb with the same name already exists here.',
        'images.images.thumb_cannot_remove_old' => 'Cannot remove old thumb.',
        'images.images.image_cannot_get_base' => 'Cannot get base image.',
        'images.images.image_cannot_store_temp' => 'Cannot store temporary image.',
        'images.images.image_cannot_load_temp' => 'Cannot load temporary image.',
        'images.images.thumb_cannot_copy' => 'Cannot copy base thumb.',
        'images.images.thumb_cannot_move' => 'Cannot move base thumb.',
        'images.images.thumb_cannot_rename' => 'Cannot rename base thumb.',

        'images.page' => 'Photos',
    ],
    'cze' => [
        'images.list_dir' => 'V&#x00FD;pis slo&#x017E;ky',
        'images.dir_props' => 'Vlastnosti slo&#x017E;ky',
        'images.create_dir' => 'Vytvo&#x0159;it slo&#x017E;ku',
        'images.upload_image' => 'Nahr&#x00E1;v&#x00E1;n&#x00ED;',

        'images.dir.props' => 'Vlastnosti slo&#x017E;ky',
        'images.dir_props.short' => 'Vlastnosti slo&#x017E;ky',
        'images.dir.allow_extra_data' => 'V&#x00FD;roba pot&#x0159;ebn&#x00FD;ch slo&#x017E;ek',
        'images.current_desc' => 'Popisek',
        'images.props_updated' => 'Popisek byl upraven.',
        'images.dirs_created' => 'Byly vytvo&#x0159;eny pot&#x0159;ebn&#x00E9; slo&#x017E;ky.',

        'images.upload.short' => 'Nahr&#x00E1;v&#x00E1;n&#x00ED;',
        'images.upload.file' => 'Nahr&#x00E1;t obr&#x00E1;zek',
        'images.file.select' => 'Vybrat obr&#x00E1;zek',
        'images.description' => 'Popisek obr&#x00E1;zku',
        'images.upload.note' => 'Detaily - max 1024 x 1024 px, 10MB',
        'images.error.must_contain_file' => 'Polo&#x017E;ka mus&#x00ED; obsahovat soubor.',
        'images.must_be_sent' => 'Polo&#x017E;ka mus&#x00ED; b&#x00FD;t odesl&#x00E1;na.',
        'images.upload.cannot_move' => 'Obr&#x00E1;zek nelze p&#x0159;esunout z do&#x010D;asn&#x00E9;ho um&#x00ED;st&#x011B;n&#x00ED;.',
        'images.uploaded' => 'Obr&#x00E1;zek %s nahr&#x00E1;n.',

        'images.thumb' => 'N&#x00E1;hled',
        'images.name' => 'N&#x00E1;zev',
        'images.size' => 'Velikost',
        'images.desc' => 'Popisek',
        'images.actions' => 'Operace',

        'images.filter.from' => 'Od',
        'images.filter.to' => 'Do',
        'images.update_item' => 'Upravit',

        'images.dir_create.short' => 'Vytvo&#x0159;it slo&#x017E;ku',
        'images.dir.new' => 'Vytvo&#x0159;it slo&#x017E;ku',
        'images.dir.name' => 'Vytvo&#x0159;it novou slo&#x017E;ku',
        'images.dir.select' => 'Ve kter&#x00E9; slo&#x017E;ce',
        'images.dir.move_into' => 'A pak se tam p&#x0159;esunout',
        'images.dir_created' => 'Slo&#x017E;ka byla vytvo&#x0159;ena',

        'images.files_props.short' => 'Vlastnosti',
        'images.file.current_name' => 'N&#x00E1;zev',
        'images.file.target' => 'Kam',
        'images.file.desc' => 'Popisek',

        'images.single.thumb' => 'N&#x00E1;hled',
        'images.single.desc' => 'Popisek',
        'images.single.rename' => 'P&#x0159;ejmenovat',
        'images.single.copy' => 'Kop&#x00ED;rovat',
        'images.single.move' => 'P&#x0159;esunout',
        'images.single.primary_thumb' => 'N&#x00E1;hled nastavit pro celou slo&#x017E;ku',
        'images.single.delete' => 'Odstranit',

        'images.copied' => 'Obr&#x00E1;zek zkop&#x00ED;rov&#x00E1;n.',
        'images.moved' => 'Obr&#x00E1;zek p&#x0159;esunut.',
        'images.renamed' => 'Obr&#x00E1;zek p&#x0159;jmenov&#x00E1;n.',
        'images.removed' => 'Obr&#x00E1;zek odstran&#x011B;n.',
        'images.desc_updated' => 'Popisek upraven.',
        'images.thumb_recreated' => 'N&#x00E1;hled p&#x0159;egenerov&#x00E1;n.',
        'images.set_as_primary' => 'N&#x00E1;hled nastaven pro celou slo&#x017E;ku.',

        'images.file_name.invalid' => 'Jm&#x00E9;no souboru *%s* je neplatn&#x00E9;!',
        'images.file_name.not_found' => 'Soubor se jm&#x00E9;nem *%s* neexistuje nebo to nen&#x00ED; obr&#x00E1;zek!',

        'images.images.no_gd_library' => 'Knihovna GD2 není k dispozici!',
        'images.images.no_imagemagick' => 'ImageMagic není k dispozici nebo je zastaralý!',
        'images.images.cannot_create_from_resource' => 'Z těchto zdrojových dat nejde načíst obrázek!',
        'images.images.cannot_save_resource' => 'Připravený obrázek nejde uložit do zdrojových dat!',
        'images.images.unknown_type' => 'Neznámý formát *%s*',
        'images.images.bad_instance' => 'Špatný objekt *%s*, musí to být instance třídy \kalanis\kw_images\Graphics\Format\AFormat',
        'images.images.wrong_file_mime' => 'Špatný typ souboru - přišel *%s*',
        'images.images.compare_size_not_set' => 'Nejsou nastavené velikosti k porovnání.',
        'images.images.image_cannot_be_resized' => 'Obrázek nejde oříznout!',
        'images.images.image_cannot_be_resampled' => 'Nejde změnit velikost obrázku!',
        'images.images.image_cannot_create_empty' => 'Nemohu vytvořit prázdný obrázek!',
        'images.images.image_cannot_get_size' => 'Nelze načíst velikost obrázku!',
        'images.images.image_load_first' => 'Nejdříve je třeba načíst obrázek.',
        'images.images.description_cannot_remove' => 'Nelze odstranit popisek!',
        'images.images.description_cannot_find' => ' Popisek nelze načíst.',
        'images.images.description_already_exists' => 'Popisek s tímto jménem už tu je.',
        'images.images.description_cannot_remove_old' => 'Nemohu odstranit starý popisek.',
        'images.images.description_cannot_copy' => 'Nemohu zkopírovat popisek.',
        'images.images.description_cannot_move' => 'Nemohu přesunout popisek.',
        'images.images.description_cannot_rename' => 'Nehohu přejmenovat popisek.',
        'images.images.thumb_dir_cannot_remove' => 'Nemohu odstranit náhled složky!',
        'images.images.thumb_cannot_remove_current' => 'Nemohu odstranit aktuální náhled!',
        'images.images.image_cannot_read_size' => 'Nemohu načíst velikost obrázku. Existsuje vůbec?',
        'images.images.image_too_large' => 'Tento obrázek je pro místní užití až moc velký.',
        'images.images.image_cannot_find' => 'Nemohu najít obrázek.',
        'images.images.image_cannot_remove' => 'Nemohu odstranit obrázek.',
        'images.images.image_already_exists' => 'Obrázek s tímto jménem už tu je.',
        'images.images.image_cannot_remove_old' => 'Nemohu odstranit starý obrázek.',
        'images.images.image_cannot_copy' => 'Nemohu zkopírovat obrázek.',
        'images.images.image_cannot_move' => 'Nemohu přesunout obrázek.',
        'images.images.image_cannot_rename' => 'Nemohu přejmenovat obrázek.',
        'images.images.thumb_cannot_find' => 'Nemohu najít náhled.',
        'images.images.thumb_cannot_remove' => 'Nemohu odstranit náhled.',
        'images.images.thumb_already_exists' => 'Náhled s tímto jménem už tu je.',
        'images.images.thumb_cannot_remove_old' => 'Nemohu odstranit starý náhled.',
        'images.images.image_cannot_get_base' => 'Nemohu načíst obrázek z úložiště.',
        'images.images.image_cannot_store_temp' => 'Nemohu uložit dočasný obrázek.',
        'images.images.image_cannot_load_temp' => 'Nemohu načíst dočasný obrázek.',
        'images.images.thumb_cannot_copy' => 'Nemohu zkopírovat náhled.',
        'images.images.thumb_cannot_move' => 'Nemohu přesunout náhled.',
        'images.images.thumb_cannot_rename' => 'Nemohu přejmenovat náhled.',

        'images.page' => 'Obr&#x00E1;zky',
    ],
];

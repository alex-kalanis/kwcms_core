<?php

############################################################
#       FILES/LANGS.PHP                                    #
#       texts for files                                    #
############################################################
#       if you translate to new language,                  #
#       please use UTF-8 charset entities                  #
#       (for compatibility)                                #
############################################################

$lang = [
    'eng' => [
        "files.dashboard" => "Actions",
        "files.file.upload" => "Upload file",
        "files.file.read" => "Show file",
        "files.file.copy" => "Copy files",
        "files.file.move" => "Move files",
        "files.file.rename" => "Rename file",
        "files.file.delete" => "Delete files",
        "files.dir.new" => "New directory",
        "files.dir.copy" => "Copy directories",
        "files.dir.move" => "Move directories",
        "files.dir.rename" => "Rename directory",
        "files.dir.delete" => "Delete directories",

        "files.files" => "Files",
        "files.dirs" => "Dirs",
        "files.file.newName" => "New filename",
        "files.dir.newName" => "New dirname",
        "files.file.select" => "Select file",
        "files.file.selectMany" => "Select files",
        "files.dir.select" => "Select directory",
        "files.dir.selectMany" => "Select directories",
        "files.dir.selectTo" => "Set dorectory to",
        "files.check.really" => "Really?",
        "files.check.no" => "No",
        "files.check.yes" => "Yes",

        "files.error.must_contain_file" => "Uploaded data must contains a file.",
        "{DIR_NOT_EXISTS}" => "Directory doesn't exists.",
        "{TARGET_DIR_NOT_EXISTS}" => "Target directory doesn't exists.",
        "{TARGET_DIR_ALREADY_EXISTS}" => "Target directory already exists.",
        "files.must_be_sent" => "Cannot upload file.",
        "{FILE_NOT_MOVED_FROM_TEMP}" => "Cannot move file from temp.",
        "{FILE_ALREADY_EXISTS}" => "File already exists.",
        "{DIR_ALREADY_EXISTS}" => "Directory already exists.",
        "{FILE_NOT_EXISTS}" => "File doesn't exists.",
        "{NOT_LOADED}" => "Couldn't load.",
        "{FILE_IN_DEST_ALREADY_EXISTS}" => "File in destination already exists.",
        "{NOT_COPIED}" => "Not copied.",
        "{NOT_MOVED}" => "Not moved.",
        "{NOT_RENAMED}" => "Not renamed.",
        "{DELETE_NOT_ACCEPTED}" => "Deleting not accepted.",
        "{NOT_DELETED}" => "Not deleted.",
        "{DIR_CONTAIN_UNACCEPT_CHARS}" => "Name contains unacceptable chars.",
        "{NOT_CREATED}" => "Not created.",
        "{DIR_NOT_FREE}" => "Directory isn't free.",
        "files.uploaded" => "The file %s has been uploaded.",

        "files.dashboard.short" => "Actions",
        "files.file.upload.short" => "Upload",
        "files.file.read.short" => "Show",
        "files.file.copy.short" => "Copy",
        "files.file.move.short" => "Move",
        "files.file.rename.short" => "Rename",
        "files.file.delete.short" => "Delete",
        "files.dir.new.short" => "New dir",
        "files.dir.copy.short" => "Copy",
        "files.dir.move.short" => "Move",
        "files.dir.rename.short" => "Rename",
        "files.dir.delete.short" => "Delete",

        "files.page" => "Directories andf files"
    ],
    'fra' => [
        "files.dashboard" => "Actions",
        "files.file.upload" => "T&#x00E9;l&#x00E9;charger le fichier",
        "files.file.read" => "Voir le fichier",
        "files.file.copy" => "Copier les fichiers",
        "files.file.move" => "D&#x00E9;placer des fichiers",
        "files.file.rename" => "Renommer le fichier",
        "files.file.delete" => "Supprimer les fichiers",
        "files.dir.new" => "Le composant noveau",
        "files.dir.copy" => "Copier les composants",
        "files.dir.move" => "D&#x00E9;placer des composants",
        "files.dir.rename" => "Renommer le composant",
        "files.dir.delete" => "Supprimer les composants",

        "files.files" => "Fichiers",
        "files.dirs" => "Composants",
        "files.file.newName" => "Le nom noveau",
        "files.dir.newName" => "Le nom noveau",
        "files.file.select" => "Choisi le fichier",
        "files.file.selectMany" => "S&#x00E9;lectionnez le fichier",
        "files.dir.select" => "Choisi le composant",
        "files.dir.selectMany" => "S&#x00E9;lectionnez le composant",
        "files.dir.selectTo" => "Choisi le composant o&#x00F9;",
        "files.check.really" => "Vraiment?",
        "files.check.no" => "Non",
        "files.check.yes" => "Oui",

        "files.error.must_contain_file" => "Il n'y a pas un fichier au demande.",
        "{DIR_NOT_EXISTS}" => "Le composant n'existe pas.",
        "{TARGET_DIR_NOT_EXISTS}" => "Le composant cible n'existe pas.",
        "{TARGET_DIR_ALREADY_EXISTS}" => "Le composant cible existe d&#x00E9;j&#x00E0;.",
        "files.must_be_sent" => "Le fichier n'a pas transf&#x00E9;r&#x00E9;",
        "{FILE_NOT_MOVED_FROM_TEMP}" => "Impossible de d&#x00E9;placer le fichier &#x00E0;partir de temporaries.",
        "{FILE_ALREADY_EXISTS}" => "Le fichier existe d&#x00E9;j&#x00E0;.",
        "{DIR_ALREADY_EXISTS}" => "Le composant existe d&#x00E9;j&#x00E0;.",
        "{FILE_NOT_EXISTS}" => "Le fichier n'existe pas.",
        "{NOT_LOADED}" => "Impossible de lire",
        "{FILE_IN_DEST_ALREADY_EXISTS}" => "Le fichier cible existe d&#x00E9;j&#x00E0;.",
        "{NOT_COPIED}" => "Pas copi&#x00E9;",
        "{NOT_MOVED}" => "Pas d&#x00E9;plac&#x00E9;",
        "{NOT_RENAMED}" => "Pas renomm&#x00E9;",
        "{DELETE_NOT_ACCEPTED}" => "Suppresion pas admis",
        "{NOT_DELETED}" => "Pas supprim&#x00E9;",
        "{DIR_CONTAIN_UNACCEPT_CHARS}" => "Le nom contient signes nonautoris&#x00E9;s",
        "{NOT_CREATED}" => "Pas cr&#x00E9;&#x00E9;",
        "{DIR_NOT_FREE}" => "Le composant pas libre",
        "files.uploaded" => "Le fichier %s est t&#x00E9;l&#x00E9;charge.",

        "files.dashboard.short" => "Actions",
        "files.file.upload.short" => "T&#x00E9;l&#x00E9;charger",
        "files.file.read.short" => "Voir",
        "files.file.copy.short" => "Copier",
        "files.file.move.short" => "D&#x00E9;placer",
        "files.file.rename.short" => "Renommer",
        "files.file.delete.short" => "Supprimer",
        "files.dir.new.short" => "Noveau",
        "files.dir.copy.short" => "Copier",
        "files.dir.move.short" => "D&#x00E9;placer",
        "files.dir.rename.short" => "Renommer",
        "files.dir.delete.short" => "Supprimer",

        "files.page" => "Composants et fichiers"
    ],
    'cze' => [
        "files.dashboard" => "Operace",
        "files.file.upload" => "Nahr&#x00E1;t soubor",
        "files.file.read" => "Zobrazit soubor",
        "files.file.copy" => "Kop&#x00ED;rovat soubory",
        "files.file.move" => "P&#x0159;esunout soubory",
        "files.file.rename" => "P&#x0159;ejmenovat soubor",
        "files.file.delete" => "Smazat soubory",
        "files.dir.new" => "Nov&#x00E1; slo&#x017E;ka",
        "files.dir.copy" => "Kop&#x00ED;rovat slo&#x017E;ky",
        "files.dir.move" => "P&#x0159;esunout slo&#x017E;ky",
        "files.dir.rename" => "P&#x0159;ejmenovat slo&#x017E;ku",
        "files.dir.delete" => "Smazat slo&#x017E;ky",

        // basic translates
        "files.files" => "Soubory",
        "files.dirs" => "Slo&#x017E;ky",
        "files.file.newName" => "Nov&#x00E9; jm&#x00E9;no",
        "files.dir.newName" => "Nov&#x00E9; jm&#x00E9;no",
        "files.file.select" => "Vyber soubor",
        "files.file.selectMany" => "Vyber soubory",
        "files.dir.select" => "Vyber slo&#x017E;ku",
        "files.dir.selectMany" => "Vyber slo&#x017E;ky",
        "files.dir.selectTo" => "Vyber slo&#x017E;ku kam",
        "files.check.really" => "Opravdu prov&#x00E9;st?",
        "files.check.no" => "Ne",
        "files.check.yes" => "Ano",

        // error responses
        "{DELETE_NOT_ACCEPTED}" => "Smaz&#x00E1;n&#x00ED; nebylo povoleno",
        "{UPLOAD_FAIL_1}" => "Ne&#x0161;lo nahr&#x00E1;t - podle php moc velk&#x00E9;",
        "{UPLOAD_FAIL_2}" => "Ne&#x0161;lo nahr&#x00E1;t - podle formul&#x00E1;&#x0159;e moc velk&#x00E9;",
        "{UPLOAD_FAIL_3}" => "Ne&#x0161;lo nahr&#x00E1;t - nen&#x00ED; to cel&#x00E9;",
        "{UPLOAD_FAIL_6}" => "Ne&#x0161;lo nahr&#x00E1;t - nem&#x00E1;m do&#x010D;asnou slo&#x017E;ku",
        "{UPLOAD_FAIL_7}" => "Ne&#x0161;lo nahr&#x00E1;t - nem&#x016F;&#x017E;u zapsat na disk",
        "{UNALLOWED_FILE_NAME_SLASH}" => "Nepovolen&#x00FD; znak ve jm&#x00E9;n&#x011B; - lom&#x00ED;tko",
        "{UNALLOWED_FILE_NAME_NULL}" => "Nepovolen&#x00FD; znak ve jm&#x00E9;n&#x011B; - pr&#x00E1;zdn&#x00FD; znak",
        "{UNALLOWED_FILE_NAME_DOUBLEDOT}" => "Nepovolen&#x00FD; znak ve jm&#x00E9;n&#x011B; - dvojte&#x010D;ka",
        "{UNALLOWED_FILE_NAME_DOT}" => "Nepovolen&#x00FD; znak ve jm&#x00E9;n&#x011B; - te&#x010D;ka",
        "{UPLOAD_VARNAME_NOT_SPECIFIED}" => "Ne&#x0161;lo nahr&#x00E1;t - nev&#x00ED;m z &#x010D;eho",
        "{FILE_NOT_FULLY_UPLOADED}" => "Soubor ne&#x0161;lo nahr&#x00E1;t",
        "{FILE_NOT_MOVED_FROM_TEMP}" => "Soubor ne&#x0161;lo vyt&#x00E1;hnout z do&#x010D;asn&#x00E9;ho um&#x00ED;st&#x011B;n&#x00ED;",
        "{FILE_NOT_EXISTS_HERE}" => "Soubor tu neexistuje",
        "{FILE_NOT_COPYIED_SOURCE_TARGET_SAME}" => "Soubor ne&#x0161;lo zkop&#x00ED;rovat - zdrojov&#x00FD; a c&#x00ED;lov&#x00FD; soubor je kupodivu stejn&#x00FD;",
        "{FILE_ALREADY_EXISTS_IN_CURRENT_DIR}" => "Soubor tu u&#x017E; existuje",
        "{FILE_NOT_COPYIED_IN_CURRENT_DIR}" => "Soubor ne&#x0161;lo zkop&#x00ED;rovat",
        "{TARGET_DIR_NOT_READABLE}" => "Z c&#x00ED;lov&#x00E9; slo&#x017E;ky nejde &#x010D;&#x00ED;st",
        "{TARGET_DIR_NOT_WRITABLE}" => "Do c&#x00ED;lov&#x00E9; slo&#x017E;ky nejde zapsat",
        "{FILE_ALREADY_EXISTS_IN_TARGET_DIR}" => "Soubor u&#x017E; v c&#x00ED;li existuje",
        "{FILE_NOT_COPYIED_ONTO_TARGET_DIR}" => "Soubor prost&#x011B; nebyl zkop&#x00ED;rov&#x00E1;n",
        "{FILE_NOT_MOVED_SOURCE_TARGET_SAME}" => "Soubor ne&#x0161;lo p&#x0159;esunout - zdrojov&#x00FD; a c&#x00ED;lov&#x00FD; soubor je kupodivu stejn&#x00FD;",
        "{FILE_NOT_RENAMED_IN_CURRENT_DIR}" => "Soubor ne&#x0161;lo p&#x0159;ejmenovat",
        "{FILE_NOT_MOVED_ONTO_TARGET_DIR}" => "Soubor prost&#x011B; nebyl p&#x0159;esunut",
        "{DIR_ALREADY_EXISTS_HERE}" => "Slo&#x017E;ka u&#x017E; existuje",
        "{DIR_CANNOT_CREATE}" => "Slo&#x017E;ku ne&#x0161;lo vytvo&#x0159;it",
        "{CANNOT_DETERMINE_CONTENT}" => "Nemohu rozli&#x0161;it obsah",
        "{FILE_CANNOT_READ}" => "Soubor nejde p&#x0159;e&#x010D;&#x00ED;st",


        "files.error.must_contain_file" => "Nahran&#x00E1; data mus&#x00ED; obsahovat soubor!",
        "{DIR_NOT_EXISTS}" => "Slo&#x017E;ka neexistuje",
        "{TARGET_DIR_NOT_EXISTS}" => "C&#x00ED;lov&#x00E1; slo&#x017E;ka neexistuje",
        "{TARGET_DIR_ALREADY_EXISTS}" => "C&#x00ED;lov&#x00E1; slo&#x017E;ka u&#x017E; existuje",
        "files.must_be_sent" => "Soubor ne&#x0161;lo nahr&#x00E1;t",
        "{FILE_ALREADY_EXISTS}" => "Soubor u&#x017E; existuje",
        "{DIR_ALREADY_EXISTS}" => "Slo&#x017E;ka u&#x017E; existuje",
        "{FILE_NOT_EXISTS}" => "Soubor neexistuje",
        "{NOT_LOADED}" => "Nena&#x010D;iteln&#x00E9;",
        "{FILE_IN_DEST_ALREADY_EXISTS}" => "Soubor v c&#x00ED;li u&#x017E; existuje",
        "{NOT_COPIED}" => "Nezkop&#x00ED;rov&#x00E1;no",
        "{NOT_MOVED}" => "Nep&#x0159;esunuto",
        "{NOT_RENAMED}" => "Nep&#x0159;ejmenov&#x00E1;no",
        "{NOT_DELETED}" => "Nesmaz&#x00E1;no",
        "{DIR_CONTAIN_UNACCEPT_CHARS}" => "N&#x00E1;zev obsahuje nepovolen&#x00E9; znaky",
        "{NOT_CREATED}" => "Nevytvo&#x0159;eno",
        "{DIR_NOT_FREE}" => "Slo&#x017E;ka nen&#x00ED; pr&#x00E1;zdn&#x00E1;",
        "files.uploaded" => "Soubor %s nahr&#x00E1;n.",

        // menu naming
        "files.dashboard.short" => "Operace",
        "files.file.upload.short" => "Nahr&#x00E1;v&#x00E1;n&#x00ED;",
        "files.file.read.short" => "Zobrazit soubor",
        "files.file.copy.short" => "Kop&#x00ED;rov&#x00E1;n&#x00ED;",
        "files.file.move.short" => "P&#x0159;esun",
        "files.file.rename.short" => "P&#x0159;ejmenov&#x00E1;n&#x00ED;",
        "files.file.delete.short" => "Maz&#x00E1;n&#x00ED;",
        "files.dir.new.short" => "Nov&#x00E1; slo&#x017E;ka",
        "files.dir.copy.short" => "Kop&#x00ED;rov&#x00E1;n&#x00ED;",
        "files.dir.move.short" => "P&#x0159;esun",
        "files.dir.rename.short" => "P&#x0159;ejmenov&#x00E1;n&#x00ED;",
        "files.dir.delete.short" => "Maz&#x00E1;n&#x00ED;",

        "files.page" => "Slo&#x017E;ky a soubory"
    ],
];

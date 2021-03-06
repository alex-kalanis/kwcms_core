<?php

############################################################
#       UPLOAD/LANGS.PHP                                   #
#       names and contents of languages                    #
#       first shown on pages with selection                #
############################################################
#       if you translate to new language,                  #
#       please use UTF-8 charset entities                  #
#       (for compatibility)                                #
############################################################

$lang = [
    'eng' => [
        'upload.list_upload' => 'Uploading',

        'upload.button.start' => 'Start uploading',
        'upload.button.abort' => 'Cancel all uploading',
        'upload.button.clear' => 'Clear all files to upload',
        'upload.button.retry' => 'Restart from begining',
        'upload.button.resume' => 'Resume uploading',
        'upload.button.stop' => 'Stop uploading',

        'upload.form.file_name' => 'File name',
        'upload.form.elapsed_time' => 'Passed time',
        'upload.form.estimated_time' => 'Estimated time',
        'upload.form.estimated_speed' => 'Estimated speed',

        'upload.vendor.sent_file_name_is_empty' => 'Sent file name is empty!',
        'upload.vendor.upload_file_name_is_empty' => 'Uploaded file name is empty!',
        'upload.vendor.shared_key_is_empty' => 'Shared key is empty!',
        'upload.vendor.shared_key_is_invalid' => 'Shared key is invalid!',
        'upload.vendor.key_variant_not_known' => 'Wanted variant of key is not known!',
        'upload.vendor.target_dir_is_empty' => 'The target dir is not set!',
        'upload.vendor.drive_file_already_exists' => 'The drive data are already there!',
        'upload.vendor.drive_file_not_continuous' => 'The drive data does not get data to continue where ends!',
        'upload.vendor.drive_file_cannot_remove' => 'Cannot remove drive data!',
        'upload.vendor.drive_file_variant_not_known' => 'The drive data format is not known!',
        'upload.vendor.drive_file_cannot_read' => 'Cannot read drive data!',
        'upload.vendor.drive_file_cannot_write' => 'Cannot write to drive data!',
        'upload.vendor.data_cannot_remove' => 'Cannot remove existing data!',
        'upload.vendor.data_read_early' => 'The segment has been sent to early!',
        'upload.vendor.data_cannot_open' => 'Cannot open drive data!',
        'upload.vendor.data_cannot_read' => 'Cannot read drive data!',
        'upload.vendor.data_cannot_seek' => 'Cannot seek through drive data!',
        'upload.vendor.data_cannot_write' => 'Cannot write into drive data!',
        'upload.vendor.data_cannot_truncate' => 'Cannot truncate drive data!',
        'upload.vendor.segment_out_of_bounds' => 'Sent segment number is out of bounds!',
        'upload.vendor.segment_not_uploaded' => 'Wanted segment is not uploaded yet!',

        'upload.script.read_file_cannot_slice' => 'Cannot slice file',
        'upload.script.init_returns_following_error' => 'Init returns following error: ',
        'upload.script.init_returns_something_failed' => 'Init does not return a JSON data. More at console.',
        'upload.script.checker_returns_something_failed' => 'Data check does not return a JSON data. More at console.',
        'upload.script.data_upload_returns_something_failed' => 'Data upload does not return a JSON. More at console.',
        'upload.script.done_returns_something_failed' => 'Done does not return a JSON data. More at console.',

        'upload.page' => 'Upload large files',
    ],
    'cze' => [
        'upload.list_upload' => 'Nahr??v??n??',

        'upload.button.start' => 'Spustit nahr??v??n??',
        'upload.button.abort' => 'Zru??it v??echna nahr??v??n??',
        'upload.button.clear' => 'Vy??istit plo??ky k nahr??v??n??',
        'upload.button.retry' => 'Zopakovat od za????tku',
        'upload.button.resume' => 'Pokra&#x010D;ovat v nahr??v??n??',
        'upload.button.stop' => 'Zastavit nahr??v??n??',

        'upload.form.file_name' => 'Jm&#x00E9;no souboru',
        'upload.form.elapsed_time' => 'Uplynul?? ??as',
        'upload.form.estimated_time' => 'Odhadovan?? ??as',
        'upload.form.estimated_speed' => 'Odhadovan?? rychlost',

        'upload.vendor.sent_file_name_is_empty' => 'Jm??no poslen??ho souboru je pr??zdn??!',
        'upload.vendor.upload_file_name_is_empty' => 'Po??adovan?? n??zev souboru je pr??zdn??!',
        'upload.vendor.shared_key_is_empty' => 'Sd??len?? kl???? je pr??zdn??!',
        'upload.vendor.shared_key_is_invalid' => 'Sd??len?? kl???? je neplatn??!',
        'upload.vendor.key_variant_not_known' => 'Varianta sd??len??ho kl????e nen?? zn??m??!',
        'upload.vendor.target_dir_is_empty' => 'Nen?? nastavena c??lov?? slo??ka!',
        'upload.vendor.drive_file_already_exists' => '????d??c?? data ji?? existuj??!',
        'upload.vendor.drive_file_not_continuous' => '????d??c?? data m?????? tam, kam je??t?? nemaj??!',
        'upload.vendor.drive_file_cannot_remove' => 'Nelze smazat ????d??c?? data!',
        'upload.vendor.drive_file_variant_not_known' => 'Form??t ????d??c??ch dat nen?? zn??m??!',
        'upload.vendor.drive_file_cannot_read' => 'Nelze p??e????st ????d??c?? data!',
        'upload.vendor.drive_file_cannot_write' => 'Do ????d??c??ch dat nelze zapsat!',
        'upload.vendor.data_cannot_remove' => 'Existuj??c?? soubor nelze odstranit!',
        'upload.vendor.data_read_early' => 'Segment souboru p??i??le p????li?? brzo!',
        'upload.vendor.data_cannot_open' => 'Nelze otev????t soubor s daty!',
        'upload.vendor.data_cannot_read' => 'Nelze ????st v souboru s daty!',
        'upload.vendor.data_cannot_seek' => 'Nelze sk??kat v souboru s daty!',
        'upload.vendor.data_cannot_write' => 'Nelze zapsat do souboru s daty!',
        'upload.vendor.data_cannot_truncate' => 'Nelze zkr??tit soubor s daty!',
        'upload.vendor.segment_out_of_bounds' => '????slo poslan??ho segmentu je mimo povolen?? meze!',
        'upload.vendor.segment_not_uploaded' => 'Po??adovan?? segment je??t?? nebyl nahr??n!',

        'upload.script.read_file_cannot_slice' => 'Soubor nelze nakr??jet',
        'upload.script.init_returns_following_error' => 'Inicializace vrac?? chybu: ',
        'upload.script.init_returns_something_failed' => 'Inicializace nevr??tila odpov???? ve form??tu JSON. V??c v konzoli.',
        'upload.script.checker_returns_something_failed' => 'Kontrola dat nevr??tila odpov???? ve form??tu JSON. V??c v konzoli.',
        'upload.script.data_upload_returns_something_failed' => 'Nahr??v??n?? dat nevr??tilo odpov???? ve form??tu JSON. V??c v konzoli.',
        'upload.script.done_returns_something_failed' => 'Uzav??en?? nahr??v??n?? nevr??tilo odpov???? ve form??tu JSON. V??c v konzoli.',

        'upload.page' => 'Nahr??v??n?? velk??ch soubor??',
    ],
];

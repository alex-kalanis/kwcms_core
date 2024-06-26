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
        'upload.vendor.drive_data_not_set' => 'The drive data class is not set!',
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

        'upload.vendor.target_not_set' => 'The target is not set.',
        'upload.vendor.target_is_wrong' => 'The target is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.checksum_is_wrong' => 'The checksum is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.decoder_is_wrong' => 'The decoder is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.cannot_decode_incoming_data' => 'Cannot decode incoming data!',
        'upload.vendor.sent_file_name_empty' => 'Sent file name is empty.',
        'upload.vendor.checksum_data_is_empty' => 'There is no data for checksum on storage.',
        'upload.vendor.driving_encoder_not_set' => 'The driving data encoder variant is not set!',
        'upload.vendor.driving_encoder_is_wrong' => 'The driving data encoder is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_modifier_not_set' => 'The driving data modifier variant is not set!',
        'upload.vendor.driving_modifier_is_wrong' => 'The driving data modifier is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_key_encoder_not_set' => 'The driving data key encoder variant is not set!',
        'upload.vendor.driving_key_encoder_is_wrong' => 'The driving data key encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_key_modifier_not_set' => 'The driving data key modifier variant is not set!',
        'upload.vendor.driving_key_modifier_is_wrong' => 'The driving data key modifier variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.drive_file_storage_not_set' => 'The driving file storage is not set correctly!',
        'upload.vendor.temp_encoder_not_set' => 'The temporary storage encoder variant is not set!',
        'upload.vendor.temp_encoder_is_wrong' => 'The temporary storage encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.temp_storage_not_set' => 'The temporary storage is not set correctly!',
        'upload.vendor.final_encoder_not_set' => 'The final storage encoder variant is not set!',
        'upload.vendor.final_encoder_is_wrong' => 'The final storage encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.final_storage_not_set' => 'The final storage is not set correctly!',

        'upload.script.read_file_cannot_slice' => 'Cannot slice file',
        'upload.script.init_returns_following_error' => 'Init returns following error: ',
        'upload.script.init_returns_something_failed' => 'Init does not return a JSON data. More at console.',
        'upload.script.checker_returns_something_failed' => 'Data check does not return a JSON data. More at console.',
        'upload.script.data_upload_returns_something_failed' => 'Data upload does not return a JSON. More at console.',
        'upload.script.done_returns_something_failed' => 'Done does not return a JSON data. More at console.',

        'upload.page' => 'Upload large files',
    ],
    'cze' => [
        'upload.list_upload' => 'Nahrávání',

        'upload.button.start' => 'Spustit nahrávání',
        'upload.button.abort' => 'Zrušit všechna nahrávání',
        'upload.button.clear' => 'Vyčistit pložky k nahrávání',
        'upload.button.retry' => 'Zopakovat od začátku',
        'upload.button.resume' => 'Pokra&#x010D;ovat v nahrávání',
        'upload.button.stop' => 'Zastavit nahrávání',

        'upload.form.file_name' => 'Jm&#x00E9;no souboru',
        'upload.form.elapsed_time' => 'Uplynulý čas',
        'upload.form.estimated_time' => 'Odhadovaný čas',
        'upload.form.estimated_speed' => 'Odhadovaná rychlost',

        'upload.vendor.sent_file_name_is_empty' => 'Jméno posleného souboru je prázdné!',
        'upload.vendor.upload_file_name_is_empty' => 'Požadovaný název souboru je prázdný!',
        'upload.vendor.shared_key_is_empty' => 'Sdílený klíč je prázdný!',
        'upload.vendor.shared_key_is_invalid' => 'Sdílený klíč je neplatný!',
        'upload.vendor.key_variant_not_known' => 'Varianta sdíleného klíče není známá!',
        'upload.vendor.target_dir_is_empty' => 'Není nastavena cílová složka!',
        'upload.vendor.drive_file_already_exists' => 'Řídící data již existují!',
        'upload.vendor.drive_file_not_continuous' => 'Řídící data míří tam, kam ještě nemají!',
        'upload.vendor.drive_file_cannot_remove' => 'Nelze smazat řídící data!',
        'upload.vendor.drive_file_variant_not_known' => 'Formát řídících dat není známý!',
        'upload.vendor.drive_data_not_set' => 'Není nastavena třída řídících dat!',
        'upload.vendor.drive_file_cannot_read' => 'Nelze přečíst řídící data!',
        'upload.vendor.drive_file_cannot_write' => 'Do řídících dat nelze zapsat!',
        'upload.vendor.data_cannot_remove' => 'Existující soubor nelze odstranit!',
        'upload.vendor.data_read_early' => 'Segment souboru přišle příliš brzo!',
        'upload.vendor.data_cannot_open' => 'Nelze otevřít soubor s daty!',
        'upload.vendor.data_cannot_read' => 'Nelze číst v souboru s daty!',
        'upload.vendor.data_cannot_seek' => 'Nelze skákat v souboru s daty!',
        'upload.vendor.data_cannot_write' => 'Nelze zapsat do souboru s daty!',
        'upload.vendor.data_cannot_truncate' => 'Nelze zkrátit soubor s daty!',
        'upload.vendor.segment_out_of_bounds' => 'Číslo poslaného segmentu je mimo povolené meze!',

        'upload.vendor.target_not_set' => 'The target is not set.',
        'upload.vendor.target_is_wrong' => 'The target is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.checksum_is_wrong' => 'The checksum is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.decoder_is_wrong' => 'The decoder is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.cannot_decode_incoming_data' => 'Cannot decode incoming data!',
        'upload.vendor.sent_file_name_empty' => 'Sent file name is empty.',
        'upload.vendor.checksum_data_is_empty' => 'There is no data for checksum on storage.',
        'upload.vendor.driving_encoder_not_set' => 'The driving data encoder variant is not set!',
        'upload.vendor.driving_encoder_is_wrong' => 'The driving data encoder is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_modifier_not_set' => 'The driving data modifier variant is not set!',
        'upload.vendor.driving_modifier_is_wrong' => 'The driving data modifier is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_key_encoder_not_set' => 'The driving data key encoder variant is not set!',
        'upload.vendor.driving_key_encoder_is_wrong' => 'The driving data key encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.driving_key_modifier_not_set' => 'The driving data key modifier variant is not set!',
        'upload.vendor.driving_key_modifier_is_wrong' => 'The driving data key modifier variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.drive_file_storage_not_set' => 'The driving file storage is not set correctly!',
        'upload.vendor.temp_encoder_not_set' => 'The temporary storage encoder variant is not set!',
        'upload.vendor.temp_encoder_is_wrong' => 'The temporary storage encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.temp_storage_not_set' => 'The temporary storage is not set correctly!',
        'upload.vendor.final_encoder_not_set' => 'The final storage encoder variant is not set!',
        'upload.vendor.final_encoder_is_wrong' => 'The final storage encoder variant is set in a wrong way. Cannot determine it. *%s*',
        'upload.vendor.final_storage_not_set' => 'The final storage is not set correctly!',

        'upload.vendor.segment_not_uploaded' => 'Požadovaný segment ještě nebyl nahrán!',

        'upload.script.read_file_cannot_slice' => 'Soubor nelze nakrájet',
        'upload.script.init_returns_following_error' => 'Inicializace vrací chybu: ',
        'upload.script.init_returns_something_failed' => 'Inicializace nevrátila odpověď ve formátu JSON. Víc v konzoli.',
        'upload.script.checker_returns_something_failed' => 'Kontrola dat nevrátila odpověď ve formátu JSON. Víc v konzoli.',
        'upload.script.data_upload_returns_something_failed' => 'Nahrávání dat nevrátilo odpověď ve formátu JSON. Víc v konzoli.',
        'upload.script.done_returns_something_failed' => 'Uzavření nahrávání nevrátilo odpověď ve formátu JSON. Víc v konzoli.',

        'upload.page' => 'Nahrávání velkých souborů',
    ],
];

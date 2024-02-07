<?php

namespace KWCMS\modules\Krep\Libs;


class Translations
{
    protected static $lang = [
        // discus
        "warning" => 'Varov&#x00E1;n&#x00ED;: NADM&#x011A;RN&#x00C9; VYU&#x017D;V&#x00C1;N&#x00CD; F&#x00D3;RA K-REPORT M&#x016E;&#x017D;E VYVOLAT (A VYVOL&#x00C1;V&#x00C1;) Z&#x00C1;VISLOST!!!!',
        "hint" => 'Miniverze f&#x00F3;ra K-report ur&#x010D;en&#x00E1; pro slab&#x00E9; prohl&#x00ED;&#x017e;e&#x010D;e nebo p&#x0159;et&#x00ED;&#x017e;en&#x00E9; linky. Pokud chcete v&#x011B;d&#x011B;t, co um&#x00ED; a co t&#x00ED;mto poslat m&#x016F;&#x017e;ete, tak nahl&#x00E9;dn&#x011B;te do <a href="poznamky.html">adminov&#x00FD;ch pozn&#x00E1;mek</a>. N&#x00E1;sleduj&#x00ED;c&#x00ED; <b>v&#x00FD;straha</b> je ur&#x010D;ena hlavn&#x011B; t&#x011B;m, co maj&#x00ED; tyto znaky ve sv&#x00E9;m <b><i>jm&#x00E9;n&#x011B;</i></b> a <b><i>hesle</i></b>!!! D&#x0159;&#x00ED;ve, ne&#x017e; budete "na ostro" pos&#x00ED;lat p&#x0159;&#x00ED;sp&#x011B;vky do diskuz&#x00ED;, <b>otestujtete</b>, co v&#x00E1;m d&#x011B;l&#x00E1; mobil <i>s</i> na&#x0165;ukanou <i>diakritikou</i> (abyste nebyli nemile p&#x0159;ekvapen&#x00ED; z absenc&#x00ED; h&#x00E1;&#x010D;k&#x016F;, krou&#x017e;k&#x016F; &#x010D;i p&#x0159;ehl&#x00E1;sek (a podobn&#x00FD;ch gramatick&#x00FD;ch jev&#x016F;)). ',
        "err_problem" => 'Probl&#x00E9;m!',
        "err_no_sent" => 'Ne&#x0161;lo to poslat.',
        "err_no_connect" => 'Nespojil jsem se.',
        "err_another_exception" => 'N&#x011B;kde je chyba.',
        "add_post" => 'P&#x0159;idat post&#x0159;eh',
        "add_mess" => 'Zanechat zpr&#x00E1;vu',
        "err_only_for_registered" => 'Jen pro registrovan&#x00E9;.',
        "down" => 'Dolu',
        "up" => 'Nahoru',
        "archive" => 'Archiv',
        "topic_no" => 'P&#x0159;&#x00ED;sp&#x011B;vek &#x010D;.',
        "from_user" => 'od u&#x017e;ivatele',
        "back" => 'Zp&#x00E1;tky',
        "read" => '&#x010D;&#x00ED;st',
        "not_found" => 'Obsah nebyl nalezen.',
        "long_version" => 'Verze co nekr&#x00E1;t&#x00ED;.',
        "cut_version" => 'Verze co kr&#x00E1;t&#x00ED;.',
        // post
        "user" => 'U&#x017e;ivatel: ',
        "pass" => 'Heslo: ',
        "web" => 'Str&#x00E1;nky: ',
        "mail" => 'Mail: ',
        "post" => 'P&#x0159;&#x00ED;sp&#x011B;vek: ',
        "added_post" => 'p&#x0159;&#x00ED;sp&#x011B;vek',
        "send_post" => 'Odesl&#x00E1;n&#x00ED; p&#x0159;&#x00ED;sp&#x011B;vku',
        "hints" => 'Je t&#x0159;eba vyplnit jak p&#x0159;&#x00ED;sp&#x011B;vek, tak od koho je (jinak to nepo&#x0161;lu)! V p&#x0159;&#x00ED;pad&#x011B;, &#x017e;e si budete hr&#x00E1;t se styly a dal&#x0161;&#x00ED;mi detaily a nedodr&#x017e;&#x00ED;te po&#x017e;adavky k-reportu, tak se nedivte, &#x017e;e p&#x0159;&#x00ED;sp&#x011B;vek bude zahozen. <b>Ale hv&#x011B;zdi&#x010D;ky pro zm&#x011B;nu nevypl&#x0148;ujte!</b> Jinak na v&#x00E1;s &#x010D;ek&#x00E1; ovocn&#x00FD; p&#x0159;&#x00ED;d&#x011B;l! Odesl&#x00E1;n&#x00ED;m p&#x0159;&#x00ED;sp&#x011B;vku automaticky souhlas&#x00ED;te se zpracov&#x00E1;n&#x00ED;m osobn&#x00ED;ch údaj&#x016F; (nutn&#x00E9; z principu fungov&#x00E1;n&#x00ED). Odvol&#x00E1;n&#x00ED; souhlasu je mo&#x017e;n&#x00E9; pouze nahl&#x00E1;&#x0161;en&#x00ED;m v&#x0161;ech vlastn&#x00ED;ch p&#x0159;&#x00ED;sp&#x011B;vk&#x016F; p&#x0159;&#x00ED;mo na k-reportu a n&#x00E1;sledn&#x011B; jejich smaz&#x00E1;n&#x00ED;m.',
        "back_discus" => 'Zp&#x00E1;tky do diskuze...',
        "back_discus_d" => 'Diskuze',
        "back_themes" => 'T&#x00E9;mata',
        "back_begin" => '&#x00DA;vod',
        // error
        "get_error" => 'Nastala CHYBA',
        "on_action_get" => 'B&#x011B;hem z&#x00ED;sk&#x00E1;v&#x00E1;n&#x00ED; dat nastala chyba:',
        "on_action_post" => 'B&#x011B;hem odes&#x00ED;l&#x00E1;n&#x00ED; p&#x0159;&#x00ED;sp&#x011B;vku nastala chyba:',
        "on_action_where" => 'B&#x011B;hem c&#x00ED;len&#x00ED; p&#x0159;&#x00ED;sp&#x011B;vku nastala chyba:',
        "no_target" => 'Nen&#x00ED; kam to poslat.',
        "site_down" => 'Str&#x00E1;nky zdechly.',
        "no_connect" => 'S&#x00ED;&#x0165; neodpov&#x00ED;d&#x00E1;.',
        "on_banlist" => 'M&#x00E1;te ovocn&#x00FD; p&#x0159;&#x00ED;d&#x011B;l.',
        "something_wrong" => 'N&#x011B;kde se n&#x011B;co posralo.',
        // sent
        "post_sent" => 'Odesl&#x00E1;no.',
        "post_sent_dsc" => 'Zpr&#x00E1;va odesl&#x00E1;na.',
        "" => '',
    ];

    public static function tr($key)
    {
        return isset(static::$lang[$key]) ? static::$lang[$key] : $key ;
    }
}

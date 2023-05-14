<?php

namespace kalanis\kw_mapper\Mappers\Database;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers\AMapper;
use kalanis\kw_mapper\Storage;


/**
 * Class AReadWriteDatabase
 * @package kalanis\kw_mapper\Mappers\Database
 * Separated Read and write DB entry without need to reload mapper
 * The most parts are similar to usual read/write one, just with separation of read-write operations
 *
 * todo: Tohle smrdi jako zadost o rozdeleni - mapper by pak mel pouze nosit mapovaci data, samotna zpracovavajici trida
 *       (nebo jejich soustava) se ho pak bude na ne ptat. Tyhle tridy budou rozdilne pro soubory a jednotlive databaze.
 *       Otazka zni jak to rozdelit. Vsechno samozrejme dostane read a write datasources, pro shodne se naplni stejne,
 *       pro rozdilne budou proste sources jine. Mozna je ten rozdelujici bod uz v recordu - operace zapisu chodi jinudy
 *       nez operace cteni. Record tak dostane 2 mappery - jeden cteci a druhy zapisovaci. Na oba pujde nalepit pristup
 *       do dalsich ulozist nebo jinych forem. Takze cteni muze byt lokalne naprimo, ale zapis poleti pres nejakou
 *       frontu na vzdalenem ulozisti. V recordu pak pujde i prepinat zmenou patricneho mapperu, jestli se to provede
 *       takhle nebo jinak. Navic to umozni spojit veci se searchem, kde je obdobna implementace jen pro cteni dat.
 *
 * todo: Rozdelit read a write database podle interface - mozna to rozpadne tuhle classu
 */
abstract class AReadWriteDatabase extends AMapper
{
    use TTable;
    use TReadDatabase;
    use TWriteDatabase;

    /**
     * @throws MapperException
     */
    public function __construct()
    {
        parent::__construct();

        $this->initTReadDatabase();
        $this->initTWriteDatabase();
    }

    public function getAlias(): string
    {
        return $this->getTable();
    }
}

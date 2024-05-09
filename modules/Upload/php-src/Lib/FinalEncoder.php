<?php

namespace KWCMS\modules\Upload\Lib;


use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\UploadPerPartes\Interfaces\IUppTranslations;
use kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders\AEncoder;
use kalanis\UploadPerPartes\Traits\TLang;
use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class FinalEncoder
 * @package KWCMS\modules\Upload\Lib
 * Encode final path
 */
class FinalEncoder extends AEncoder
{
    use TLang;

    protected ArrayPath $paths;

    /**
     * @param ArrayPath|null $arrayPaths
     * @param IUppTranslations|null $lang
     */
    public function __construct(?ArrayPath $arrayPaths = null, IUppTranslations $lang = null)
    {
        $this->paths = $arrayPaths ?: new ArrayPath();
        parent::__construct($lang);
    }

    /**
     * @param Data $data
     * @throws PathsException
     * @return string
     */
    public function toPath(Data $data): string
    {
        $target = $this->paths->setString($data->targetDir)->getArray();
        return $this->paths->setArray(array_filter(array_merge(
            $target,
            [$data->targetName],
        )))->getString();
    }
}

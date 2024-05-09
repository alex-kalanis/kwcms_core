<?php

namespace kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders;


use kalanis\UploadPerPartes\Uploader\Data;


/**
 * Class Name
 * @package kalanis\UploadPerPartes\Target\Local\FinalStorage\KeyEncoders
 */
class Name extends AEncoder
{
    public function toPath(Data $data): string
    {
        return $data->targetName;
    }
}

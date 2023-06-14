<?php

namespace KWCMS\modules\Video\Templates;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Templates\ATemplate;
use kalanis\kw_templates\TemplateException;


/**
 * Class Display
 * @package KWCMS\modules\Video\Templates

# style of player -> which template and which style may be shown
# - nothing -> empty space to fill
# - player -> display player

 */
class Player extends ATemplate
{
    protected $moduleName = 'Video';
    protected $templateName = 'nothing';

    /* Which styles are available and if they want solo rows */
    protected static $styles = [ # usage of one line - one file (for count cols)
        'player' => false,
        'nothing' => false,
    ];

    /**
     * @param string $name
     * @throws TemplateException
     * @return $this
     */
    public function setTemplateName(string $name): self
    {
        if (in_array($name, array_keys(static::$styles))) {
            $this->templateName = $name;
            $this->setTemplate($this->loadTemplate());
        }
        return $this;
    }

    protected function fillInputs(): void
    {
        $this->addInput('{WIDTH}');
        $this->addInput('{HEIGHT}');
        $this->addInput('{NO_PLAYER}', Lang::get('video.no_player'));
        $this->addInput('{VIDEO_THUMB}');
        $this->addInput('{VIDEO_LINK}');
        $this->addInput('{VIDEO_MIME}');
    }

    public function setData(string $thumb, int $width, int $height, string $link, string $mime): self
    {
        $this->updateItem('{VIDEO_THUMB}', $thumb);
        $this->updateItem('{WIDTH}', $width);
        $this->updateItem('{HEIGHT}', $height);
        $this->updateItem('{VIDEO_LINK}', $link);
        $this->updateItem('{VIDEO_MIME}', $mime);
        return $this;
    }
}

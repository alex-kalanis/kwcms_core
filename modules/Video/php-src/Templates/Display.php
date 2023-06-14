<?php

namespace KWCMS\modules\Video\Templates;


use kalanis\kw_confs\Config;
use kalanis\kw_modules\Templates\ATemplate;
use kalanis\kw_templates\TemplateException;


/**
 * Class Display
 * @package KWCMS\modules\Video\Templates

# style of directory -> which template and which style may be shown
# - icon -> miniature thumbs with names
# - compact -> list of files without thumbs (only icons)
# - list -> table with listed details (icon-name-details)

 */
class Display extends ATemplate
{
    protected $moduleName = 'Video';
    protected $templateName = 'list';

    /* Which styles are available and if they want solo rows */
    protected static $styles = [ # usage of one line - one file (for count cols)
        'icon' => false,
        'compact' => false,
        'list' => true
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
        $this->addInput('{ICON}');
        $this->addInput('{ICON_WIDTH}');
        $this->addInput('{ICON_HEIGHT}');
        $this->addInput('{LINK}');
        $this->addInput('{IMAGE_THUMB}');
        $this->addInput('{NAME}');
        $this->addInput('{DETAILS}');
        $this->addInput('{FILE_INFO}');
    }

    public function setData(string $icon, string $link, string $thumb, string $name, string $details, string $info): self
    {
        $this->updateItem('{ICON}', $icon);
        $this->updateItem('{LINK}', $link);
        $this->updateItem('{IMAGE_THUMB}', $thumb);
        $this->updateItem('{NAME}', $name);
        $this->updateItem('{DETAILS}', $details);
        $this->updateItem('{FILE_INFO}', $info);
        $this->updateItem('{ICON_WIDTH}', Config::get('Video', 'icon_width'));
        $this->updateItem('{ICON_HEIGHT}', Config::get('Video', 'icon_height'));
        return $this;
    }

    public function getStyles(): array
    {
        return static::$styles;
    }
}

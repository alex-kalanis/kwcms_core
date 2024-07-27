<?php

namespace KWCMS\modules\Dirlist\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Display
 * @package KWCMS\modules\Dirlist\Templates

# style of directory -> which template and which style may be shown
# - icon -> miniature thumbs with names
# - compact -> list of files without thumbs (only icons)
# - list -> table with listed details (icon-name-details)

 */
class Display extends ATemplate
{
    protected string $moduleName = 'Dirlist';
    protected string $templateName = 'list';

    /* Which styles are available and if they want solo rows */
    protected static array $styles = [ # usage of one line - one file (for count cols)
        'icon' => false,
        'named' => false,
        'compact' => false,
        'list' => true,
    ];

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
        return $this;
    }

    public function getStyles(): array
    {
        return static::$styles;
    }
}

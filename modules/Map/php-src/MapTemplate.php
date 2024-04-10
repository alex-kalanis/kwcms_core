<?php

namespace KWCMS\modules\Map;


use KWCMS\modules\Core\Libs\ATemplate;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_templates\TemplateException;


/**
 * Class MapTemplate
 * @package KWCMS\modules\Map
 */
class MapTemplate extends ATemplate
{
    protected string $moduleName = 'Map';
    protected string $templateName = 'st_osm';
    protected string $prefix = 'dn';

    /* Which styles are available and if they want solo rows */
    protected static array $maps = [ # usage of one line - one file (for count cols)
        'google' => false,
        'osm' => false,
//        'seznam' => true,
    ];

    public function setIfImage(bool $isStaticImage): self
    {
        $this->prefix = $isStaticImage ? 'st_' : 'dn_';
        return $this;
    }

    /**
     * @param string $name
     * @throws TemplateException
     * @return MapTemplate
     */
    public function setTemplateName(string $name): self
    {
        if (in_array($name, array_keys(static::$maps))) {
            $this->templateName = $this->prefix . $name;
            $this->setTemplate($this->loadTemplate());
        }
        return $this;
    }

    protected function fillInputs(): void
    {
        $this->addInput('{ID}');
        $this->addInput('{NS}');
        $this->addInput('{EW}');
        $this->addInput('{LV}');
        $this->addInput('{WD}');
        $this->addInput('{HG}');
    }

    public function setData(string $id, string $latitude, string $longitude, string $level, string $width, string $height): self
    {
        $this->updateItem('{ID}', $id);
        $this->updateItem('{NS}', $latitude);
        $this->updateItem('{EW}', $longitude);
        $this->updateItem('{LV}', $level);
        $this->updateItem('{WD}', $width);
        $this->updateItem('{HG}', $height);
        return $this;
    }

    public function getMaps(): array
    {
        return static::$maps;
    }
}

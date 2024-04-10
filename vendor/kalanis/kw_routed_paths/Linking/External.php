<?php

namespace kalanis\kw_routed_paths\Linking;


/**
 * Class Link
 * @package kalanis\kw_routed_paths\Linking
 */
class External
{
    protected Link $lib;
    /** @var string[] */
    protected array $presetPath = [];
    protected ?string $userName = null;

    /**
     * @param Link $lib
     * @param string[] $presetPath
     * @param string|null $userName
     */
    public function __construct(Link $lib, array $presetPath, ?string $userName = null)
    {
        $this->lib = $lib;
        $this->presetPath = $presetPath;
        $this->userName = $userName;
    }

    /**
     * @param string[] $path
     * @param string[] $module
     * @param bool $single
     * @return string
     */
    public function link(?array $path = null, array $module = [], bool $single = false): string
    {
        return $this->lib->link(
            is_null($path) ? $this->presetPath : $path,
            $module,
            $single,
            $this->userName
        );
    }
}

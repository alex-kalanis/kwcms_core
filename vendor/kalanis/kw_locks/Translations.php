<?php

namespace kalanis\kw_locks;


use kalanis\kw_locks\Interfaces\IKLTranslations;


/**
 * Class Translations
 * @package kalanis\kw_locks
 */
class Translations implements IKLTranslations
{
    public function iklLockedByOther(): string
    {
        return 'Locked by another!';
    }

    public function iklProblemWithStorage(): string
    {
        return 'Problem with storage';
    }

    /**
     * @param string $lockFilename
     * @return string
     * @codeCoverageIgnore
     */
    public function iklCannotUseFile(string $lockFilename): string
    {
        return 'Cannot use: ' . $lockFilename . ' as lock file. Path is not writable.';
    }

    /**
     * @param string $path
     * @return string
     * @codeCoverageIgnore
     */
    public function iklCannotUsePath(string $path): string
    {
        return 'Cannot use: ' . $path . ' to lock. Path not found and cannot be created.';
    }

    /**
     * @param string $lockFilename
     * @return string
     * @codeCoverageIgnore
     */
    public function iklCannotOpenFile(string $lockFilename): string
    {
        return 'Could not open lock file: '. $lockFilename;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function iklCannotUseOS(): string
    {
        return 'Unusable OS. Cannot use external programs to determine process ID.';
    }
}

<?php

namespace KWCMS\modules\Krep\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Krep\Libs\Config;
use KWCMS\modules\Krep\Libs\ModuleException;


/**
 * Class Bans
 * @package KWCMS\modules\Krep\Controllers
 * Update local database of bans
 */
class Bans extends ADisposition
{
    /** @var string */
    protected $bansPath = '';
    /** @var string */
    protected $banIp = 'https://www.k-report.net/pomocnaslozka/bnip.txt';
    /** @var string */
    protected $banWord = 'https://www.k-report.net/pomocnaslozka/bnwd.txt';
    /** @var array<mixed> */
    protected $data = [];

    public function __construct(Config $config, string $bans_path)
    {
        parent::__construct($config);
        $this->bansPath = $bans_path;
    }

    public function process(): void
    {
        $code = 0;
        $status = 'OK';
        $ips = false;
        $words = false;
        try {
            $ips = $this->updateFromRemote($this->banIp, 'ban4.txt');
            $words = $this->updateFromRemote($this->banWord, 'banbw.txt');
        } catch (ModuleException $ex) {
            $code = $ex->getCode();
            $status = $ex->getMessage();
        }
        $result = $ips || $words;
        $this->data = compact('result', 'code', 'status', 'ips', 'words');
    }

    public function output(): Output\AOutput
    {
        return (new Output\Json())->setContent($this->data);
    }

    protected function getContent(): string
    {
        return '';
    }

    /**
     * @param string $address
     * @param string $file
     * @throws ModuleException
     * @return bool
     */
    protected function updateFromRemote(string $address, string $file): bool
    {
        $remoteData = $this->remoteRequest($address, $this->contextData());
        if (preg_match('/(.*?)<h[12345]>(.*)/s', $remoteData)) {
            throw new ModuleException('Not Acceptable', 406);
        }
        $currentFile = $this->bansPath . $file;
        $backupFile = $this->bansPath . $file . '.backup';
        $newFile = $this->bansPath . $file . '.new';
        if (false === @file_put_contents($newFile, $remoteData)) {
            throw new ModuleException('Cannot save', 400);
        }
        if (is_file($currentFile)) {
            @rename($currentFile, $backupFile);
        }
        if (is_file($newFile)) {
            @rename($newFile, $currentFile);
        }
        if (is_file($currentFile) && is_file($backupFile)) {
            @unlink($backupFile);
        }
        chmod($currentFile, 0666);
        return true;
    }

    /**
     * @param string $address
     * @param array<mixed> $contextData
     * @throws ModuleException
     * @return string
     */
    protected function remoteRequest(string $address, array $contextData): string
    {
        $content = @file_get_contents($address, false, stream_context_create($contextData));
        if (false === $content) {
            throw new ModuleException('Gone', 410);
        }
        return strval($content);
    }

    protected function contextData(): array
    {
        return [
//            "ssl" => [
//                "verify_peer" => true,
//                "verify_peer_name" => true,
//            ],
        ];
    }
}

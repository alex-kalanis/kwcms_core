<?php

namespace KWCMS\modules\Krep\Controllers;


use kalanis\kw_modules\Output;
use KWCMS\modules\Krep\Libs\Config;
use KWCMS\modules\Krep\Libs\ModuleException;
use KWCMS\modules\Krep\Libs\Shared\Query;


/**
 * Class Bans
 * @package KWCMS\modules\Krep\Controllers
 * Update local database of bans
 *
 * @OA\Get(
 *     path="/bans.php",
 *     tags={"Bans"},
 *     @OA\RequestBody(),
 *     @OA\Response(response="200", description="What happend with code", @OA\JsonContent()),
 * )
 */
class Bans extends ADisposition
{
    protected string $banIp = 'https://www.k-report.net/pomocnaslozka/bnip.txt';
    protected string $banWord = 'https://www.k-report.net/pomocnaslozka/bnwd.txt';
    /** @var array<mixed> */
    protected array $data = [];

    public function __construct(
        Config $config,
        protected readonly string $bans_path,
        protected readonly Query $query,
    )
    {
        parent::__construct($config);
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
        $remoteData = $this->query->postToServer($address, $this->contextData());
        if (preg_match('/(.*?)<h[12345]>(.*)/s', $remoteData->data)) {
            throw new ModuleException('Not Acceptable', 406);
        }
        $currentFile = $this->bans_path . $file;
        $backupFile = $this->bans_path . $file . '.backup';
        $newFile = $this->bans_path . $file . '.new';
        if (false === @file_put_contents($newFile, $remoteData->data)) {
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

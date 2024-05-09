<?php


namespace KWCMS\modules\Krep\Libs\Add;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;
use KWCMS\modules\Krep\Libs\ModuleException;


/**
 * Class Server
 * External input datasource - SERVER variables
 *
 * @package KWCMS\modules\Krep\Libs\Add
 */
class ServerData
{
    /** @var IEntry[] */
    protected array $in = [];

    public function setInputs(IFiltered $filtered)
    {
        $this->in = $filtered->getInArray(null, [IEntry::SOURCE_SERVER]);
    }

    /**
     * Returns actual IP
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getIp(string $default = '0.0.0.0'): string
    {
        $requestIp = $default;
        $in = $this->getIn();
        if (isset($in['GEOIP_ADDR'])) {
            $requestIp = strval($in['GEOIP_ADDR']);
        } elseif (isset($in['HTTP_X_REAL_IP'])) {
            $requestIp = strval($in['HTTP_X_REAL_IP']);
        } elseif (isset($in['HTTP_X_FORWARDED_FOR'])) {
            $requestIp = strval($in['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($in['REMOTE_ADDR'])) {
            $requestIp = strval($in['REMOTE_ADDR']);
        }
        return $requestIp;
    }

    /**
     * Returns current domain
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getHost(string $default = ''): string
    {
        return $this->getVariable('HTTP_HOST', $default);
    }

    /**
     * returns link from which query comes
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getReferer(string $default = ''): string
    {
        return $this->getVariable('HTTP_REFERER', $default);
    }

    /**
     * What is current user's browser
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getUserAgent(string $default = 'unknown'): string
    {
        return $this->getVariable('HTTP_USER_AGENT', $default);
    }

    /**
     * Country which is known to user
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getCountryName(string $default = 'unknown'): string
    {
        return $this->getVariable('GEOIP_COUNTRY_NAME', $default);
    }

    /**
     * Languages accepted by user
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getAcceptLanguage(string $default = 'unknown'): string
    {
        return $this->getVariable('HTTP_ACCEPT_LANGUAGE', $default);
    }

    /**
     * Content accepted by user
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    public function getAccept(string $default = '*/*'): string
    {
        return $this->getVariable('HTTP_ACCEPT', $default);
    }

    /**
     * @throws ModuleException
     * @return IEntry[]
     */
    protected function getIn(): array
    {
        if (empty($this->in)) {
            throw new ModuleException('No inputs set!');
        }
        return $this->in;
    }

    /**
     * @param string $key
     * @param string $default
     * @throws ModuleException
     * @return string
     */
    protected function getVariable(string $key, string $default = ''): string
    {
        $in = $this->getIn();
        if (isset($in[$key])) {
            return strval($in[$key]);
        }
        return $default;
    }
}

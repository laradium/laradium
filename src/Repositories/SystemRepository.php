<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Models\SystemLog;

/**
 * Class SystemRepository
 * @package Laradium\Laradium\Repositories
 */
class SystemRepository
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $config;

    /**
     * @var \Illuminate\Foundation\Application|SystemLog
     */
    private $model;

    /**
     * SystemRepository constructor.
     */
    public function __construct()
    {
        $this->config = config('laradium-system');
        $this->model = app(SystemLog::class);
    }

    /**
     * @return object
     */
    public function info()
    {
        $serverMemory = serverMemory();
        $networkStats = networkStats();

        $totalDiskSpace = disk_total_space('/');
        $freeDiskSpace = disk_free_space('/');

        return (object)[
            'disk'    => (object)[
                'percent' => round(($freeDiskSpace / $totalDiskSpace) * 100, 2),
                'total'   => formatBytes($totalDiskSpace),
                'free'    => formatBytes($totalDiskSpace - $freeDiskSpace)
            ],
            'ram'     => (object)[
                'percent' => round($serverMemory->percent, 2),
                'used'    => formatBytes($serverMemory->used),
                'total'   => formatBytes($serverMemory->total)
            ],
            'network' => (object)[
                'in'  => formatBytes($networkStats->in) . '/s',
                'out' => formatBytes($networkStats->out) . '/s'
            ],
            'cpu'     => systemLoad(systemCoreCount())
        ];
    }

    /**
     * Get users browser
     *
     * @return null|string
     */
    public function browser(): ?string
    {
        $agent = request()->server("HTTP_USER_AGENT");
        if (stripos($agent, 'Firefox') !== false) {
            $agent = 'Mozilla Firefox';
        } elseif (stripos($agent, 'Edge') !== false) {
            $agent = 'Microsoft Edge';
        } elseif (stripos($agent, 'MSIE') !== false || stripos($agent, 'Trident') !== false) {
            $agent = 'Internet Explorer';
        } elseif (stripos($agent, 'iPad') !== false) {
            $agent = 'iPad';
        } elseif (stripos($agent, 'Android') !== false) {
            $agent = 'Android';
        } elseif (stripos($agent, 'Chrome') !== false) {
            $agent = 'Google Chrome';
        } elseif (stripos($agent, 'Safari') !== false) {
            $agent = 'Safari';
        } elseif (stripos($agent, 'AIR') !== false) {
            $agent = 'Air';
        } elseif (stripos($agent, 'Fluid') !== false) {
            $agent = 'Fluid';
        } else {
            $agent = 'Unknown';
        }

        return $agent;
    }

    /**
     * Get users OS
     *
     * @return mixed|string
     */
    public function os()
    {
        $agent = request()->server('HTTP_USER_AGENT');

        $osPlatform = 'Unknown OS';

        $osArray = array(
            'windows nt 10.0' => 'Windows 10',
            'windows nt 6.3'  => 'Windows 8.1',
            'windows nt 6.2'  => 'Windows 8',
            'windows nt 6.1'  => 'Windows 7',
            'windows nt 6.0'  => 'Windows Vista',
            'windows nt 5.2'  => 'Windows Server 2003/XP x64',
            'windows nt 5.1'  => 'Windows XP',
            'windows xp'      => 'Windows XP',
            'windows nt 5.0'  => 'Windows 2000',
            'windows me'      => 'Windows ME',
            'win98'           => 'Windows 98',
            'win95'           => 'Windows 95',
            'win16'           => 'Windows 3.11',
            'macintosh'       => 'Mac OS X',
            'mac os x'        => 'Mac OS X',
            'mac_powerpc'     => 'Mac OS 9',
            'linux'           => 'Linux',
            'ubuntu'          => 'Ubuntu',
            'iphone'          => 'iPhone',
            'ipod'            => 'iPod',
            'ipad'            => 'iPad',
            'android'         => 'Android',
            'blackberry'      => 'BlackBerry',
            'webos'           => 'Mobile'
        );

        foreach ($osArray as $needle => $value) {
            if (stripos($agent, $needle)) {
                $osPlatform = $value;
            }
        }

        return $osPlatform;
    }

    /***
     * @return string
     */
    public function userIp(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $key) {
            if (array_key_exists($key, $_SERVER) !== true) {
                continue;
            }

            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                    continue;
                }

                return $ip;
            }
        }

        return request()->ip();
    }

    /**
     * @param $message
     * @param array $data
     * @return mixed
     */
    public function error($message, $data = [])
    {
        return $this->log([
            'message' => $message,
            'type'    => 'error',
            'data'    => $data
        ]);
    }

    /**
     * @param $message
     * @param array $data
     * @return mixed
     */
    public function warning($message, $data = [])
    {
        return $this->log([
            'message' => $message,
            'type'    => 'warning',
            'data'    => $data
        ]);
    }

    /**
     * @param $message
     * @param array $data
     * @return mixed
     */
    public function debug($message, $data = [])
    {
        return $this->log([
            'message' => $message,
            'type'    => 'debug',
            'data'    => $data
        ]);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function log($data)
    {
        $log = [];

        if (is_array($data)) {
            $log = $data;
        } else {
            $log['message'] = $data;
        }

        if (!isset($log['user_id'])) {
            $log['user_id'] = auth()->check() ? auth()->user()->id : 0;
        }

        if (!isset($log['type'])) {
            $log['type'] = 'log';
        }

        if (!isset($log['method'])) {
            $log['method'] = request()->getMethod();
        }

        if (!isset($log['url'])) {
            $log['url'] = request()->getUri();
        }

        if (!isset($log['ip'])) {
            $log['ip'] = $this->userIp();
        }

        if (!isset($log['browser'])) {
            $log['browser'] = $this->browser();
        }

        if (!isset($log['platform'])) {
            $log['platform'] = $this->os();
        }

        if (!isset($log['data'])) {
            $log['data'] = json_encode([]);
        } else {
            $log['data'] = json_encode($log['data']);
        }

        return $this->model->create($log);
    }
}

<?php
if (!function_exists('formatBytes')) {
    /**
     * @param $size
     * @return string
     */
    function formatBytes($size)
    {
        if ($size == 0) {
            return '0 B';
        }

        $base = log($size) / log(1024);
        $suffix = array('B', 'KB', 'MB', 'GB', 'TB');
        $f_base = floor($base);
        try {
            return round(1024 ** ($base - floor($base)), 1) . ' ' . $suffix[$f_base];
        } catch (\Exception $e) {
            return '0 B';
        }
    }
}

if (!function_exists('systemLoad')) {
    /**
     * @param int $coreCount
     * @param int $interval
     * @return float
     */
    function systemLoad($coreCount = 2, $interval = 1)
    {
        try {
            $rs = sys_getloadavg();
            $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
            $load = $rs[$interval];
            return round(($load * 100) / $coreCount, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('systemCoreCount')) {
    /**
     * @return int
     */
    function systemCoreCount()
    {
        try {
            $cmd = "uname";
            $OS = strtolower(trim(shell_exec($cmd)));
            switch ($OS) {
                case('linux'):
                    $cmd = 'cat /proc/cpuinfo | grep processor | wc -l';
                    break;
                case('freebsd'):
                    $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
                    break;
                default:
                    $cmd = false;
            }

            if ($cmd) {
                $cpuCoreNo = (int)trim(shell_exec($cmd));
            }

            return empty($cpuCoreNo) ? 1 : $cpuCoreNo;
        } catch (\Exception $e) {
            return 1;
        }
    }
}

if (!function_exists('serverMemory')) {
    function serverMemory()
    {
        try {
            $free = shell_exec('free -b');
            if (!$free) {
                return (object)[
                    'percent' => 0,
                    'used'    => 0,
                    'total'   => 0
                ];
            }
            $free = trim($free);
            $freeArr = explode("\n", $free);
            $mem = explode(' ', $freeArr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);

            array_splice($mem, 0, 1);

            $total = array_sum($mem) - $mem[0] - $mem[1] - $mem[2];
            $used = $mem[0] - $total;

            return (object)[
                'percent' => $used / $mem[0] * 100,
                'used'    => $used,
                'total'   => $mem[0]
            ];

        } catch (\Exception $e) {
            return (object)[
                'percent' => 0,
                'used'    => 0,
                'total'   => 0
            ];
        }
    }
}
if (!function_exists('networkStats')) {
    /**
     * @return object
     */
    function networkStats()
    {
        try {
            $networkStats = shell_exec('ifstat 1 1');
            $networkStats = trim($networkStats);
            $networkStats = explode("\n", $networkStats);
            $networkStats = explode(' ', $networkStats[2]);
            $networkStats = array_values(array_filter($networkStats));

            foreach ($networkStats as $key => $value) {
                $networkStats[$key] = round($value * 1024);
            }

            if (isset($networkStats[2], $networkStats[3])) {
                return (object)[
                    'in'  => $networkStats[2],
                    'out' => $networkStats[3],
                ];
            }

            return (object)[
                'in'  => $networkStats[0],
                'out' => $networkStats[1],
            ];
        } catch (\Exception $e) {
            return (object)[
                'in'  => 0.00,
                'out' => 0.00,
            ];
        }
    }
}

if (!function_exists('tailCustom')) {

    /**
     * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     * @author Torleif Berger, Lorenzo Stanco
     * @link http://stackoverflow.com/a/15025877/995958
     * @license http://creativecommons.org/licenses/by/3.0/
     *
     * @param $filepath
     * @param int $lines
     * @param bool $adaptive
     * @return bool|string
     */
    function tailCustom($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");

        if ($f === false) {
            return false;
        }

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) !== "\n") {
            --$lines;
        }

        // Start reading
        $output = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }

        // Close file and return
        fclose($f);

        return trim($output);
    }
}

#!/usr/bin/env php
<?php

// Nginx enabled sites config path
define('CONFIG_DIR', '/etc/nginx/sites-enabled');

if (!is_dir(CONFIG_DIR)) {
    echo 'Incorrect config directory path or insufficient privileges.' . PHP_EOL;
    exit;
}

$dh = @opendir(CONFIG_DIR);
if (!$dh) {
    echo 'Could not open config directory' . PHP_EOL;
    exit;
}

$confList = array();
while ($confFile = readdir($dh)) {
    if (in_array($confFile, array('.', '..'))) {
        continue;
    }
    $ext = pathinfo($confFile, PATHINFO_EXTENSION);
    if ("conf" !== $ext) {
        continue;
    }
    $filePath = CONFIG_DIR . "/" .$confFile;
    $confList[] = $filePath;
}

sort($confList);

/**
 * Colors:
 *   Black 0;30
 *   Blue 0;34
 *   Green 0;32
 *   Cyan 0;36
 *   Red 0;31
 *   Purple 0;35
 *   Brown 0;33
 *   Light Gray 0;37
 *   Dark Gray 1;30
 *   Light Blue 1;34
 *   Light Green 1;32
 *   Light Cyan 1;36
 *   Light Red 1;31
 *   Light Purple 1;35
 *   Yellow 1;33
 *   White 1;37
 **/
echo "\033[1;33mList of virtual hosts:\033[0m" . PHP_EOL;

foreach ($confList as $k => $v) {
    $output = "\"\033[1;30m{$v}\"" . PHP_EOL;
    $confContents = file_get_contents($v);

    $output .= "\t\033[1;37mServer name: \033[0m";
    // Server name
    try {
        $output .= "" . getServerName($confContents);
    } catch (\Exception $e) {
        $output .= "\t{$e->getMessage()}";
    }

    $output .= PHP_EOL . "\t\033[1;37mServer root path: \033[0m";
    // Root pwd
    try {
        $output .= "\t" . getRootPwd($confContents);
    } catch (\Exception $e) {
        $output .= "\t{$e->getMessage()}";
    }

    echo $output . PHP_EOL;
}

/**
 * @param $config
 *
 * @return string
 *
 * @throws Exception
 */
function getServerName($config)
{
    preg_match_all('/server_name\s+(?<s_name>[^;]+)/i', $config, $matches);

    $match = $matches['s_name'];
    $output = "- ";
    switch (count($match)) {
        case 0:
            throw new \Exception("\033[0;31mEmpty server name\033[0m");
        case 1:
            $output .= "\033[32m{$match[0]}\033[0m";
            break;
        case 2:
            $output .= "\033[0;32m{$match[1]}\033[0m (\033[0;33m{$match[0]}\033[0m)";
            break;
        case 2:
            $output .= "\033[0;32m{$match[1]}\033[0m (\033[0;33m{$match[0]}\033[0m)";
            break;
        // more than 2 server names
        default:
            $output .= "\033[0;32m{$match[0]}\033[0m (";
            for ($i = 1; $i < count($match); $i++) {
                $output .= "\033[0;33m{$match[$i]}";
                if (($i + 1) !== count($match)) {
                    $output .= ", ";
                } else {
                    $output .= "\033[0m)";
                }
            }
            break;
    }

    return $output;
}

/**
 * @param $config
 *
 * @return string
 *
 * @throws Exception
 */
function getRootPwd($config) {
    preg_match_all('/root\s+(?<s_root>[^;]+)/i', $config, $matches);

    $match = $matches['s_root'];
    if (0 == count($match)) {
        throw new \Exception("\033[0;31mEmpty root directive\033[0m");
    } elseif (1 < count($match)) {
        throw new \Exception("\033[0;31mMore than one root directive\033[0m");
    }
    $output = "- \033[0;32m{$match[0]}\033[0m";

    return $output;
}

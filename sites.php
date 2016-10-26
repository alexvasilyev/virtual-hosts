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
    $confContents = file_get_contents($v);

    // Server name
    preg_match_all('/server_name\s+(?<s_name>[^;]+)/i', $confContents, $matches);

    $match = $matches['s_name'];
    switch (count($match)) {
        case 0:
            echo "\033[0;31m empty server name\033[0m(\033[1;30m{$v})" . PHP_EOL;
        case 1:
            echo "\033[32m{$match[0]}\033[0m" . PHP_EOL;
            break;
        case 2:
            echo "\033[0;32m{$match[1]}\033[0m (\033[0;33m{$match[0]}\033[0m)" . PHP_EOL;
            break;
        case 2:
            echo "\033[0;32m{$match[1]}\033[0m (\033[0;33m{$match[0]}\033[0m)" . PHP_EOL;
            break;
        // more than 2 server names
        default:
            echo "\033[0;32m{$match[0]}\033[0m (";
            for ($i = 1; $i < count($match); $i++) {
                echo "\033[0;33m{$match[$i]}";
                if (($i + 1) !== count($match)) {
                    echo ", ";
                } else {
                    echo "\033[0m)" . PHP_EOL;
                }
            }

            break;
    }

//    var_dump($matches['s_name']);
}

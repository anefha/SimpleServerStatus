<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';
$cache_file = dirname(__FILE__) . '/cache.json';
$cache_time = 30;

function checkServerStatus($host, $port)
{
    $timeout = 2;
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

    if ($fp) {
        fclose($fp);
        return true;
    }
    return false;
}

function getServerStatuses($servers, $hide_ip_addresses)
{
    global $cache_file, $cache_time;

    $cache_dir = dirname($cache_file);
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0755, true);
    }

    // return cached data if still valid
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached_data = file_get_contents($cache_file);
        if ($cached_data !== false) {
            return json_decode($cached_data, true);
        }
    }

    // check
    $results = [];
    foreach ($servers as $server) {
        $status = checkServerStatus($server['host'], $server['port']);

        // handle ip
        if ($hide_ip_addresses) {
            $display_host = '';
        } else {
            $display_host = $server['host'];
        }

        $results[] = [
            'name' => $server['name'],
            'host' => $server['host'],
            'display_host' => $display_host,
            'port' => $server['port'],
            'status' => $status,
            'last_check' => date('Y-m-d H:i:s')
        ];
    }

    // update cache
    file_put_contents($cache_file, json_encode($results));
    return $results;
}

try {
    $serverData = getServerStatuses($servers, $hide_ip_addresses);
    echo json_encode($serverData);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'server status check failed: ' . $e->getMessage()]);
}
?>
<?php
/* --------------------------------------------------------------
 ec_proxy.php 2019-10-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

function getClientIp() {
    $remoteAddressKey = 'REMOTE_ADDR';
    $proxyHeader      = 'HTTP_X_FORWARDED_FOR';
    
    if (!array_key_exists($remoteAddressKey, $_SERVER)) {
        return null;
    }
    
    if (array_key_exists($proxyHeader, $_SERVER)) {
        $proxies = explode(',', $_SERVER[$proxyHeader]);
        
        $clientIp = $proxies[0];
        
        if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $clientIp;
        }
    }
    
    return $_SERVER[$remoteAddressKey];
}

$query = $_GET;

// parses the google path to extract the version query param
$gPath       = $query['prx'] ?? '';
$parsedGPath = parse_url($gPath);

// SECURITY FIX: Validate path to prevent SSRF attacks
// Only allow legitimate Google Analytics paths
$allowedPathPrefixes = ['/collect', '/r/collect', '/g/collect', '/j/collect', '/analytics.js', '/gtag/js'];
$path = $parsedGPath['path'] ?? '';

// Ensure path starts with / and doesn't contain directory traversal
$pathIsValid = false;
if (strpos($path, '/') === 0 && strpos($path, '..') === false) {
    foreach ($allowedPathPrefixes as $allowed) {
        if (strpos($path, $allowed) === 0) {
            $pathIsValid = true;
            break;
        }
    }
}

if (!$pathIsValid) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain');
    echo 'Invalid path';
    exit;
}

if (array_key_exists('query', $parsedGPath)) {
    $querySegments = explode('=', $parsedGPath['query']);
    if (count($querySegments) >= 2) {
        $query = array_merge([$querySegments[0] => $querySegments[1]], $query);
    }
}
unset($query['prx']);

// creates the final google analytics url
$gUrl = 'https://www.google-analytics.com' . $path;

// try to fetch the client ip
$clientIp = getClientIp();
if ($clientIp) {
    $query['uip'] = $clientIp;
}
if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
    $query['ua'] = $_SERVER['HTTP_USER_AGENT'];
}

// sends the analytics data to the google servers
$finalUrl  = $gUrl . '?' . http_build_query($query);
$gCurl     = curl_init($finalUrl);
curl_setopt($gCurl, CURLOPT_RETURNTRANSFER, true);
$gResponse = curl_exec($gCurl);
curl_close($gCurl);

header('Content-Type: image/gif');
echo $gResponse;


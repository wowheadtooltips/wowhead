<?php
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/includes/wowhead_cache.php');
$cache = new wowhead_cache();
$cache->clearCache();
$cache->close(); unset($cache);
?>
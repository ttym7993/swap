<?php
/**
 * PSS 请求入口
 */
require_once __DIR__ . '/../path.php';
if (is_readable(kern\root_dir . '/close.php')) {
    header('Content-Type: text/css');
    exit('');
}
require_once kern\kern_dir . '/kern.php';
kern\kernel::serve_pss_request();

<?php
/**
 * PHP 请求入口
 */
require_once __DIR__ . '/../path.php';
$close_file = kern\root_dir . '/close.php';
if (is_readable($close_file)) {
    require $close_file;
    exit();
}
require_once kern\kern_dir . '/kern.php';
kern\kernel::serve_php_request();

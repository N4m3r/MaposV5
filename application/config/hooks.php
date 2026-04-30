<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// compress output
$hook['display_override'][] = [
    'class' => '',
    'function' => 'compress',
    'filename' => 'compress.php',
    'filepath' => 'hooks',
];

$hook['pre_system'][] = [
    'class' => 'WhoopsHook',
    'function' => 'bootWhoops',
    'filename' => 'whoops.php',
    'filepath' => 'hooks',
    'params' => [],
];

// Security Headers Hook
$hook['post_controller_constructor'][] = [
    'class' => '',
    'function' => 'apply_security_headers',
    'filename' => 'security_headers.php',
    'filepath' => 'hooks',
];

// Email Queue Processor - Poor Man's Cron
$hook['post_controller'][] = [
    'class' => '',
    'function' => 'process_email_queue',
    'filename' => 'email_queue_processor.php',
    'filepath' => 'hooks',
];

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */

<?php
/**
 * Autoloader Helper
 * Registra autoloader para classes com namespace Libraries\*
 *
 * Uso: $this->load->helper('autoload'); no controller
 * Ou adicione ao autoload.php: $autoload['helper'] = array('autoload');
 */

if (!function_exists('register_libraries_autoloader')) {
    function register_libraries_autoloader()
    {
        static $registered = false;

        if ($registered) {
            return;
        }

        spl_autoload_register(function ($class) {
            $prefix = 'Libraries\\';
            $base_dir = APPPATH . 'libraries/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });

        $registered = true;
    }
}

// Registra automaticamente ao carregar o helper
register_libraries_autoloader();

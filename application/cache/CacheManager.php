<?php
/**
 * Cache Manager
 * Gerenciador de cache multi-camadas
 */

namespace Libraries\Cache;

class CacheManager
{
    private array $drivers = [];
    private string $defaultDriver = 'file';

    public function __construct(string $default = 'file')
    {
        $this->defaultDriver = $default;
    }

    /**
     * Obtém driver de cache
     */
    public function driver(string $name = 'default')
    {
        $driverName = $name === 'default' ? $this->defaultDriver : $name;

        if (!isset($this->drivers[$driverName])) {
            $this->drivers[$driverName] = $this->createDriver($driverName);
        }

        return $this->drivers[$driverName];
    }

    /**
     * Cria driver de cache
     */
    private function createDriver(string $name)
    {
        switch ($name) {
            case 'redis':
                return new RedisCache();
            case 'file':
                return new FileCache();
            case 'apcu':
                return new ApcuCache();
            default:
                return new FileCache();
        }
    }

    /**
     * Executa callback com cache
     */
    public function remember(string $key, callable $callback, int $ttl = 3600, string $driver = 'default')
    {
        $cache = $this->driver($driver);

        if ($cached = $cache->get($key)) {
            return $cached;
        }

        $value = $callback();
        $cache->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Força cache
     */
    public function put(string $key, $value, int $ttl = 3600, string $driver = 'default'): bool
    {
        return $this->driver($driver)->set($key, $value, $ttl);
    }

    /**
     * Obtém do cache
     */
    public function get(string $key, $default = null, string $driver = 'default')
    {
        $value = $this->driver($driver)->get($key);
        return $value !== false ? $value : $default;
    }

    /**
     * Remove do cache
     */
    public function forget(string $key, string $driver = 'default'): bool
    {
        return $this->driver($driver)->delete($key);
    }

    /**
     * Limpa cache
     */
    public function flush(string $driver = 'default'): bool
    {
        return $this->driver($driver)->flush();
    }
}

/**
 * File Cache
 */
class FileCache
{
    private string $cachePath;

    public function __construct()
    {
        $this->cachePath = APPPATH . 'cache/';
    }

    public function get(string $key)
    {
        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return false;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires'] < time()) {
            unlink($file);
            return false;
        }

        return $data['value'];
    }

    public function set(string $key, $value, int $ttl = 3600): bool
    {
        $file = $this->cachePath . $this->sanitizeKey($key) . '.cache';
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function delete(string $key): bool
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    public function flush(): bool
    {
        $files = glob($this->cachePath . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }

    private function getCacheFile(string $key): string
    {
        return $this->cachePath . $this->sanitizeKey($key) . '.cache';
    }

    private function sanitizeKey(string $key): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
    }
}

/**
 * Redis Cache (usa Predis - biblioteca PHP, não precisa de extensão)
 */
class RedisCache
{
    private $redis;
    private bool $available = false;

    public function __construct()
    {
        try {
            // Tenta usar Predis (biblioteca PHP)
            if (class_exists('\Predis\Client')) {
                $this->redis = new \Predis\Client([
                    'scheme' => 'tcp',
                    'host'   => config_item('redis_host') ?? '127.0.0.1',
                    'port'   => config_item('redis_port') ?? 6379,
                    'password' => config_item('redis_password') ?? null,
                ]);
                $this->redis->ping();
                $this->available = true;
            }
        } catch (\Exception $e) {
            // Redis não disponível, usará FileCache
            $this->available = false;
        }
    }

    public function get(string $key)
    {
        if (!$this->available) {
            return false;
        }
        try {
            $value = $this->redis->get($key);
            return $value ? unserialize($value) : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function set(string $key, $value, int $ttl = 3600): bool
    {
        if (!$this->available) {
            return false;
        }
        try {
            return (bool) $this->redis->setex($key, $ttl, serialize($value));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        if (!$this->available) {
            return false;
        }
        try {
            return (bool) $this->redis->del([$key]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function flush(): bool
    {
        if (!$this->available) {
            return false;
        }
        try {
            return (bool) $this->redis->flushdb();
        } catch (\Exception $e) {
            return false;
        }
    }
}

/**
 * APCu Cache
 */
class ApcuCache
{
    public function get(string $key)
    {
        if (!function_exists('apcu_fetch')) {
            return false;
        }
        return apcu_fetch($key);
    }

    public function set(string $key, $value, int $ttl = 3600): bool
    {
        if (!function_exists('apcu_store')) {
            return false;
        }
        return apcu_store($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        if (!function_exists('apcu_delete')) {
            return false;
        }
        return apcu_delete($key);
    }

    public function flush(): bool
    {
        if (!function_exists('apcu_clear_cache')) {
            return false;
        }
        return apcu_clear_cache();
    }
}

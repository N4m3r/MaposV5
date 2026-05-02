<?php
/**
 * Script para limpar caches PHP
 */
echo "Limpando caches...\n";

// Limpa opcache se existir
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPcache limpo\n";
}

// Limpa APCu se existir
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✓ APCu limpo\n";
}

// Limpa cache realpath
clearstatcache();
echo "✓ Stat cache limpo\n";

echo "\nCaches limpos com sucesso!\n";
echo "Tente acessar o sistema novamente.\n";

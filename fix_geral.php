<?php
// Script para corrigir o erro de sintaxe no geral.php

$file = 'application/views/obras/relatorios/geral.php';
$content = file_get_contents($file);

// Corrigir ?% para ?>
$content = str_replace('?%"', '?>%"', $content);

file_put_contents($file, $content);
echo "Correção aplicada!\n";

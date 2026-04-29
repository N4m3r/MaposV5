# Diagnostico - Pagina index.php/notificacoes/configuracoes

## Data: 2026-04-29
## Status: EM DIAGNOSTICO

---

## 1. Resumo Executivo

Apos analise completa do codigo, a pagina `index.php/notificacoes/configuracoes` **deve estar funcionando** pois:
- A rota esta configurada em `routes.php` (linha 104)
- O hook de bootstrap cria as tabelas automaticamente
- O menu do sistema aponta para a URL correta

Se a pagina NAO esta carregando, o problema e provavelmente **um erro fatal** que esta sendo suprimido.

---

## 2. URLs Corretas para Testar

### Com index.php (padrao):
```
https://seudominio.com/index.php/notificacoes/configuracoes
```

### Direto no controller (sem rota):
```
https://seudominio.com/index.php/notificacoesconfig/configuracoes
```

**IMPORTANTE**: A URL `index.php/notificacoes/configuracoes` usa a **rota** configurada em `routes.php`. Se essa rota estiver funcionando, redireciona para `notificacoesConfig/configuracoes`.

---

## 3. Script de Diagnostico Completo

Crie o arquivo `test_pagina.php` na raiz do projeto e acesse pelo navegador:

```php
<?php
// test_pagina.php - Coloque na raiz do projeto (mesmo nivel do index.php)

ob_start();

echo "<h1>Diagnostico: notificacoes/configuracoes</h1>";
echo "<hr>";

// ============================================
// 1. VERIFICAR ARQUIVOS EXISTENTES
// ============================================
$files = [
    'application/controllers/NotificacoesConfig.php',
    'application/models/Notificacoes_config_model.php',
    'application/Services/WhatsAppService.php',
    'application/views/notificacoes/configuracoes.php',
    'application/config/routes.php',
    'application/hooks/check_notificacoes_tables.php',
    'application/core/MY_Controller.php',
];

echo "<h2>1. Verificacao de Arquivos</h2>";
$allOk = true;
foreach ($files as $file) {
    $exists = file_exists($file);
    echo ($exists ? '✓' : '✗') . " $file<br>";
    if (!$exists) $allOk = false;
}

// ============================================
// 2. VERIFICAR SINTAXE DO CONTROLLER E SERVICE
// ============================================
echo "<h2>2. Sintaxe dos Arquivos PHP</h2>";

$phpFiles = [
    'application/controllers/NotificacoesConfig.php',
    'application/models/Notificacoes_config_model.php',
    'application/Services/WhatsAppService.php',
];

foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        exec("php -l $file 2>&1", $output, $return);
        echo "<strong>$file:</strong> ";
        if ($return === 0) {
            echo "<span style='color:green'>✓ OK</span><br>";
        } else {
            echo "<span style='color:red'>✗ ERRO: " . implode(' ', $output) . "</span><br>";
        }
    }
}

// ============================================
// 3. VERIFICAR ROTAS CONFIGURADAS
// ============================================
echo "<h2>3. Rotas Configuradas</h2>";
if (file_exists('application/config/routes.php')) {
    $routesContent = file_get_contents('application/config/routes.php');
    
    $rotasEsperadas = [
        'notificacoes/configuracoes',
        'notificacoes/obter-qr',
        'notificacoes/verificar-status',
        'notificacoes/desconectar',
        'notificacoes/testar-envio',
    ];
    
    foreach ($rotasEsperadas as $rota) {
        if (strpos($routesContent, "'$rota'") !== false || strpos($routesContent, '"' . $rota . '"') !== false) {
            echo "✓ Rota '$rota' configurada<br>";
        } else {
            echo "✗ Rota '$rota' NAO encontrada<br>";
        }
    }
}

// ============================================
// 4. VERIFICAR BANCO DE DADOS
// ============================================
echo "<h2>4. Conexao com Banco de Dados</h2>";
if (file_exists('application/config/database.php')) {
    require_once 'application/config/database.php';
    
    $dbConfig = $db['default'];
    $mysqli = @new mysqli($dbConfig['hostname'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
    
    if ($mysqli->connect_error) {
        echo "✗ Erro na conexao: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✓ Conectado ao banco: " . $dbConfig['database'] . "<br>";
        
        // Verificar tabelas
        $tabelas = ['notificacoes_config', 'notificacoes_templates', 'notificacoes_log'];
        foreach ($tabelas as $tabela) {
            $result = $mysqli->query("SHOW TABLES LIKE '$tabela'");
            if ($result && $result->num_rows > 0) {
                echo "✓ Tabela '$tabela' existe<br>";
                
                // Verificar colunas especificas
                if ($tabela === 'notificacoes_config') {
                    $res = $mysqli->query("SHOW COLUMNS FROM `notificacoes_config` LIKE 'evolution_instance_token'");
                    if ($res && $res->num_rows > 0) {
                        echo "  ✓ Coluna 'evolution_instance_token' existe<br>";
                    } else {
                        echo "  ✗ Coluna 'evolution_instance_token' NAO existe<br>";
                    }
                    
                    $res = $mysqli->query("SHOW COLUMNS FROM `notificacoes_config` LIKE 'evolution_version'");
                    if ($res && $res->num_rows > 0) {
                        echo "  ✓ Coluna 'evolution_version' existe<br>";
                    } else {
                        echo "  ✗ Coluna 'evolution_version' NAO existe<br>";
                    }
                }
            } else {
                echo "✗ Tabela '$tabela' NAO existe<br>";
            }
        }
        
        $mysqli->close();
    }
}

// ============================================
// 5. TENTAR CARREGAR O CONTROLLER
// ============================================
echo "<h2>5. Teste de Carregamento do Controller</h2>";
try {
    define('BASEPATH', true);
    
    if (file_exists('system/core/Controller.php')) {
        require_once 'system/core/Controller.php';
        echo "✓ Controller.php carregado<br>";
    }
    
    if (file_exists('application/core/MY_Controller.php')) {
        require_once 'application/core/MY_Controller.php';
        if (class_exists('MY_Controller')) {
            echo "✓ MY_Controller carregado<br>";
        }
    }
    
    if (file_exists('application/controllers/NotificacoesConfig.php')) {
        require_once 'application/controllers/NotificacoesConfig.php';
        if (class_exists('NotificacoesConfig')) {
            echo "✓ Classe NotificacoesConfig carregada com sucesso!<br>";
            
            // Verificar metodos
            $metodos = ['index', 'configuracoes', 'verificar_status', 'obter_qr', 'desconectar', 'testar_envio', 'diagnostico', 'testar_curl'];
            foreach ($metodos as $metodo) {
                if (method_exists('NotificacoesConfig', $metodo)) {
                    echo "  ✓ Metodo '$metodo' existe<br>";
                } else {
                    echo "  ✗ Metodo '$metodo' NAO existe<br>";
                }
            }
        } else {
            echo "✗ Classe NotificacoesConfig nao encontrada apos require<br>";
        }
    }
} catch (Exception $e) {
    echo "✗ Erro ao carregar: " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "✗ Erro fatal: " . $e->getMessage() . "<br>";
}

// ============================================
// 6. VERIFICAR PERMISSOES DE ARQUIVO
// ============================================
echo "<h2>6. Permissoes de Arquivo</h2>";
$controllerFile = 'application/controllers/NotificacoesConfig.php';
if (file_exists($controllerFile)) {
    $perms = fileperms($controllerFile);
    $permString = substr(sprintf('%o', $perms), -4);
    echo "Controller: permissoes $permString ";
    if (is_readable($controllerFile)) {
        echo "(legivel ✓)<br>";
    } else {
        echo "(NAO legivel ✗)<br>";
    }
}

// ============================================
// 7. ERROS DO PHP
// ============================================
echo "<h2>7. Erros do PHP</h2>";
$errorLog = ini_get('error_log');
echo "Arquivo de log: $errorLog<br>";

if (file_exists($errorLog) && is_readable($errorLog)) {
    $errors = file($errorLog);
    $recentErrors = array_slice($errors, -10);
    echo "Ultimos erros:<br><pre>";
    foreach ($recentErrors as $err) {
        echo htmlspecialchars($err);
    }
    echo "</pre>";
} else {
    echo "Nao foi possivel ler o log de erros.<br>";
}

// ============================================
// 8. CONFIGURACOES DO PHP
// ============================================
echo "<h2>8. Configuracoes do PHP</h2>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "error_reporting: " . ini_get('error_reporting') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";

// ============================================
// 9. TESTE DE REESCRITA DE URL (mod_rewrite)
// ============================================
echo "<h2>9. mod_rewrite</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "✓ mod_rewrite esta habilitado<br>";
    } else {
        echo "✗ mod_rewrite NAO esta habilitado<br>";
    }
} else {
    echo "? Nao foi possivel verificar mod_rewrite (pode ser CGI/FPM)<br>";
}

// ============================================
// 10. LINKS PARA TESTAR
// ============================================
echo "<h2>10. Links para Testar</h2>";
echo "<ul>";
echo "<li><a href='index.php/notificacoes/configuracoes' target='_blank'>index.php/notificacoes/configuracoes</a> (com rota)</li>";
echo "<li><a href='index.php/notificacoesconfig/configuracoes' target='_blank'>index.php/notificacoesconfig/configuracoes</a> (controller direto)</li>";
echo "<li><a href='index.php/notificacoesConfig/configuracoes' target='_blank'>index.php/notificacoesConfig/configuracoes</a> (case sensitive)</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>RESUMO</h2>";
if ($allOk) {
    echo "<p style='color:green'><strong>Todos os arquivos necessarios existem.</strong></p>";
    echo "<p>Se a pagina ainda nao carrega, o problema e provavelmente:</p>";
    echo "<ol>";
    echo "<li>Erro fatal no WhatsAppService.php (verifique a sintaxe)</li>";
    echo "<li>Erro no banco de dados (tabela nao criada corretamente)</li>";
    echo "<li>Erro de memoria ou tempo de execucao excedido</li>";
    echo "</ol>";
} else {
    echo "<p style='color:red'><strong>Alguns arquivos estao faltando!</strong></p>";
}

$content = ob_get_clean();
echo $content;

// Salvar resultado
file_put_contents('diagnostico_resultado.html', $content);
echo "<hr><p>Resultado salvo em: <strong>diagnostico_resultado.html</strong></p>";
```

---

## 4. Erros Comuns e Solucoes

### Erro: Pagina em branco (500 Internal Server Error)
**Causa**: Erro fatal no PHP sendo suprimido
**Solucao**: Ative o display_errors no php.ini ou .htaccess:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### Erro: "404 Page Not Found"
**Causa**: URL incorreta ou rota nao funcionando
**Solucao**: Use `index.php/notificacoesconfig/configuracoes` (sem barra, lowercase)

### Erro: "Unable to load the requested file"
**Causa**: View `notificacoes/configuracoes.php` nao encontrada
**Solucao**: Verifique se o arquivo existe em `application/views/notificacoes/configuracoes.php`

### Erro: "Table 'notificacoes_config' doesn't exist"
**Causa**: Hook nao criou a tabela
**Solucao**: Execute o SQL em `application/database/migrations/notificacoes_config.sql`

### Erro: "Unknown column 'evolution_instance_token'"
**Causa**: Coluna nao adicionada
**Solucao**: O hook deve adicionar automaticamente, mas se falhar:
```sql
ALTER TABLE `notificacoes_config` ADD COLUMN `evolution_instance_token` VARCHAR(255) DEFAULT NULL AFTER `evolution_instance`;
ALTER TABLE `notificacoes_config` ADD COLUMN `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2' AFTER `evolution_instance_token`;
```

---

## 5. Checklist de Verificacao

- [ ] Todos os arquivos listados na secao 1 existem
- [ ] Sintaxe PHP OK (sem erros no `php -l`)
- [ ] Rota `notificacoes/configuracoes` esta em `routes.php`
- [ ] Tabela `notificacoes_config` existe no banco
- [ ] Coluna `evolution_instance_token` existe
- [ ] Coluna `evolution_version` existe
- [ ] Permissoes dos arquivos estao corretas (644)
- [ ] O menu aponta para `notificacoes/configuracoes`

---

## 6. Resultado Esperado

Se tudo estiver correto, ao executar o script `test_pagina.php`, voce deve ver:
- ✓ Todos os arquivos existem
- ✓ Sintaxe OK
- ✓ Rotas configuradas
- ✓ Banco conectado
- ✓ Tabelas existem
- ✓ Controller carregado

Se algum item mostrar ✗, esse e o problema a ser corrigido.

---

## 7. Proximos Passos

1. Crie o arquivo `test_pagina.php` na raiz do projeto
2. Acesse pelo navegador: `https://seudominio.com/test_pagina.php`
3. Analise o resultado e corrija os itens com ✗
4. Se tudo estiver OK mas a pagina ainda nao carrega, ative o log de erros do PHP

---

*Documento gerado automaticamente durante sessao de debug.*

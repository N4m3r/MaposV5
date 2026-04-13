<?php

namespace Libraries\Email;

/**
 * Email Template Engine
 * Gerencia templates de email
 */
class TemplateEngine
{
    private $ci;
    private $templatePath;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->templatePath = APPPATH . 'views/emails/templates/';
    }

    /**
     * Renderiza um template
     */
    public function render(string $template, array $data = []): array
    {
        $templateFile = $this->templatePath . $template . '.php';

        if (!file_exists($templateFile)) {
            // Template padrão simples
            $html = $this->getDefaultTemplate($template, $data);
            $text = strip_tags($html);
            return ['html' => $html, 'text' => $text];
        }

        // Extrai variáveis para o template
        extract($data);

        // Renderiza HTML
        ob_start();
        include $templateFile;
        $html = ob_get_clean();

        // Processa tags {{variavel}} no formato novo
        $html = $this->processTemplateTags($html, $data);

        // Versão texto
        $text = strip_tags($html);

        return ['html' => $html, 'text' => $text];
    }

    /**
     * Processa tags no formato {{variavel}} substituindo pelos valores de $data
     */
    private function processTemplateTags(string $content, array $data): string
    {
        // Mapeamento de tags para valores
        $tagMappings = [
            // Cliente
            '{{cliente_nome}}' => $data['cliente_nome'] ?? $data['nome'] ?? 'Cliente',
            '{{cliente_email}}' => $data['cliente_email'] ?? $data['email'] ?? '',
            '{{cliente_telefone}}' => $data['cliente_telefone'] ?? '',
            '{{cliente_celular}}' => $data['cliente_celular'] ?? '',
            '{{cliente_endereco}}' => $data['cliente_endereco'] ?? '',
            '{{cliente_documento}}' => $data['cliente_documento'] ?? $data['documento'] ?? '',
            // OS
            '{{os_id}}' => $data['os_id'] ?? '',
            '{{os_titulo}}' => $data['os_titulo'] ?? '',
            '{{os_descricao}}' => $data['os_descricao'] ?? '',
            '{{os_status}}' => $data['os_status'] ?? '',
            '{{os_data_criacao}}' => $data['os_data_criacao'] ?? '',
            '{{os_data_vencimento}}' => $data['os_data_vencimento'] ?? '',
            '{{os_valor_total}}' => $data['os_valor_total'] ?? '',
            '{{os_link_visualizar}}' => $data['os_link_visualizar'] ?? '',
            // Venda
            '{{venda_id}}' => $data['venda_id'] ?? '',
            '{{venda_data}}' => $data['venda_data'] ?? '',
            '{{venda_valor_total}}' => $data['venda_valor_total'] ?? '',
            '{{venda_status}}' => $data['venda_status'] ?? '',
            '{{venda_link_visualizar}}' => $data['venda_link_visualizar'] ?? '',
            // Usuário/Sistema
            '{{usuario_nome}}' => $data['usuario_nome'] ?? $data['usuario'] ?? '',
            '{{usuario_email}}' => $data['usuario_email'] ?? '',
            '{{empresa_nome}}' => $data['empresa_nome'] ?? $this->ci->config->item('app_name') ?? 'MAPOS',
            '{{empresa_telefone}}' => $data['empresa_telefone'] ?? '',
            '{{empresa_email}}' => $data['empresa_email'] ?? '',
            '{{empresa_endereco}}' => $data['empresa_endereco'] ?? '',
            '{{data_atual}}' => $data['data_atual'] ?? date('d/m/Y'),
            '{{hora_atual}}' => $data['hora_atual'] ?? date('H:i'),
            '{{sistema_url}}' => $data['sistema_url'] ?? base_url(),
            '{{ano_atual}}' => $data['ano_atual'] ?? date('Y'),
            // Cobrança
            '{{cobranca_descricao}}' => $data['cobranca_descricao'] ?? '',
            '{{cobranca_valor}}' => $data['cobranca_valor'] ?? '',
            '{{cobranca_data_vencimento}}' => $data['cobranca_data_vencimento'] ?? '',
            '{{cobranca_dias_atraso}}' => $data['cobranca_dias_atraso'] ?? '0',
            '{{cobranca_link_pagamento}}' => $data['cobranca_link_pagamento'] ?? '',
            // Personalizadas
            '{{titulo}}' => $data['titulo'] ?? $data['subject'] ?? 'Notificação',
            '{{mensagem}}' => $data['mensagem'] ?? $data['content'] ?? '',
            '{{conteudo}}' => $data['conteudo'] ?? $data['content'] ?? '',
            '{{destinatario}}' => $data['destinatario'] ?? $data['nome'] ?? 'Cliente',
            '{{link}}' => $data['link'] ?? '',
        ];

        return str_replace(array_keys($tagMappings), array_values($tagMappings), $content);
    }

    /**
     * Lista templates disponíveis
     */
    public function listTemplates(): array
    {
        $templates = [];
        
        if (is_dir($this->templatePath)) {
            $files = glob($this->templatePath . '*.php');
            foreach ($files as $file) {
                $templates[] = basename($file, '.php');
            }
        }
        
        // Templates padrão sempre disponíveis
        $defaultTemplates = ['os_nova', 'os_atualizada', 'cobranca', 'boas_vindas'];
        foreach ($defaultTemplates as $t) {
            if (!in_array($t, $templates)) {
                $templates[] = $t;
            }
        }
        
        return $templates;
    }

    /**
     * Template padrão
     */
    private function getDefaultTemplate(string $template, array $data): string
    {
        $content = $data['content'] ?? $data['mensagem'] ?? '';
        $titulo = $data['titulo'] ?? $data['subject'] ?? 'Notificação';
        
        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$titulo}</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2>{$titulo}</h2>
                <div>{$content}</div>
                <hr>
                <p style='font-size: 12px; color: #666;'>
                    Este é um email automático. Por favor, não responda.
                </p>
            </div>
        </body>
        </html>";
    }

    /**
     * Preview de template
     */
    public function preview(string $template, array $data = []): string
    {
        $rendered = $this->render($template, $data);
        return $rendered['html'];
    }

    /**
     * Lê o conteúdo de um template
     */
    public function getTemplateContent(string $template): ?string
    {
        $templateFile = $this->templatePath . $template . '.php';

        if (!file_exists($templateFile)) {
            return null;
        }

        return file_get_contents($templateFile);
    }

    /**
     * Salva o conteúdo de um template
     */
    public function saveTemplate(string $template, string $content): bool
    {
        $templateFile = $this->templatePath . $template . '.php';

        // Cria diretório se não existir
        if (!is_dir($this->templatePath)) {
            mkdir($this->templatePath, 0755, true);
        }

        // Validação básica de segurança - verifica por código PHP perigoso
        $dangerousPatterns = [
            '/<\?php\s*(eval|exec|system|shell_exec|passthru|proc_open|popen|curl_exec|curl_multi_exec|parse_ini_file|show_source|phpinfo)\s*\(/i',
            '/<(script|iframe|object|embed|form)[^>]*>/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \Exception('Código potencialmente perigoso detectado no template.');
            }
        }

        return file_put_contents($templateFile, $content) !== false;
    }

    /**
     * Retorna as tags disponíveis do sistema para templates
     */
    public function getAvailableTags(): array
    {
        return [
            // Tags de Cliente
            'cliente' => [
                ['tag' => '{{cliente_nome}}', 'descricao' => 'Nome completo do cliente'],
                ['tag' => '{{cliente_email}}', 'descricao' => 'Email do cliente'],
                ['tag' => '{{cliente_telefone}}', 'descricao' => 'Telefone do cliente'],
                ['tag' => '{{cliente_celular}}', 'descricao' => 'Celular do cliente'],
                ['tag' => '{{cliente_endereco}}', 'descricao' => 'Endereço completo do cliente'],
                ['tag' => '{{cliente_documento}}', 'descricao' => 'CPF/CNPJ do cliente'],
            ],
            // Tags de OS (Ordem de Serviço)
            'os' => [
                ['tag' => '{{os_id}}', 'descricao' => 'Número da OS'],
                ['tag' => '{{os_titulo}}', 'descricao' => 'Título da OS'],
                ['tag' => '{{os_descricao}}', 'descricao' => 'Descrição da OS'],
                ['tag' => '{{os_status}}', 'descricao' => 'Status da OS'],
                ['tag' => '{{os_data_criacao}}', 'descricao' => 'Data de criação da OS'],
                ['tag' => '{{os_data_vencimento}}', 'descricao' => 'Data de vencimento da OS'],
                ['tag' => '{{os_valor_total}}', 'descricao' => 'Valor total da OS'],
                ['tag' => '{{os_link_visualizar}}', 'descricao' => 'Link para visualizar a OS'],
            ],
            // Tags de Venda
            'venda' => [
                ['tag' => '{{venda_id}}', 'descricao' => 'ID da venda'],
                ['tag' => '{{venda_data}}', 'descricao' => 'Data da venda'],
                ['tag' => '{{venda_valor_total}}', 'descricao' => 'Valor total da venda'],
                ['tag' => '{{venda_status}}', 'descricao' => 'Status da venda'],
                ['tag' => '{{venda_link_visualizar}}', 'descricao' => 'Link para visualizar a venda'],
            ],
            // Tags de Usuário/Sistema
            'sistema' => [
                ['tag' => '{{usuario_nome}}', 'descricao' => 'Nome do usuário logado'],
                ['tag' => '{{usuario_email}}', 'descricao' => 'Email do usuário logado'],
                ['tag' => '{{empresa_nome}}', 'descricao' => 'Nome da empresa'],
                ['tag' => '{{empresa_telefone}}', 'descricao' => 'Telefone da empresa'],
                ['tag' => '{{empresa_email}}', 'descricao' => 'Email da empresa'],
                ['tag' => '{{empresa_endereco}}', 'descricao' => 'Endereço da empresa'],
                ['tag' => '{{data_atual}}', 'descricao' => 'Data atual (dd/mm/YYYY)'],
                ['tag' => '{{hora_atual}}', 'descricao' => 'Hora atual (HH:mm)'],
                ['tag' => '{{sistema_url}}', 'descricao' => 'URL do sistema'],
            ],
            // Tags de Cobrança
            'cobranca' => [
                ['tag' => '{{cobranca_descricao}}', 'descricao' => 'Descrição da cobrança'],
                ['tag' => '{{cobranca_valor}}', 'descricao' => 'Valor da cobrança'],
                ['tag' => '{{cobranca_data_vencimento}}', 'descricao' => 'Data de vencimento'],
                ['tag' => '{{cobranca_dias_atraso}}', 'descricao' => 'Dias de atraso'],
                ['tag' => '{{cobranca_link_pagamento}}', 'descricao' => 'Link para pagamento'],
            ],
            // Tags Personalizadas
            'personalizado' => [
                ['tag' => '{{titulo}}', 'descricao' => 'Título do email'],
                ['tag' => '{{mensagem}}', 'descricao' => 'Mensagem principal'],
                ['tag' => '{{conteudo}}', 'descricao' => 'Conteúdo personalizado'],
                ['tag' => '{{destinatario}}', 'descricao' => 'Nome do destinatário'],
                ['tag' => '{{link}}', 'descricao' => 'Link genérico'],
                ['tag' => '{{ano_atual}}', 'descricao' => 'Ano atual (YYYY)'],
            ],
        ];
    }

    /**
     * Cria um novo template se não existir
     */
    public function createTemplateIfNotExists(string $template): bool
    {
        $templateFile = $this->templatePath . $template . '.php';

        if (file_exists($templateFile)) {
            return true;
        }

        $defaultContent = $this->getDefaultTemplateContent($template);
        return $this->saveTemplate($template, $defaultContent);
    }

    /**
     * Retorna conteúdo padrão para um template
     */
    private function getDefaultTemplateContent(string $template): string
    {
        $templates = [
            'os_nova' => '<?php
/**
 * Template: OS Nova
 * Disparado quando uma nova OS é criada
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{titulo}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Olá {{cliente_nome}},</h2>
        <p>Uma nova Ordem de Serviço foi criada para você:</p>
        <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin: 15px 0;">
            <strong>OS #{{os_id}}</strong><br>
            <strong>Título:</strong> {{os_titulo}}<br>
            <strong>Status:</strong> {{os_status}}<br>
            <strong>Data:</strong> {{os_data_criacao}}
        </div>
        <p>{{os_descricao}}</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{os_link_visualizar}}" style="background: #3498db; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Visualizar OS</a>
        </p>
        <hr>
        <p style="font-size: 12px; color: #666;">
            {{empresa_nome}}<br>
            Tel: {{empresa_telefone}}<br>
            Este é um email automático. Por favor, não responda.
        </p>
    </div>
</body>
</html>',
            'cobranca' => '<?php
/**
 * Template: Cobrança
 * Disparado para cobranças
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{titulo}}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Olá {{cliente_nome}},</h2>
        <p>{{mensagem}}</p>
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0;">
            <strong>{{cobranca_descricao}}</strong><br>
            <strong>Valor:</strong> R$ {{cobranca_valor}}<br>
            <strong>Vencimento:</strong> {{cobranca_data_vencimento}}
        </div>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{cobranca_link_pagamento}}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Pagar Agora</a>
        </p>
        <hr>
        <p style="font-size: 12px; color: #666;">
            {{empresa_nome}} - {{ano_atual}}
        </p>
    </div>
</body>
</html>',
            'boas_vindas' => '<?php
/**
 * Template: Boas Vindas
 * Disparado para novos clientes
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo!</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Bem-vindo, {{cliente_nome}}!</h2>
        <p>Obrigado por se cadastrar em nosso sistema.</p>
        <p>Estamos à disposição para atendê-lo.</p>
        <div style="background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <strong>Nossos contatos:</strong><br>
            Email: {{empresa_email}}<br>
            Telefone: {{empresa_telefone}}
        </div>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{sistema_url}}" style="background: #3498db; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">Acessar Sistema</a>
        </p>
        <hr>
        <p style="font-size: 12px; color: #666;">
            {{empresa_nome}} - {{ano_atual}}
        </p>
    </div>
</body>
</html>',
        ];

        return $templates[$template] ?? $this->getDefaultTemplate($template, ['titulo' => '{{titulo}}', 'content' => '{{conteudo}}']);
    }
}

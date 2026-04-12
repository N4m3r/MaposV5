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
        
        // Versão texto
        $text = strip_tags($html);
        
        return ['html' => $html, 'text' => $text];
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
}

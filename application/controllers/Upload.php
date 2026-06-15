<?php
/**
 * Controller de upload de arquivos.
 *
 * Endpoints:
 *   POST /api/upload            multipart/form-data
 *     - files[]      File[]     (1+ arquivos)
 *     - folder?      string     subpasta em assets/uploads/ (default: 'misc')
 *     - entity?      string     ('os', 'cliente', etc) - prefixo no nome
 *     - entity_id?   int|string id do registro
 *     res: { success, message?, files: [{name, originalName, size, type, url, thumb?}] }
 *
 *   POST /api/upload_delete    application/json
 *     - url          string     URL retornada no upload
 *     res: { success, message? }
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Upload extends MY_Controller
{
    /** Extensoes bloqueadas (RCE) */
    private const BLOCKED_EXT = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'cgi', 'pl', 'py', 'sh', 'bash', 'ksh',
        'htaccess', 'htpasswd', 'ini', 'env', 'sql',
    ];

    /** Tipos MIME permitidos (whitelist conservadora) */
    private const ALLOWED_MIME = [
        // Imagens
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        // Documentos
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'text/csv', 'text/html',
        // Compactados
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
    ];

    public function __construct()
    {
        parent::__construct();
        // Exige login
        if (!$this->session->userdata('logged_in')) {
            $this->json(['success' => false, 'message' => 'Autenticação requerida'], 401);
            return;
        }
    }

    /**
     * POST /api/upload
     */
    public function index()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método não permitido'], 405);
            return;
        }

        $folder = $this->input->post('folder') ?: 'misc';
        $entity = $this->input->post('entity') ?: 'misc';
        $entityId = $this->input->post('entity_id');

        // Sanitiza folder (nao permite .. nem / absoluto)
        $folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', (string) $folder);
        $folder = trim((string) $folder, '/');
        if (strpos($folder, '..') !== false) {
            $this->json(['success' => false, 'message' => 'Folder inválido'], 400);
            return;
        }

        $files = $_FILES['files'] ?? null;
        if (!$files || empty($files['name'][0])) {
            $this->json(['success' => false, 'message' => 'Nenhum arquivo enviado'], 400);
            return;
        }

        $uploadsBase = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads';
        $destDir = $uploadsBase . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        $results = [];
        $errors = [];
        $count = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $count; $i++) {
            $origName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $tmpPath  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $size     = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $error    = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            $type     = is_array($files['type']) ? $files['type'][$i] : $files['type'];

            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "$origName: erro upload code $error";
                continue;
            }
            if (!is_uploaded_file($tmpPath)) {
                $errors[] = "$origName: arquivo temporário inválido";
                continue;
            }
            if ($size <= 0 || $size > 50 * 1024 * 1024) { // 50MB max
                $errors[] = "$origName: tamanho inválido (máx 50MB)";
                continue;
            }

            // Valida extensão
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (in_array($ext, self::BLOCKED_EXT, true)) {
                $errors[] = "$origName: extensão não permitida (.{$ext})";
                continue;
            }

            // Valida MIME real
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            if (!in_array($realMime, self::ALLOWED_MIME, true)) {
                $errors[] = "$origName: tipo de arquivo não permitido ($realMime)";
                continue;
            }

            // Gera nome único
            $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $safeBase = substr($safeBase, 0, 50) ?: 'file';
            $prefix = $entity . ($entityId ? '_' . $entityId : '') . '_';
            $newName = uniqid($prefix, true) . '_' . $safeBase . '.' . $ext;
            $dest = $destDir . DIRECTORY_SEPARATOR . $newName;

            if (!move_uploaded_file($tmpPath, $dest)) {
                $errors[] = "$origName: falha ao salvar";
                continue;
            }
            @chmod($dest, 0644);

            $url = base_url('assets/uploads/' . $folder . '/' . $newName);
            $thumb = null;
            if (strpos($realMime, 'image/') === 0) {
                $thumb = $url; // imagem já é o próprio thumb
            }

            $results[] = [
                'name' => $newName,
                'originalName' => $origName,
                'size' => (int) $size,
                'type' => $realMime,
                'url' => $url,
                'thumb' => $thumb,
            ];
        }

        log_info(sprintf(
            'Upload: %d sucesso, %d erros, folder=%s, user=%s',
            count($results), count($errors), $folder, $this->session->userdata('email')
        ));

        $this->json([
            'success' => count($results) > 0,
            'files' => $results,
            'errors' => $errors,
            'message' => count($results) > 0
                ? count($results) . ' arquivo(s) enviado(s)'
                : 'Nenhum arquivo enviado com sucesso',
        ]);
    }

    /**
     * POST /api/upload_delete
     */
    public function delete()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método não permitido'], 405);
            return;
        }

        $url = $this->input->post('url') ?: ($this->input->body()['url'] ?? null);
        if (!$url) {
            $raw = file_get_contents('php://input');
            $body = is_string($raw) ? json_decode($raw, true) : null;
            $url = is_array($body) ? ($body['url'] ?? null) : null;
        }
        if (!$url) {
            $this->json(['success' => false, 'message' => 'URL não informada'], 400);
            return;
        }

        // Converte URL -> path local
        $base = base_url('assets/uploads/');
        if (strpos($url, $base) !== 0) {
            $this->json(['success' => false, 'message' => 'URL inválida'], 400);
            return;
        }
        $rel = substr($url, strlen($base));
        $rel = str_replace('..', '', $rel);
        $rel = preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', $rel);
        $abs = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);

        if (is_file($abs)) {
            @unlink($abs);
        }
        $this->json(['success' => true]);
    }

    private function json(array $data, int $code = 200): void
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}

<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Sistema de Backup e Restauração - Mapos OS
 *
 * Permite realizar backup do banco de dados e restaurar
 * a partir de arquivos SQL das versões anteriores.
 *
 * ATENÇÃO: Apenas usuários com permissão de administrador
 */
class Backup extends CI_Controller
{
    private $backup_dir;
    private $max_file_size;
    private $allowed_extensions;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mapos_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('file');
        $this->load->database();

        // Verificar autenticação
        if (!$this->session->userdata('logado')) {
            redirect('login');
        }

        // Verificar permissão de administrador
        $permissao = $this->session->userdata('permissao');
        if (!$this->permission->checkPermission($permissao, 'cBackup')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esta área.');
            redirect('mapos');
        }

        // Configurações
        $this->backup_dir = FCPATH . 'backups/';
        $this->max_file_size = 100 * 1024 * 1024; // 100MB
        $this->allowed_extensions = ['sql', 'gz', 'zip'];

        // Criar diretório se não existir
        if (!is_dir($this->backup_dir)) {
            mkdir($this->backup_dir, 0755, true);
        }
    }

    /**
     * Dashboard de backup
     */
    public function index()
    {
        $data['backups'] = $this->listarBackups();
        $data['database_info'] = $this->obterInfoDatabase();
        $data['ultimo_backup'] = $this->obterUltimoBackup();

        $this->load->view('backup/dashboard', $data);
    }

    /**
     * Realizar backup do banco de dados
     */
    public function realizar_backup()
    {
        try {
            // Gerar nome do arquivo
            $data_hora = date('Y-m-d_H-i-s');
            $nome_arquivo = 'backup_mapos_' . $data_hora . '.sql';
            $caminho_arquivo = $this->backup_dir . $nome_arquivo;

            // Obter configurações do banco
            $db_config = $this->db->database . ' -h ' . $this->db->hostname . ' -u ' . $this->db->username;

            // Comando mysqldump
            $command = sprintf(
                'mysqldump --opt -h%s -u%s %s > %s 2>&1',
                escapeshellarg($this->db->hostname),
                escapeshellarg($this->db->username),
                escapeshellarg($this->db->database),
                escapeshellarg($caminho_arquivo)
            );

            // Adicionar senha se existir
            if (!empty($this->db->password)) {
                $command = sprintf(
                    'mysqldump --opt -h%s -u%s -p%s %s > %s 2>&1',
                    escapeshellarg($this->db->hostname),
                    escapeshellarg($this->db->username),
                    escapeshellarg($this->db->password),
                    escapeshellarg($this->db->database),
                    escapeshellarg($caminho_arquivo)
                );
            }

            // Executar backup via exec (se disponível)
            if (function_exists('exec')) {
                exec($command, $output, $return_var);

                if ($return_var !== 0 || !file_exists($caminho_arquivo) || filesize($caminho_arquivo) === 0) {
                    // Fallback: backup via PHP
                    $this->realizarBackupPHP($caminho_arquivo);
                }
            } else {
                // Backup via PHP
                $this->realizarBackupPHP($caminho_arquivo);
            }

            // Compactar o arquivo
            $arquivo_compactado = $this->compactarArquivo($caminho_arquivo);

            // Registrar log
            $this->registrarLog('BACKUP', 'Backup realizado com sucesso: ' . basename($arquivo_compactado));

            // Retornar JSON se for AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Backup realizado com sucesso!',
                    'arquivo' => basename($arquivo_compactado),
                    'tamanho' => $this->formatarTamanho(filesize($arquivo_compactado))
                ]);
                return;
            }

            $this->session->set_flashdata('success', 'Backup realizado com sucesso! Arquivo: ' . basename($arquivo_compactado));

        } catch (Exception $e) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                return;
            }
            $this->session->set_flashdata('error', 'Erro ao realizar backup: ' . $e->getMessage());
        }

        redirect('backup');
    }

    /**
     * Realizar backup via PHP (fallback)
     */
    private function realizarBackupPHP($arquivo_saida)
    {
        $handle = fopen($arquivo_saida, 'w');

        if (!$handle) {
            throw new Exception('Não foi possível criar o arquivo de backup.');
        }

        // Header do arquivo
        fwrite($handle, "-- Mapos OS Backup\n");
        fwrite($handle, "-- Data: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Versão: 5.x\n");
        fwrite($handle, "-- Database: {$this->db->database}\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        // Obter todas as tabelas
        $tables = $this->db->list_tables();

        foreach ($tables as $table) {
            // Estrutura da tabela
            $create = $this->db->query("SHOW CREATE TABLE `{$table}`")->row_array();
            fwrite($handle, "\n-- ----------------------------\n");
            fwrite($handle, "-- Table structure for `{$table}`\n");
            fwrite($handle, "-- ----------------------------\n\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $create['Create Table'] . ";\n\n");

            // Dados da tabela
            $result = $this->db->query("SELECT * FROM `{$table}`");

            if ($result->num_rows() > 0) {
                fwrite($handle, "-- ----------------------------\n");
                fwrite($handle, "-- Records of `{$table}`\n");
                fwrite($handle, "-- ----------------------------\n\n");

                // Obter nomes das colunas
                $fields = $result->field_data();
                $field_names = array_map(function($f) { return $f->name; }, $fields);

                // Inserir em batches
                $batch_size = 100;
                $rows = $result->result_array();
                $chunks = array_chunk($rows, $batch_size);

                foreach ($chunks as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $row_values = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $row_values[] = 'NULL';
                            } else {
                                $row_values[] = $this->db->escape($value);
                            }
                        }
                        $values[] = '(' . implode(',', $row_values) . ')';
                    }

                    $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $field_names) . "`) VALUES\n";
                    $sql .= implode(",\n", $values) . ";\n";
                    fwrite($handle, $sql);
                }
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $arquivo_saida;
    }

    /**
     * Página de restauração
     */
    public function restaurar()
    {
        $this->form_validation->set_rules('confirmacao', 'Confirmação', 'required');

        if ($this->form_validation->run() === false) {
            $data['backups_disponiveis'] = $this->listarBackups();
            $this->load->view('backup/restaurar', $data);
        } else {
            // Processo de restauração via upload
            $this->processarRestauracao();
        }
    }

    /**
     * Processar upload e restauração
     */
    public function processar_restauracao()
    {
        // Verificar se há arquivo
        if (!isset($_FILES['arquivo_sql']) || $_FILES['arquivo_sql']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'Erro no upload do arquivo. Código: ' . ($_FILES['arquivo_sql']['error'] ?? 'N/A'));
            redirect('backup/restaurar');
        }

        $arquivo = $_FILES['arquivo_sql'];

        // Validar extensão
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extensao, $this->allowed_extensions)) {
            $this->session->set_flashdata('error', 'Extensão não permitida. Use: .sql, .gz ou .zip');
            redirect('backup/restaurar');
        }

        // Validar tamanho
        if ($arquivo['size'] > $this->max_file_size) {
            $this->session->set_flashdata('error', 'Arquivo muito grande. Tamanho máximo: 100MB');
            redirect('backup/restaurar');
        }

        // Mover arquivo temporário
        $nome_temp = 'temp_restore_' . time() . '_' . uniqid() . '.sql';
        $caminho_temp = $this->backup_dir . $nome_temp;

        if (!move_uploaded_file($arquivo['tmp_name'], $caminho_temp)) {
            $this->session->set_flashdata('error', 'Erro ao mover arquivo temporário.');
            redirect('backup/restaurar');
        }

        // Descompactar se necessário
        $arquivo_sql = $this->descompactarSeNecessario($caminho_temp, $extensao);

        if (!$arquivo_sql) {
            $this->session->set_flashdata('error', 'Erro ao descompactar arquivo.');
            unlink($caminho_temp);
            redirect('backup/restaurar');
        }

        // Fazer backup de segurança antes de restaurar
        $backup_seguranca = $this->backup_dir . 'auto_backup_pre_restore_' . date('Y-m-d_H-i-s') . '.sql';
        try {
            $this->realizarBackupPHP($backup_seguranca);
        } catch (Exception $e) {
            $this->session->set_flashdata('warning', 'Não foi possível criar backup de segurança automático.');
        }

        // Executar restauração
        $resultado = $this->executarRestauracao($arquivo_sql);

        // Limpar arquivos temporários
        if (file_exists($caminho_temp)) {
            unlink($caminho_temp);
        }
        if ($arquivo_sql !== $caminho_temp && file_exists($arquivo_sql)) {
            unlink($arquivo_sql);
        }

        if ($resultado['success']) {
            $this->registrarLog('RESTORE', 'Restauração realizada com sucesso. Arquivo: ' . $arquivo['name']);
            $this->session->set_flashdata('success', 'Restauração concluída com sucesso! ' . $resultado['mensagem']);
        } else {
            $this->registrarLog('RESTORE', 'Erro na restauração: ' . $resultado['erro']);
            $this->session->set_flashdata('error', 'Erro na restauração: ' . $resultado['erro']);
        }

        redirect('backup');
    }

    /**
     * Executar restauração do SQL
     */
    private function executarRestauracao($arquivo_sql)
    {
        $resultado = ['success' => false, 'mensagem' => '', 'erro' => ''];

        try {
            // Ler arquivo SQL
            $conteudo = file_get_contents($arquivo_sql);

            if ($conteudo === false) {
                throw new Exception('Não foi possível ler o arquivo SQL.');
            }

            // Dividir em statements
            $queries = $this->dividirQueriesSQL($conteudo);

            $total = count($queries);
            $executadas = 0;
            $erros = [];

            // Desabilitar foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Executar cada query
            foreach ($queries as $index => $query) {
                $query = trim($query);
                if (empty($query) || strpos($query, '--') === 0) {
                    continue;
                }

                try {
                    $this->db->query($query);
                    $executadas++;
                } catch (Exception $e) {
                    // Ignora erros de DROP IF EXISTS
                    if (strpos($query, 'DROP TABLE IF EXISTS') === false) {
                        $erros[] = "Query " . ($index + 1) . ": " . $e->getMessage();
                    }
                }

                // Liberar memória a cada 100 queries
                if ($index % 100 === 0) {
                    gc_collect_cycles();
                }
            }

            // Reabilitar foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            $resultado['success'] = true;
            $resultado['mensagem'] = "Total de queries: {$total}, Executadas com sucesso: {$executadas}";

            if (!empty($erros)) {
                $resultado['mensagem'] .= ", Erros: " . count($erros);
                log_message('warning', 'Erros na restauração: ' . implode("\n", $erros));
            }

        } catch (Exception $e) {
            $resultado['erro'] = $e->getMessage();
        }

        return $resultado;
    }

    /**
     * Dividir SQL em queries individuais
     */
    private function dividirQueriesSQL($sql)
    {
        $queries = [];
        $current_query = '';
        $in_string = false;
        $string_char = null;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $current_query .= $char;

            // Verificar strings
            if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $sql[$i - 1] !== '\\')) {
                if (!$in_string) {
                    $in_string = true;
                    $string_char = $char;
                } elseif ($char === $string_char) {
                    $in_string = false;
                    $string_char = null;
                }
            }

            // Fim de query
            if ($char === ';' && !$in_string) {
                $queries[] = trim($current_query);
                $current_query = '';
            }
        }

        // Adicionar última query se não terminar com ;
        if (!empty(trim($current_query))) {
            $queries[] = trim($current_query);
        }

        return $queries;
    }

    /**
     * Descompactar arquivo se necessário
     */
    private function descompactarSeNecessario($arquivo, $extensao)
    {
        switch ($extensao) {
            case 'gz':
                $saida = str_replace('.gz', '', $arquivo);
                $this->descompactarGzip($arquivo, $saida);
                return $saida;

            case 'zip':
                return $this->extrairZip($arquivo);

            case 'sql':
            default:
                return $arquivo;
        }
    }

    /**
     * Descompactar arquivo GZIP
     */
    private function descompactarGzip($origem, $destino)
    {
        $gz = gzopen($origem, 'rb');
        $out = fopen($destino, 'wb');

        while (!gzeof($gz)) {
            fwrite($out, gzread($gz, 4096));
        }

        gzclose($gz);
        fclose($out);

        return true;
    }

    /**
     * Extrair arquivo ZIP
     */
    private function extrairZip($arquivo)
    {
        $zip = new ZipArchive();
        if ($zip->open($arquivo) === true) {
            $temp_dir = $this->backup_dir . 'temp_' . uniqid() . '/';
            mkdir($temp_dir, 0755, true);
            $zip->extractTo($temp_dir);
            $zip->close();

            // Procurar arquivo SQL
            $files = glob($temp_dir . '*.sql');
            if (!empty($files)) {
                $sql_file = $temp_dir . 'extracted_' . basename($files[0]);
                copy($files[0], $sql_file);
                // Limpar diretório temporário
                $this->recursiveDelete($temp_dir);
                return $sql_file;
            }
        }
        return false;
    }

    /**
     * Compactar arquivo
     */
    private function compactarArquivo($arquivo)
    {
        $saida = $arquivo . '.gz';

        $fp = fopen($arquivo, 'rb');
        $gz = gzopen($saida, 'wb9');

        while (!feof($fp)) {
            gzwrite($gz, fread($fp, 4096));
        }

        fclose($fp);
        gzclose($gz);

        // Remover arquivo original
        unlink($arquivo);

        return $saida;
    }

    /**
     * Listar backups disponíveis
     */
    private function listarBackups()
    {
        $backups = [];

        if (is_dir($this->backup_dir)) {
            $files = glob($this->backup_dir . 'backup_mapos_*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    $backups[] = [
                        'nome' => basename($file),
                        'tamanho' => $this->formatarTamanho(filesize($file)),
                        'data' => date('Y-m-d H:i:s', filemtime($file)),
                        'tamanho_bytes' => filesize($file)
                    ];
                }
            }

            // Ordenar por data (mais recente primeiro)
            usort($backups, function($a, $b) {
                return strcmp($b['data'], $a['data']);
            });
        }

        return $backups;
    }

    /**
     * Download de backup
     */
    public function download($arquivo)
    {
        // Validar nome do arquivo
        $arquivo = basename($arquivo);
        $caminho = $this->backup_dir . $arquivo;

        if (!file_exists($caminho)) {
            $this->session->set_flashdata('error', 'Arquivo não encontrado.');
            redirect('backup');
        }

        // Headers para download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $arquivo . '"');
        header('Content-Length: ' . filesize($caminho));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($caminho);
        exit;
    }

    /**
     * Excluir backup
     */
    public function excluir($arquivo)
    {
        $arquivo = basename($arquivo);
        $caminho = $this->backup_dir . $arquivo;

        if (file_exists($caminho) && unlink($caminho)) {
            $this->registrarLog('DELETE', 'Backup excluído: ' . $arquivo);
            $this->session->set_flashdata('success', 'Backup excluído com sucesso.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao excluir backup.');
        }

        redirect('backup');
    }

    /**
     * Verificar integridade do backup
     */
    public function verificar()
    {
        $arquivo = $this->input->post('arquivo');
        $arquivo = basename($arquivo);
        $caminho = $this->backup_dir . $arquivo;

        if (!file_exists($caminho)) {
            echo json_encode(['valido' => false, 'mensagem' => 'Arquivo não encontrado']);
            return;
        }

        // Verificar extensão
        $ext = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

        // Se for .gz, verificar integridade do gzip
        if ($ext === 'gz') {
            $gz = @gzopen($caminho, 'rb');
            if ($gz === false) {
                echo json_encode(['valido' => false, 'mensagem' => 'Arquivo corrompido ou inválido']);
                return;
            }
            gzclose($gz);
        }

        // Verificar se contém comandos SQL válidos
        $conteudo = file_get_contents($caminho);
        if ($ext === 'gz') {
            $conteudo = gzdecode($conteudo);
        }

        $tem_create = strpos($conteudo, 'CREATE TABLE') !== false;
        $tem_insert = strpos($conteudo, 'INSERT INTO') !== false;

        echo json_encode([
            'valido' => true,
            'tem_estrutura' => $tem_create,
            'tem_dados' => $tem_insert,
            'tamanho' => $this->formatarTamanho(filesize($caminho))
        ]);
    }

    /**
     * Obter informações do banco
     */
    private function obterInfoDatabase()
    {
        // Tamanho do banco
        $this->db->query("SELECT table_schema, ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables
            WHERE table_schema = '{$this->db->database}'
            GROUP BY table_schema");

        $result = $this->db->query("SHOW TABLE STATUS");
        $tabelas = $result->num_rows();

        return [
            'nome' => $this->db->database,
            'tabelas' => $tabelas,
            'versao' => $this->db->version()
        ];
    }

    /**
     * Obter último backup
     */
    private function obterUltimoBackup()
    {
        $files = glob($this->backup_dir . 'backup_mapos_*');

        if (empty($files)) {
            return null;
        }

        // Ordenar por data de modificação
        array_multisort(array_map('filemtime', $files), SORT_DESC, $files);

        $ultimo = $files[0];

        return [
            'nome' => basename($ultimo),
            'data' => date('d/m/Y H:i:s', filemtime($ultimo)),
            'tamanho' => $this->formatarTamanho(filesize($ultimo))
        ];
    }

    /**
     * Formatar tamanho do arquivo
     */
    private function formatarTamanho($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Registrar log
     */
    private function registrarLog($tipo, $mensagem)
    {
        $dados = [
            'usuario' => $this->session->userdata('nome') ?? 'Sistema',
            'tarefa' => "[BACKUP] {$tipo}: {$mensagem}",
            'data' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'ip' => $this->input->ip_address()
        ];

        $this->db->insert('logs', $dados);
    }

    /**
     * Excluir diretório recursivamente
     */
    private function recursiveDelete($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->recursiveDelete($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - Sistema de Obras</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .diagnostico-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin-top: 0;
            color: #333;
            font-size: 22px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .tabela-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .tabela-item:last-child {
            border-bottom: none;
        }
        .status-ok {
            color: #27ae60;
            font-weight: bold;
        }
        .status-erro {
            color: #e74c3c;
            font-weight: bold;
        }
        .btn-acao {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            margin-top: 15px;
        }
        .btn-sucesso {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .btn-sucesso:hover {
            text-decoration: none;
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-numero {
            font-size: 36px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            font-family: monospace;
            font-size: 13px;
            overflow-x: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="diagnostico-container">

    <div class="header">
        <h1><i class="icon-cogs"></i> Diagnóstico do Sistema</h1>
        <p>Verificação completa das tabelas e funcionalidades</p>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <i class="icon-ok"></i> <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Status das Tabelas -->
    <div class="card">
        <h2><i class="icon-table"></i> Status das Tabelas</h2>

        <?php if (isset($tabelas) && is_array($tabelas)): ?>
            <?php foreach ($tabelas as $nome => $existe): ?>
                <div class="tabela-item">
                    <span><i class="icon-database"></i> <?= $nome ?></span>
                    <span class="<?= $existe ? 'status-ok' : 'status-erro' ?>">
                        <?= $existe ? '✅ EXISTE' : '❌ NÃO EXISTE' ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <?php
            // Verificar se todas existem
            $todas_existem = !in_array(false, $tabelas, true);
            ?>

            <?php if (!$todas_existem): ?>
                <a href="<?= site_url('diagnostico/criar_tabelas') ?>" class="btn-acao btn-sucesso">
                    <i class="icon-plus"></i> Criar Tabelas Ausentes
                </a>
            <?php else: ?>
                <div class="alert alert-success" style="margin-top: 20px;">
                    <i class="icon-check"></i> Todas as tabelas estão criadas corretamente!
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-warning">
                <i class="icon-warning-sign"></i> Não foi possível verificar as tabelas.
            </div>
        <?php endif; ?>
    </div>

    <!-- Estatísticas -->
    <div class="card">
        <h2><i class="icon-bar-chart"></i> Estatísticas</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-numero"><?= isset($total_obras) ? $total_obras : 0 ?></div>
                <div class="stat-label">Obras Cadastradas</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?= isset($total_equipe) ? $total_equipe : 0 ?></div>
                <div class="stat-label">Membros na Equipe</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?= isset($total_etapas) ? $total_etapas : 0 ?></div>
                <div class="stat-label">Etapas Criadas</div>
            </div>

            <div class="stat-card">
                <div class="stat-numero"><?= isset($total_atividades) ? $total_atividades : 0 ?></div>
                <div class="stat-label">Atividades Registradas</div>
            </div>
        </div>
    </div>

    <!-- Links Rápidos -->
    <div class="card">
        <h2><i class="icon-link"></i> Links Rápidos</h2>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?= site_url('obras') ?>" class="btn-acao btn-sucesso">
                <i class="icon-building"></i> Gerenciar Obras
            </a>

            <a href="<?= site_url('tecnicos/login') ?>" class="btn-acao btn-sucesso" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="icon-user"></i> Portal do Técnico
            </a>

            <a href="<?= site_url('mine') ?>" class="btn-acao btn-sucesso" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="icon-user"></i> Portal do Cliente
            </a>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="card">
        <h2><i class="icon-info-sign"></i> Informações do Sistema</h2>

        <p><strong>Versão PHP:</strong> <?= phpversion() ?></p>
        <p><strong>Versão CodeIgniter:</strong> <?= CI_VERSION ?></p>
        <p><strong>Data/Hora Atual:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>Base URL:</strong> <?= base_url() ?></p>
    </div>

</div>

</body>
</html>

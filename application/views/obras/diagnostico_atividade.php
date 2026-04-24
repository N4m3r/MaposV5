<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="container-fluid" style="padding: 20px;">
    <h2>Diagnóstico da Atividade #<?php echo $atividade_id; ?></h2>

    <div class="row-fluid">
        <div class="span12">
            <h3>Dados da Atividade Planejada (obra_atividades)</h3>
            <pre><?php print_r($atividade); ?></pre>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <h3>Dados da Atividade Real (os_atividades)</h3>
            <?php if ($atividade_real): ?>
            <pre><?php print_r($atividade_real); ?></pre>
            <?php else: ?>
            <div class="alert alert-warning">Nenhuma atividade real encontrada</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <h3>Colunas da Tabela os_atividades</h3>
            <pre><?php print_r($colunas); ?></pre>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <h3>Query Executada</h3>
            <pre><?php echo $last_query; ?></pre>
        </div>
    </div>

    <a href="<?php echo site_url('obras/visualizarAtividade/' . $atividade_id); ?>" class="btn btn-primary">
        <i class="icon-arrow-left"></i> Voltar para Visualização
    </a>
</div>

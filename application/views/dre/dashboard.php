<?php
/**
 * Dashboard DRE - Demonstração do Resultado do Exercício
 */
$dre = $results['dre'];
$indicadores = $results['indicadores'];
$evolucao = $results['evolucao'];
$data_inicio = $results['data_inicio'];
$data_fim = $results['data_fim'];
$comparativo = isset($dre['comparativo']) ? $dre['comparativo'] : null;
$periodo_anterior = $dre['periodo_anterior'] ?? null;
?\>

<style\>
.dre-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.dre-card h4 {
    margin: 0 0 15px 0;
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
}
.dre-value {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}
.dre-value.positive { color: #27ae60; }
.dre-value.negative { color: #e74c3c; }
.dre-percent {
    font-size: 12px;
    margin-top: 5px;
}
.dre-percent.positive { color: #27ae60; }
.dre-percent.negative { color: #e74c3c; }

.dre-table {
    width: 100%;
    border-collapse: collapse;
}
.dre-table th, .dre-table td {
    padding: 10px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
.dre-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #666;
    font-size: 12px;
    text-transform: uppercase;
}
.dre-table .grupo-principal {
    font-weight: bold;
    background: #f8f9fa;
}
.dre-table .grupo-destaque {
    font-weight: bold;
    background: #e8f4f8;
    border-top: 2px solid #3498db;
    border-bottom: 2px solid #3498db;
}
.dre-table .valor { text-align: right; font-family: monospace; }
.dre-table .percentual { text-align: right; color: #666; font-size: 12px; }
.dre-table .conta-detalhe { padding-left: 30px; font-size: 13px; color: #666; }

.dre-grafico-container {
    height: 300px;
    position: relative;
}

.metric-box {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 15px;
}
.metric-box .titulo {
    font-size: 12px;
    color: #666;
    margin-bottom: 10px;
}
.metric-box .valor {
    font-size: 24px;
    font-weight: bold;
}
.metric-box .percent {
    font-size: 11px;
    margin-top: 5px;
}
</style\>

<!-- Header --\>
<div class="row-fluid"\>
    <div class="span12"\>
        <ul class="breadcrumb"\>
            <li\>\<a href="\<\?= site_url('dashboard') ?\>"\>Dashboard</a\> <span class="divider"\>/</span\></li\>
            <li class="active"\>DRE - Demonstração do Resultado</li\>
        </ul\>
    </div\>
</div\>

<!-- Filtros --\>
<div class="row-fluid"\>
    <div class="span12"\>
        <div class="widget-box"\>
            <div class="widget-title"\>
                <span class="icon"\>\<i class="fas fa-filter"\></i\></span\>
                <h5\>Filtros</h5\>
                <div class="buttons"\>
                    \<a href="\<\?= site_url('dre/exportar?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?\>" class="btn btn-success btn-small"\>
                        \<i class="fas fa-download"\></i\> Exportar CSV
                    </a\>
                    \<a href="\<\?= site_url('dre/relatorio?data_inicio=' . $data_inicio . '&data_fim=' . $data_fim) ?\>" class="btn btn-info btn-small"\>
                        \<i class="fas fa-print"\></i\> Relatório Completo
                    </a\>
                </div\>
            </div\>
            <div class="widget-content"\>
                <form method="get" action="\<\?= site_url('dre') ?\>" class="form-inline"\>
                    <div class="row-fluid"\>
                        <div class="span3"\>
                            <label\>Data Início:</label\>
                            <input type="date" name="data_inicio" class="span12" value="\<\?= $data_inicio ?\>" /\>
                        </div\>
                        <div class="span3"\>
                            <label\>Data Fim:</label\>
                            <input type="date" name="data_fim" class="span12" value="\<\?= $data_fim ?\>" /\>
                        </div\>
                        <div class="span2"\>
                            <label\>&nbsp;</label\>
                            <button type="submit" class="btn btn-primary span12"\>
                                \<i class="fas fa-search"\></i\> Gerar DRE
                            </button\>
                        </div\>
                        <div class="span4"\>
                            <label\>&nbsp;</label\>
                            <div class="btn-group span12"\>
                                \<a href="?data_inicio=\<\?= date('Y-m-01') ?\>&data_fim=\<\?= date('Y-m-t') ?\>" class="btn btn-small"\>Mês Atual</a\>
                                \<a href="?data_inicio=\<\?= date('Y-m-01', strtotime('-1 month')) ?\>&data_fim=\<\?= date('Y-m-t', strtotime('-1 month')) ?\>" class="btn btn-small"\>Mês Anterior</a\>
                                \<a href="?data_inicio=\<\?= date('Y-01-01') ?\>&data_fim=\<\?= date('Y-m-d') ?\>" class="btn btn-small"\>Ano</a\>
                            </div\>
                        </div\>
                    </div\>
                </form\>
            </div\>
        </div\>
    </div\>
</div\>

<!-- Cards de Indicadores --\>
<div class="row-fluid"\>
    <div class="span3"\>
        <div class="dre-card"\>
            <h4\>\<i class="fas fa-dollar-sign"\></i\> Receita Bruta</h4\>
            <div class="dre-value"\>R$ \<\?= number_format($indicadores['receita_bruta'], 2, ',', '.') ?\></div\>
            \<\?php if ($comparativo): ?\>
            \<\?php
                $diff = $indicadores['receita_bruta'] - ($comparativo['grupos']['RECEITA_BRUTA']['valor'] ?? 0);
                $percent = $comparativo['grupos']['RECEITA_BRUTA']['valor'] > 0 ? ($diff / $comparativo['grupos']['RECEITA_BRUTA']['valor'] * 100) : 0;
            ?\>
            <div class="dre-percent \<\?= $percent >= 0 ? 'positive' : 'negative' ?\>"\>
                \<i class="fas fa-arrow-\<\?= $percent >= 0 ? 'up' : 'down' ?\>"\></i\>
                \<\?= number_format(abs($percent), 2) ?\>% vs período anterior
            </div\>
            \<\?php endif; ?\>
        </div\>
    </div\>
    <div class="span3"\>
        <div class="dre-card"\>
            <h4\>\<i class="fas fa-chart-line"\></i\> Lucro Bruto</h4\>
            <div class="dre-value \<\?= $indicadores['lucro_bruto'] >= 0 ? 'positive' : 'negative' ?\>"\>
                R$ \<\?= number_format($indicadores['lucro_bruto'], 2, ',', '.') ?\>
            </div\>
            <div class="dre-percent"\>Margem: \<\?= $indicadores['margem_bruta'] ?\>%</div\>
        </div\>
    </div\>
    <div class="span3"\>
        <div class="dre-card"\>
            <h4\>\<i class="fas fa-briefcase"\></i\> Lucro Operacional</h4\>
            <div class="dre-value \<\?= $indicadores['lucro_operacional'] >= 0 ? 'positive' : 'negative' ?\>"\>
                R$ \<\?= number_format($indicadores['lucro_operacional'], 2, ',', '.') ?\>
            </div\>
            <div class="dre-percent"\>Margem: \<\?= $indicadores['margem_operacional'] ?\>%</div\>
        </div\>
    </div\>
    <div class="span3"\>
        <div class="dre-card"\>
            <h4\>\<i class="fas fa-piggy-bank"\></i\> Lucro Líquido</h4\>
            <div class="dre-value \<\?= $indicadores['lucro_liquido'] >= 0 ? 'positive' : 'negative' ?\>"\>
                R$ \<\?= number_format($indicadores['lucro_liquido'], 2, ',', '.') ?\>
            </div\>
            <div class="dre-percent"\>Margem: \<\?= $indicadores['margem_liquida'] ?\>%</div\>
        </div\>
    </div\>
</div\>

<!-- Gráfico de Evolução --\>
<div class="row-fluid"\>
    <div class="span8"\>
        <div class="widget-box"\>
            <div class="widget-title"\>
                \<span class="icon"\>\<i class="fas fa-chart-area"\></i\></span\>
                <h5\>Evolução Mensal</h5\>
            </div\>
            <div class="widget-content"\>
                <div class="dre-grafico-container"\>
                    <canvas id="chartEvolucao"\></canvas\>
                </div\>
            </div\>
        </div\>
    </div\>

    <!-- Métricas --\>
    <div class="span4"\>
        <div class="widget-box"\>
            <div class="widget-title"\>
                \<span class="icon"\>\<i class="fas fa-calculator"\></i\></span\>
                <h5\>Métricas do Período</h5\>
            </div\>
            <div class="widget-content"\>
                <div class="metric-box"\>
                    <div class="titulo"\>Margem Bruta</div\>
                    <div class="valor"\>\<\?= $indicadores['margem_bruta'] ?\>%</div\>
                    <div class="percent"\>\<\?= $indicadores['margem_bruta'] >= 30 ? 'Saudável' : 'Atenção' ?\></div\>
                </div\>
                <div class="metric-box"\>
                    <div class="titulo"\>Margem Operacional</div\>
                    <div class="valor"\>\<\?= $indicadores['margem_operacional'] ?\>%</div\>
                    <div class="percent"\>\<\?= $indicadores['margem_operacional'] >= 15 ? 'Saudável' : 'Atenção' ?\></div\>
                </div\>
                <div class="metric-box"\>
                    <div class="titulo"\>Margem Líquida</div\>
                    <div class="valor"\>\<\?= $indicadores['margem_liquida'] ?\>%</div\>
                    <div class="percent"\>\<\?= $indicadores['margem_liquida'] >= 10 ? 'Saudável' : 'Atenção' ?\></div\>
                </div\>
            </div\>
        </div\>
    </div\>
</div\>

<!-- Tabela DRE --\>
<div class="row-fluid"\>
    <div class="span12"\>
        <div class="widget-box"\>
            <div class="widget-title"\>
                \<span class="icon"\>\<i class="fas fa-table"\></i\></span\>
                <h5\>
                    Demonstração do Resultado do Exercício
                    \<\?php if ($comparativo): ?\>
                    <small class="muted"\> - Comparativo com período anterior (\<\?= date('d/m/Y', strtotime($periodo_anterior['inicio'])) ?\> a \<\?= date('d/m/Y', strtotime($periodo_anterior['fim'])) ?\>)</small\>
                    \<\?php endif; ?\>
                </h5\>
            </div\>
            <div class="widget-content nopadding"\>
                <table class="dre-table"\>
                    <thead\>
                        <tr\>
                            <th\>Descrição</th\>
                            <th class="valor"\>Valor (R$)</th\>
                            <th class="percentual"\>% da Receita</th\>
                            \<\?php if ($comparativo): ?\>
                            <th class="valor"\>Período Anterior</th\>
                            <th class="percentual"\>Variação</th\>
                            \<\?php endif; ?\>
                        </tr\>
                    </thead\>
                    <tbody\>
                        \<\?php foreach ($dre['grupos'] as $grupo): ?\>
                        <tr class="\<\?= $grupo['destaque'] ? 'grupo-destaque' : 'grupo-principal' ?\>"\>
                            <td\>\<\?= $grupo['titulo'] ?\></td\>
                            <td class="valor"\>
                                \<\?= $grupo['valor'] >= 0 ? '' : '-' ?\>
                                R$ \<\?= number_format(abs($grupo['valor']), 2, ',', '.') ?\>
                            </td\>
                            <td class="percentual"\>\<\?= number_format($grupo['percentual'], 2) ?\>%</td\>
                            \<\?php if ($comparativo):
                                $valor_ant = $comparativo['grupos'][array_search($grupo, $dre['grupos'])]['valor'] ?? 0;
                                $diff = $grupo['valor'] - $valor_ant;
                                $percent_var = $valor_ant != 0 ? (($grupo['valor'] - $valor_ant) / abs($valor_ant)) * 100 : 0;
                            ?\>
                            <td class="valor"\>R$ \<\?= number_format(abs($valor_ant), 2, ',', '.') ?\></td\>
                            <td class="percentual"\>
                                \<span style="color: \<\?= $diff >= 0 ? '#27ae60' : '#e74c3c' ?\>"\>
                                    \<i class="fas fa-arrow-\<\?= $diff >= 0 ? 'up' : 'down' ?\>"\></i\>
                                    \<\?= number_format(abs($percent_var), 2) ?\>%
                                </span\>
                            </td\>
                            \<\?php endif; ?\>
                        </tr\>
                            \<\?php if (!$grupo['destaque'] && !empty($grupo['contas'])): ?\>
                            \<\?php foreach ($grupo['contas'] as $conta): ?\>
                            \<\?php
                                // Buscar valor da conta
                                $valor_conta = $this->dre_model->getTotalPorConta($conta->id, $data_inicio, $data_fim);
                                if ($valor_conta != 0):
                            ?\>
                            <tr\>
                                <td class="conta-detalhe"\>\<\?= $conta->nome ?\></td\>
                                <td class="valor"\>R$ \<\?= number_format(abs($valor_conta), 2, ',', '.') ?\></td\>
                                <td class="percentual"\>
                                    \<\?= $indicadores['receita_bruta'] > 0 ? number_format(($valor_conta / $indicadores['receita_bruta']) * 100, 2) : 0 ?\>%
                                </td\>
                                \<\?php if ($comparativo): ?\>
                                <td class="valor"\>-</td\>
                                <td class="percentual"\>-</td\>
                                \<\?php endif; ?\>
                            </tr\>
                            \<\?php endif; ?\>
                            \<\?php endforeach; ?\>
                            \<\?php endif; ?\>
                        \<\?php endforeach; ?\>
                    </tbody\>
                </table\>
            </div\>
        </div\>
    </div\>
</div\>

<!-- Ações Rápidas --\>
<div class="row-fluid"\>
    <div class="span12"\>
        <div class="widget-box"\>
            <div class="widget-title"\>
                \<span class="icon"\>\<i class="fas fa-cogs"\></i\></span\>
                <h5\>Ações Rápidas</h5\>
            </div\>
            <div class="widget-content"\>
                <div class="btn-group"\>
                    \<a href="\<\?= site_url('dre/contas') ?\>" class="btn btn-info"\>
                        \<i class="fas fa-list-alt"\></i\> Plano de Contas
                    </a\>
                    \<a href="\<\?= site_url('dre/lancamentos') ?\>" class="btn btn-primary"\>
                        \<i class="fas fa-book"\></i\> Lançamentos
                    </a\>
                    \<a href="\<\?= site_url('dre/analise') ?\>" class="btn btn-warning"\>
                        \<i class="fas fa-chart-pie"\></i\> Análise Vertical/Horizontal
                    </a\>
                    \<a href="\<\?= site_url('dre/relatorio') ?\>" class="btn btn-success"\>
                        \<i class="fas fa-print"\></i\> Imprimir DRE
                    </a\>
                </div\>

                <form method="post" action="\<\?= site_url('dre/integrar') ?\>" class="form-inline pull-right" style="margin: 0;"\>
                    <input type="hidden" name="data_inicio" value="\<\?= $data_inicio ?\>" /\>
                    <input type="hidden" name="data_fim" value="\<\?= $data_fim ?\>" /\>
                    <button type="submit" class="btn btn-inverse" onclick="return confirm('Importar dados de OS, Vendas e Financeiro automaticamente?')"\>
                        \<i class="fas fa-sync"\></i\> Integrar Dados Automáticos
                    </button\>
                </form\>
            </div\>
        </div\>
    </div\>
</div\>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"\></script\>
<script\>
const evolucao = \<\?= json_encode($evolucao) ?\>;

// Gráfico de Evolução
new Chart(document.getElementById('chartEvolucao'), {
    type: 'line',
    data: {
        labels: evolucao.map(e => e.mes_formatado),
        datasets: [
            {
                label: 'Receita Bruta',
                data: evolucao.map(e => e.receita_bruta),
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                fill: false,
                tension: 0.4
            },
            {
                label: 'Lucro Bruto',
                data: evolucao.map(e => e.lucro_bruto),
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                fill: false,
                tension: 0.4
            },
            {
                label: 'Lucro Líquido',
                data: evolucao.map(e => e.lucro_liquido),
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                fill: false,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});
</script\>

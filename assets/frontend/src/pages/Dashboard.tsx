import { useEffect, useState } from 'react';
import { CCard, CCardBody, CCardHeader, CRow, CCol, CSpinner, CWidgetStatsA } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { CChartLine, CChartDoughnut } from '@coreui/react-chartjs';
import { getStats } from '../api/dashboard';
import type { DashboardStats } from '../types';

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

export default function Dashboard() {
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [lastUpdate, setLastUpdate] = useState<Date>(new Date());

    useEffect(() => {
        let mounted = true;

        async function load() {
            try {
                const data = await getStats();
                if (mounted) {
                    setStats(data);
                    setError(null);
                    setLastUpdate(new Date());
                }
            } catch (err) {
                if (mounted) {
                    setError(err instanceof Error ? err.message : 'Erro ao carregar');
                }
            } finally {
                if (mounted) setLoading(false);
            }
        }

        load();
        // Auto-refresh a cada 30s
        const id = setInterval(load, 30000);
        return () => {
            mounted = false;
            clearInterval(id);
        };
    }, []);

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: 400 }}>
                <CSpinner color="primary" />
            </div>
        );
    }

    if (error || !stats) {
        return (
            <CCard className="border-danger">
                <CCardBody>
                    <h4>Erro ao carregar dashboard</h4>
                    <p className="text-muted">{error || 'Sem dados disponiveis'}</p>
                    <button className="btn btn-primary" onClick={() => window.location.reload()}>
                        Tentar novamente
                    </button>
                </CCardBody>
            </CCard>
        );
    }

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilSpeedometer" className="me-2" />
                    Dashboard
                </h2>
                <small className="text-muted">
                    Atualizado: {lastUpdate.toLocaleTimeString('pt-BR')}
                </small>
            </div>

            {/* KPI Cards */}
            <CRow className="g-3 mb-4">
                <CCol xs={12} sm={6} lg={3}>
                    <CWidgetStatsA
                        color="primary"
                        value={stats.os_total.toString()}
                        title="Ordens de Servico"
                        action={<CIcon icon="cilClipboard" size="xl" />}
                    />
                </CCol>
                <CCol xs={12} sm={6} lg={3}>
                    <CWidgetStatsA
                        color="warning"
                        value={stats.os_pendentes.toString()}
                        title="OS Pendentes"
                        action={<CIcon icon="cilClock" size="xl" />}
                    />
                </CCol>
                <CCol xs={12} sm={6} lg={3}>
                    <CWidgetStatsA
                        color="info"
                        value={stats.clientes_total.toString()}
                        title="Clientes Ativos"
                        action={<CIcon icon="cilPeople" size="xl" />}
                    />
                </CCol>
                <CCol xs={12} sm={6} lg={3}>
                    <CWidgetStatsA
                        color="success"
                        value={formatCurrency(stats.faturamento_mes)}
                        title="Faturamento (mes)"
                        action={<CIcon icon="cilWallet" size="xl" />}
                    />
                </CCol>
            </CRow>

            {/* Graficos */}
            <CRow className="g-3">
                <CCol xs={12} lg={8}>
                    <CCard>
                        <CCardHeader>
                            <strong>Receita vs Despesa (ultimos 6 meses)</strong>
                        </CCardHeader>
                        <CCardBody>
                            <CChartLine
                                data={{
                                    labels: ['Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out'],
                                    datasets: [
                                        {
                                            label: 'Receita',
                                            data: [12000, 14500, 13200, 16800, 15400, stats.faturamento_mes || 0],
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            tension: 0.3,
                                            fill: true,
                                        },
                                        {
                                            label: 'Despesa',
                                            data: [8000, 9200, 8800, 10100, 9500, stats.contas_pagar || 0],
                                            borderColor: '#ef4444',
                                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                            tension: 0.3,
                                            fill: true,
                                        },
                                    ],
                                }}
                                options={{
                                    plugins: { legend: { position: 'bottom' } },
                                    scales: { y: { beginAtZero: true } },
                                }}
                            />
                        </CCardBody>
                    </CCard>
                </CCol>

                <CCol xs={12} lg={4}>
                    <CCard>
                        <CCardHeader>
                            <strong>Status das OS</strong>
                        </CCardHeader>
                        <CCardBody>
                            <CChartDoughnut
                                data={{
                                    labels: ['Aberto', 'Andamento', 'Aguardando', 'Finalizado'],
                                    datasets: [{
                                        backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981'],
                                        data: [
                                            stats.os_pendentes,
                                            stats.os_andamento,
                                            stats.os_total - stats.os_pendentes - stats.os_andamento - stats.os_finalizadas,
                                            stats.os_finalizadas,
                                        ],
                                    }],
                                }}
                                options={{ plugins: { legend: { position: 'bottom' } } }}
                            />
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
        </>
    );
}

/**
 * Tipos compartilhados em toda a aplicacao.
 */

export interface AppConfig {
    baseUrl: string;
    userName: string;
    userEmail: string;
    userAvatar?: string;
    permissions: string[];
    theme: string;
}

export interface User {
    id: number;
    nome: string;
    email: string;
    usuario?: string;
    permissao?: number;
}

export interface Cliente {
    id?: number;
    nomeCliente: string;
    documento?: string;
    email?: string;
    telefone?: string;
    celular?: string;
    dataCadastro?: string;
    ativo?: number;
}

export interface Os {
    id?: number;
    cliente_id: number;
    cliente_nome?: string;
    status: OsStatus;
    descricao: string;
    valor?: number;
    data_inicio?: string;
    data_fim?: string;
    tecnico_id?: number;
    tecnico_nome?: string;
}

export type OsStatus =
    | 'Aberto'
    | 'Orcamento'
    | 'Aprovado'
    | 'Em Andamento'
    | 'Aguardando Pecas'
    | 'Pronto'
    | 'Finalizado'
    | 'Cancelado';

export interface KanbanCard {
    id: number;
    os_id: number;
    titulo: string;
    cliente: string;
    status: OsStatus;
    data_inicio?: string;
    valor?: number;
    prioridade?: 'baixa' | 'normal' | 'alta' | 'urgente';
}

export interface DashboardStats {
    os_total: number;
    os_pendentes: number;
    os_andamento: number;
    os_finalizadas: number;
    clientes_total: number;
    vendas_mes: number;
    faturamento_mes: number;
    obras_andamento: number;
    contas_receber: number;
    contas_pagar: number;
}

export interface ApiResponse<T = unknown> {
    success: boolean;
    data?: T;
    message?: string;
    error?: string;
}

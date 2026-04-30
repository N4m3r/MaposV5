<?php
/**
 * Auto Events
 * Eventos automáticos pré-configurados
 */

namespace Libraries\Scheduler;

use Libraries\Email\EmailQueue;

class AutoEvents
{
    private EventScheduler $scheduler;
    private EmailQueue $emailQueue;

    public function __construct()
    {
        $this->scheduler = new EventScheduler();
        $this->emailQueue = new EmailQueue();
    }

    /**
     * Evento: OS prestes a vencer (2 dias antes)
     */
    public function scheduleOsVencendo(int $osId, string $dataFinal, string $emailCliente): void
    {
        $vencimento = new \DateTime($dataFinal);
        $vencimento->modify('-2 days');

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'os_vencendo',
            'entity_type' => 'os',
            'entity_id' => $osId,
            'scheduled_at' => $vencimento->format('Y-m-d 09:00:00'),
            'payload' => [
                'to' => $emailCliente,
                'subject' => 'Sua Ordem de Serviço vence em 2 dias',
                'template' => 'os_vencendo',
                'template_data' => ['os_id' => $osId]
            ]
        ]);
    }

    /**
     * Evento: Cobrança vencendo (1 dia antes)
     */
    public function scheduleCobrancaVencendo(int $cobrancaId, string $dataVencimento, string $emailCliente): void
    {
        $vencimento = new \DateTime($dataVencimento);
        $vencimento->modify('-1 day');

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'cobranca_vencendo',
            'entity_type' => 'cobranca',
            'entity_id' => $cobrancaId,
            'scheduled_at' => $vencimento->format('Y-m-d 08:00:00'),
            'payload' => [
                'to' => $emailCliente,
                'subject' => 'Sua cobrança vence amanhã',
                'template' => 'cobranca_vencendo',
                'template_data' => ['cobranca_id' => $cobrancaId]
            ]
        ]);
    }

    /**
     * Evento: Cobrança vencida (lembrança)
     */
    public function scheduleCobrancaVencida(int $cobrancaId, string $dataVencimento, string $emailCliente): void
    {
        $vencimento = new \DateTime($dataVencimento);
        $vencimento->modify('+1 day');

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'cobranca_vencida',
            'entity_type' => 'cobranca',
            'entity_id' => $cobrancaId,
            'scheduled_at' => $vencimento->format('Y-m-d 09:00:00'),
            'payload' => [
                'to' => $emailCliente,
                'subject' => 'Cobrança em atraso - Ação necessária',
                'template' => 'cobranca_vencida',
                'priority' => 2,
                'template_data' => ['cobranca_id' => $cobrancaId]
            ]
        ]);
    }

    /**
     * Evento: Aniversário do cliente
     */
    public function scheduleAniversario(int $clienteId, string $dataNascimento, string $email): void
    {
        $aniversario = new \DateTime($dataNascimento);
        $hoje = new \DateTime();
        $aniversario->setDate($hoje->format('Y'), $aniversario->format('m'), $aniversario->format('d'));

        if ($aniversario < $hoje) {
            $aniversario->modify('+1 year');
        }

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'aniversario_cliente',
            'entity_type' => 'cliente',
            'entity_id' => $clienteId,
            'scheduled_at' => $aniversario->format('Y-m-d 08:00:00'),
            'payload' => [
                'to' => $email,
                'subject' => 'Feliz Aniversario!',
                'template' => 'aniversario',
                'priority' => 3
            ]
        ]);
    }

    /**
     * Evento: Follow-up pós venda (7 dias após)
     */
    public function scheduleFollowUpVenda(int $vendaId, string $dataVenda, string $emailCliente): void
    {
        $followUp = new \DateTime($dataVenda);
        $followUp->modify('+7 days');

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'followup_venda',
            'entity_type' => 'venda',
            'entity_id' => $vendaId,
            'scheduled_at' => $followUp->format('Y-m-d 10:00:00'),
            'payload' => [
                'to' => $emailCliente,
                'subject' => 'Como foi sua experiencia?',
                'template' => 'followup_venda',
                'template_data' => ['venda_id' => $vendaId]
            ]
        ]);
    }

    /**
     * Evento: Recorrente - Relatório semanal
     */
    public function scheduleRelatorioSemanal(int $userId, string $email): void
    {
        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'relatorio_semanal',
            'entity_type' => 'relatorio',
            'entity_id' => $userId,
            'scheduled_at' => date('Y-m-d 07:00:00', strtotime('next Monday')),
            'recurring' => true,
            'recurrence_rule' => '0 7 * * 1',
            'payload' => [
                'to' => $email,
                'subject' => 'Relatório Semanal - MAPOS',
                'template' => 'relatorio_semanal',
                'template_data' => ['user_id' => $userId]
            ]
        ]);
    }

    /**
     * Evento: Lembrete de manutenção periódica
     */
    public function scheduleManutencaoPeriodica(int $osId, int $diasIntervalo, string $emailCliente): void
    {
        $proxima = new \DateTime();
        $proxima->modify("+{$diasIntervalo} days");

        $this->scheduler->scheduleEvent([
            'type' => 'email',
            'name' => 'manutencao_periodica',
            'entity_type' => 'os',
            'entity_id' => $osId,
            'scheduled_at' => $proxima->format('Y-m-d 09:00:00'),
            'payload' => [
                'to' => $emailCliente,
                'subject' => 'Hora da manutenção preventiva',
                'template' => 'manutencao_preventiva',
                'template_data' => ['os_id' => $osId]
            ]
        ]);
    }
}

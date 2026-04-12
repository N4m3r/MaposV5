<?php
/**
 * Service Interface
 * Interface base para todos os serviços
 */

namespace Interfaces;

interface ServiceInterface
{
    /**
     * Valida dados antes de processar
     *
     * @param array $data
     * @return array Lista de erros (vazio se válido)
     */
    public function validate(array $data): array;

    /**
     * Executa ação do serviço
     *
     * @param array $data
     * @return array Resultado da operação
     */
    public function execute(array $data): array;
}

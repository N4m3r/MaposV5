<?php

// Legacy log function (writes to 'logs' table)
function log_info($task)
{
    $ci = &get_instance();
    $ci->load->model('Audit_model');

    $data = [
        'usuario' => $ci->session->userdata('nome_admin'),
        'ip'      => $ci->input->ip_address(),
        'tarefa'  => $task,
        'data'    => date('Y-m-d'),
        'hora'    => date('H:i:s'),
    ];

    $ci->Audit_model->add($data);
}

// New structured audit log (writes to 'audit_log' table)
function log_audit(string $action, string $tableName, $recordId = null, $oldData = null, $newData = null): bool
{
    $ci = &get_instance();
    $ci->load->model('Audit_model');

    $data = [
        'user_id'     => $ci->session->userdata('idUsuarios') ?: null,
        'username'    => $ci->session->userdata('nome_admin') ?: ($ci->input->server('PHP_AUTH_USER') ?? 'system'),
        'action'      => $action,
        'table_name'  => $tableName,
        'record_id'   => $recordId !== null ? (string) $recordId : null,
        'old_data'    => $oldData,
        'new_data'    => $newData,
        'ip_address'  => $ci->input->ip_address(),
        'user_agent'  => $ci->input->user_agent(),
    ];

    return $ci->Audit_model->logAction($data);
}
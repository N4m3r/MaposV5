<?php
/**
 * Tenant Manager
 * Sistema de multi-tenancy (preparação para SaaS)
 */

namespace Libraries;

class TenantManager
{
    private static ?string $currentTenant = null;
    private static array $config = [];

    /**
     * Define o tenant atual
     */
    public static function setTenant(string $tenantId): void
    {
        self::$currentTenant = $tenantId;
        self::configureDatabase();
        self::configureStorage();
    }

    /**
     * Obtém o tenant atual
     */
    public static function getTenant(): ?string
    {
        return self::$currentTenant;
    }

    /**
     * Verifica se há um tenant ativo
     */
    public static function hasTenant(): bool
    {
        return self::$currentTenant !== null;
    }

    /**
     * Configura conexão com banco para tenant
     */
    private static function configureDatabase(): void
    {
        if (!self::$currentTenant) {
            return;
        }

        $ci = \u0026get_instance();

        // Opção 1: Prefixo de tabela
        // $ci->db->set_prefix(self::$currentTenant . '_');

        // Opção 2: Banco de dados separado (não implementado)
        // $config = self::getTenantConfig(self::$currentTenant);
        // $ci->db = $ci->load->database($config, true);

        // Opção 3: Campo tenant_id em todas as tabelas
        // Isso é implementado nos models
    }

    /**
     * Configura storage para tenant
     */
    private static function configureStorage(): void
    {
        if (!self::$currentTenant) {
            return;
        }

        // Cria diretório para uploads do tenant
        $tenantPath = FCPATH . 'assets/uploads/' . self::$currentTenant;
        if (!is_dir($tenantPath)) {
            mkdir($tenantPath, 0755, true);
        }
    }

    /**
     * Obtém configuração de um tenant
     */
    public static function getTenantConfig(string $tenantId): array
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        $config = $ci->db
            ->where('tenant_id', $tenantId)
            ->where('active', 1)
            ->get('tenants')
            ->row();

        return $config ? (array) $config : [];
    }

    /**
     * Lista todos os tenants ativos
     */
    public static function listTenants(): array
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        return $ci->db
            ->where('active', 1)
            ->get('tenants')
            ->result_array();
    }

    /**
     * Cria um novo tenant
     */
    public static function createTenant(array $data): int
    {
        $ci = \u0026get_instance();
        $ci->load->database();

        $tenantData = [
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'domain' => $data['domain'] ?? null,
            'settings' => json_encode($data['settings'] ?? []),
            'plan' => $data['plan'] ?? 'basic',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $ci->db->insert('tenants', $tenantData);
        $id = $ci->db->insert_id();

        // Cria tabelas para o tenant
        self::setupTenantTables($data['tenant_id']);

        return $id;
    }

    /**
     * Configura tabelas para novo tenant
     */
    private static function setupTenantTables(string $tenantId): void
    {
        // Aqui seria criado as tabelas específicas do tenant
        // ou aplicado migrations
    }

    /**
     * Obtém caminho de upload para tenant
     */
    public static function getUploadPath(): string
    {
        if (self::$currentTenant) {
            return 'assets/uploads/' . self::$currentTenant . '/';
        }
        return 'assets/uploads/';
    }

    /**
     * Adiciona scope de tenant a query
     */
    public static function scopeTenant($query)
    {
        if (self::$currentTenant) {
            return $query->where('tenant_id', self::$currentTenant);
        }
        return $query;
    }

    /**
     * Limpa tenant atual
     */
    public static function clearTenant(): void
    {
        self::$currentTenant = null;
    }
}

/**
 * Trait para models com suporte a multi-tenancy
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // Adiciona scope global para tenant
        static::addGlobalScope('tenant', function ($query) {
            if (TenantManager::hasTenant()) {
                $query->where('tenant_id', TenantManager::getTenant());
            }
        });
    }

    public static function create(array $attributes = [])
    {
        if (TenantManager::hasTenant()) {
            $attributes['tenant_id'] = TenantManager::getTenant();
        }
        return parent::create($attributes);
    }
}

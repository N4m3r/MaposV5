# Integração do Menu de Técnicos no Mapos

Este arquivo explica como integrar o menu de gestão de técnicos na estrutura existente do Mapos.

## 1. Adicionar Menu Principal

Edite o arquivo `application/views/tema/xxx/header.php` (substitua xxx pelo tema atual) e adicione o seguinte item no menu:

```php
<!-- Menu Técnicos -->
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon icon-wrench"></i>
        <span class="text">Técnicos</span>
        <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
        <li><a href="<?php echo site_url('tecnicos_admin'); ?>">
            <i class="icon icon-dashboard"></i> Dashboard
        </a></li>
        <li class="divider"></li>
        <li><a href="<?php echo site_url('tecnicos_admin/tecnicos'); ?>">
            <i class="icon icon-user"></i> Gerenciar Técnicos
        </a></li>
        <li><a href="<?php echo site_url('tecnicos_admin/servicos_catalogo'); ?>">
            <i class="icon icon-list"></i> Catálogo de Serviços
        </a></li>
        <li><a href="<?php echo site_url('tecnicos_admin/checklists'); ?>">
            <i class="icon icon-check"></i> Checklists
        </a></li>
        <li><a href="<?php echo site_url('tecnicos_admin/obras'); ?>">
            <i class="icon icon-building"></i> Obras
        </a></li>
        <li class="divider"></li>
        <li><a href="<?php echo site_url('tecnicos_admin/relatorios'); ?>">
            <i class="icon icon-bar-chart"></i> Relatórios
        </a></li>
    </ul>
</li>
```

## 2. Adicionar Permissões no Sistema

Adicione as seguintes permissões no banco de dados na tabela `permissoes` (ou através da interface de permissões):

```sql
-- Verificar o ID do grupo "Administrador"
-- Adicionar as permissões no campo "permissoes" (formato JSON)

-- Permissões a adicionar:
-- "tec_admin" - Acesso total ao sistema de técnicos
-- "tec_view" - Apenas visualização
-- "tec_edit" - Edição de técnicos
-- "tec_del" - Exclusão de técnicos
```

Ou se houver uma interface para gerenciar permissões, adicione:
- `tec_admin` - Administrador de Técnicos
- `tec_view` - Visualizar Técnicos
- `tec_edit` - Editar Técnicos
- `tec_del` - Excluir Técnicos

## 3. Configurar Rotas (opcional)

No arquivo `application/config/routes.php`, você pode adicionar rotas amigáveis:

```php
// Rotas do Sistema de Técnicos
$route['tecnicos'] = 'tecnicos';
$route['tecnicos/login'] = 'tecnicos/login';
$route['tecnicos/dashboard'] = 'tecnicos/dashboard';
$route['tecnicos/minhas-os'] = 'tecnicos/minhas_os';
$route['tecnicos/executar/(:num)'] = 'tecnicos/executar_os/$1';
$route['tecnicos/estoque'] = 'tecnicos/meu_estoque';
$route['tecnicos/perfil'] = 'tecnicos/perfil';

// Rotas Admin
$route['admin/tecnicos'] = 'tecnicos_admin';
$route['admin/tecnicos/servicos'] = 'tecnicos_admin/servicos_catalogo';
```

## 4. Adicionar Link Direto no Painel

Para adicionar um link rápido no painel principal (`application/views/mapos/painel.php`), adicione:

```php
<!-- Verificar se tem permissão -->
<?php if($this->permission->checkPermission($this->session->userdata('permissao'), 'vTecnicos')) { ?>
<div class="quick-actions_homepage">
    <ul class="quick-actions">
        <li class="bg_lg span3">
            <a href="<?php echo site_url('tecnicos_admin') ?>">
                <i class="icon-wrench"></i>
                Gestão de Técnicos
            </a>
        </li>
    </ul>
</div>
<?php } ?>
```

## 5. Atualizar Query de Permissões

Se o sistema usa uma query específica para carregar permissões, certifique-se de que a coluna `permissoes` suporta JSON ou adicione as novas permissões no formato adequado.

## 6. Testar Acesso

Após a configuração:
1. Faça login como administrador
2. Verifique se o menu "Técnicos" aparece
3. Acesse a dashboard de técnicos
4. Teste o portal do técnico acessando `/tecnicos/login`

## 7. Configurações Adicionais

### Configurar tipos de serviço (opcional)

No formulário de OS, adicione um campo para selecionar serviços do catálogo:

```php
<!-- No formulário de OS -->
<div class="control-group">
    <label class="control-label">Serviços</label>
    <div class="controls">
        <select name="servicos[]" multiple class="span8 chzn-select">
            <?php foreach($servicos_catalogo as $servico): ?>
                <option value="<?php echo $servico->id ?>">
                    <?php echo $servico->codigo ?> - <?php echo $servico->nome ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
```

### Configurar atribuição automática

No controller de OS (`Os.php`), adicione a atribuição automática baseada em especialidade:

```php
// Buscar técnico disponível com especialidade adequada
public function autoAtribuirTecnico($servico_tipo) {
    $this->load->model('tecnicos_model');
    $tecnicos = $this->tecnicos_model->getByEspecialidade($servico_tipo);
    // Lógica de atribuição...
}
```

## Notas Importantes

- O portal do técnico (`Tecnicos.php`) usa views mobile-optimized
- O admin (`Tecnicos_admin.php`) reutiliza o tema padrão do Mapos
- Fotos são salvas em `assets/tecnicos/fotos/ANO/MES/`
- Certifique-se de que a pasta `assets/tecnicos/fotos` tem permissão de escrita

## Suporte

Para problemas ou dúvidas, verifique:
1. Logs de erro do CodeIgniter
2. Console do navegador (para erros JS)
3. Permissões de pastas
4. Configurações do banco de dados

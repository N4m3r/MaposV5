SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';



-- -----------------------------------------------------
-- Table `ci_sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
);


-- -----------------------------------------------------
-- Table `clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `idClientes` INT(11) NOT NULL AUTO_INCREMENT,
  `asaas_id` VARCHAR(255) DEFAULT NULL,
  `nomeCliente` VARCHAR(255) NOT NULL,
  `sexo` VARCHAR(20) NULL,
  `pessoa_fisica` BOOLEAN NOT NULL DEFAULT 1,
  `documento` VARCHAR(20) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `celular` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(200) NOT NULL,
  `dataCadastro` DATE NULL DEFAULT NULL,
  `rua` VARCHAR(70) NULL DEFAULT NULL,
  `numero` VARCHAR(15) NULL DEFAULT NULL,
  `bairro` VARCHAR(45) NULL DEFAULT NULL,
  `cidade` VARCHAR(45) NULL DEFAULT NULL,
  `estado` VARCHAR(20) NULL DEFAULT NULL,
  `cep` VARCHAR(20) NULL DEFAULT NULL,
  `contato` varchar(45) DEFAULT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `fornecedor` BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY (`idClientes`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `resets_de_senha` ( 
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(200) NOT NULL , 
  `token` VARCHAR(255) NOT NULL , 
  `data_expiracao` DATETIME NOT NULL, 
  `token_utilizado` TINYINT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
  `idCategorias` INT NOT NULL AUTO_INCREMENT,
  `categoria` VARCHAR(80) NULL,
  `cadastro` DATE NULL,
  `status` TINYINT(1) NULL,
  `tipo` VARCHAR(15) NULL,
  PRIMARY KEY (`idCategorias`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `contas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `contas` (
  `idContas` INT NOT NULL AUTO_INCREMENT,
  `conta` VARCHAR(45) NULL,
  `banco` VARCHAR(45) NULL,
  `numero` VARCHAR(45) NULL,
  `saldo` DECIMAL(10,2) NULL,
  `cadastro` DATE NULL,
  `status` TINYINT(1) NULL,
  `tipo` VARCHAR(80) NULL,
  PRIMARY KEY (`idContas`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `permissoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissoes` (
  `idPermissao` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `permissoes` TEXT NULL,
  `situacao` TINYINT(1) NULL,
  `data` DATE NULL,
  PRIMARY KEY (`idPermissao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `idUsuarios` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `rg` VARCHAR(20) NULL DEFAULT NULL,
  `cpf` VARCHAR(20) NOT NULL,
  `cep` VARCHAR(9) NOT NULL,
  `rua` VARCHAR(70) NULL DEFAULT NULL,
  `numero` VARCHAR(15) NULL DEFAULT NULL,
  `bairro` VARCHAR(45) NULL DEFAULT NULL,
  `cidade` VARCHAR(45) NULL DEFAULT NULL,
  `estado` VARCHAR(20) NULL DEFAULT NULL,
  `email` VARCHAR(80) NOT NULL,
  `senha` VARCHAR(200) NOT NULL,
  `telefone` VARCHAR(20) NOT NULL,
  `celular` VARCHAR(20) NULL DEFAULT NULL,
  `situacao` TINYINT(1) NOT NULL,
  `dataCadastro` DATE NOT NULL,
  `permissoes_id` INT NOT NULL,
  `dataExpiracao` date DEFAULT NULL,
  `url_image_user` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`idUsuarios`),
  INDEX `fk_usuarios_permissoes1_idx` (`permissoes_id` ASC),
  CONSTRAINT `fk_usuarios_permissoes1`
    FOREIGN KEY (`permissoes_id`)
    REFERENCES `permissoes` (`idPermissao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;



-- -----------------------------------------------------
-- Table `lancamentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lancamentos` (
  `idLancamentos` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NULL DEFAULT NULL,
  `valor` DECIMAL(10, 2) NULL DEFAULT 0,
  `desconto` DECIMAL(10, 2) NULL DEFAULT 0,
  `valor_desconto` DECIMAL(10, 2) NULL DEFAULT 0,
  `tipo_desconto` varchar(8) NULL DEFAULT NULL,
  `data_vencimento` DATE NOT NULL,
  `data_pagamento` DATE NULL DEFAULT NULL,
  `baixado` TINYINT(1) NULL DEFAULT 0,
  `cliente_fornecedor` VARCHAR(255) NULL DEFAULT NULL,
  `forma_pgto` VARCHAR(100) NULL DEFAULT NULL,
  `tipo` VARCHAR(45) NULL DEFAULT NULL,
  `anexo` VARCHAR(250) NULL,
  `observacoes` TEXT NULL,
  `clientes_id` INT(11) NULL DEFAULT NULL,
  `categorias_id` INT NULL,
  `contas_id` INT NULL,
  `vendas_id` INT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `webhook_notificado` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`idLancamentos`),
  INDEX `fk_lancamentos_clientes1` (`clientes_id` ASC),
  INDEX `fk_lancamentos_categorias1_idx` (`categorias_id` ASC),
  INDEX `fk_lancamentos_contas1_idx` (`contas_id` ASC),
  INDEX `fk_lancamentos_usuarios1` (`usuarios_id` ASC),
  CONSTRAINT `fk_lancamentos_clientes1`
    FOREIGN KEY (`clientes_id`)
    REFERENCES `clientes` (`idClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lancamentos_categorias1`
    FOREIGN KEY (`categorias_id`)
    REFERENCES `categorias` (`idCategorias`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lancamentos_contas1`
    FOREIGN KEY (`contas_id`)
    REFERENCES `contas` (`idContas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lancamentos_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `usuarios` (`idUsuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `Garantia`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `garantias` (
  `idGarantias` INT NOT NULL AUTO_INCREMENT,
  `dataGarantia` DATE NULL,
  `refGarantia` VARCHAR(15) NULL,
  `textoGarantia` TEXT NULL,
  `usuarios_id` INT(11) NULL,
  PRIMARY KEY (`idGarantias`),
  INDEX `fk_garantias_usuarios1` (`usuarios_id` ASC),
  CONSTRAINT `fk_garantias_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `usuarios` (`idUsuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os` (
  `idOs` INT(11) NOT NULL AUTO_INCREMENT,
  `dataInicial` DATE NULL DEFAULT NULL,
  `dataFinal` DATE NULL DEFAULT NULL,
  `garantia` VARCHAR(45) NULL DEFAULT NULL,
  `descricaoProduto` TEXT NULL DEFAULT NULL,
  `defeito` TEXT NULL DEFAULT NULL,
  `status` VARCHAR(45) NULL DEFAULT NULL,
  `observacoes` TEXT NULL DEFAULT NULL,
  `laudoTecnico` TEXT NULL DEFAULT NULL,
  `valorTotal` DECIMAL(10, 2) NULL DEFAULT 0,
  `desconto`DECIMAL(10, 2) NULL DEFAULT 0,
  `valor_desconto` DECIMAL(10, 2) NULL DEFAULT 0,
  `tipo_desconto` varchar(8) NULL DEFAULT NULL,
  `clientes_id` INT(11) NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `lancamento` INT(11) NULL DEFAULT NULL,
  `faturado` TINYINT(1) NOT NULL,
  `garantias_id` int(11) NULL,
  `tecnico_responsavel` INT(11) NULL COMMENT 'ID do usuario tecnico responsavel pela OS',
  `nfse_status` ENUM('Pendente', 'Emitida', 'Cancelada') NOT NULL DEFAULT 'Pendente' COMMENT 'Status da NFS-e vinculada',
  `boleto_status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente' COMMENT 'Status do boleto vinculado',
  `data_vencimento_boleto` DATE NULL DEFAULT NULL COMMENT 'Data de vencimento do boleto',
  `valor_com_impostos` DECIMAL(15, 2) NULL DEFAULT NULL COMMENT 'Valor liquido apos deducao de impostos',
  `certificado_vinculado` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'ID do certificado digital vinculado',
  `retencao_impostos` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag de retencao de impostos',
  `calculo_impostos` TEXT NULL COMMENT 'JSON com detalhes dos impostos calculados',
  PRIMARY KEY (`idOs`),
  INDEX `fk_os_clientes1` (`clientes_id` ASC),
  INDEX `fk_os_usuarios1` (`usuarios_id` ASC),
  INDEX `fk_os_lancamentos1` (`lancamento` ASC),
  INDEX `fk_os_garantias1` (`garantias_id` ASC),
  INDEX `idx_tecnico_responsavel` (`tecnico_responsavel` ASC),
  CONSTRAINT `fk_os_clientes1`
    FOREIGN KEY (`clientes_id`)
    REFERENCES `clientes` (`idClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_os_lancamentos1`
    FOREIGN KEY (`lancamento`)
    REFERENCES `lancamentos` (`idLancamentos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_os_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `usuarios` (`idUsuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `produtos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos` (
  `idProdutos` INT(11) NOT NULL AUTO_INCREMENT,
  `codDeBarra` VARCHAR(70) NOT NULL,
  `descricao` VARCHAR(80) NOT NULL,
  `unidade` VARCHAR(10) NULL DEFAULT NULL,
  `precoCompra` DECIMAL(10,2) NULL DEFAULT NULL,
  `precoVenda` DECIMAL(10,2) NOT NULL,
  `estoque` INT(11) NOT NULL,
  `estoqueMinimo` INT(11) NULL DEFAULT NULL,
  `saida`	TINYINT(1) NULL DEFAULT NULL,
  `entrada`	TINYINT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`idProdutos`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `produtos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `produtos_os` (
  `idProdutos_os` INT(11) NOT NULL AUTO_INCREMENT,
  `quantidade` INT(11) NOT NULL,
  `descricao` VARCHAR(80) NULL,
  `preco` DECIMAL(10,2) NULL DEFAULT 0,
  `os_id` INT(11) NOT NULL,
  `produtos_id` INT(11) NOT NULL,
  `subTotal` DECIMAL(10,2) NULL DEFAULT 0,
  PRIMARY KEY (`idProdutos_os`),
  INDEX `fk_produtos_os_os1` (`os_id` ASC),
  INDEX `fk_produtos_os_produtos1` (`produtos_id` ASC),
  CONSTRAINT `fk_produtos_os_os1`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_produtos_os_produtos1`
    FOREIGN KEY (`produtos_id`)
    REFERENCES `produtos` (`idProdutos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `servicos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos` (
  `idServicos` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(45) NULL DEFAULT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`idServicos`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `servicos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos_os` (
  `idServicos_os` INT(11) NOT NULL AUTO_INCREMENT,
  `servico` VARCHAR(80) NULL,
  `quantidade` DOUBLE NULL,
  `preco` DECIMAL(10,2) NULL DEFAULT 0,
  `os_id` INT(11) NOT NULL,
  `servicos_id` INT(11) NOT NULL,
  `subTotal` DECIMAL(10,2) NULL DEFAULT 0,
  PRIMARY KEY (`idServicos_os`),
  INDEX `fk_servicos_os_os1` (`os_id` ASC),
  INDEX `fk_servicos_os_servicos1` (`servicos_id` ASC),
  CONSTRAINT `fk_servicos_os_os1`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_servicos_os_servicos1`
    FOREIGN KEY (`servicos_id`)
    REFERENCES `servicos` (`idServicos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `vendas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` INT(11) NOT NULL AUTO_INCREMENT,
  `dataVenda` DATE NULL,
  `valorTotal` DECIMAL(10, 2) NULL DEFAULT 0,
  `desconto` DECIMAL(10, 2) NULL DEFAULT 0,
  `valor_desconto` DECIMAL(10, 2) NULL DEFAULT 0,
  `tipo_desconto` varchar(8) NULL DEFAULT NULL,
  `faturado` TINYINT(1) NULL,
  `observacoes` TEXT NULL,
  `observacoes_cliente` TEXT NULL,
  `clientes_id` INT(11) NOT NULL,
  `usuarios_id` INT(11) NULL,
  `lancamentos_id` INT(11) NULL,
  `status` VARCHAR(45) NULL,
  `garantia` INT(11) NULL,
  PRIMARY KEY (`idVendas`),
  INDEX `fk_vendas_clientes1` (`clientes_id` ASC),
  INDEX `fk_vendas_usuarios1` (`usuarios_id` ASC),
  INDEX `fk_vendas_lancamentos1` (`lancamentos_id` ASC),
  CONSTRAINT `fk_vendas_clientes1`
    FOREIGN KEY (`clientes_id`)
    REFERENCES `clientes` (`idClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vendas_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `usuarios` (`idUsuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vendas_lancamentos1`
    FOREIGN KEY (`lancamentos_id`)
    REFERENCES `lancamentos` (`idLancamentos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


--
-- Estrutura da tabela `cobrancas`
--
CREATE TABLE IF NOT EXISTS `cobrancas` (
  `idCobranca` INT(11) NOT NULL AUTO_INCREMENT,
  `charge_id` varchar(255) DEFAULT NULL,
  `conditional_discount_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` DATETIME NULL DEFAULT NULL,
  `custom_id` int(11) DEFAULT NULL,
  `expire_at` date NOT NULL,
  `paid_at` DATETIME NULL DEFAULT NULL,
  `message` TEXT NULL,
  `payment_method` varchar(11) DEFAULT NULL,
  `payment_url` varchar(255) DEFAULT NULL,
  `request_delivery_address` varchar(64) DEFAULT NULL,
  `status` varchar(36) NOT NULL,
  `total` varchar(15) DEFAULT NULL,
  `barcode` varchar(255) NOT NULL,
  `linha_digitavel` varchar(255) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `pix_code` TEXT NULL,
  `payment_gateway` varchar(255) NULL DEFAULT NULL,
  `payment` varchar(64) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `vendas_id` int(11) DEFAULT NULL,
  `os_id` int(11) DEFAULT NULL,
  `clientes_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCobranca`),
  INDEX `fk_cobrancas_os1` (`os_id` ASC),
  CONSTRAINT `fk_cobrancas_os1` FOREIGN KEY (`os_id`) REFERENCES `os` (`idOs`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  INDEX `fk_cobrancas_vendas1` (`vendas_id` ASC),
  CONSTRAINT `fk_cobrancas_vendas1` FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  INDEX `fk_cobrancas_clientes1` (`clientes_id` ASC),
  CONSTRAINT `fk_cobrancas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  INDEX `idx_cobrancas_charge_id` (`charge_id`),
  INDEX `idx_cobrancas_status_gateway` (`status`, `payment_gateway`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `itens_de_vendas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `itens_de_vendas` (
  `idItens` INT NOT NULL AUTO_INCREMENT,
  `subTotal` DECIMAL(10,2) NULL DEFAULT 0,
  `quantidade` INT(11) NULL,
  `preco` DECIMAL(10,2) NULL DEFAULT 0,
  `vendas_id` INT NOT NULL,
  `produtos_id` INT(11) NOT NULL,
  PRIMARY KEY (`idItens`),
  INDEX `fk_itens_de_vendas_vendas1` (`vendas_id` ASC),
  INDEX `fk_itens_de_vendas_produtos1` (`produtos_id` ASC),
  CONSTRAINT `fk_itens_de_vendas_vendas1`
    FOREIGN KEY (`vendas_id`)
    REFERENCES `vendas` (`idVendas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_itens_de_vendas_produtos1`
    FOREIGN KEY (`produtos_id`)
    REFERENCES `produtos` (`idProdutos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `anexos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anexos` (
  `idAnexos` INT NOT NULL AUTO_INCREMENT,
  `anexo` VARCHAR(45) NULL,
  `thumb` VARCHAR(45) NULL,
  `url` VARCHAR(300) NULL,
  `path` VARCHAR(300) NULL,
  `os_id` INT(11) NOT NULL,
  PRIMARY KEY (`idAnexos`),
  INDEX `fk_anexos_os1` (`os_id` ASC),
  CONSTRAINT `fk_anexos_os1`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `documentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `documentos` (
  `idDocumentos` INT NOT NULL AUTO_INCREMENT,
  `documento` VARCHAR(70) NULL,
  `descricao` TEXT NULL,
  `file` VARCHAR(100) NULL,
  `path` VARCHAR(300) NULL,
  `url` VARCHAR(300) NULL,
  `cadastro` DATE NULL,
  `categoria` VARCHAR(80) NULL,
  `tipo` VARCHAR(15) NULL,
  `tamanho` VARCHAR(45) NULL,
  PRIMARY KEY (`idDocumentos`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `marcas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `marcas` (
  `idMarcas` INT NOT NULL AUTO_INCREMENT,
  `marca` VARCHAR(100) NULL,
  `cadastro` DATE NULL,
  `situacao` TINYINT(1) NULL,
  PRIMARY KEY (`idMarcas`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `equipamentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipamentos` (
  `idEquipamentos` INT NOT NULL AUTO_INCREMENT,
  `equipamento` VARCHAR(150) NOT NULL,
  `num_serie` VARCHAR(80) NULL,
  `modelo` VARCHAR(80) NULL,
  `cor` VARCHAR(45) NULL,
  `descricao` VARCHAR(150) NULL,
  `tensao` VARCHAR(45) NULL,
  `potencia` VARCHAR(45) NULL,
  `voltagem` VARCHAR(45) NULL,
  `data_fabricacao` DATE NULL,
  `marcas_id` INT NULL,
  `clientes_id` INT(11) NULL,
  PRIMARY KEY (`idEquipamentos`),
  INDEX `fk_equipanentos_marcas1_idx` (`marcas_id` ASC),
  INDEX `fk_equipanentos_clientes1_idx` (`clientes_id` ASC),
  CONSTRAINT `fk_equipanentos_marcas1`
    FOREIGN KEY (`marcas_id`)
    REFERENCES `marcas` (`idMarcas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_equipanentos_clientes1`
    FOREIGN KEY (`clientes_id`)
    REFERENCES `clientes` (`idClientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `equipamentos_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `equipamentos_os` (
  `idEquipamentos_os` INT NOT NULL AUTO_INCREMENT,
  `defeito_declarado` VARCHAR(200) NULL,
  `defeito_encontrado` VARCHAR(200) NULL,
  `solucao` VARCHAR(45) NULL,
  `equipamentos_id` INT NULL,
  `os_id` INT(11) NULL,
  PRIMARY KEY (`idEquipamentos_os`),
  INDEX `fk_equipamentos_os_equipanentos1_idx` (`equipamentos_id` ASC),
  INDEX `fk_equipamentos_os_os1_idx` (`os_id` ASC),
  CONSTRAINT `fk_equipamentos_os_equipanentos1`
    FOREIGN KEY (`equipamentos_id`)
    REFERENCES `equipamentos` (`idEquipamentos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_equipamentos_os_os1`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- -----------------------------------------------------
-- Table `logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
  `idLogs` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(80) NULL,
  `tarefa` VARCHAR(100) NULL,
  `data` DATE NULL,
  `hora` TIME NULL,
  `ip` VARCHAR(45) NULL,
  PRIMARY KEY (`idLogs`))
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `emitente`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `emitente` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(255) NULL ,
  `cnpj` VARCHAR(45) NULL ,
  `ie` VARCHAR(50) NULL ,
  `rua` VARCHAR(70) NULL ,
  `numero` VARCHAR(15) NULL ,
  `bairro` VARCHAR(45) NULL ,
  `cidade` VARCHAR(45) NULL ,
  `uf` VARCHAR(20) NULL ,
  `telefone` VARCHAR(20) NULL ,
  `email` VARCHAR(255) NULL ,
  `url_logo` VARCHAR(225) NULL ,
  `cep` VARCHAR(20) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `email_queue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `to_email` VARCHAR(255) NOT NULL,
  `to_name` VARCHAR(255) NULL,
  `subject` VARCHAR(500) NOT NULL,
  `body_html` LONGTEXT NULL,
  `body_text` LONGTEXT NULL,
  `template` VARCHAR(100) NULL,
  `template_data` TEXT NULL,
  `attachments` TEXT NULL,
  `priority` TINYINT(1) DEFAULT 3,
  `status` ENUM('pending', 'processing', 'sent', 'failed', 'cancelled', 'scheduled') DEFAULT 'pending',
  `attempts` TINYINT(1) DEFAULT 0,
  `max_retries` TINYINT(1) DEFAULT 3,
  `tracking_id` VARCHAR(32) NULL,
  `message_id` VARCHAR(255) NULL,
  `scheduled_at` DATETIME NULL,
  `sent_at` DATETIME NULL,
  `opened_at` DATETIME NULL,
  `clicked_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `last_attempt` DATETIME NULL,
  `failed_at` DATETIME NULL,
  `error_message` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_priority` (`priority`),
  INDEX `idx_scheduled_at` (`scheduled_at`),
  INDEX `idx_tracking_id` (`tracking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `email_tracking`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_tracking` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email_queue_id` INT(11) UNSIGNED NOT NULL,
  `tracking_id` VARCHAR(64) NOT NULL UNIQUE,
  `opened` TINYINT(1) DEFAULT 0,
  `opened_at` DATETIME NULL,
  `clicked` TINYINT(1) DEFAULT 0,
  `clicked_at` DATETIME NULL,
  `clicked_url` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_tracking_id` (`tracking_id`),
  INDEX `idx_email_queue_id` (`email_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `email_clicks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_clicks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tracking_id` VARCHAR(32) NOT NULL,
  `url` TEXT NOT NULL,
  `clicked_at` DATETIME NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  INDEX `idx_tracking_id` (`tracking_id`),
  INDEX `idx_clicked_at` (`clicked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `scheduled_events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `scheduled_events` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(100) NOT NULL,
  `event_data` JSON NULL,
  `execute_at` DATETIME NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  `executed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_execute_at` (`execute_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `webhooks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `webhooks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `secret` VARCHAR(255) NULL,
  `events` JSON NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `webhook_logs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `webhook_id` INT(11) UNSIGNED NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `payload` TEXT NULL,
  `response` TEXT NULL,
  `status_code` INT NULL,
  `success` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_webhook_id` (`webhook_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `certificado_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificado_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_emitente` INT(11) UNSIGNED NOT NULL,
  `certificado_p12` LONGTEXT NULL,
  `senha_certificado` VARCHAR(255) NULL,
  `cnpj_certificado` VARCHAR(14) NULL,
  `valido_de` DATETIME NULL,
  `valido_ate` DATETIME NULL,
  `arquivo_crt` LONGTEXT NULL,
  `arquivo_key` LONGTEXT NULL,
  `ambiente` ENUM('homologacao', 'producao') DEFAULT 'homologacao',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_id_emitente` (`id_emitente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `nfse_importada`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `nfse_importada` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_os` INT(11) UNSIGNED NULL,
  `numero_nfse` VARCHAR(50) NOT NULL,
  `codigo_verificacao` VARCHAR(50) NULL,
  `data_emissao` DATETIME NULL,
  `valor_servico` DECIMAL(10,2) NULL,
  `valor_liquido` DECIMAL(10,2) NULL,
  `prestador_cnpj` VARCHAR(14) NULL,
  `prestador_nome` VARCHAR(255) NULL,
  `tomador_cnpj` VARCHAR(14) NULL,
  `tomador_nome` VARCHAR(255) NULL,
  `status` ENUM('ativa', 'cancelada') DEFAULT 'ativa',
  `xml_content` LONGTEXT NULL,
  `pdf_content` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_id_os` (`id_os`),
  INDEX `idx_numero_nfse` (`numero_nfse`),
  INDEX `idx_prestador_cnpj` (`prestador_cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `dre_contas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_contas` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(50) NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `tipo` ENUM('receita', 'custo', 'despesa') NOT NULL,
  `grupo` VARCHAR(100) NULL,
  `sinal` ENUM('POSITIVO','NEGATIVO') DEFAULT 'POSITIVO',
  `conta_pai_id` INT(11) UNSIGNED NULL,
  `nivel` INT DEFAULT 1,
  `ordem` INT DEFAULT 0,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `dre_lancamentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_lancamentos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conta_id` INT(11) UNSIGNED NOT NULL,
  `data` DATE NOT NULL,
  `valor` DECIMAL(15,2) NOT NULL,
  `tipo_movimento` ENUM('CREDITO','DEBITO') DEFAULT 'CREDITO',
  `descricao` TEXT NULL,
  `documento` VARCHAR(100) NULL,
  `os_id` INT(11) UNSIGNED NULL,
  `venda_id` INT(11) UNSIGNED NULL,
  `lancamento_id` INT(11) UNSIGNED NULL,
  `usuarios_id` INT(11) UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_conta_id` (`conta_id`),
  INDEX `idx_data_referencia` (`data`),
  INDEX `idx_os_id` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `impostos_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `impostos_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tipo_regime` ENUM('simples_nacional', 'lucro_presumido', 'lucro_real') DEFAULT 'simples_nacional',
  `anexo_simples` ENUM('I', 'II', 'III', 'IV', 'V') NULL,
  `aliquota_iss` DECIMAL(5,2) DEFAULT 0,
  `retem_iss` TINYINT(1) DEFAULT 0,
  `aliquota_pis` DECIMAL(5,2) DEFAULT 0,
  `aliquota_cofins` DECIMAL(5,2) DEFAULT 0,
  `aliquota_csll` DECIMAL(5,2) DEFAULT 0,
  `aliquota_ir` DECIMAL(5,2) DEFAULT 0,
  `aliquota_inss` DECIMAL(5,2) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `impostos_retidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `impostos_retidos` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_os` INT(11) UNSIGNED NULL,
  `id_venda` INT(11) UNSIGNED NULL,
  `tipo_imposto` ENUM('iss', 'pis', 'cofins', 'csll', 'ir', 'inss') NOT NULL,
  `base_calculo` DECIMAL(15,2) NOT NULL,
  `aliquota` DECIMAL(5,2) NOT NULL,
  `valor_retido` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_id_os` (`id_os`),
  INDEX `idx_id_venda` (`id_venda`),
  INDEX `idx_tipo_imposto` (`tipo_imposto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `push_notifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `push_notifications` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `data` JSON NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `checkin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `checkin` (
  `idCheckin` INT NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `tecnico_id` INT(11) NOT NULL,
  `tipo` ENUM('inicio', 'pausa', 'retorno', 'finalizacao', 'checkin', 'checkout') NOT NULL,
  `data_hora` DATETIME NOT NULL,
  `observacao` TEXT NULL,
  `foto` VARCHAR(255) NULL,
  `latitude` DECIMAL(10, 8) NULL,
  `longitude` DECIMAL(11, 8) NULL,
  `localizacao` VARCHAR(255) NULL,
  PRIMARY KEY (`idCheckin`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `os_tecnico_atribuicao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_tecnico_atribuicao` (
  `idAtribuicao` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `tecnico_id` INT(11) NOT NULL COMMENT 'ID do tecnico atribuido',
  `atribuido_por` INT(11) NOT NULL COMMENT 'ID do usuario que fez a atribuicao',
  `data_atribuicao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_remocao` DATETIME NULL,
  `motivo_remocao` TEXT NULL,
  `observacao` TEXT NULL,
  PRIMARY KEY (`idAtribuicao`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_tecnico_id` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `fotos_atendimento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fotos_atendimento` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `checkin_id` INT(11) NOT NULL,
  `os_id` INT(11) NOT NULL,
  `imagem` LONGBLOB NOT NULL,
  `imagem_base64` LONGTEXT NULL,
  `mime_type` VARCHAR(30) NULL,
  `tipo` ENUM('antes', 'depois', 'assinatura', 'outro') DEFAULT 'outro',
  `data` DATETIME NOT NULL,
  INDEX `idx_checkin_id` (`checkin_id`),
  INDEX `idx_os_id` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `os_status_history`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_status_history` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT(11) NOT NULL,
  `status_antigo` VARCHAR(45) NULL,
  `status_novo` VARCHAR(45) NOT NULL,
  `usuario_id` INT(11) NULL,
  `observacao` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `anotacaoes_os`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anotacoes_os` (
    `idAnotacoes` INT(11) NOT NULL AUTO_INCREMENT,
    `anotacao` VARCHAR(255) NOT NULL ,
    `data_hora` DATETIME NOT NULL ,
    `os_id` INT(11) NOT NULL ,
    PRIMARY KEY (`idAnotacoes`)
) ENGINE = InnoDB
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `configuracoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracoes` ( 
  `idConfig` INT NOT NULL AUTO_INCREMENT , `config` VARCHAR(20) NOT NULL UNIQUE, `valor` TEXT NULL , PRIMARY KEY (`idConfig`)
  ) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `migrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `version` BIGINT(20) NOT NULL
);

INSERT IGNORE INTO `configuracoes` (`idConfig`, `config`, `valor`) VALUES
(2, 'app_name', 'Map-OS'),
(3, 'app_theme', 'white'),
(4, 'per_page', '10'),
(5, 'os_notification', 'cliente'),
(6, 'control_estoque', '1'),
(7, 'notifica_whats', 'Prezado(a), {CLIENTE_NOME} a OS de nº {NUMERO_OS} teve o status alterado para: {STATUS_OS} segue a descrição {DESCRI_PRODUTOS} com valor total de {VALOR_OS}! Para mais informações entre em contato conosco. Atenciosamente, {EMITENTE} {TELEFONE_EMITENTE}.'),
(8, 'control_baixa', '0'),
(9, 'control_editos', '1'),
(10, 'control_datatable', '1'),
(11, 'pix_key', ''),
(12, 'os_status_list', '[\"Aberto\",\"Faturado\",\"Negocia\\u00e7\\u00e3o\",\"Em Andamento\",\"Or\\u00e7amento\",\"Finalizado\",\"Cancelado\",\"Aguardando Pe\\u00e7as\",\"Aprovado\"]'),
(13, 'control_edit_vendas', '1'),
(14, 'email_automatico', '1'),
(15, 'control_2vias', '0');

-- -----------------------------------------------------
-- Table `os_nfse_emitida` - Notas fiscais de serviço emitidas
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_nfse_emitida` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL COMMENT 'ID da OS vinculada',
  `numero_nfse` VARCHAR(20) NULL COMMENT 'Número da NFS-e',
  `chave_acesso` VARCHAR(50) NULL,
  `data_emissao` DATETIME NULL,
  `valor_servicos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_deducoes` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `aliquota_iss` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `valor_iss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_inss` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_irrf` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_csll` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_pis` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_cofins` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_total_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `situacao` ENUM('Pendente', 'Emitida', 'Cancelada', 'Substituida') NOT NULL DEFAULT 'Pendente',
  `codigo_verificacao` VARCHAR(20) NULL,
  `link_impressao` VARCHAR(500) NULL,
  `xml_path` VARCHAR(500) NULL,
  `protocolo` VARCHAR(50) NULL,
  `mensagem_retorno` TEXT NULL,
  `cobranca_id` INT(11) NULL COMMENT 'ID da cobrança/boleto vinculado',
  `emitido_por` INT(11) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_numero_nfse` (`numero_nfse`),
  CONSTRAINT `fk_nfse_emitida_os`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_boleto_emitido` - Boletos gerados para OS
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_boleto_emitido` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `nfse_id` INT(11) UNSIGNED NULL COMMENT 'ID da NFS-e vinculada',
  `nosso_numero` VARCHAR(50) NULL,
  `linha_digitavel` VARCHAR(60) NULL,
  `codigo_barras` VARCHAR(44) NULL,
  `data_emissao` DATE NULL,
  `data_vencimento` DATE NULL,
  `data_pagamento` DATE NULL,
  `valor_original` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_desconto_impostos` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor descontado dos impostos (NFSe)',
  `valor_liquido` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_pago` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `multa` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `juros` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('Pendente', 'Emitido', 'Pago', 'Vencido', 'Cancelado') NOT NULL DEFAULT 'Pendente',
  `instrucoes` TEXT NULL,
  `sacado_nome` VARCHAR(255) NULL,
  `sacado_documento` VARCHAR(20) NULL,
  `sacado_endereco` VARCHAR(500) NULL,
  `pdf_path` VARCHAR(500) NULL,
  `remessa_id` INT(11) NULL,
  `retorno_id` INT(11) NULL,
  `gateway` VARCHAR(50) NULL COMMENT 'Gateway de pagamento usado',
  `gateway_transaction_id` VARCHAR(100) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_nfse_id` (`nfse_id`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_boleto_emitido_os`
    FOREIGN KEY (`os_id`)
    REFERENCES `os` (`idOs`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_boleto_emitido_nfse`
    FOREIGN KEY (`nfse_id`)
    REFERENCES `os_nfse_emitida` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- NOTA: As permissões e usuário administrador são criados pelo instalador (do_install.php)
-- para evitar problemas de serialização. O instalador usa PHP para gerar os dados corretamente.
-- Veja install/do_install.php para os inserts programáticos.

INSERT IGNORE INTO `usuarios` (`idUsuarios`, `nome`, `rg`, `cpf`, `cep`, `rua`, `numero`, `bairro`, `cidade`, `estado`, `email`, `senha`, `telefone`, `celular`, `situacao`, `dataCadastro`, `permissoes_id`,`dataExpiracao`) VALUES
(1, 'admin_name', '', '', '', '', '', '', '', '', 'admin_email', 'admin_password', '', '', 1, 'admin_created_at', 1, '3000-01-01');

INSERT IGNORE INTO `migrations`(`version`) VALUES ('20260413000003');

-- Dados iniciais V5
INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `tipo`, `grupo`, `sinal`, `nivel`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
('1', 'RECEITA BRUTA', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 1, 1, 1, NOW(), NOW()),
('1.1', 'Receita de Serviços', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 2, 2, 1, NOW(), NOW()),
('1.2', 'Receita de Produtos', 'receita', 'RECEITA_BRUTA', 'POSITIVO', 2, 3, 1, NOW(), NOW()),
('1.3', 'Outras Receitas', 'receita', 'OUTRAS_RECEITAS', 'POSITIVO', 2, 4, 1, NOW(), NOW()),
('2', 'DEDUÇÕES DA RECEITA', 'despesa', 'DEDUCOES', 'NEGATIVO', 1, 5, 1, NOW(), NOW()),
('2.1', 'ISS', 'despesa', 'DEDUCOES', 'NEGATIVO', 2, 6, 1, NOW(), NOW()),
('3', 'CUSTO DOS SERVIÇOS', 'custo', 'CUSTO', 'NEGATIVO', 1, 10, 1, NOW(), NOW()),
('4', 'DESPESAS OPERACIONAIS', 'despesa', 'DESPESA_OPERACIONAL', 'NEGATIVO', 1, 20, 1, NOW(), NOW()),
('6', 'IMPOSTO DE RENDA E CONTRIBUIÇÕES', 'despesa', 'IMPOSTO_RENDA', 'NEGATIVO', 1, 30, 1, NOW(), NOW()),
('7', 'OUTRAS DESPESAS', 'despesa', 'OUTRAS_DESPESAS', 'NEGATIVO', 1, 35, 1, NOW(), NOW());

INSERT IGNORE INTO `impostos_config` (`tipo_regime`, `anexo_simples`, `aliquota_iss`, `retem_iss`, `created_at`, `updated_at`) VALUES
('simples_nacional', 'III', 2.00, 0, 'admin_created_at', 'admin_created_at');

-- -----------------------------------------------------
-- TABELAS DO SISTEMA DE TÉCNICOS - MapOS v5
-- Adicionadas em: 2026-04-13
-- -----------------------------------------------------

-- Adicionar campos na tabela usuarios para técnicos
ALTER TABLE `usuarios`
ADD COLUMN IF NOT EXISTS `is_tecnico` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica se é técnico de campo' AFTER `url_image_user`,
ADD COLUMN IF NOT EXISTS `nivel_tecnico` ENUM('I','II','III','IV') DEFAULT 'II' COMMENT 'Nível do técnico: I=Aprendiz, II=Técnico, III=Especialista, IV=Coordenador' AFTER `is_tecnico`,
ADD COLUMN IF NOT EXISTS `especialidades` VARCHAR(255) DEFAULT NULL COMMENT 'Especialidades separadas por vírgula: CFTV,Alarmes,Redes,ControleAcesso' AFTER `nivel_tecnico`,
ADD COLUMN IF NOT EXISTS `veiculo_placa` VARCHAR(10) DEFAULT NULL AFTER `especialidades`,
ADD COLUMN IF NOT EXISTS `veiculo_tipo` ENUM('Moto','Carro','Nenhum') DEFAULT 'Nenhum' AFTER `veiculo_placa`,
ADD COLUMN IF NOT EXISTS `coordenadas_base_lat` DECIMAL(10,8) DEFAULT NULL COMMENT 'Latitude da base/matriz' AFTER `veiculo_tipo`,
ADD COLUMN IF NOT EXISTS `coordenadas_base_lng` DECIMAL(11,8) DEFAULT NULL COMMENT 'Longitude da base/matriz' AFTER `coordenadas_base_lat`,
ADD COLUMN IF NOT EXISTS `raio_atuacao_km` INT DEFAULT 50 COMMENT 'Raio máximo de atuação em km' AFTER `coordenadas_base_lng`,
ADD COLUMN IF NOT EXISTS `plantao_24h` TINYINT(1) DEFAULT 0 AFTER `raio_atuacao_km`,
ADD COLUMN IF NOT EXISTS `app_tecnico_instalado` TINYINT(1) DEFAULT 0 AFTER `plantao_24h`,
ADD COLUMN IF NOT EXISTS `token_app` VARCHAR(255) DEFAULT NULL COMMENT 'Token para notificações push' AFTER `app_tecnico_instalado`,
ADD COLUMN IF NOT EXISTS `token_expira` DATETIME DEFAULT NULL AFTER `token_app`,
ADD COLUMN IF NOT EXISTS `ultimo_acesso_app` DATETIME DEFAULT NULL AFTER `token_expira`,
ADD COLUMN IF NOT EXISTS `foto_tecnico` VARCHAR(255) DEFAULT NULL AFTER `ultimo_acesso_app`;

-- -----------------------------------------------------
-- Table `servicos_catalogo` - Catálogo de serviços técnico
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `servicos_catalogo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) UNIQUE,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT,
  `categoria` VARCHAR(100) DEFAULT 'Geral' COMMENT 'CFTV, Alarme, Rede, ControleAcesso, etc',
  `especialidade` VARCHAR(50) DEFAULT NULL COMMENT 'Qual técnico pode executar',
  `tempo_estimado_minutos` INT DEFAULT 60,
  `checklist_padrao` JSON DEFAULT NULL COMMENT 'Checklist em formato JSON',
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_categoria` (`categoria`),
  INDEX `idx_especialidade` (`especialidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_servicos` - Serviços vinculados à OS
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_servicos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `servico_id` INT(11) NOT NULL,
  `quantidade` INT DEFAULT 1,
  `observacao` TEXT COMMENT 'Detalhes específicos desta OS',
  `status` ENUM('Pendente','EmExecucao','Concluido','Cancelado') DEFAULT 'Pendente',
  `checklist_execucao` JSON DEFAULT NULL COMMENT 'Itens marcados pelo técnico',
  `checklist_completude` INT DEFAULT 0 COMMENT 'Percentual 0-100',
  `tecnico_id` INT(11) DEFAULT NULL COMMENT 'Quem executou',
  `data_inicio` DATETIME DEFAULT NULL,
  `data_conclusao` DATETIME DEFAULT NULL,
  `tempo_execucao_minutos` INT DEFAULT NULL,
  `fotos` JSON DEFAULT NULL COMMENT 'URLs das fotos',
  `assinatura_cliente` TEXT COMMENT 'Base64 da imagem',
  `laudo_tecnico` TEXT COMMENT 'Descrição do serviço executado',
  `ordem_execucao` INT DEFAULT 0 COMMENT 'Ordem na OS',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE,
  FOREIGN KEY (`servico_id`) REFERENCES `servicos_catalogo`(`id`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_tecnico` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tec_os_execucao` - Execução técnica detalhada
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tec_os_execucao` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `tecnico_id` INT(11) NOT NULL,
  `tipo_servico` ENUM('INS','MP','MC','CT','TR','UP','URG') DEFAULT 'MC' COMMENT 'INS=Instalação, MP=Manut.Prev, MC=Manut.Corr, CT=Consultoria, TR=Treinamento, UP=Upgrade, URG=Urgência',
  `especialidade` VARCHAR(50) DEFAULT NULL,

  -- Check-in
  `checkin_horario` DATETIME DEFAULT NULL,
  `checkin_latitude` DECIMAL(10,8) DEFAULT NULL,
  `checkin_longitude` DECIMAL(11,8) DEFAULT NULL,
  `checkin_endereco` VARCHAR(255) DEFAULT NULL,
  `checkin_foto` VARCHAR(255) DEFAULT NULL,
  `checkin_distancia_metros` INT DEFAULT NULL COMMENT 'Distância do cliente',

  -- Check-out
  `checkout_horario` DATETIME DEFAULT NULL,
  `checkout_latitude` DECIMAL(10,8) DEFAULT NULL,
  `checkout_longitude` DECIMAL(11,8) DEFAULT NULL,
  `checkout_endereco` VARCHAR(255) DEFAULT NULL,
  `checkout_foto` VARCHAR(255) DEFAULT NULL,
  `checkout_distancia_metros` INT DEFAULT NULL,

  -- Tempos
  `tempo_atendimento_minutos` INT DEFAULT NULL,
  `tempo_deslocamento_minutos` INT DEFAULT NULL,
  `km_deslocamento` DECIMAL(10,2) DEFAULT NULL,

  -- Checklist e fotos
  `checklist_json` JSON DEFAULT NULL,
  `checklist_completude` INT DEFAULT 0,
  `fotos_antes` JSON DEFAULT NULL,
  `fotos_depois` JSON DEFAULT NULL,
  `fotos_durante` JSON DEFAULT NULL,

  -- Cliente
  `assinatura_cliente` TEXT,
  `nome_responsavel` VARCHAR(255) DEFAULT NULL,
  `avaliacao` INT DEFAULT NULL COMMENT '1-5 estrelas',
  `comentario_cliente` TEXT,

  -- Técnico
  `laudo_tecnico` TEXT,
  `materiais_utilizados` JSON DEFAULT NULL,
  `observacoes_tecnico` TEXT,
  `problema_encontrado` TEXT,
  `solucao_aplicada` TEXT,
  `recomendacoes` TEXT,
  `oportunidade_venda` TINYINT(1) DEFAULT 0,
  `descricao_oportunidade` TEXT,

  -- Status
  `status_execucao` ENUM('Agendada','EmDeslocamento','EmAtendimento','Pausada','Concluida','Cancelada') DEFAULT 'Agendada',
  `aprovada` TINYINT(1) DEFAULT 0,
  `aprovada_por` INT(11) DEFAULT NULL,
  `data_aprovacao` DATETIME DEFAULT NULL,

  -- Controle
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE,
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_tecnico_id` (`tecnico_id`),
  INDEX `idx_data_execucao` (`checkin_horario`),
  INDEX `idx_status` (`status_execucao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tec_checklist_template` - Templates de checklists
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tec_checklist_template` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo_os` VARCHAR(50) NOT NULL COMMENT 'CFTV, Alarme, Rede, etc',
  `tipo_servico` ENUM('INS','MP','MC','CT','TR','UP') DEFAULT 'MC',
  `nome_template` VARCHAR(100),
  `itens` JSON NOT NULL COMMENT 'Itens do checklist em JSON',
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_template` (`tipo_os`, `tipo_servico`),
  INDEX `idx_tipo_os` (`tipo_os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tec_estoque_veiculo` - Estoque no veículo do técnico
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tec_estoque_veiculo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tecnico_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `quantidade_disponivel` INT DEFAULT 0,
  `quantidade_reservada` INT DEFAULT 0 COMMENT 'Para OS agendadas',
  `localizacao` ENUM('Veiculo','EmUso','Retirado') DEFAULT 'Veiculo',
  `ultima_movimentacao` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`idProdutos`),
  UNIQUE KEY `uk_tecnico_produto` (`tecnico_id`, `produto_id`),
  INDEX `idx_tecnico` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tec_rotas_tracking` - Histórico de rotas
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tec_rotas_tracking` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tecnico_id` INT(11) NOT NULL,
  `data` DATE NOT NULL,
  `pontos_rota` JSON DEFAULT NULL COMMENT 'Array de pontos GPS',
  `km_total` DECIMAL(10,2) DEFAULT 0,
  `os_atendidas` INT DEFAULT 0,
  `tempo_total_horas` DECIMAL(5,2) DEFAULT 0,
  `combustivel_estimado` DECIMAL(10,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  INDEX `idx_tecnico_data` (`tecnico_id`, `data`),
  UNIQUE KEY `uk_tecnico_dia` (`tecnico_id`, `data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `obras` - Cadastro de obras grandes
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `obras` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(50) UNIQUE,
  `nome` VARCHAR(255) NOT NULL,
  `cliente_id` INT(11) NOT NULL,
  `tipo_obra` ENUM('Condominio','Comercio','Residencia','Industrial','Publica') DEFAULT 'Condominio',
  `especialidade_principal` VARCHAR(50) DEFAULT NULL,
  `endereco` TEXT,
  `bairro` VARCHAR(100) DEFAULT NULL,
  `cidade` VARCHAR(100) DEFAULT NULL,
  `estado` VARCHAR(2) DEFAULT NULL,
  `cep` VARCHAR(9) DEFAULT NULL,
  `coordenadas_lat` DECIMAL(10,8) DEFAULT NULL,
  `coordenadas_lng` DECIMAL(11,8) DEFAULT NULL,
  `data_inicio_contrato` DATE DEFAULT NULL,
  `data_fim_prevista` DATE DEFAULT NULL,
  `data_fim_real` DATE DEFAULT NULL,
  `prazo_dias` INT DEFAULT NULL,
  `status` ENUM('Prospeccao','Orcamentacao','Contratada','EmExecucao','Paralisada','Finalizada','Entregue','Garantia') DEFAULT 'Prospeccao',
  `percentual_concluido` INT DEFAULT 0,
  `gestor_obra_id` INT(11) DEFAULT NULL,
  `responsavel_tecnico_id` INT(11) DEFAULT NULL,
  `responsavel_comercial_id` INT(11) DEFAULT NULL,
  `contrato_arquivo` VARCHAR(255) DEFAULT NULL,
  `projeto_arquivo` VARCHAR(255) DEFAULT NULL,
  `art_arquivo` VARCHAR(255) DEFAULT NULL,
  `memorial_descritivo` TEXT,
  `observacoes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`idClientes`),
  FOREIGN KEY (`gestor_obra_id`) REFERENCES `usuarios`(`idUsuarios`),
  FOREIGN KEY (`responsavel_tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  INDEX `idx_cliente` (`cliente_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `obra_etapas` - Cronograma de obras
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `obra_etapas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `obra_id` INT(11) NOT NULL,
  `numero_etapa` INT DEFAULT 1,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT,
  `especialidade` VARCHAR(50) DEFAULT NULL,
  `data_inicio_prevista` DATE DEFAULT NULL,
  `data_fim_prevista` DATE DEFAULT NULL,
  `data_inicio_real` DATE DEFAULT NULL,
  `data_fim_real` DATE DEFAULT NULL,
  `percentual_concluido` INT DEFAULT 0,
  `status` ENUM('NaoIniciada','EmAndamento','Concluida','Atrasada','Paralisada') DEFAULT 'NaoIniciada',
  `tecnicos_designados` JSON DEFAULT NULL,
  `os_ids` JSON DEFAULT NULL COMMENT 'OS vinculadas',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`obra_id`) REFERENCES `obras`(`id`) ON DELETE CASCADE,
  INDEX `idx_obra_id` (`obra_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `obra_diario` - Diário de obra
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `obra_diario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `obra_id` INT(11) NOT NULL,
  `data` DATE NOT NULL,
  `clima_manha` ENUM('Sol','Nublado','Chuva','Garoa') DEFAULT NULL,
  `clima_tarde` ENUM('Sol','Nublado','Chuva','Garoa') DEFAULT NULL,
  `equipe_presente` JSON DEFAULT NULL COMMENT 'Array de técnicos presentes',
  `atividades_executadas` TEXT,
  `etapas_avancadas` JSON DEFAULT NULL,
  `fotos` JSON DEFAULT NULL,
  `problemas` TEXT,
  `acoes_corretivas` TEXT,
  `material_recebido` TEXT,
  `material_consumido` TEXT,
  `visitas_cliente` TINYINT(1) DEFAULT 0,
  `visitas_fiscalizacao` TINYINT(1) DEFAULT 0,
  `preenchido_por` INT(11) DEFAULT NULL,
  `preenchido_em` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`obra_id`) REFERENCES `obras`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`preenchido_por`) REFERENCES `usuarios`(`idUsuarios`),
  UNIQUE KEY `uk_obra_data` (`obra_id`, `data`),
  INDEX `idx_data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `obra_equipe` - Equipe de técnicos alocados em obras
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `obra_equipe` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `obra_id` INT(11) NOT NULL,
  `tecnico_id` INT(11) NOT NULL,
  `funcao` VARCHAR(50) DEFAULT 'Técnico' COMMENT 'Função na obra: Técnico, Encarregado, Supervisor',
  `data_entrada` DATE NOT NULL,
  `data_saida` DATE DEFAULT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `observacoes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`obra_id`) REFERENCES `obras`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  UNIQUE KEY `uk_obra_tecnico` (`obra_id`, `tecnico_id`, `data_entrada`),
  INDEX `idx_tecnico` (`tecnico_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tec_estoque_historico` - Histórico de movimentação de estoque
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tec_estoque_historico` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tecnico_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `tipo` ENUM('entrada','saida') NOT NULL COMMENT 'entrada=abastecimento, saida=uso em OS',
  `quantidade` INT NOT NULL,
  `os_id` INT(11) DEFAULT NULL COMMENT 'OS relacionada, se aplicável',
  `observacao` VARCHAR(255) DEFAULT NULL,
  `data_hora` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `registrado_por` INT(11) DEFAULT NULL COMMENT 'Admin que registrou, se não foi o técnico',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`idProdutos`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE SET NULL,
  INDEX `idx_tecnico` (`tecnico_id`),
  INDEX `idx_data` (`data_hora`),
  INDEX `idx_os` (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Dados Iniciais - Catálogo de Serviços
-- -----------------------------------------------------
INSERT IGNORE INTO `servicos_catalogo` (`codigo`, `nome`, `descricao`, `categoria`, `especialidade`, `tempo_estimado_minutos`, `checklist_padrao`, `ativo`) VALUES
('SRV-CFTV-001', 'Instalação de Câmeras', 'Instalação completa de câmeras de segurança', 'CFTV', 'CFTV', 90, '[{"ordem":1,"desc":"Verificar posição com cliente"},{"ordem":2,"desc":"Instalar suporte"},{"ordem":3,"desc":"Conectar cabos"},{"ordem":4,"desc":"Ajustar ângulo"},{"ordem":5,"desc":"Testar imagem"}]', 1),
('SRV-CFTV-002', 'Configuração de Sistema CFTV', 'Configuração de gravadores e acesso remoto', 'CFTV', 'CFTV', 60, '[{"ordem":1,"desc":"Configurar rede"},{"ordem":2,"desc":"Configurar gravação"},{"ordem":3,"desc":"Configurar app cliente"},{"ordem":4,"desc":"Testar acesso remoto"}]', 1),
('SRV-CFTV-003', 'Manutenção Preventiva CFTV', 'Limpeza e verificação de sistema CFTV', 'CFTV', 'CFTV', 45, '[{"ordem":1,"desc":"Limpar lentes"},{"ordem":2,"desc":"Verificar conexões"},{"ordem":3,"desc":"Testar gravação"},{"ordem":4,"desc":"Verificar espaço em disco"}]', 1),
('SRV-ALM-001', 'Instalação de Alarme', 'Instalação de sensores e central de alarme', 'Alarmes', 'Alarmes', 120, '[{"ordem":1,"desc":"Instalar sensores"},{"ordem":2,"desc":"Instalar sirene"},{"ordem":3,"desc":"Programar zonas"},{"ordem":4,"desc":"Testar disparo"}]', 1),
('SRV-RED-001', 'Passagem de Cabos de Rede', 'Passagem e organização de cabos estruturados', 'Redes', 'Redes', 90, '[{"ordem":1,"desc":"Identificar pontos"},{"ordem":2,"desc":"Passar cabos"},{"ordem":3,"desc":"Crimpagem"},{"ordem":4,"desc":"Testar conectividade"}]', 1),
('SRV-ACE-001', 'Instalação de Controle de Acesso', 'Instalação de catracas/leitores/fechaduras', 'ControleAcesso', 'ControleAcesso', 180, '[{"ordem":1,"desc":"Instalar equipamentos"},{"ordem":2,"desc":"Ligar elétrica"},{"ordem":3,"desc":"Configurar usuários"},{"ordem":4,"desc":"Testar liberações"}]', 1);

-- -----------------------------------------------------
-- Dados Iniciais - Checklists Templates
-- -----------------------------------------------------
INSERT IGNORE INTO `tec_checklist_template` (`tipo_os`, `tipo_servico`, `nome_template`, `itens`, `ativo`) VALUES
('CFTV', 'INS', 'Instalação CFTV Padrão', '[{"ordem":1,"desc":"Verificar integridade dos equipamentos","obrigatorio":true},{"ordem":2,"desc":"Definir posições das câmeras com cliente","obrigatorio":true},{"ordem":3,"desc":"Tirar foto do local antes","obrigatorio":true},{"ordem":4,"desc":"Instalar suportes","obrigatorio":true},{"ordem":5,"desc":"Passar cabeamento","obrigatorio":true},{"ordem":6,"desc":"Conectar câmeras","obrigatorio":true},{"ordem":7,"desc":"Configurar gravação","obrigatorio":true},{"ordem":8,"desc":"Testar acesso remoto","obrigatorio":true},{"ordem":9,"desc":"Orientar cliente","obrigatorio":true}]', 1),
('CFTV', 'MP', 'Manutenção CFTV Padrão', '[{"ordem":1,"desc":"Verificar funcionamento das câmeras","obrigatorio":true},{"ordem":2,"desc":"Limpar lentes","obrigatorio":true},{"ordem":3,"desc":"Verificar conexões","obrigatorio":true},{"ordem":4,"desc":"Verificar espaço em disco","obrigatorio":true},{"ordem":5,"desc":"Testar gravação","obrigatorio":true}]', 1),
('Alarme', 'INS', 'Instalação de Alarme', '[{"ordem":1,"desc":"Verificar equipamentos"},{"ordem":2,"desc":"Instalar sensores"},{"ordem":3,"desc":"Instalar sirene"},{"ordem":4,"desc":"Programar central"},{"ordem":5,"desc":"Testar disparo"}]', 1),
('Rede', 'INS', 'Passagem de Cabos', '[{"ordem":1,"desc":"Identificar pontos"},{"ordem":2,"desc":"Passar cabos"},{"ordem":3,"desc":"Instalar tomadas"},{"ordem":4,"desc":"Testar conectividade"}]', 1);

-- -----------------------------------------------------
-- Table `os_checkin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_checkin` (
  `idCheckin` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `data_entrada` DATETIME NULL,
  `data_saida` DATETIME NULL,
  `latitude_entrada` DECIMAL(10,8) NULL,
  `longitude_entrada` DECIMAL(11,8) NULL,
  `latitude_saida` DECIMAL(10,8) NULL,
  `longitude_saida` DECIMAL(11,8) NULL,
  `observacao_entrada` TEXT NULL,
  `observacao_saida` TEXT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'Em Andamento',
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` DATETIME NULL,
  PRIMARY KEY (`idCheckin`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_assinaturas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_assinaturas` (
  `idAssinatura` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `checkin_id` INT(11) NULL,
  `tipo` VARCHAR(20) NOT NULL COMMENT 'tecnico_entrada, tecnico_saida, cliente_saida',
  `assinatura` VARCHAR(255) NOT NULL COMMENT 'Caminho da imagem da assinatura',
  `nome_assinante` VARCHAR(100) NULL,
  `documento_assinante` VARCHAR(20) NULL,
  `data_assinatura` DATETIME NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idAssinatura`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_fotos_atendimento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_fotos_atendimento` (
  `idFoto` INT(11) NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) NOT NULL,
  `checkin_id` INT(11) NULL,
  `usuarios_id` INT(11) NOT NULL,
  `arquivo` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `descricao` TEXT NULL,
  `etapa` VARCHAR(20) NOT NULL DEFAULT 'durante' COMMENT 'entrada, durante, saida',
  `tamanho` INT(11) NULL,
  `tipo_arquivo` VARCHAR(10) NULL,
  `data_upload` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idFoto`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_etapa` (`etapa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `checklist_templates`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `checklist_templates` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT NULL,
  `categoria` VARCHAR(50) DEFAULT 'geral',
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `checklist_template_items`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `checklist_template_items` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `ordem` INT DEFAULT 0,
  `descricao` VARCHAR(255) NOT NULL,
  `tipo` VARCHAR(20) DEFAULT 'checkbox',
  `obrigatorio` TINYINT(1) DEFAULT 0,
  `opcoes` TEXT NULL,
  FOREIGN KEY (`template_id`) REFERENCES `checklist_templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_checklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_checklist` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT NOT NULL,
  `template_id` INT NULL,
  `item_id` INT NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `status` ENUM('pendente', 'ok', 'nao_aplicavel', 'com_problema') DEFAULT 'pendente',
  `observacao` TEXT NULL,
  `evidencia_foto` VARCHAR(255) NULL,
  `verificado_por` INT NULL,
  `verificado_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_os` (`os_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_timeline`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_timeline` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT NOT NULL,
  `tipo` VARCHAR(50) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` TEXT NULL,
  `usuario_id` INT NULL,
  `usuario_nome` VARCHAR(100) NULL,
  `metadata` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_os` (`os_id`),
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_created` (`created_at`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_pecas_utilizadas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_pecas_utilizadas` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT NOT NULL,
  `produto_id` INT NULL,
  `nome_peca` VARCHAR(255) NOT NULL,
  `codigo` VARCHAR(100) NULL,
  `quantidade` DECIMAL(10,2) DEFAULT 1,
  `valor_unitario` DECIMAL(10,2) DEFAULT 0,
  `valor_total` DECIMAL(10,2) DEFAULT 0,
  `tipo` ENUM('produto', 'servico', 'insumo', 'outro') DEFAULT 'produto',
  `instalado_por` INT NULL,
  `instalado_at` TIMESTAMP NULL,
  `garantia_dias` INT DEFAULT 0,
  `observacao` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_os` (`os_id`),
  INDEX `idx_produto` (`produto_id`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_etapas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_etapas` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT NOT NULL,
  `etapa` VARCHAR(50) NOT NULL,
  `status` ENUM('pendente', 'em_andamento', 'concluida', 'cancelada') DEFAULT 'pendente',
  `ordem` INT DEFAULT 0,
  `tempo_estimado_minutos` INT NULL,
  `tempo_real_minutos` INT NULL,
  `iniciado_at` TIMESTAMP NULL,
  `concluido_at` TIMESTAMP NULL,
  `responsavel_id` INT NULL,
  `observacao` TEXT NULL,
  `checklist` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_os` (`os_id`),
  INDEX `idx_etapa` (`etapa`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tecnico_competencias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tecnico_competencias` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `competencia` VARCHAR(100) NOT NULL,
  `nivel` ENUM('basico', 'intermediario', 'avancado', 'especialista') DEFAULT 'basico',
  `certificado` VARCHAR(255) NULL,
  `validade_certificado` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_usuario_competencia` (`usuario_id`, `competencia`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `tecnico_avaliacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tecnico_avaliacoes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `os_id` INT NOT NULL,
  `tecnico_id` INT NOT NULL,
  `cliente_id` INT NOT NULL,
  `nota_geral` INT CHECK (nota_geral BETWEEN 1 AND 5),
  `nota_atendimento` INT CHECK (nota_atendimento BETWEEN 1 AND 5),
  `nota_solucao` INT CHECK (nota_solucao BETWEEN 1 AND 5),
  `nota_tempo` INT CHECK (nota_tempo BETWEEN 1 AND 5),
  `comentario` TEXT NULL,
  `avaliado_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`os_id`) REFERENCES `os`(`idOs`) ON DELETE CASCADE,
  FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios`(`idUsuarios`) ON DELETE CASCADE,
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`idClientes`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `usuarios_cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_cliente` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` INT(11) NULL COMMENT 'ID do cliente vinculado',
  `nome` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `telefone` VARCHAR(20) NULL,
  `celular` VARCHAR(20) NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `ultimo_acesso` DATETIME NULL,
  `token_reset` VARCHAR(255) NULL,
  `token_expira` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  INDEX `idx_cliente_id` (`cliente_id`),
  INDEX `idx_token_reset` (`token_reset`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `usuarios_cliente_cnpjs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_cliente_cnpjs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_cliente_id` INT(11) NOT NULL,
  `cnpj` VARCHAR(18) NOT NULL COMMENT 'CNPJ no formato 00.000.000/0000-00',
  `razao_social` VARCHAR(255) NULL,
  `nome_fantasia` VARCHAR(255) NULL,
  `principal` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_cnpj` (`usuario_cliente_id`, `cnpj`),
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_principal` (`principal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `usuarios_cliente_permissoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios_cliente_permissoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_cliente_id` INT(11) NOT NULL,
  `chave` VARCHAR(100) NOT NULL COMMENT 'Nome da permissao/configuracao',
  `valor` TEXT NULL COMMENT 'Valor da configuracao (pode ser serializado)',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_chave` (`usuario_cliente_id`, `chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `notificacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `notificacoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `url` VARCHAR(500) NULL,
  `icone` VARCHAR(50) DEFAULT 'bx-bell',
  `tipo` VARCHAR(30) DEFAULT 'info',
  `lida` TINYINT(1) DEFAULT 0,
  `data_notificacao` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario_lida` (`usuario_id`, `lida`),
  INDEX `idx_data` (`data_notificacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `dre_demonstracoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_demonstracoes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `descricao` TEXT NULL,
  `data_inicio` DATE NOT NULL,
  `data_fim` DATE NOT NULL,
  `tipo` ENUM('mensal', 'trimestral', 'anual') DEFAULT 'mensal',
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_data` (`data_inicio`, `data_fim`),
  INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `configuracoes_impostos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracoes_impostos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cnpj` VARCHAR(18) NOT NULL,
  `razao_social` VARCHAR(255) NULL,
  `anexo_simples` ENUM('I', 'II', 'III', 'IV', 'V') DEFAULT 'III',
  `faixa_simples` TINYINT(1) DEFAULT 1,
  `aliquota_simples` DECIMAL(5,2) DEFAULT 6.00,
  `retencao_iss` TINYINT(1) DEFAULT 0,
  `aliquota_iss` DECIMAL(5,2) DEFAULT 2.00,
  `retencao_pis` TINYINT(1) DEFAULT 0,
  `aliquota_pis` DECIMAL(5,2) DEFAULT 0.65,
  `retencao_cofins` TINYINT(1) DEFAULT 0,
  `aliquota_cofins` DECIMAL(5,2) DEFAULT 3.00,
  `retencao_csll` TINYINT(1) DEFAULT 0,
  `aliquota_csll` DECIMAL(5,2) DEFAULT 1.00,
  `retencao_inss` TINYINT(1) DEFAULT 0,
  `aliquota_inss` DECIMAL(5,2) DEFAULT 11.00,
  `retencao_ir` TINYINT(1) DEFAULT 0,
  `aliquota_ir` DECIMAL(5,2) DEFAULT 1.50,
  `valor_minimo_retencao` DECIMAL(15,2) DEFAULT 0.00,
  `ativar_retencao_automatica` TINYINT(1) DEFAULT 0,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_anexo` (`anexo_simples`),
  INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `calculos_impostos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `calculos_impostos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cnpj` VARCHAR(18) NOT NULL,
  `os_id` INT(11) UNSIGNED NULL,
  `venda_id` INT(11) UNSIGNED NULL,
  `cobranca_id` INT(11) UNSIGNED NULL,
  `valor_bruto` DECIMAL(15,2) NOT NULL,
  `valor_liquido` DECIMAL(15,2) NOT NULL,
  `iss` DECIMAL(15,2) DEFAULT 0.00,
  `pis` DECIMAL(15,2) DEFAULT 0.00,
  `cofins` DECIMAL(15,2) DEFAULT 0.00,
  `csll` DECIMAL(15,2) DEFAULT 0.00,
  `inss` DECIMAL(15,2) DEFAULT 0.00,
  `ir` DECIMAL(15,2) DEFAULT 0.00,
  `total_impostos` DECIMAL(15,2) DEFAULT 0.00,
  `aliquota_efetiva` DECIMAL(5,2) NULL,
  `competencia` DATE NOT NULL,
  `status` ENUM('calculado', 'retido', 'recolhido', 'cancelado') DEFAULT 'calculado',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_venda_id` (`venda_id`),
  INDEX `idx_cobranca_id` (`cobranca_id`),
  INDEX `idx_competencia` (`competencia`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `os_documentos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `os_documentos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `os_id` INT(11) UNSIGNED NOT NULL,
  `tipo` ENUM('boleto', 'nfse', 'nfe', 'nfce', 'recibo', 'contrato', 'outro') NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `numero_documento` VARCHAR(100) NULL,
  `valor` DECIMAL(15,2) NULL,
  `data_emissao` DATE NULL,
  `data_vencimento` DATE NULL,
  `status` VARCHAR(50) NULL,
  `arquivo` VARCHAR(500) NULL,
  `link_externo` TEXT NULL,
  `gateway_id` VARCHAR(100) NULL COMMENT 'ID do boleto no gateway',
  `charge_id` VARCHAR(100) NULL COMMENT 'ID da cobranca',
  `nfse_id` INT(11) UNSIGNED NULL,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_os_id` (`os_id`),
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_gateway_id` (`gateway_id`),
  INDEX `idx_charge_id` (`charge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `dre_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dre_config` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('MAPEAMENTO_OS', 'MAPEAMENTO_VENDA', 'MAPEAMENTO_LANCAMENTO', 'CONFIG') NOT NULL,
  `origem_tabela` VARCHAR(50) NULL,
  `origem_campo` VARCHAR(50) NULL,
  `conta_dre_id` INT(11) UNSIGNED NOT NULL,
  `condicao` TEXT NULL,
  `ativo` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_conta_dre_id` (`conta_dre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `config_sistema_impostos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `config_sistema_impostos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chave` VARCHAR(100) NOT NULL,
  `valor` TEXT NULL,
  `descricao` VARCHAR(255) NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados padrão para config_sistema_impostos
INSERT IGNORE INTO `config_sistema_impostos` (`chave`, `valor`, `descricao`) VALUES
('IMPOSTO_ANEXO_PADRAO', 'III', 'Anexo do Simples Nacional padrao'),
('IMPOSTO_FAIXA_ATUAL', '1', 'Faixa de faturamento atual (1-5)'),
('IMPOSTO_RETENCAO_AUTOMATICA', '1', 'Habilitar retencao automatica (1=Sim, 0=Nao)'),
('IMPOSTO_DRE_INTEGRACAO', '1', 'Integrar retencoes com DRE (1=Sim, 0=Nao)'),
('IMPOSTO_ISS_MUNICIPAL', '5.00', 'Aliquota de ISS municipal (%)'),
('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL', '010701', 'Codigo de Tributacao Nacional (LC 116/2003)'),
('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL', '100', 'Codigo de Tributacao Municipal'),
('IMPOSTO_DESCRICAO_SERVICO', 'Suporte tecnico em informatica, inclusive instalacao, configuracao e manutencao de programas de computacao e bancos de dados.', 'Descricao do servico para NFS-e');

-- -----------------------------------------------------
-- Table `certificado_digital`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificado_digital` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('A1', 'A3') DEFAULT 'A1',
  `cnpj` VARCHAR(14) NOT NULL,
  `razao_social` VARCHAR(255) NULL,
  `nome_fantasia` VARCHAR(255) NULL,
  `arquivo_caminho` VARCHAR(500) NULL,
  `arquivo_hash` VARCHAR(255) NULL,
  `senha` TEXT NULL,
  `data_validade` DATETIME NULL,
  `data_emissao` DATETIME NULL,
  `emissor` VARCHAR(100) NULL,
  `serial_number` VARCHAR(100) NULL,
  `ativo` TINYINT(1) DEFAULT 0,
  `ultimo_acesso` DATETIME NULL,
  `ultimo_erro` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cnpj` (`cnpj`),
  INDEX `idx_ativo` (`ativo`),
  INDEX `idx_data_validade` (`data_validade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `certificado_consultas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificado_consultas` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificado_id` INT(11) UNSIGNED NOT NULL,
  `tipo_consulta` ENUM('CNPJ', 'SIMPLES_NACIONAL', 'NFE', 'NFSE', 'SITUACAO_CADASTRO') NOT NULL,
  `data_consulta` DATETIME NOT NULL,
  `sucesso` TINYINT(1) DEFAULT 0,
  `dados_retorno` LONGTEXT NULL,
  `erro` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_certificado_id` (`certificado_id`),
  INDEX `idx_tipo_consulta` (`tipo_consulta`),
  INDEX `idx_data_consulta` (`data_consulta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table `certificado_nfe_importada`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificado_nfe_importada` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `certificado_id` INT(11) UNSIGNED NOT NULL,
  `chave_acesso` VARCHAR(44) NOT NULL,
  `numero` VARCHAR(20) NULL,
  `serie` VARCHAR(10) NULL,
  `data_emissao` DATETIME NULL,
  `data_importacao` DATETIME NULL,
  `cnpj_destinatario` VARCHAR(14) NULL,
  `valor_total` DECIMAL(15,2) NULL,
  `valor_impostos` DECIMAL(15,2) NULL,
  `xml_path` VARCHAR(500) NULL,
  `situacao` ENUM('Autorizada', 'Cancelada', 'Denegada', 'Inutilizada') DEFAULT 'Autorizada',
  `imposto_integrado` TINYINT(1) DEFAULT 0,
  `dados_xml` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_certificado_id` (`certificado_id`),
  INDEX `idx_chave_acesso` (`chave_acesso`),
  INDEX `idx_cnpj_destinatario` (`cnpj_destinatario`),
  INDEX `idx_situacao` (`situacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Dados iniciais - Anexo V Impostos (configuracoes_impostos)
-- -----------------------------------------------------
-- NOTA: As aliquotas do Anexo V sao inseridas aqui como configuracao padrao.
-- A tabela impostos_config ja recebeu seu registro basico acima (linha 1065).
INSERT IGNORE INTO `configuracoes_impostos` (`cnpj`, `razao_social`, `anexo_simples`, `faixa_simples`, `aliquota_simples`, `retencao_iss`, `aliquota_iss`, `retencao_pis`, `aliquota_pis`, `retencao_cofins`, `aliquota_cofins`, `retencao_csll`, `aliquota_csll`, `retencao_ir`, `aliquota_ir`, `retencao_inss`, `aliquota_inss`, `valor_minimo_retencao`, `ativar_retencao_automatica`, `ativo`, `created_at`) VALUES
('00000000000000', 'Empresa Padrao', 'V', 1, 6.00, 0, 0.00, 0, 0.50, 0, 2.34, 0, 0.80, 0, 0.80, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 2, 8.21, 0, 0.00, 0, 0.63, 0, 2.67, 0, 1.46, 0, 1.46, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 3, 10.46, 0, 0.00, 0, 0.77, 0, 3.27, 0, 1.90, 0, 1.90, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 4, 11.14, 0, 0.00, 0, 0.82, 0, 3.46, 0, 2.15, 0, 2.15, 0, 0.00, 0.00, 0, 1, NOW()),
('00000000000000', 'Empresa Padrao', 'V', 5, 12.05, 0, 0.00, 0, 0.87, 0, 3.67, 0, 2.36, 0, 2.36, 0, 0.00, 0.00, 0, 1, NOW());

-- -----------------------------------------------------
-- Dados iniciais - Contas DRE Comercio
-- -----------------------------------------------------
INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `tipo`, `categoria`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
('1.4', 'Vendas de Materiais de Construcao', 'receita', 'Receitas', 14, 1, NOW(), NOW()),
('1.4.1', 'Vendas de Cimento e Argamassa', 'receita', 'Receitas', 141, 1, NOW(), NOW()),
('1.4.2', 'Vendas de Tijolos e Blocos', 'receita', 'Receitas', 142, 1, NOW(), NOW()),
('1.4.3', 'Vendas de Tintas e Vernizes', 'receita', 'Receitas', 143, 1, NOW(), NOW()),
('1.4.4', 'Vendas de Ferragens e Metais', 'receita', 'Receitas', 144, 1, NOW(), NOW()),
('1.4.5', 'Vendas de Materiais Eletricos', 'receita', 'Receitas', 145, 1, NOW(), NOW()),
('1.4.6', 'Vendas de Madeiras e Esquadrias', 'receita', 'Receitas', 146, 1, NOW(), NOW()),
('2.4', 'ICMS sobre Vendas', 'despesa', 'Impostos', 24, 1, NOW(), NOW()),
('2.5', 'PIS/COFINS sobre Vendas', 'despesa', 'Impostos', 25, 1, NOW(), NOW()),
('4.4', 'Custo das Mercadorias Vendidas', 'custo', 'Custos', 44, 1, NOW(), NOW()),
('4.4.1', 'Custo - Cimentos e Argamassas', 'custo', 'Custos', 441, 1, NOW(), NOW()),
('4.4.2', 'Custo - Tijolos e Blocos', 'custo', 'Custos', 442, 1, NOW(), NOW()),
('4.4.3', 'Custo - Tintas e Vernizes', 'custo', 'Custos', 443, 1, NOW(), NOW()),
('4.4.4', 'Custo - Ferragens e Metais', 'custo', 'Custos', 444, 1, NOW(), NOW()),
('4.4.5', 'Custo - Materiais Eletricos', 'custo', 'Custos', 445, 1, NOW(), NOW()),
('4.4.6', 'Custo - Madeiras e Esquadrias', 'custo', 'Custos', 446, 1, NOW(), NOW()),
('6.3', 'Despesas com Vendas', 'despesa', 'Despesas', 63, 1, NOW(), NOW()),
('6.3.1', 'Frete e Carretos', 'despesa', 'Despesas', 631, 1, NOW(), NOW()),
('6.3.2', 'Comissoes de Vendedores', 'despesa', 'Despesas', 632, 1, NOW(), NOW()),
('6.3.3', 'Despesas com Armazenagem', 'despesa', 'Despesas', 633, 1, NOW(), NOW()),
('6.3.4', 'Perdas e Quebras de Estoque', 'despesa', 'Despesas', 634, 1, NOW(), NOW());

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

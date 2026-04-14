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
  `usuarios_id` INT NOT NULL,
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
  `idVendas` INT NOT NULL AUTO_INCREMENT,
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
  `custom_id` int(11) DEFAULT NULL,
  `expire_at` date NOT NULL,
  `message` varchar(255) NOT NULL,
  `payment_method` varchar(11) DEFAULT NULL,
  `payment_url` varchar(255) DEFAULT NULL,
  `request_delivery_address` varchar(64) DEFAULT NULL,
  `status` varchar(36) NOT NULL,
  `total` varchar(15) DEFAULT NULL,
  `barcode` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
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
  CONSTRAINT `fk_cobrancas_clientes1` FOREIGN KEY (`clientes_id`) REFERENCES `clientes` (`idClientes`) ON DELETE NO ACTION ON UPDATE NO ACTION

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
  `categoria` VARCHAR(100) NULL,
  `pai_id` INT(11) UNSIGNED NULL,
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
  `data_referencia` DATE NOT NULL,
  `valor` DECIMAL(15,2) NOT NULL,
  `descricao` TEXT NULL,
  `id_os` INT(11) UNSIGNED NULL,
  `id_venda` INT(11) UNSIGNED NULL,
  `id_lancamento` INT(11) UNSIGNED NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_conta_id` (`conta_id`),
  INDEX `idx_data_referencia` (`data_referencia`),
  INDEX `idx_id_os` (`id_os`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `impostos_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `impostos_config` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tipo_regime` ENUM('simples_nacional', 'lucro_presumido', 'lucro_real') DEFAULT 'simples_nacional',
  `anexo_simples` ENUM('i', 'ii', 'iii', 'iv', 'v') NULL,
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
  `nfse_id` INT(11) NULL COMMENT 'ID da NFS-e vinculada',
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

INSERT IGNORE INTO `migrations`(`version`) VALUES ('20210125173741');

-- Dados iniciais V5
INSERT IGNORE INTO `dre_contas` (`codigo`, `nome`, `tipo`, `categoria`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
('1', 'RECEITA BRUTA', 'receita', 'Receitas', 1, 1, 'admin_created_at', 'admin_created_at'),
('1.1', 'Serviços', 'receita', 'Receitas', 2, 1, 'admin_created_at', 'admin_created_at'),
('1.2', 'Produtos', 'receita', 'Receitas', 3, 1, 'admin_created_at', 'admin_created_at'),
('2', 'IMPOSTOS', 'despesa', 'Impostos', 10, 1, 'admin_created_at', 'admin_created_at'),
('2.1', 'ISS', 'despesa', 'Impostos', 11, 1, 'admin_created_at', 'admin_created_at'),
('3', 'CUSTOS', 'custo', 'Custos', 20, 1, 'admin_created_at', 'admin_created_at'),
('4', 'DESPESAS OPERACIONAIS', 'despesa', 'Despesas', 30, 1, 'admin_created_at', 'admin_created_at');

INSERT IGNORE INTO `impostos_config` (`tipo_regime`, `anexo_simples`, `aliquota_iss`, `retem_iss`, `created_at`, `updated_at`) VALUES
('simples_nacional', 'iii', 2.00, 0, 'admin_created_at', 'admin_created_at');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema caixa_smartifix
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `caixa_smartifix` DEFAULT CHARACTER SET utf8 ;
USE `caixa_smartifix` ;

-- -----------------------------------------------------
-- Table `caixa_smartifix`.`perfis`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`perfis` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `permissoes` JSON NOT NULL COMMENT 'Lista de permissões do perfil e JSON',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`funcionarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`funcionarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `cargo` VARCHAR(50) NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `senha` VARCHAR(255) NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `perfil_id` INT NOT NULL,
  `criado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `funcionario_id` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `fk_usuarios_perfis_idx` (`perfil_id` ASC),
  INDEX `fk_usuarios_funcionarios1_idx` (`funcionario_id` ASC),
  CONSTRAINT `fk_usuarios_perfis`
    FOREIGN KEY (`perfil_id`)
    REFERENCES `caixa_smartifix`.`perfis` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_funcionarios1`
    FOREIGN KEY (`funcionario_id`)
    REFERENCES `caixa_smartifix`.`funcionarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`caixas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`caixas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `data_operacao` DATE NOT NULL COMMENT 'Dia do caixa, apenas um caixa aberto por dia.',
  `status` ENUM('aberto', 'fechado', 'reaberto') NOT NULL,
  `aberto_por` INT NOT NULL,
  `aberto_em` DATETIME NOT NULL,
  `fechado_por` INT NULL,
  `fechado_em` DATETIME NULL,
  `observacao` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_caixas_usuarios1_idx` (`aberto_por` ASC),
  INDEX `fk_caixas_usuarios2_idx` (`fechado_por` ASC),
  UNIQUE INDEX `data_operacao_UNIQUE` (`data_operacao` ASC),
  CONSTRAINT `fk_caixas_usuarios1`
    FOREIGN KEY (`aberto_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_caixas_usuarios2`
    FOREIGN KEY (`fechado_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`contas_financeiras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`contas_financeiras` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL COMMENT 'Ex: Caixa Físico, Nubank, PagBank',
  `tipo` ENUM('caixa_fisico', 'conta_bancaria', 'carteira_digital', 'outro') NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`naturezas_financeiras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`naturezas_financeiras` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('entrada', 'saida') NULL COMMENT 'Determina se é receita ou despesa',
  `categoria_base` ENUM('venda', 'servico', 'despesa_adm', 'imposto', 'aporte', 'sangria', 'devolucao', 'retirada', 'outro') NOT NULL,
  `nome` VARCHAR(100) NULL,
  `descricao` VARCHAR(255) NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `criado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`formas_pagamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`formas_pagamento` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Ex: Dinheiro, PIX, Cartão Débito',
  `nome` VARCHAR(100) NOT NULL,
  `ativo` TINYINT(1) NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`item_movimentacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`item_movimentacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('os', 'servico_avulso', 'venda') NULL,
  `numero_os` VARCHAR(50) NULL COMMENT 'Número gerado pelo sistema externo',
  `data` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valor_total` DECIMAL(15,2) NOT NULL COMMENT 'Valor contratado da OS.',
  `descricao` VARCHAR(255) NULL,
  `item` VARCHAR(100) NOT NULL,
  `cliente` VARCHAR(100) NULL,
  `criado_por` INT NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `numero_os_UNIQUE` (`numero_os` ASC),
  INDEX `fk_ordens_servico_usuarios1_idx` (`criado_por` ASC),
  CONSTRAINT `fk_ordens_servico_usuarios1`
    FOREIGN KEY (`criado_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`movimentacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`movimentacoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('entrada', 'saida') NULL,
  `caixa_id` INT NOT NULL,
  `conta_financeira_id` INT NOT NULL,
  `natureza_financeira_id` INT NOT NULL,
  `forma_pagamento_id` INT NOT NULL,
  `item_movimentacao_id` INT NULL,
  `valor` DECIMAL(15,2) NULL,
  `descricao` VARCHAR(255) NULL,
  `data_movimentacao` DATETIME NULL,
  `funcionario_id` INT NULL COMMENT 'Campo para identificar funcionario vendedor.',
  `criado_por` INT NOT NULL,
  `status` ENUM('ativa', 'cancelada') NOT NULL,
  `eh_parcela` TINYINT(1) NOT NULL DEFAULT 0,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_movimentacoes_caixas1_idx` (`caixa_id` ASC),
  INDEX `fk_movimentacoes_contas_financeiras1_idx` (`conta_financeira_id` ASC),
  INDEX `fk_movimentacoes_naturezas_financeiras1_idx` (`natureza_financeira_id` ASC),
  INDEX `fk_movimentacoes_formas_pagamento1_idx` (`forma_pagamento_id` ASC),
  INDEX `fk_movimentacoes_ordens_servico1_idx` (`item_movimentacao_id` ASC),
  INDEX `fk_movimentacoes_funcionarios1_idx` (`funcionario_id` ASC),
  INDEX `fk_movimentacoes_usuarios1_idx` (`criado_por` ASC),
  CONSTRAINT `fk_movimentacoes_caixas1`
    FOREIGN KEY (`caixa_id`)
    REFERENCES `caixa_smartifix`.`caixas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_contas_financeiras1`
    FOREIGN KEY (`conta_financeira_id`)
    REFERENCES `caixa_smartifix`.`contas_financeiras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_naturezas_financeiras1`
    FOREIGN KEY (`natureza_financeira_id`)
    REFERENCES `caixa_smartifix`.`naturezas_financeiras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_formas_pagamento1`
    FOREIGN KEY (`forma_pagamento_id`)
    REFERENCES `caixa_smartifix`.`formas_pagamento` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_ordens_servico1`
    FOREIGN KEY (`item_movimentacao_id`)
    REFERENCES `caixa_smartifix`.`item_movimentacao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_funcionarios1`
    FOREIGN KEY (`funcionario_id`)
    REFERENCES `caixa_smartifix`.`funcionarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_usuarios1`
    FOREIGN KEY (`criado_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`fornecedores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`fornecedores` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  `criado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `cadastrado_por` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_fornecedores_usuarios1_idx` (`cadastrado_por` ASC),
  CONSTRAINT `fk_fornecedores_usuarios1`
    FOREIGN KEY (`cadastrado_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`custos_operacionais`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`custos_operacionais` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `item_movimentacao_id` INT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `tipo` ENUM('estoque', 'fornecedor', 'mao_obra', 'nenhum') NOT NULL COMMENT 'Peça, mão de obra, fornecedor, estoque...',
  `fornecedor_id` INT NULL,
  `valor` DECIMAL(15,2) NOT NULL,
  `criado_por` INT NOT NULL,
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_custos_os_ordens_servico1_idx` (`item_movimentacao_id` ASC),
  INDEX `fk_custos_operacionais_fornecedores1_idx` (`fornecedor_id` ASC),
  CONSTRAINT `fk_custos_os_ordens_servico1`
    FOREIGN KEY (`item_movimentacao_id`)
    REFERENCES `caixa_smartifix`.`item_movimentacao` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_custos_operacionais_fornecedores1`
    FOREIGN KEY (`fornecedor_id`)
    REFERENCES `caixa_smartifix`.`fornecedores` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`caixa_saldos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`caixa_saldos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `caixa_id` INT NOT NULL,
  `conta_financeira_id` INT NOT NULL,
  `saldo_inicial` DECIMAL(15,2) NOT NULL COMMENT 'Puxado automaticamente do fechamento anterior',
  `saldo_final` DECIMAL(15,2) NOT NULL COMMENT 'Calculado no fechamento',
  PRIMARY KEY (`id`),
  INDEX `fk_caixa_saldos_caixas1_idx` (`caixa_id` ASC),
  INDEX `fk_caixa_saldos_contas_financeiras1_idx` (`conta_financeira_id` ASC),
  CONSTRAINT `fk_caixa_saldos_caixas1`
    FOREIGN KEY (`caixa_id`)
    REFERENCES `caixa_smartifix`.`caixas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_caixa_saldos_contas_financeiras1`
    FOREIGN KEY (`conta_financeira_id`)
    REFERENCES `caixa_smartifix`.`contas_financeiras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`reaberturas_caixa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`reaberturas_caixa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `caixas_id` INT NOT NULL,
  `reaberto_por` INT NOT NULL,
  `reaberto_em` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `justificativa` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_reaberturas_caixa_caixas1_idx` (`caixas_id` ASC),
  INDEX `fk_reaberturas_caixa_usuarios1_idx` (`reaberto_por` ASC),
  CONSTRAINT `fk_reaberturas_caixa_caixas1`
    FOREIGN KEY (`caixas_id`)
    REFERENCES `caixa_smartifix`.`caixas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reaberturas_caixa_usuarios1`
    FOREIGN KEY (`reaberto_por`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `caixa_smartifix`.`logs_auditoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `caixa_smartifix`.`logs_auditoria` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `usuarios_id` INT NOT NULL,
  `tabela_afetada` VARCHAR(100) NOT NULL,
  `registro_id` INT NOT NULL COMMENT 'PK do registro afertado',
  `operacao` ENUM('insert', 'update', 'delete', 'delete_logico', 'reabertura_caixa', 'cancelamento') NOT NULL,
  `dados_anteriores` JSON NULL COMMENT 'Snapshot do registro antes da alteração',
  `dados_novos` JSON NULL COMMENT 'Snapshot do registro após a alteração',
  `ip_origem` VARCHAR(45) NULL COMMENT 'IPv4 ou IPv6',
  `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_logs_auditoria_usuarios1_idx` (`usuarios_id` ASC),
  CONSTRAINT `fk_logs_auditoria_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `caixa_smartifix`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- INSERTS PADRÕES PARA INÍCIO
-- Usuário: admin@dominio.com.br
-- Senha: password
INSERT INTO `perfis` VALUES (1,'Administrador','Acesso total ao sistema','{\"todas\":true,\"caixa:visualizar\":true,\"caixa:abrir\":true,\"caixa:fechar\":true,\"caixa:reabrir\":true,\"caixa:ver_saldo\":true,\"sangria:criar\":true,\"movimentacao:visualizar_todas\":true,\"movimentacao:criar\":true,\"movimentacao:editar\":true,\"movimentacao:cancelar\":true,\"os:visualizar\":true,\"venda:visualizar\":true,\"custos:gerenciar\":true,\"dashboard:visualizar\":true,\"relatorios:visualizar\":true,\"relatorios:gerar\":true,\"configuracoes:visualizar\":true,\"cadastros:gerenciar\":true,\"auditoria:visualizar\":true}');
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@dominio.com.br','$2y$10$Uf/IfJWGaHZoihVR8XWz3e4SwZ2C9DJBvbkOcuCUfL8/ALcItG0QS',1,1,'2026-06-08 10:59:54','2026-06-08 10:59:54',NULL);
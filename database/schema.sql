CREATE DATABASE IF NOT EXISTS ecoescambo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ecoescambo;

CREATE TABLE IF NOT EXISTS utilizadores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  senha_hash VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 0,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_utilizadores_email (email),
  KEY idx_utilizadores_ativo (ativo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  utilizador_id INT UNSIGNED NOT NULL,
  nome VARCHAR(140) NOT NULL,
  descricao TEXT NOT NULL,
  foto_url VARCHAR(255) NULL,
  estado ENUM('Em aberto', 'Finalizada') NOT NULL DEFAULT 'Em aberto',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_produtos_utilizador
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  KEY idx_produtos_catalogo (estado, utilizador_id, id),
  KEY idx_produtos_utilizador (utilizador_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS interesses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  interessado_id INT UNSIGNED NOT NULL,
  ofertante_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_interesses_interessado
    FOREIGN KEY (interessado_id) REFERENCES utilizadores(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_interesses_ofertante
    FOREIGN KEY (ofertante_id) REFERENCES utilizadores(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_interesses_produto
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  UNIQUE KEY uq_interesses_interessado_produto (interessado_id, produto_id),
  KEY idx_interesses_ofertante_produto (ofertante_id, produto_id),
  KEY idx_interesses_interessado (interessado_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS propostas_troca (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  interesse_id INT UNSIGNED NOT NULL,
  produto_proposto_id INT UNSIGNED NOT NULL,
  estado ENUM('Pendente', 'Aceita', 'Recusada') NOT NULL DEFAULT 'Pendente',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_propostas_interesse
    FOREIGN KEY (interesse_id) REFERENCES interesses(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_propostas_produto_proposto
    FOREIGN KEY (produto_proposto_id) REFERENCES produtos(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  UNIQUE KEY uq_propostas_interesse_produto (interesse_id, produto_proposto_id),
  KEY idx_propostas_estado (estado),
  KEY idx_propostas_produto_proposto (produto_proposto_id)
) ENGINE=InnoDB;

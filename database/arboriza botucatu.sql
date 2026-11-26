CREATE DATABASE IF NOT EXISTS arboriza_botucatu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arboriza_botucatu;

DROP TABLE IF EXISTS fotos_arvores;
DROP TABLE IF EXISTS intervencoes;
DROP TABLE IF EXISTS solicitacoes;
DROP TABLE IF EXISTS arvores;
DROP TABLE IF EXISTS bairros;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('Administrador','Técnico','Leitor') DEFAULT 'Leitor',
    ativo TINYINT(1) DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE bairros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL UNIQUE,
    zona VARCHAR(60) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE arvores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    especie VARCHAR(140) NOT NULL,
    nome_popular VARCHAR(140),
    idade_estimada INT DEFAULT 0,
    altura_metros DECIMAL(8,2) DEFAULT 0,
    diametro_cm DECIMAL(8,2) DEFAULT 0,
    latitude VARCHAR(40),
    longitude VARCHAR(40),
    endereco VARCHAR(220) NOT NULL,
    bairro_id INT NOT NULL,
    estado_conservacao ENUM('Excelente','Bom','Regular','Crítico') DEFAULT 'Bom',
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bairro_id) REFERENCES bairros(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE fotos_arvores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arvore_id INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    legenda VARCHAR(180),
    data_foto DATE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arvore_id) REFERENCES arvores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arvore_id INT NOT NULL,
    solicitante VARCHAR(140) NOT NULL,
    telefone VARCHAR(40),
    tipo ENUM('Poda','Manutenção','Remoção','Vistoria') DEFAULT 'Poda',
    prioridade ENUM('Baixa','Média','Alta','Urgente') DEFAULT 'Média',
    status ENUM('Aberta','Em análise','Agendada','Concluída','Cancelada') DEFAULT 'Aberta',
    descricao TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arvore_id) REFERENCES arvores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE intervencoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arvore_id INT NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    responsavel VARCHAR(140) NOT NULL,
    data_intervencao DATE NOT NULL,
    custo_estimado DECIMAL(10,2) DEFAULT 0,
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arvore_id) REFERENCES arvores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES
('cesar', 'cesar123@gmail.com', '$2y$12$vI5P8SKAYzqcXHxrBZ5NMOnrECQ4B7mwh8vlYLRgqE38pUrYP/BzG', 'Administrador', 1),
('João Piastrelli', 'joaopiastrelli0908@gmail.com', '$2y$12$gQXupGATFUAi24t7VSs2quACkDJI.sVJ75WVs0aEBL2bGjijsYFbi', 'Administrador', 1),
('Renan Winkler', 'renanwinkler@gmail.com', '$2y$12$gQXupGATFUAi24t7VSs2quACkDJI.sVJ75WVs0aEBL2bGjijsYFbi', 'Técnico', 1),
('Angela Silva', 'angelavsilva@gmail.com', '$2y$12$gQXupGATFUAi24t7VSs2quACkDJI.sVJ75WVs0aEBL2bGjijsYFbi', 'Leitor', 1);
UPDATE usuarios
SET senha = '$2y$12$gQXupGATFUAi24t7VSs2quACkDJI.sVJ75WVs0aEBL2bGjijsYFbi', ativo = 1
WHERE email IN (
    'joaopiastrelli0908@gmail.com',
    'renanwinkler@gmail.com',
    'angelavsilva@gmail.com'
);

UPDATE usuarios
SET senha = '$2y$12$vI5P8SKAYzqcXHxrBZ5NMOnrECQ4B7mwh8vlYLRgqE38pUrYP/BzG', ativo = 1
WHERE email = 'cesar123@gmail.com';

INSERT INTO bairros (nome, zona) VALUES
('Centro','Central'),('Vila Antártica','Norte'),('Rubião Júnior','Oeste'),('Jardim Paraíso','Sul'),('Cohab I','Leste'),('Vila dos Lavradores','Norte');

INSERT INTO arvores (codigo, especie, nome_popular, idade_estimada, altura_metros, diametro_cm, latitude, longitude, endereco, bairro_id, estado_conservacao, observacoes) VALUES
('ARB-1001','Handroanthus albus','Ipê-amarelo',8,5.60,28.00,'-22.8859','-48.4450','Praça Emílio Peduti',1,'Excelente','Árvore ornamental com boa copa.'),
('ARB-1002','Caesalpinia pluviosa','Sibipiruna',14,8.20,42.50,'-22.8870','-48.4482','Rua Amando de Barros',1,'Bom','Necessita vistoria anual.'),
('ARB-1003','Licania tomentosa','Oiti',10,6.10,33.20,'-22.8702','-48.4594','Av. Vital Brasil',2,'Regular','Raízes próximas à calçada.'),
('ARB-1004','Poincianella pluviosa','Sibipiruna',6,4.70,21.00,'-22.8983','-48.4951','Rubião Júnior',3,'Bom','Crescimento saudável.'),
('ARB-1005','Schinus molle','Aroeira-salsa',5,3.90,17.50,'-22.9031','-48.4410','Jardim Paraíso',4,'Crítico','Galhos secos e risco de queda.'),
('ARB-1006','Handroanthus impetiginosus','Ipê-roxo',7,5.20,25.30,'-22.8810','-48.4300','Cohab I',5,'Excelente','Floração intensa.'),
('ARB-1007','Ficus benjamina','Figueira-benjamim',12,7.80,40.10,'-22.8765','-48.4528','Vila dos Lavradores',6,'Regular','Solicitada poda preventiva.');

INSERT INTO solicitacoes (arvore_id, solicitante, telefone, tipo, prioridade, status, descricao) VALUES
(5,'Maria Oliveira','(14) 99999-1111','Poda','Urgente','Aberta','Galhos secos avançando sobre a rua.'),
(3,'Equipe Técnica','(14) 3811-0000','Vistoria','Alta','Em análise','Avaliar impacto das raízes na calçada.'),
(7,'João Lima','(14) 98888-2222','Poda','Média','Agendada','Poda preventiva antes do período de chuvas.'),
(2,'Secretaria de Meio Ambiente','(14) 3811-1111','Manutenção','Baixa','Concluída','Inspeção visual e limpeza de entorno.');

INSERT INTO intervencoes (arvore_id, tipo, responsavel, data_intervencao, custo_estimado, descricao) VALUES
(2,'Poda preventiva','Equipe Verde','2026-04-15',180.00,'Poda leve para afastamento de fiação.'),
(6,'Tratamento fitossanitário','Téc. Ana Souza','2026-04-28',95.50,'Aplicado tratamento preventivo.'),
(1,'Vistoria técnica','Eng. Carlos Mendes','2026-05-03',0.00,'Árvore em excelente estado.'),
(4,'Plantio de reforço','Equipe Verde','2026-05-10',120.00,'Adubação e tutoramento.');
INSERT INTO fotos_arvores (arvore_id, caminho, legenda, data_foto)
VALUES
(1, 'arvore_1_1779762132.jpg', 'Foto periódica da árvore 1', CURDATE()),
(1, 'arvore_1_1779762183.jpg', 'Foto periódica da árvore 1', CURDATE()),
(2, 'arvore_2_1779762078.jpg', 'Foto periódica da árvore 2', CURDATE()),
(2, 'arvore_2_1779762149.jpg', 'Foto periódica da árvore 2', CURDATE()),
(3, 'arvore_3_1779762215.jpg', 'Foto periódica da árvore 3', CURDATE()),
(3, 'arvore_3_1779762228.jpg', 'Foto periódica da árvore 3', CURDATE()),
(4, 'arvore_4_1779762301.jpg', 'Foto periódica da árvore 4', CURDATE()),
(5, 'arvore_5_1779762327.jpg', 'Foto periódica da árvore 5', CURDATE()),
(7, 'arvore_7_1779762264.jpg', 'Foto periódica da árvore 7', CURDATE());

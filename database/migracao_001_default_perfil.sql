-- migracao_001_default_perfil.sql
-- Alinha o valor padrão de usuarios.perfil para o princípio do menor privilégio (ADR-002).
-- Não remove tabelas nem dados existentes (ADR-001 cancelado a pedido).
--
-- Backup recomendado antes de aplicar:
-- mysqldump -u root -p arboriza_botucatu > backup_pre_migracao.sql

USE arboriza_botucatu;

ALTER TABLE usuarios
    MODIFY COLUMN perfil ENUM('Administrador','Técnico','Leitor')
    NOT NULL DEFAULT 'Leitor';

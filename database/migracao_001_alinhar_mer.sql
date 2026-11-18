-- =====================================================================
-- Migração 001 — Alinhamento ao MER (Remoção de tabelas fora de escopo)
-- Projeto: Arboriza Botucatu
--
-- RECOMENDAÇÃO DE SEGURANÇA:
-- Execute um backup completo antes de rodar este script:
-- mysqldump -u root -p arboriza_botucatu > backup_pre_migracao_001.sql
-- =====================================================================

USE arboriza_botucatu;

-- 1. Remoção das tabelas filhas/dependentes fora do MER (respeitando FKs)
DROP TABLE IF EXISTS fotos_arvores;
DROP TABLE IF EXISTS intervencoes;

-- 2. Correção de segurança P0: Perfil padrão com menor privilégio ('Leitor')
ALTER TABLE usuarios 
    MODIFY perfil ENUM('Administrador','Técnico','Leitor') DEFAULT 'Leitor';

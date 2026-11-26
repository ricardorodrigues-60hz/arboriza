# Arboriza Botucatu

Sistema web para inventário arbóreo municipal e gestão de solicitações de manutenção (poda, remoção, vistoria e intervenções), desenvolvido em PHP e MySQL sem frameworks.

## Funcionalidades

- **Dashboard:** exibe árvores em estado crítico e solicitações urgentes/abertas no topo, totais do inventário e gráficos de distribuição por bairro, estado de conservação e status das solicitações.
- **Árvores:** cadastro, edição, consulta e exclusão de indivíduos arbóreos com espécie, medidas, bairro, endereço, coordenadas e estado de conservação (`Excelente`, `Bom`, `Regular`, `Crítico`).
- **Bairros:** cadastro e organização territorial por zona (`Central`, `Norte`, `Sul`, `Leste`, `Oeste`).
- **Solicitações:** abertura de pedidos de `Poda`, `Manutenção`, `Remoção` ou `Vistoria` com prioridade e acompanhamento de status.
- **Fotos periódicas:** registro fotográfico com data e legenda vinculado a cada árvore.
- **Intervenções:** histórico técnico de serviços realizados, responsável e custo estimado.
- **Relatórios PDF:** geração dinâmica em PDF de árvores por bairro, espécies predominantes, solicitações e intervenções.
- **Usuários:** gestão de contas e perfis (`Administrador`, `Técnico`, `Leitor`) restrita ao administrador.

## Requisitos

- PHP 7.4+ (com extensões `pdo_mysql`, `mbstring`, `fileinfo`)
- MySQL / MariaDB (ex.: XAMPP)
- Servidor web Apache ou Nginx

## Instalação e uso

1. **Banco de dados:**
   - Para instalação nova, importe [database/arboriza botucatu.sql](database/arboriza%20botucatu.sql).
   - Para banco já existente, faça backup e execute [database/migracao_001_default_perfil.sql](database/migracao_001_default_perfil.sql).
2. **Configuração:**
   - Ajuste host, banco, usuário, senha e `APP_URL` em [config/config.php](config/config.php) conforme o seu ambiente.
3. **Gráficos locais:**
   - Caso a pasta `node_modules/chart.js` não exista, execute `npm install chart.js` na raiz do projeto.
4. **Acesso:**
   - Abra `http://localhost/arboriza/login.php` no navegador.
   - O cadastro de novos usuários é feito exclusivamente por um `Administrador` no menu **Usuários**.

## Perfis de acesso

| Perfil | Permissões |
|---|---|
| **Administrador** | Acesso total: usuários, bairros, árvores, fotos, intervenções, solicitações e relatórios. |
| **Técnico** | Cadastra/edita árvores, fotos, intervenções e solicitações; gera relatórios. |
| **Leitor** | Visualiza registros e gera relatórios PDF; pode abrir novas solicitações. |

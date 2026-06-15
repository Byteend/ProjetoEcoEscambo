# Backend EcoEscambo

Backend em PHP puro com PDO e MySQL. As telas HTML podem chamar estes scripts por `GET` ou `POST` e receber JSON.

## Configuracao

1. Instale PHP e MySQL.
2. Crie o banco executando `database/schema.sql` no phpMyAdmin ou no cliente MySQL.
3. Ajuste as credenciais em `backend/config/app.php`.

Por padrao, o backend usa `127.0.0.1:3306`, banco `ecoescambo`, usuario `root` e senha vazia, que e o padrao comum do XAMPP.

## Rodando com servidor PHP embutido

No macOS com Homebrew:

```bash
brew services start mysql
mysql -uroot < database/schema.sql
php -S localhost:8000
```

Com esse modo, deixe `APP_BASE_PATH` vazio em `backend/config/app.php`:

```php
const APP_BASE_PATH = '';
```

Depois abra no navegador:

```text
http://localhost:8000/backend/health_check.php
```

## Rodando com XAMPP/WAMP/Laragon

Coloque a pasta `ProjetoEcoEscambo` no servidor local:

- XAMPP: `htdocs/ProjetoEcoEscambo`
- WAMP: `www/ProjetoEcoEscambo`
- Laragon: `www/ProjetoEcoEscambo`

Nesse modo, ajuste `APP_BASE_PATH` em `backend/config/app.php`:

```php
const APP_BASE_PATH = '/ProjetoEcoEscambo';
```

Depois abra:

```text
http://localhost/ProjetoEcoEscambo/backend/health_check.php
```

Se estiver tudo certo, a resposta sera JSON com `success: true`.

Variaveis de ambiente `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS` ainda funcionam, mas nao sao obrigatorias.

## Rotas

- `auth/register.php` `POST`: campos `nome`, `email`, `senha`, `confirmacao_senha`.
- `auth/activate.php?id=ID` `GET`: ativa conta simulando confirmacao por link.
- `auth/login.php` `POST`: campos `email`/`txt_email` e `senha`/`txt_senha`.
- `auth/logout.php` `GET`: encerra sessao.
- `products/create.php` `POST`: campos `nome`/`titulo`, `descricao`/`descricaoP`, `foto_url`.
- `products/catalog.php?page=1&per_page=10` `GET`: lista produtos em aberto de outros utilizadores.
- `products/my_products.php` `GET`: lista produtos do utilizador logado. Use `com_interessados=1` para filtrar.
- `interests/create.php` `POST`: campo `produto_id`.
- `interests/received.php?produto_id=ID` `GET`: lista interessados em um produto do ofertante.
- `interests/interested_products.php?interesse_id=ID` `GET`: lista produtos abertos do interessado.
- `interests/delete.php` `POST`: campo `interesse_id`, usado para "nao tenho interesse por nenhum".
- `proposals/create.php` `POST`: campos `interesse_id`, `produto_proposto_id`.
- `proposals/received.php` `GET`: propostas recebidas pelo interessado inicial.
- `proposals/reject.php` `POST`: campo `proposta_id`, deleta a proposta recusada.
- `proposals/accept.php` `POST`: campo `proposta_id`, finaliza os dois produtos e retorna contato do ofertante.

Todas as rotas protegidas exigem sessao criada por `auth/login.php`.

Para proteger uma pagina renderizada por PHP, renomeie a tela de `.html` para `.php` e inclua no topo:

```php
<?php require_once __DIR__ . '/../../backend/middleware/protect_page.php'; ?>
```

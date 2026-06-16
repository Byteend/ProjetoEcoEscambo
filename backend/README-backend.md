# Backend (PHP) para ProjetoEcoEscambo

Como usar (local, SQLite):

1. Aceda à pasta `backend` e execute a migração:

```bash
php migrate.php
```

2. Os endpoints principais (retornam JSON):
- `register.php` (POST): `name,email,password,password2` — cria conta e permite login imediato.
- `login.php` (POST): `email,password` — inicia sessão PHP.
- `logout.php` (GET)
- `products.php` (GET): `page`, `per_page` — lista catálogo (apenas `aberto`).
- `product.php` (GET): `id` — detalhe do produto.
- `my_products.php` (GET/POST) — listar/criar produtos do utilizador autenticado.
- `interest.php` (POST): `product_id` — registar interesse (autenticado).
- `offers_received.php` (GET/POST) — ver interesses recebidos; `POST action=reject|reject_all`.
- `propose.php` (POST): `interest_id`, `offered_product_id` — ofertante cria proposta escolhendo um produto do utilizador-interessado.
- `accept_proposal.php` (POST): `proposal_id` — interessado aceita e os dois produtos vão para `finalizada`.
- `reject_proposal.php` (POST): `proposal_id` — rejeitar proposta.

Nota: os endpoints foram reorganizados em `backend/api/` e os ficheiros de suporte estão em `backend/lib/`. O ficheiro de migração está em `backend/migrations/migrations.sql` e o runner `migrate.php` permanece em `backend/`.

3. Observações:
- A aplicação usa SQLite (ficheiro em `backend/data/database.sqlite`).
- O registo cria a conta ativa imediatamente.
- Para autenticação, use sessões PHP (cookies do browser). Use chamadas via formulário ou fetch/XHR mantendo cookies.

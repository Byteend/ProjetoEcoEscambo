<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$interessadoId = current_user_id();

$stmt = db()->prepare(
    'SELECT pt.id AS proposta_id, pt.estado AS proposta_estado, pt.criado_em,
            i.id AS interesse_id,
            produto_original.id AS produto_original_id,
            produto_original.nome AS produto_original_nome,
            produto_original.descricao AS produto_original_descricao,
            produto_original.foto_url AS produto_original_foto_url,
            produto_proposto.id AS produto_proposto_id,
            produto_proposto.nome AS produto_proposto_nome,
            produto_proposto.descricao AS produto_proposto_descricao,
            produto_proposto.foto_url AS produto_proposto_foto_url,
            ofertante.id AS ofertante_id,
            ofertante.nome AS ofertante_nome
     FROM propostas_troca pt
     INNER JOIN interesses i ON i.id = pt.interesse_id
     INNER JOIN produtos produto_original ON produto_original.id = i.produto_id
     INNER JOIN produtos produto_proposto ON produto_proposto.id = pt.produto_proposto_id
     INNER JOIN utilizadores ofertante ON ofertante.id = i.ofertante_id
     WHERE i.interessado_id = :interessado_id
       AND pt.estado = "Pendente"
     ORDER BY pt.criado_em DESC, pt.id DESC'
);
$stmt->execute(['interessado_id' => $interessadoId]);

success_response('Propostas recebidas encontradas.', [
    'items' => $stmt->fetchAll(),
]);

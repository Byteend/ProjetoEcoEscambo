<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$interessadoId = current_user_id();
$propostaId = input_int('proposta_id');

if ($propostaId <= 0) {
    error_response('Proposta invalida.', 422);
}

$pdo = db();

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'SELECT pt.id AS proposta_id, pt.estado AS proposta_estado,
                pt.produto_proposto_id,
                i.id AS interesse_id, i.produto_id AS produto_original_id,
                i.interessado_id, i.ofertante_id,
                ofertante.nome AS ofertante_nome, ofertante.email AS ofertante_email,
                produto_original.estado AS produto_original_estado,
                produto_proposto.estado AS produto_proposto_estado
         FROM propostas_troca pt
         INNER JOIN interesses i ON i.id = pt.interesse_id
         INNER JOIN utilizadores ofertante ON ofertante.id = i.ofertante_id
         INNER JOIN produtos produto_original ON produto_original.id = i.produto_id
         INNER JOIN produtos produto_proposto ON produto_proposto.id = pt.produto_proposto_id
         WHERE pt.id = :proposta_id AND i.interessado_id = :interessado_id
         FOR UPDATE'
    );
    $stmt->execute([
        'proposta_id' => $propostaId,
        'interessado_id' => $interessadoId,
    ]);
    $proposal = $stmt->fetch();

    if (!$proposal) {
        $pdo->rollBack();
        error_response('Proposta nao encontrada para este utilizador.', 404);
    }

    if ($proposal['proposta_estado'] !== 'Pendente') {
        $pdo->rollBack();
        error_response('Esta proposta nao esta pendente.', 409);
    }

    if ($proposal['produto_original_estado'] !== 'Em aberto' || $proposal['produto_proposto_estado'] !== 'Em aberto') {
        $pdo->rollBack();
        error_response('Um dos produtos envolvidos ja nao esta aberto para troca.', 409);
    }

    $updateProposal = $pdo->prepare('UPDATE propostas_troca SET estado = "Aceita" WHERE id = :proposta_id');
    $updateProposal->execute(['proposta_id' => $propostaId]);

    $updateProducts = $pdo->prepare(
        'UPDATE produtos
         SET estado = "Finalizada"
         WHERE id IN (:produto_original_id, :produto_proposto_id)'
    );
    $updateProducts->execute([
        'produto_original_id' => (int) $proposal['produto_original_id'],
        'produto_proposto_id' => (int) $proposal['produto_proposto_id'],
    ]);

    $deleteOtherProposals = $pdo->prepare(
        'DELETE pt
         FROM propostas_troca pt
         INNER JOIN interesses i ON i.id = pt.interesse_id
         WHERE pt.id <> :proposta_id
           AND (i.produto_id IN (:produto_original_id_a, :produto_proposto_id_a)
                OR pt.produto_proposto_id IN (:produto_original_id_b, :produto_proposto_id_b))'
    );
    $deleteOtherProposals->execute([
        'proposta_id' => $propostaId,
        'produto_original_id_a' => (int) $proposal['produto_original_id'],
        'produto_proposto_id_a' => (int) $proposal['produto_proposto_id'],
        'produto_original_id_b' => (int) $proposal['produto_original_id'],
        'produto_proposto_id_b' => (int) $proposal['produto_proposto_id'],
    ]);

    $pdo->commit();

    success_response('Proposta aceita. Os produtos foram finalizados e sairam do catalogo.', [
        'contato_ofertante' => [
            'id' => (int) $proposal['ofertante_id'],
            'nome' => $proposal['ofertante_nome'],
            'email' => $proposal['ofertante_email'],
        ],
        'produtos_finalizados' => [
            (int) $proposal['produto_original_id'],
            (int) $proposal['produto_proposto_id'],
        ],
    ]);
} catch (PDOException $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_response('Nao foi possivel aceitar a proposta.', 500);
}

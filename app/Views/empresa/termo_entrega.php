<?php
/**
 * View Termo de Conclusão e Entrega de Projeto (PDF / Impressão)
 * Desenvolvido por Vitor Hugo Dos Santos Pedrini
 * 100% Bootstrap 5 - Zero estilos inline
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo de Entrega - <?= htmlspecialchars($projeto['nome'] ?? 'Projeto') ?> | IISoul</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light text-dark py-4">

<div class="container my-3">
    <!-- Barra de ações superior (oculta na impressão) -->
    <div class="d-print-none mb-4 p-3 bg-white border rounded shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= $urlBase ?>/empresa/projeto?id=<?= (int)$projeto['id'] ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Projeto
            </a>
            <span class="text-muted small">Termo gerado pronto para exportação/impressão em PDF (Ctrl + P)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir / Salvar em PDF
            </button>
        </div>
    </div>

    <!-- Documento Formal do Termo de Entrega -->
    <div class="bg-white border rounded p-4 p-md-5 shadow-sm">
        <!-- Cabeçalho Institucional -->
        <div class="row align-items-center border-bottom pb-4 mb-4">
            <div class="col-8">
                <h3 class="fw-bold text-primary mb-1"><?= htmlspecialchars($empresa['empresa_nome'] ?? $empresa['nome'] ?? 'IISoul Soluções Digitais') ?></h3>
                <p class="text-muted mb-0 small">
                    CNPJ: <?= htmlspecialchars($empresa['cnpj'] ?? 'Não informado') ?> &bull; 
                    E-mail: <?= htmlspecialchars($empresa['email'] ?? 'contato@suamarca.com') ?> &bull; 
                    Tel: <?= htmlspecialchars($empresa['telefone'] ?? '(00) 0000-0000') ?>
                </p>
            </div>
            <div class="col-4 text-end">
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Concluído & Entregue
                </span>
                <div class="text-muted small mt-1">Data: <?= date('d/m/Y') ?></div>
            </div>
        </div>

        <div class="text-center my-4">
            <h4 class="fw-bold text-uppercase tracking-wide">Termo de Homologação, Conclusão e Entrega</h4>
            <p class="text-muted small mb-0">Documento de encerramento do ciclo de desenvolvimento e termo de quitação/entrega de software.</p>
        </div>

        <!-- Dados do Contratante e do Projeto -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card h-100 border">
                    <div class="card-header bg-light py-2 fw-semibold small text-uppercase">
                        <i class="bi bi-building me-1"></i> Dados do Contratante
                    </div>
                    <div class="card-body small">
                        <div class="mb-1"><strong>Razão Social / Nome:</strong> <?= htmlspecialchars($cliente['nome'] ?? 'Não informado') ?></div>
                        <div class="mb-1"><strong>CNPJ / CPF:</strong> <?= htmlspecialchars($cliente['cnpj'] ?? 'Não informado') ?></div>
                        <div class="mb-1"><strong>Contato Responsável:</strong> <?= htmlspecialchars($cliente['contato'] ?? $cliente['nome'] ?? '') ?></div>
                        <div class="mb-1"><strong>E-mail:</strong> <?= htmlspecialchars($cliente['email'] ?? '') ?></div>
                        <div class="mb-0"><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone'] ?? 'Não informado') ?></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card h-100 border">
                    <div class="card-header bg-light py-2 fw-semibold small text-uppercase">
                        <i class="bi bi-folder-check me-1"></i> Dados do Projeto
                    </div>
                    <div class="card-body small">
                        <div class="mb-1"><strong>Identificador:</strong> #PROJ-<?= str_pad((string)$projeto['id'], 4, '0', STR_PAD_LEFT) ?></div>
                        <div class="mb-1"><strong>Nome do Projeto:</strong> <?= htmlspecialchars($projeto['nome']) ?></div>
                        <div class="mb-1"><strong>Categoria / Tipo:</strong> <?= htmlspecialchars($projeto['tipo'] ?? 'Desenvolvimento Web/Mobile') ?></div>
                        <div class="mb-1"><strong>Progresso Técnico:</strong> 100% Concluído</div>
                        <div class="mb-0"><strong>Prazo de Entrega:</strong> <?= !empty($projeto['prazo_entrega']) ? date('d/m/Y', strtotime($projeto['prazo_entrega'])) : 'Finalizado no prazo' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descrição e Escopo Realizado -->
        <div class="card border mb-4">
            <div class="card-header bg-light py-2 fw-semibold small text-uppercase">
                <i class="bi bi-card-checklist me-1"></i> Resumo do Escopo Entregue
            </div>
            <div class="card-body small">
                <p class="mb-2"><?= nl2br(htmlspecialchars($projeto['descricao'] ?? 'Desenvolvimento integral do sistema conforme briefing aprovado, incluindo levantamento de requisitos, arquitetura de software, prototipação UI/UX, homologação assistida e implantação em ambiente definitivo.')) ?></p>
                <div class="row g-2 mt-2">
                    <div class="col-sm-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Ambiente de Produção:</strong> 
                            <?= !empty($projeto['link_producao']) ? htmlspecialchars($projeto['link_producao']) : 'Implantado no servidor do cliente' ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Repositório / Protótipo:</strong>
                            <?= !empty($projeto['link_prototipo']) ? htmlspecialchars($projeto['link_prototipo']) : 'Código entregue integralmente' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro e Parcelas -->
        <div class="card border mb-4">
            <div class="card-header bg-light py-2 fw-semibold small text-uppercase d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-1"></i> Situação Financeira do Projeto</span>
                <span class="badge <?= ($projeto['status_financeiro'] ?? '') === 'pago' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    Status: <?= strtoupper($projeto['status_financeiro'] ?? 'pendente') ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Descrição da Parcela</th>
                                <th>Valor (R$)</th>
                                <th>Vencimento</th>
                                <th>Data Pagamento</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($parcelas)): ?>
                                <?php foreach ($parcelas as $parc): ?>
                                    <tr>
                                        <td><?= (int)$parc['numero_parcela'] ?></td>
                                        <td><?= htmlspecialchars($parc['descricao']) ?></td>
                                        <td>R$ <?= number_format((float)$parc['valor'], 2, ',', '.') ?></td>
                                        <td><?= !empty($parc['data_vencimento']) ? date('d/m/Y', strtotime($parc['data_vencimento'])) : '-' ?></td>
                                        <td><?= !empty($parc['data_pagamento']) ? date('d/m/Y', strtotime($parc['data_pagamento'])) : '-' ?></td>
                                        <td>
                                            <?php if ($parc['status'] === 'pago'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success">Quitada</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td>1</td>
                                    <td>Projeto Integral</td>
                                    <td>R$ <?= number_format((float)($projeto['valor_orcamento'] ?? 0), 2, ',', '.') ?></td>
                                    <td><?= date('d/m/Y') ?></td>
                                    <td><?= date('d/m/Y') ?></td>
                                    <td><span class="badge bg-success-subtle text-success border border-success">Quitada</span></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cláusula de Aceite e Garantia -->
        <div class="p-3 border rounded bg-light small mb-5">
            <h6 class="fw-bold mb-2 text-uppercase fs-7"><i class="bi bi-shield-check me-1"></i> Declaração de Aceite Final</h6>
            <p class="mb-0 text-muted">
                Pelo presente instrumento, a CONTRATANTE atesta que os serviços contratados foram integralmente executados a contento, 
                tendo sido realizados todos os testes de homologação, recebidos os arquivos de código-fonte, credenciais e banco de dados. 
                Inicia-se a partir desta data o período de garantia legal de 90 (noventa) dias para eventuais correções de inconsistências no escopo homologado.
            </p>
        </div>

        <!-- Assinaturas Formais -->
        <div class="row pt-4 text-center">
            <div class="col-6">
                <div class="border-top pt-2 mx-3">
                    <p class="fw-bold mb-0 small"><?= htmlspecialchars($empresa['empresa_nome'] ?? $empresa['nome'] ?? 'IISoul Soluções Digitais') ?></p>
                    <span class="text-muted small">Contratada / Responsável Técnico</span>
                </div>
            </div>
            <div class="col-6">
                <div class="border-top pt-2 mx-3">
                    <p class="fw-bold mb-0 small"><?= htmlspecialchars($cliente['nome'] ?? 'Contratante') ?></p>
                    <span class="text-muted small">Contratante / Aceite Digital</span>
                </div>
            </div>
        </div>

        <!-- Rodapé Legal -->
        <div class="text-center text-muted small mt-5 pt-4 border-top">
            <p class="mb-0">Sistema IISoul &bull; Desenvolvido por Vitor Hugo Dos Santos Pedrini &bull; Termo emitido digitalmente em <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</div>

</body>
</html>

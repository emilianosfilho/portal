$(document).ready(function() {
    
    // ====================================================================
    // 1. INICIALIZAÇÃO DE COMPONENTES BÁSICOS
    // ====================================================================
    
    $('#myModal0').modal('show');
    $('#myModal1').modal('show');

    const tb_radar = $('#tb_radar').DataTable({
        searching: false, 
        paging: false,
        ordering: false,
        info: false,
        responsive: true
    });

    $('#CODPECA').focus();


    // Captura a mudança no input de quantidade e salva na sessão
    $(document).on('change', '.input-qtd', function() {
        let $input = $(this);
        let $tr = $input.closest('tr');
        let index = $tr.data('index');
        let novaQtd = $input.val();

        $.post('pages/vendas/ajax.php', {
            acao: 'update_quantity', // Precisará adicionar esse case no seu ajax.php
            index: index,
            quantidade: novaQtd
        }, function(response) {
            console.log('Quantidade atualizada no índice ' + index);
        });
    });

    // Atualiza o total visual e a sessão no servidor em tempo real
    $(document).on('input', '.js-input-qtd', function() {
        let $input = $(this);
        let $tr = $input.closest('tr');
        let index = $tr.data('index');
        
        // 1. Pega os valores numéricos
        let qtd = parseFloat($input.val()) || 0;
        let precoUnitario = parseFloat($tr.find('.js-valor-unitario').attr('data-preco')) || 0;
        
        // 2. Realiza o cálculo do Total
        let total = qtd * precoUnitario;
        
        // 3. Formata para o padrão brasileiro (R$)
        let totalFormatado = total.toLocaleString('pt-br', {
            style: 'currency', 
            currency: 'BRL'
        });
        
        // 4. Atualiza a tela (limpa se o total for zero)
        if (total > 0) {
            $tr.find('.js-total-linha').text(totalFormatado);
        } else {
            $tr.find('.js-total-linha').text('');
        }

        // 5. Envia para o servidor salvar na sessão (AJAX)
        $.post('pages/vendas/ajax.php', {
            acao: 'update_quantity',
            index: index,
            quantidade: qtd
        }, function(response) {
            // Log opcional para monitorar a sincronização
            console.log('Sessão atualizada para o índice ' + index);
        });
    });

    // Evita que as setas do teclado (navegação da tabela) entrem em conflito 
    // se o usuário estiver digitando no campo de quantidade
    $(document).on('keydown', '.input-qtd', function(e) {
        e.stopPropagation(); 
    });

    // ====================================================================
    // 2. LÓGICA DE SELEÇÃO DE PEÇAS (CHECKBOXES / SESSÃO)
    // ====================================================================

    /**
     * Função Centralizada para Alternar Seleção
     * Utilizada tanto pelo clique quanto pela tecla Espaço
     */
    function alternarSelecao(tr) {
        if (!tr || tr.length === 0) return;

        let icon = tr.find('i.row-check');
        let index = tr.data('index');
        let isSelected = icon.hasClass('fa-square'); // Se tem quadrado vazio, vamos marcar

        // 1. Toggle visual (Bootstrap 5 + FontAwesome)
        if (isSelected) {
            icon.removeClass('fa-regular fa-square text-secondary').addClass('fa-solid fa-square-check text-warning');
        } else {
            icon.removeClass('fa-solid fa-square-check text-warning').addClass('fa-regular fa-square text-secondary');
        }

        // 2. Chamada AJAX para atualizar sessão procedural
        $.post('pages/vendas/ajax.php', {
            acao: 'update_selection',
            index: index,
            status: isSelected
        }, function(response) {
            console.log('Linha ' + index + ' sincronizada na sessão.');
        }).fail(function() {
            console.error('Erro ao atualizar seleção via AJAX');
        });
    }

    // Clique no checkbox individual
    $(document).on('click', '.td-check', function(e) {
        e.stopPropagation();
        alternarSelecao($(this).closest('tr'));
    });

    // Lógica para Selecionar/Desmarcar Tudo
    $(document).on('click', '.row-check-all', function() {
        let isSelectingAll = $(this).hasClass('fa-square'); 
        let iconHeader = $(this);
        let iconsRows  = $('.row-check');

        if (isSelectingAll) {
            iconHeader.removeClass('fa-regular fa-square text-secondary').addClass('fa-solid fa-square-check text-warning');
            iconsRows.removeClass('fa-regular fa-square text-secondary').addClass('fa-solid fa-square-check text-warning');
        } else {
            iconHeader.removeClass('fa-solid fa-square-check text-warning').addClass('fa-regular fa-square text-secondary');
            iconsRows.removeClass('fa-solid fa-square-check text-warning').addClass('fa-regular fa-square text-secondary');
        }

        $.ajax({
            url: 'pages/vendas/ajax.php',
            type: 'POST',
            data: { acao: 'update_all_selection', status: isSelectingAll },
            dataType: 'json'
        });
    });

    // ====================================================================
    // 3. NAVEGAÇÃO POR TECLADO E ATALHOS
    // ====================================================================

    let linhaSelecionada = 0;
    const $tbRadarBody = $('#tb_radar tbody');

    function linhasVisiveis() {
        return $tbRadarBody.find('tr:visible');
    }

    function atualizarSelecao() {
        const linhas = linhasVisiveis();
        if (!linhas.length) return;

        linhas.removeClass('table-active');
        const linha = linhas.eq(linhaSelecionada);
        linha.addClass('table-active');
        
        if (linha[0]) {
            linha[0].scrollIntoView({ block: "center", behavior: "smooth" });
        }

        
        // Dados complementares
        const container = document.getElementById('dados-complementares');
        const infoExtra = document.getElementById('info-extra');
        const codprod = linha.data("wint");
        console.log(linha.data("wint"));
        console.log(linha.data("locacao"));
        console.log(linha.data("fornecedor"));
        console.log(linha.data("dtultent"));

        if (codprod && String(codprod).trim() !== "") {
            container.style.display = 'block';
            infoExtra.innerHTML = `<strong>Locação:</strong> ${linha.data("locacao")} | <strong>Fornecedor:</strong> ${linha.data("fornecedor")} | <strong>Última Entrada:</strong> ${linha.data("dtultent")}`;
        } else {
            container.style.display = 'block';
            infoExtra.innerHTML = `&nbsp;`;
        }
    }

    atualizarSelecao();
    
    // Gatilho para exportação Excel
    $(document).on('click', '#btnExportarExcel', function() {
        // Abre o arquivo de exportação em uma nova aba
        window.open('pages/vendas/radar_exportExcel.php', '_blank');
    });
    
    // Gatilho para exportação Excel
    $(document).on('click', '#btnExportarOrcamento', function() {
        // Abre o arquivo de exportação em uma nova aba
        window.open('pages/vendas/radar_exportOrcamento.php', '_blank');
    });
    


    $(document).on("keydown", function (e) {
        const linhas = linhasVisiveis();
        if (!linhas.length) return;

        // --- TECLA ESPAÇO (SELEÇÃO) ---
        if (e.key === " " || e.code === "Space") {
            // Não dispara se estiver digitando em campos de texto
            if (!$(e.target).is('input, textarea, select')) {
                e.preventDefault();
                const trAtiva = linhas.eq(linhaSelecionada);
                alternarSelecao(trAtiva);
            }
        }

        // --- SETAS DE NAVEGAÇÃO ---
        if (e.key === "ArrowDown") {
            e.preventDefault();
            if (linhaSelecionada < linhas.length - 1) linhaSelecionada++;
            atualizarSelecao();
        }

        if (e.key === "ArrowUp") {
            e.preventDefault();
            if (linhaSelecionada > 0) linhaSelecionada--;
            atualizarSelecao();
        }

        // --- ATALHOS ALT ---
        if (e.altKey) {
            const tecla = e.key.toLowerCase();
            const numOriginal = linhas.eq(linhaSelecionada).data("numoriginal");

            switch (tecla) {
                case 'p': e.preventDefault(); $('#CODPECA').focus(); break;
                case 'v': e.preventDefault(); if (numOriginal) abrirModalHistorico('vendas', numOriginal); break;
                case 'c': e.preventDefault(); if (numOriginal) abrirModalHistorico('compras', numOriginal); break;
                case 'o': e.preventDefault(); if (numOriginal) abrirModalHistorico('orcamentos', numOriginal); break;
                case 'l': e.preventDefault(); window.location.href = "radar.php?acao=clear"; break;
            }
        }
    });

    // ====================================================================
    // 4. FUNÇÃO PARA REQUISIÇÃO AJAX DOS MODAIS
    // ====================================================================
    
    window.abrirModalHistorico = function(tipo, numOriginal) {
        const config = {
            'vendas': { modalId: 'radar_modalVendas', titleId: 'titleVendas', bodyId: 'conteudoVendas', acaoAjax: 'buscaHistVendas', colspan: 8, gerarLinha: (item) => `<td>${item.CODPECA || '-'}</td><td>${item.NUMTRANS || '-'}</td><td>${item.DATA || '-'}</td><td><small>${item.VENDEDOR || 'N/A'}</small></td><td><small>${item.CLIENTE || 'N/A'}</small></td><td>${item.PRODUTO || 'N/A'}</td><td>${item.MARCA || 'N/A'}</td><td>${item.QUANTIDADE || 0}</td><td class="text-end fw-bold">${item.PRECO || 0}</td>` },
            'compras': { modalId: 'radar_modalCompras', titleId: 'titleCompras', bodyId: 'conteudoCompras', acaoAjax: 'buscaHistCompras', colspan: 7, gerarLinha: (item) => `<td>${item.CODPECA || '-'}</td><td>${item.NUMTRANS || '-'}</td><td>${item.DATA || '-'}</td><td><small>${item.FORNECEDOR || 'N/A'}</small></td><td>${item.PRODUTO || 'N/A'}</td><td>${item.QUANTIDADE || 0}</td><td class="text-end fw-bold">${item.PRECO || 0}</td>` },
            'orcamentos': { modalId: 'radar_modalOrcamentos', titleId: 'titleOrcamentos', bodyId: 'conteudoOrcamentos', acaoAjax: 'buscaHistOrcamentos', colspan: 8, gerarLinha: (item) => `<td>${item.NUMORIGINAL || '-'}</td><td>${item.IDCOTACOES || '-'}</td><td>${item.DATA || '-'}</td><td><small>${item.VENDEDOR || 'N/A'}</small></td><td><small>${item.CLIENTE || 'N/A'}</small></td><td>${item.PRODUTO || 'N/A'}</td><td>${item.QUANTIDADE || 0}</td><td class="text-end fw-bold">${item.PRECO || 0}</td>` }
        };

        const conf = config[tipo];
        if (!conf) return;

        const modalElement = document.getElementById(conf.modalId);
        const titleElem = document.getElementById(conf.titleId);
        const tbody = document.getElementById(conf.bodyId);

        titleElem.innerText = `Histórico de ${tipo.toUpperCase()}: ${numOriginal}`;
        tbody.innerHTML = `<tr><td colspan="${conf.colspan}" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Carregando dados do ERP...</td></tr>`;

        let modalInstancia = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modalInstancia.show();

        fetch(`pages/vendas/ajax.php?acao=${conf.acaoAjax}&NUMORIGINAL=${encodeURIComponent(numOriginal)}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = "";
                if (!data || data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${conf.colspan}" class="text-center text-muted py-3">Nenhum registro encontrado.</td></tr>`;
                    return;
                }
                data.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = conf.gerarLinha(item);
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error(`Erro ao buscar histórico:`, error);
                tbody.innerHTML = `<tr><td colspan="${conf.colspan}" class="text-center text-danger">Falha ao conectar ao banco Winthor.</td></tr>`;
            });
    };
});
<div class="container mt-3">
  <div class="col-12 mt-5">
  <?php 
    require_once("pages/admin/function.php");
    require_once("pages/chamados/function.php");
    require_once("pages/chamados/controller.php");

    if (isset($dados['ID_CHAMADO']) && !empty($dados['ID_CHAMADO'])) {
      $chamado = buscaDadosChamadoID($dados['ID_CHAMADO']);
      marcarNotificacaoLida($_SESSION['login']["IDUSUARIO"], $dados['ID_CHAMADO']);
    } else {
      exibeMensagem("ID_CHAMADO inválido ou não localizado!");
      redireciona("index.php?op=35");
    }
  ?>

  <main>


    <div class="row">
      <div class="col-2 mb-4 px-0">
        <div class="card shadow-sm border-0 bg-light">
          <div class="card-body text-md">
            <h5 class="card-title border-bottom pb-2 mb-3">
              <i class="fa-solid fa-headset me-2"></i>
              Chamado #<?=$chamado['ID_CHAMADO']?>
            </h5>

            <div class="mb-2">
              <i class="fa-regular fa-calendar-plus me-1 text-muted"></i>
              <span class="small text-muted">Abertura:</span>
              <div class="small fw-bold"><?=formataDataOracleToBr($chamado['DATA_ABERTURA'])?></div>
              <small class="text-muted"><?=calcular_tempo_relativo($chamado['DATA_ABERTURA'])?></small>
            </div>

            <?php if ($chamado['DATA_PREVISAO']): ?>
            <div class="mb-4">
              <i class="fa-regular fa-calendar-check me-1 text-muted"></i>
              <span class="small text-muted">Previsão:</span>
              <div class="small fw-bold text-danger"><?=formataDataOracleToBr($chamado['DATA_PREVISAO'])?></div>
            </div>
            <?php endif ?>
            
            <div class="mb-3">
              <i class="fa-regular fa-user me-1 text-muted"></i>
              <label class="text-muted small fw-bold uppercase">Solicitante</label><br>
              <small class="text-muted"><?=obterPrimeiroEUltimoNome($chamado['USUARIO'])?></small>
            </div>
            
            <div class="mb-3">
              <i class="fa-regular fa-list-check me-1 text-muted"></i>
              <label class="text-muted small fw-bold uppercase">Categoria / Tipo</label>
              <p class="mb-0">
                <span class="badge bg-secondary"><?=$chamado['CATEGORIA']?></span><br>
                <small class="text-muted"><?=$chamado['TIPO']?></small>
              </p>
            </div>

            <div class="mb-3">
              <i class="fa-regular fa-check-circle me-1 text-muted"></i>
              <label class="text-muted small fw-bold uppercase">Status</label>
              <p>
                <span class="badge bg-primary"><?=$chamado['STATUS']?></span><br>
                <small class="text-muted"><?=obterPrimeiroEUltimoNome($chamado['TECNICO'])?></small>
              </p>
            </div>

            <?php if (array_search('ADM_CHAMADOS', $_SESSION['login']['PERMISSOES']) !== false): ?>
              
            <?php endif ?>
            <div class="mb-3">
              <i class="fa-solid fa-bolt-lightning me-1 text-muted"></i>
              <span class="small text-muted">Ações:</span>              
              <div class="d-grid gap-2">
              <div class="btn-group-vertical">
                <a href="index.php?op=36&acao=modalEditarPrevisao&ID_CHAMADO=<?=$chamado['ID_CHAMADO']?>&DATA_PREVISAO=<?=$chamado['DATA_PREVISAO']?>" class="btn btn-outline-secondary d-print-none"><i class="fas fa-clock"></i> Previsão</a>
                <?php
                switch ($chamado["STATUS"]) {
                   case 'PAUSADO':  
                      if (in_array($_SESSION['login']['IDUSUARIO'], [1, 1121])) {
                        echo '<a href="index.php?op=36&ID_CHAMADO='.$chamado['ID_CHAMADO'].'&acao=reiniciarAtendimento" class="btn btn-outline-secondary"><i class="fas fa-play"></i> Reiniciar</a>';
                      }
                     break;
                   case 'EM ATENDIMENTO':
                      if (in_array($_SESSION['login']['IDUSUARIO'], [1, 1121])) {
                        echo '<a href="index.php?op=36&ID_CHAMADO='.$chamado['ID_CHAMADO'].'&acao=pausarAtendimento" class="btn btn-outline-secondary"><i class="fas fa-pause"></i> Pausar</a>';
                      }
                     break;
                   case 'AGUARDANDO':
                      if (in_array($_SESSION['login']['IDUSUARIO'], [1, 1121])) {
                        echo '<a href="index.php?op=36&ID_CHAMADO='.$chamado['ID_CHAMADO'].'&ID_USUARIO='.$chamado['ID_USUARIO'].'&acao=iniciarAtendimento" class="btn btn-outline-secondary"><i class="fas fa-play"></i> Iniciar</a>';
                      }
                     break;
                } 
                if ($chamado["STATUS"] != 'FINALIZADO') {
                  echo '<a href="index.php?op=36&acao=modalFinalizar&ID_CHAMADO='.$chamado['ID_CHAMADO'].'" class="btn btn-outline-secondary d-print-none"><i class="fas fa-check"></i> Finalizar</a>';
                }
                ?> 
                <a href="index.php?op=35&nav=chamados" class="btn btn-outline-secondary d-print-none"><i class="fas fa-undo"></i> Voltar</a>

              </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-10 px-0">
        <div class="card shadow-sm border-0">




          <div id="historico-container" class="card-body bg-light" style="height: 500px; overflow-y: scroll !important; background-image: url('dist/img/background_chamado.png'); position: relative;">
              
        <?php 
        $historicosRaw = buscaHistoricoChamado($dados['ID_CHAMADO']) ?: [];
        $historicos = $historicosRaw; 
        $ultimoUsuario = null; 

        foreach ($historicos as $index => $hist): 
            $proximoHist = $historicos[$index + 1] ?? null;
            
            // Identifica se a mensagem é do usuário logado (Comparação de String ou ID)
            $isMe = ($hist['USUARIO'] == $_SESSION['login']['NOME']);
            
            // Agrupamento: esconde cabeçalho se for o mesmo autor da anterior
            $exibirCabecalho = ($hist['USUARIO'] !== $ultimoUsuario);
            
            // Espaçamento dinâmico
            $mesmoUsuarioProximo = ($proximoHist && $proximoHist['USUARIO'] == $hist['USUARIO']);
            $marginBottom = $mesmoUsuarioProximo ? 'mb-1' : 'mb-3';
        ?>
    
    <div class="d-flex <?= $marginBottom ?> <?= $isMe ? 'justify-content-end' : 'justify-content-start' ?>">
      
      <div class="card shadow-sm p-2 px-3 border-0" 
           style="max-width: 80%; 
                  border-radius: 15px;
                  <?= $isMe 
                      ? 'background-color: #dcf8c6; border-top-right-radius: 2px;' 
                      : 'background-color: #ffffff; border-top-left-radius: 2px;' ?>
                  <?= !$exibirCabecalho ? 'border-radius: 15px !important;' : '' ?>">
        
        <?php if ($exibirCabecalho): ?>
          <div class="d-flex align-items-center mb-1">
            <img src="<?= @DIR_IMG.$hist['AVATAR'] ?>" class="rounded-circle me-2" width="20" height="20" onerror="this.src='dist/img/avatar_padrao.png'">
            <span class="fw-bold" style="font-size: 0.75rem; color: <?= $isMe ? '#075e54' : '#0d6efd' ?>;">
                <?= obterPrimeiroEUltimoNome($hist['USUARIO']) ?>
            </span>
          </div>
        <?php endif; ?>

        <div class="text-dark" style="font-size: 0.92rem; line-height: 1.4; word-wrap: break-word;">
          <?php 
          if ($hist['TIPO'] == "HIST") {
            echo nl2br(htmlspecialchars($hist['HISTORICO']));
          } else {
            // $dir = DIR_UPLOAD;
            // if (file_exists($dir . $hist['HISTORICO'])) {
            //   echo '<a target="_blank" href="'.$dir . $hist['HISTORICO'].'">'.$hist['HISTORICO'].'</a>';
            // }

            $textoHistorico = $hist['HISTORICO'];
            $diretorioUpload = DIR_UPLOAD; // Definido no seu define.php

            $extensoesImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extensoesVideo  = ['mp4', 'webm', 'ogg'];
            
            // Limpeza simples para extrair o nome do arquivo se houver prefixos
            $arquivo = trim($textoHistorico);
            $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
            $caminhoCompleto = $diretorioUpload . $arquivo;

            // Se o arquivo não existir fisicamente, retorna o texto original
            if (file_exists($caminhoCompleto) || empty($extensao)) {

              $html = '<div class="anexo-container mt-2 p-2 rounded bg-light border">';
              
              if (in_array($extensao, $extensoesImagem)) {
                  // Estilo Miniatura Imagem
                  $html .= "<a href='{$caminhoCompleto}' target='_blank'>
                              <img src='{$caminhoCompleto}' class='img-fluid rounded' style='max-width: 250px; height: auto; display: block;'>
                            </a>";
              } elseif (in_array($extensao, $extensoesVideo)) {
                  // Estilo Player de Vídeo
                  $html .= "<video width='250' controls class='rounded'>
                              <source src='{$caminhoCompleto}' type='video/{$extensao}'>
                              Seu navegador não suporta vídeos.
                            </video>";
              } else {
                  // Estilo Ícone para Documentos (PDF, DOCX, etc)
                  $icon = 'fa-file-lines'; // Default
                  if ($extensao == 'pdf') $icon = 'fa-file-pdf text-danger';
                  if (in_array($extensao, ['zip', 'rar'])) $icon = 'fa-file-zipper text-warning';
                  if (in_array($extensao, ['xls', 'xlsx'])) $icon = 'fa-file-excel text-success';

                  $html .= "<a href='{$caminhoCompleto}' target='_blank' class='text-decoration-none d-flex align-items-center'>
                              <i class='fa-solid {$icon} fa-3x me-3'></i>
                              <div class='flex-grow-1'>
                                  <span class='d-block fw-bold text-dark'>{$arquivo}</span>
                                  <small class='text-muted'>Clique para baixar</small>
                              </div>
                            </a>";
              }

              $html .= '</div>';
              echo $html;
            }
            echo nl2br(htmlspecialchars($textoHistorico));
          }
          ?>
          
          <div class="text-end mt-1" style="font-size: 0.65rem; color: #8696a0;">
            <?= calcular_tempo_relativo($hist['DATA']) ?>
            <?php if ($isMe): ?>
                <i class="fa-solid fa-check-double ms-1" style="color: #53bdeb;"></i>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

<?php 
    $ultimoUsuario = $hist['USUARIO']; 
endforeach; 
?>

              <div id="final-do-chat"></div>

          </div>


          <div class="card border-1 shadow mt-1" style="border-radius: 20px; background-color: #f8f9fa;">
            <form id="formChamado" action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="op" value="36">
              <input type="hidden" name="nav" value="chamados">
              <input type="hidden" name="ID_CHAMADO" value="<?= $chamado['ID_CHAMADO'] ?>">
              <input type="hidden" name="ID_USUARIO" value="<?= $_SESSION['login']['IDUSUARIO'] ?>">
              
              <div class="card-body p-2">
                  <div class="input-group align-items-center">
                      
                      <label class="btn btn-link text-secondary m-0" for="anexo_chamado" title="Adicionar anexo">
                          <i class="fa-solid fa-paperclip fs-5"></i>
                          <input type="file" name="anexo" id="anexo_chamado" class="d-none">
                      </label>
                      <textarea name="HISTORICO" 
                                class="form-control border-0 bg-transparent shadow-none" 
                                rows="1" 
                                placeholder="Digite sua mensagem aqui..." 
                                style="resize: none; font-size: 0.95rem;" 
                                required></textarea>

                      <button type="submit" name="acao" value="addHistoricoChamado" class="btn btn-primary rounded-circle ms-2 d-flex align-items-center justify-content-center" 
                              style="width: 40px; height: 40px;">
                          <i class="fa-solid fa-paper-plane"></i>
                      </button>
                  </div>
              </div>
            </form>
          </div>


        </div>
      </div>
    </div>
  </main>
</div>

<script>
// 1. Script para expandir o textarea conforme o usuário digita
const textarea = document.querySelector('textarea[name="HISTORICO"]');
if (textarea) {
    textarea.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
}

// 2. Script para Auto-Submit ao selecionar anexo
const inputAnexo = document.getElementById('anexo_chamado');
const form = document.getElementById('formChamado');

if (inputAnexo && form) {
    inputAnexo.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'acao';
            hiddenInput.value = 'addAnexoChamado';
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
}

// 3. NOVO: Script para rolar o histórico até o fim ao carregar
document.addEventListener("DOMContentLoaded", function() {
    const containerHistorico = document.getElementById('historico-container');
    if (containerHistorico) {
        // Define o scroll para o máximo da altura do elemento
        containerHistorico.scrollTop = containerHistorico.scrollHeight;
    }
});
</script>
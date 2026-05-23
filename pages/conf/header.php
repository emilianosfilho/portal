<header>
  <nav class="navbar navbar-expand-md fixed-top bg-portal">
    <div class="container-fluid">

      <?php if (isset($_SESSION['login'])): ?>
      <a href="index.php" class="ms-5"><img src="<?=@DIR_IMG."logo_header.png"?>" height="40px"></a>
      <?php else: ?>
      <a href="#" class="ms-5"><img src="<?=@DIR_IMG."logo_header.png"?>" height="40px"></a>
      <?php endif; ?>
      <div class="float-end">

        <div class="collapse navbar-collapse float-start me-5" id="navbarsExample07">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-print-none">
            
            <?php if (isset($_SESSION['login']['PERMISSOES'])): ?>

              <?php if (array_search('ADMIN', $_SESSION['login']['PERMISSOES']) !== false): ?>
               <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='admin')?:' active')?>" href="index.php?op=11&nav=admin&aba=usuarios" title="ADMIN">
                  <i class="fa-solid fa-cogs fa-2x"></i>
                  <br><span>Admin</span> 
                </a>
               </li>
              <?php endif ?>


              <?php if (array_search('ECOMMERCE', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='ecommerce')?:' active')?>" href="index.php?op=171&nav=ecommerce&acao=limparLista&aba=tray" title="E-COMMERCE">
                  <i class="fa-solid fa-bag-shopping fa-2x"></i>
                  <br><span>E-Commerce</span> 
                </a>
              </li>
              <?php endif ?>

              <?php if (array_search('COMPRAS', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='compras')?:' active')?>" href="index.php?op=110&nav=compras&aba=produto" title="COMPRAS">
                  <i class="fa-solid fa-basket-shopping fa-2x"></i>
                  <br><span>Compras</span> 
                </a>
              </li>
              <?php endif ?>

              <?php if (array_search('LOGISTICA', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='logistica')?:' active')?>" href="index.php?op=131&nav=logistica&aba=produtos" title="LOGÍSTICA">
                  <i class="fa-solid fa-truck-fast fa-2x"></i>
                  <br><span>Logística</span> 
                </a>
              </li>
              <?php endif ?>

              <?php if (array_search('FINANCEIRO', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='financeiro')?:' active')?>" href="index.php?op=143&nav=financeiro" title="FINANCEIRO">
                  <i class="fa-solid fa-money-bill-1-wave fa-2x"></i> 
                  <br><span>Financeiro</span> 
                </a>
              </li>
              <?php endif ?>

              <?php if (array_search('VENDAS', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='vendas')?:' active')?>" href="index.php?op=68&nav=vendas" title="VENDAS">
                  <i class="fa-solid fa-cart-shopping fa-2x"></i>
                  <br><span>Vendas</span> 
                </a>
              </li>
              <?php endif ?>

              <?php if (array_search('RELATORIOS', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <li class="nav-item text-center">
                <a class="btn btn-xs border-0 btn-outline-light <?=(($dados['nav']!='relatorios')?:' active')?>" href="index.php?op=90&nav=relatorios" title="RELATORIOS">
                  <i class="fa-solid fa-file fa-2x"></i> 
                  <br><span>Relatórios</span> 
                </a>
              </li>
              <?php endif ?>

              <li class="nav-item dropdown text-center">
                  <?php 
                    $idUsuarioLogado = (int)$_SESSION['login']['IDUSUARIO'];
                    $activeClass = ($dados['nav'] === 'chamados') ? ' active' : '';
                      
                    // Buscamos as notificações (Array de objetos/arrays)
                    $notificacoes = buscarNotificacoesNaoLidas($idUsuarioLogado); 
                    $totalNotif   = count($notificacoes); 
                  ?>
                  
                  <a class="btn btn-xs border-0 btn-outline-light position-relative<?= $activeClass ?> dropdown-toggle hide-arrow" 
                     href="#" 
                     id="dropdownNotificacoes" 
                     data-bs-toggle="dropdown" 
                     aria-expanded="false"
                     title="NOTIFICAÇÕES">
                      
                      <i class="fa-solid fa-user-headset fa-2x"></i>
                      <br>
                      <span class="small">Chamados</span> 
                      <?php if ($totalNotif > 0): ?>
                        <span class="position-absolute top-50 start-100 translate-middle badge rounded-pill bg-danger text-sm"><?= $totalNotif > 99 ? '99+' : $totalNotif ?></span>
                        </span>
                      <?php endif ?>
                  </a>

                  <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="dropdownNotificacoes" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                      
                      <?php if ($totalNotif > 0) : ?>
                        <li><h6 class="dropdown-header">Notificações Pendentes</h6></li>
                          <?php foreach ($notificacoes as $notif) : ?>
                              <li>
                                  <a class="dropdown-item py-2 border-bottom border-secondary" href="index.php?op=36&ID_CHAMADO=<?= $notif['IDCHAMADO'] ?>">
                                      <div class="d-flex flex-column">
                                          <small class="text-info fw-bold">Chamado #<?= $notif['IDCHAMADO'] ?></small>
                                          <span class="text-wrap" style="font-size: 0.85rem;"><?= $notif['MENSAGEM'] ?></span>
                                          <small class="text-muted mt-1" style="font-size: 0.7rem;">
                                              <i class="fa-regular fa-clock"></i> <?= ($notif['DATA_FORMATADA']) ?>
                                          </small>
                                      </div>
                                  </a>
                              </li>
                          <?php endforeach; ?>
                          <li><hr class="dropdown-divider"></li>
                      <?php else : ?>
                        <li><h6 class="dropdown-header">Nenhuma notificação pendente</h6></li>
                      <?php endif; ?>
                        <li>
                          <a class="dropdown-item text-center small text-light" href="index.php?op=35&nav=chamados">
                            <i class="fa-regular fa-bars"></i>
                            Ver os meus chamados
                          </a>
                        </li>
                  </ul>
              </li>

            <?php endif ?>

          </ul>
        </div>

        

        <?php if (isset($_SESSION['login'])): ?>
        <ul class="navbar-nav mx-3 mb-3 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false"><img class="rounded-circle" width="35px" src="<?=@DIR_IMG.''.$_SESSION['login']['AVATAR']?>"> <?= obterPrimeiroEUltimoNome($_SESSION['login']['NOME']) ?></a>
            <ul class="dropdown-menu" aria-labelledby="dropdownUser">
              <li>
                <a class="dropdown-item" href="index.php?op=12&aba=usuarios">
                  <i class="fa-solid fa-user"></i> Meu Perfil
                </a>
              </li>
              <li>
                <a class="dropdown-item text-danger" href="login.php?acao=sair">
                  <i class="fa-solid fa-right-from-bracket"></i> Sair
                </a>
              </li>
            </ul>
          </li>
        </ul>
        <?php else: ?>
        <ul class="navbar-nav mr-auto">
          <li class="nav-item text-center">
            <a class="nav-link btn btn-warning btn-rounded" href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Fazer login</a>
          </li>
        </ul>
        <?php endif; ?>

      </div>

    </div>
  </nav>
</header>
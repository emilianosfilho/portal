  <div class="d-flex flex-column flex-shrink-0 bg-dark" style="width: 4.5rem;">
    <a href="#" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
    </a>
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
      <li class="nav-item">
        <a href="index.php?op=131&acao=limparLista&nav=logistica&aba=produtos" class="nav-link <?=(($dados['aba']=='produtos')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="Produtos" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-boxes fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=43&nav=logistica&aba=etiquetas" class="nav-link <?=(($dados['aba']=='etiquetas')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="etiquetas" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fas fa-tag fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=132&acao=limparLista&nav=logistica&aba=saida" class="nav-link <?=(($dados['aba']=='saida')?'active':'')?> py-4 border-bottom text-white" title="Saída" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-right-from-bracket"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=133&acao=limparLista&nav=logistica&aba=entrada" class="nav-link <?=(($dados['aba']=='entrada')?'active':'')?> py-4 border-bottom text-white" title="Entrada" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-left-to-bracket"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=134&acao=limparLista&nav=logistica&aba=inventario" class="nav-link <?=(($dados['aba']=='inventario')?'active':'')?> py-4 border-bottom text-white" title="Inventário" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-list-check"></i>
        </a>
      </li>
      <?php if (in_array("VIDE", $_SESSION['login']['PERMISSOES'])): ?>
      <li class="nav-item">
        <a href="index.php?op=40&acao=limparLista&nav=logistica&aba=vide" class="nav-link <?=(($dados['aba']=='vide')?'active':'')?> py-4 border-bottom text-white" title="Vide" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-retweet"></i>
        </a>
      </li>
      <?php endif; ?>
      <?php if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR"): ?>
      <li class="nav-item">
        <a href="index.php?op=149&acao=limparLista&nav=logistica&aba=equipamento" class="nav-link <?=(($dados['aba']=='equipamento')?'active':'')?> py-4 border-bottom text-white" title="Equipamentos" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-tractor"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=148&acao=limparLista&nav=logistica&aba=ficha" class="nav-link <?=(($dados['aba']=='ficha')?'active':'')?> py-4 border-bottom text-white" title="Ficha Técnica" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-regular fa-2x fa-book-atlas"></i>
        </a>
      </li>
      <?php endif; ?>

    </ul>
  </div>

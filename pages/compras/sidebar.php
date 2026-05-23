<div class="col-1">
  <div class="d-flex flex-column flex-shrink-0 bg-dark">
    <a href="#" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
    </a>
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
      <li class="nav-item">
        <a href="index.php?op=110&nav=compras&acao=clear&aba=produto" class="nav-link <?=(($dados['aba']=='produto')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="Produtos" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-boxes"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=117&nav=compras&aba=pedido&acao=clear" class="nav-link <?=(($dados['aba']=='pedido')?'active':'')?> py-4 border-bottom text-white" title="Pedido" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-basket-shopping"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=114&nav=compras&aba=precificacao&acao=clear" class="nav-link <?=(($dados['aba']=='precificacao')?'active':'')?> py-4 border-bottom text-white" title="Precificação" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-dollar"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=124&nav=compras&aba=analise&acao=clear" class="nav-link <?=(($dados['aba']=='analise')?'active':'')?> py-4 border-bottom text-white" title="Análise" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-2x fa-magnifying-glass-dollar"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=126&nav=compras&aba=classes&acao=clear" class="nav-link <?=(($dados['aba']=='classes')?'active':'')?> py-4 border-bottom text-white" title="Classes" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-regular fa-2x fa-file-signature"></i>
        </a>
      </li>
    </ul>
  </div>
</div>
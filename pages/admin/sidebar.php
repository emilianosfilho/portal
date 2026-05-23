<?php  

?>

  <div class="d-flex flex-column flex-shrink-0 bg-dark" style="width: 4.5rem;">
    <a href="#" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
    </a>
    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
        
      <?php if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR"): ?>
      <li class="nav-item">
        <a href="?op=11&nav=admin&aba=usuarios" class="nav-link <?=(($dados['aba']=='usuarios')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="Usuários" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-users fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=3&acao=limparLista&aba=oracle" class="nav-link <?=(($dados['aba']=='oracle')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="Banco de dados" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-database fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=4&acao=limparLista&aba=equipamento" class="nav-link <?=(($dados['aba']=='equipamento')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="Usuários" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-tractor fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=5&acao=limparLista&aba=upload" class="nav-link <?=(($dados['aba']=='upload')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="upload" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-upload fa-2x"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="index.php?op=6&acao=limparLista&aba=download" class="nav-link <?=(($dados['aba']=='download')?'active':'')?>  py-4 border-bottom text-white" aria-current="page" title="download" data-bs-toggle="tooltip" data-bs-placement="right">
          <i class="fa-solid fa-download fa-2x"></i>
        </a>
      </li>
      <?php endif ?>
      
    </ul>
  </div>
<div class="modal fade" id="modalNotificacoes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa-solid fa-bell"></i> Notificações
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">

        <?php foreach ($_SESSION['NOFIFICACOES'] as $key => $value): ?>
          <div class="direct-chat-msg">
            <div class="direct-chat-infos clearfix">
              <span class="direct-chat-name float-start"><?= $value['REMETENTE']?></span>
              <span class="direct-chat-timestamp float-end"><?= $value['DATA']?></span>
            </div>
            <img class="direct-chat-img" src="dist/img/<?= $value['AVATAR_REMETENTE']?>" alt="message user image">
            <div class="direct-chat-text">
              <?= $value['MENSAGEM']?>
            </div>
          </div>
        <?php endforeach ?>

        </div>
        <!--/.direct-chat-messages-->

      </div>
    </div>
  </div>
</div>


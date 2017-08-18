<script src="/public/js/jquery.jqote2.min.js"></script>
<script src="/public/js/message.agent.js"></script>
<section id="sec-message">
  <div class="container">
    <?php if ($_SESSION['agent']) : ?>
      <div class="container">
        <label>過往提問</label>
        <div id="message-list" style="width:95%;height:300px;border:solid;overflow:auto">
        </div>
        <div id="message-form">
          <div style="width:95%">
            <label>我要提問</label>
            <textarea style="width:100%;height:60px;"></textarea>
          </div>
          <div style="width:95%;text-align:center">
            <button id="message-cancel">取消</button>
            <button id="message-save" orgtxt="確認提出">確認提出</button>
          </div>
        </div>
      </div>
    <?php endif;?>
  </div>
</section>


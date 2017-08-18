<script src="/public/js/jquery.jqote2.min.js"></script>
<script src="/public/js/message.adm.js"></script>
<section id="sec-message">
  <div class="container">
    <?php if ($_SESSION['admin']) : ?>
      <div class="container">
        <label>問題列表</label>
        <div id="message-list" style="width:95%;height:300px;border:solid;overflow:auto">
        </div>
        <div id="message-form">
          <div style="width:75%">
            <label>回覆問題</label>
            <textarea style="width:100%;height:60px;"></textarea>
          </div>
          <div style="width:95%;text-align:center">
            <button id="message-cancel">取消</button>
            <button id="message-save" orgtxt="確認回覆">確認回覆</button>
          </div>
        </div>
      </div>
    <?php endif;?>
  </div>
</section>


<script src="/public/js/jquery.jqote2.min.js"></script>
<script src="/public/js/postman.adm.js"></script>
<section id="sec-postman">
  <div class="container">
    <?php if ($_SESSION['admin']) : ?>
      <div class="container">
        <div style="width:95%;">
          致：<select id="email-rcptto">
                <option value="1">所有單位人員</option>
                <option value="2">所有單位主管</option>
                <option value="3">所有單位職員</option>
                <option value="4">所有未填報單位聯絡人</option>
                <option value="9">只寄副本(測試收發用)</option>
              </select>
        </div>
        <div style="width:95%">
          <label>寄件副本 ( 多個副本收地址時以 ； 隔開 )</label>
          <input type="text" id="email-ccto" style="width:95%" />
        </div>
        <div style="width:95%;">
          <label>信件主旨</label>
          <input type="text" id="email-subject" style="width:95%" />
        </div>
        <div style="width:95%;">
          <div id="email-body" style="width:95%;height:300px;">
          <label>信件內容</label>
          <textarea style="width:100%;height:260px;"></textarea>
        </div>
        <div style="width:95%;text-align:center">
          <button id="email-send" orgtxt="發送信件">發送信件</button>
        </div>
      </div>
    <?php endif;?>
  </div>
</section>


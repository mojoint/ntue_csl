<section id="sec-admin_dashboard">
    <div class="container">
      <div class="div-admin_dashboard">
    <?php if (isset($_SESSION['admin'])) : ?>

        <div id="dialog-dashboard"></div>
        <div class="form-group">
          <div class="col-xs-12 text-center">
            <h3 for="dashboard">系統公告編輯器</h3>
          </div>
          <div class="col-xs-12">
            &nbsp;
          </div>
          <div class="col-xs-12">
            <form id="form-dashboard" method="post">
              <textarea id="dashboard" class="form-control"></textarea>
            </form>
          </div>
          <div class="col-xs-12 text-center">
            &nbsp;
          </div>
          <div class="col-xs-12 text-center">
            <label id="btn-dashboard" class="btn btn-lg btn-primary">編輯完成 (呈現於機構登入首頁)</label>
          </div>
        </div>
        <script>
          mojo.data.dashboard = JSON.parse('<?php echo json_encode($dashboard); ?>');
console.log( mojo.data.dashboard );
          if (mojo.data.dashboard)
            $('#dashboard').text( Base64.decode(mojo.data.dashboard[0]['dashboard']) );
          tinymce.init({selector: '#dashboard', menubar: false, min_height: 320});
        </script>
    <?php endif; ?>
      </div>
    </div>
</section>

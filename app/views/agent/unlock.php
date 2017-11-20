<section id="sec-agency_unlock">
    <div class="container">
      <div class="div-agency_unlock">
    <?php if (isset($_SESSION['agent'])) : ?>
        <script type="text/x-kendo-template" id="template-academic_class">
          <div class="createBtnContainer">
            <label for="editor-academic_era">預計修改季度</label>
            <select id="editor-academic_era"></select>
            <select id="editor-academic_era_quarter">
              <option value="1">第一季</option>
              <option value="2">第二季</option>
              <option value="3">第三季</option>
              <option value="4">第四季</option>
            </select>
          </div>
          <div class="toolbar"></div>
        </script>

        <div id="dialog-academic_class"></div>
        <div id="grid-academic_class"></div>
        <div id="grid-academic_class-a" class="col-xs-4"></div>
        <div id="grid-academic_class-b" class="col-xs-4"></div>
        <div id="grid-academic_class-c" class="col-xs-4"></div>

        <script type="text/x-kendo-template" id="template-academic_class_apply-footer">
          <div class="createBtnContainer">
            <label for="editor-academic_class-note">修改理由說明</label>
            <input type="text" id="editor-academic_class-note" />
            <label for="editor-academic_class-days">修改天數</label>
            <select id="editor-academic_class-work_days"></select>
            <a href="\\#" class="k-button" id="btn-academic_class-unlock"><span class="fa fa-save"></span>&nbsp;確認提出</a>
          </div>
          <div class="toolbar"></div>
        </script>
        <script type="text/x-kendo-template" id="template-academic_class_exists-footer">
          <div class="createBtnContainer">
            <label for="editor-academic_class-note">修改理由說明</label>
            <input type="text" id="editor-academic_class-note" class="disable" />
            <label for="editor-academic_class-days">修改天數</label>
            <select id="editor-academic_class-work_days" class="disable" ></select>
            <label id="editor-academic_class-status"></label>
          </div>
          <div class="toolbar"></div>
        </script>
        <div id="grid-academic_class-footer" class="col-xs-12"></div>
        <script>
          mojo.data.academic_agency_class_status = JSON.parse('<?php echo json_encode($academic_agency_class_status); ?>');
          var academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
          var academic_class = JSON.parse('<?php echo json_encode($academic_class); ?>');
          $('#grid-academic_class').kendoGrid({
            pageable: false,
            height: 0,
            toolbar: kendo.template($('#template-academic_class').html())
          });
          // set academic_era
          for (var i=0; i<academic_era.length; i++) {
            $('#editor-academic_era').append('<option value="' + academic_era[i]['id'] +'">' + academic_era[i]['cname'] + '</option>');
          } 

          mojo.grid.academic_class_a = $('#grid-academic_class-a').kendoGrid({
            pageable: false,
            columns: [
              { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
              { title: '', field: 'id' },
              { title: '', field: 'era_id' },
              { title: '', field: 'major_code' },
              { title: '', field: 'minor_code' },
              { title: '第一類', field: 'cname' }
            ]
          });
          $('#grid-academic_class-a').data('kendoGrid').hideColumn(1);
          $('#grid-academic_class-a').data('kendoGrid').hideColumn(2);
          $('#grid-academic_class-a').data('kendoGrid').hideColumn(3);
          $('#grid-academic_class-a').data('kendoGrid').hideColumn(4);
  
          mojo.grid.academic_class_b = $('#grid-academic_class-b').kendoGrid({
            pageable: false,
            columns: [
              { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
              { title: '', field: 'id' },
              { title: '', field: 'era_id' },
              { title: '', field: 'major_code' },
              { title: '', field: 'minor_code' },
              { title: '第二類', field: 'cname' }
            ]
          });
          $('#grid-academic_class-b').data('kendoGrid').hideColumn(1);
          $('#grid-academic_class-b').data('kendoGrid').hideColumn(2);
          $('#grid-academic_class-b').data('kendoGrid').hideColumn(3);
          $('#grid-academic_class-b').data('kendoGrid').hideColumn(4);
  
          mojo.grid.academic_class_c = $('#grid-academic_class-c').kendoGrid({
            pageable: false,
            columns: [
              { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
              { title: '', field: 'id' },
              { title: '', field: 'era_id' },
              { title: '', field: 'major_code' },
              { title: '', field: 'minor_code' },
              { title: '第三類', field: 'cname' }
            ]
          });
          $('#grid-academic_class-c').data('kendoGrid').hideColumn(1);
          $('#grid-academic_class-c').data('kendoGrid').hideColumn(2);
          $('#grid-academic_class-c').data('kendoGrid').hideColumn(3);
          $('#grid-academic_class-c').data('kendoGrid').hideColumn(4);

          $('#editor-academic_era').on('change', function(e) {
            var class_data_a = [];
            var class_data_b = [];
            var class_data_c = [];
            for (var i=0; i<academic_class.length; i++) {
              if ($(this).val() == academic_class[i]['era_id']) {
                switch(academic_class[i]['major_code'])
                {
                case 'A':
                  class_data_a.push(academic_class[i]);
                  break;
                case 'B':
                  class_data_b.push(academic_class[i]);
                  break;
                case 'C':
                  class_data_c.push(academic_class[i]);
                  break;
                } 
              } 
            }
            $('#grid-academic_class-a').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: class_data_a }));
            $('#grid-academic_class-b').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: class_data_b }));
            $('#grid-academic_class-c').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: class_data_c }));
          });

          $('#editor-academic_era option:first-child').attr('selected', true).trigger('change');

          console.log (mojo.data.academic_agency_class_status);
          if (mojo.data.academic_agency_class_status) {
            mojo.is_unlock = {};
            mojo.if_unlock = false;
            var now = new Date();
            for (var i=0; i<mojo.data.academic_agency_class_status.length; i++) {
              //if ((mojo.data.academic_agency_class_status[i]['unlock'] == 1) && (mojo.data.academic_agency_class_status[i]['state'] == 0)) {
              if (mojo.data.academic_agency_class_status[i]['unlock'] == 1) {
                if (mojo.data.academic_agency_class_status[i]['state'] == 0) {
                  if (/-/.test(mojo.data.academic_agency_class_status[i]['offline'])) {
                    var offs = mojo.data.academic_agency_class_status[i]['offline'].split('-');
                    var offline = new Date(offs[0], parseInt(offs[1]) -1, offs[2], '23', '59', '59');
                    if (now <= offline) {
                      $('#grid-academic_class-footer').kendoGrid({
                        pageable: false,
                        height: 0,
                        toolbar: kendo.template($('#template-academic_class_exists-footer').html())
                      });
                      mojo.if_unlock = true;
                      mojo.is_unlock = mojo.data.academic_agency_class_status[i];
                      $('#editor-academic_class-status').html( mojo.data.academic_agency_class_status[i]['online'] + ' ~ ' + mojo.data.academic_agency_class_status[i]['offline'] + ' 開放填報');
                      break;
                    }
                  } else if (mojo.data.academic_agency_class_status[i]['work_days']) {
                    $('#grid-academic_class-footer').kendoGrid({
                      pageable: false,
                      height: 0,
                      toolbar: kendo.template($('#template-academic_class_exists-footer').html())
                    });
                    $('#editor-academic_class-status').html('待審核');
                  }
                } else if (mojo.data.academic_agency_class_status[i]['state'] == 1) {
                  if (mojo.data.academic_agency_class_status[i]['offline'] == "") {
                    $('#grid-academic_class-footer').kendoGrid({
                      pageable: false,
                      height: 0,
                      toolbar: kendo.template($('#template-academic_class_exists-footer').html())
                    });
                    mojo.if_unlock = true;
                    mojo.is_unlock = mojo.data.academic_agency_class_status[i];
                    $('#editor-academic_class-status').html('待審核');
                    break;
                  }
                }
              }
            }
            if (!mojo.if_unlock) {
              $('#grid-academic_class-footer').kendoGrid({
                pageable: false,
                height: 0,
                toolbar: kendo.template($('#template-academic_class_apply-footer').html())
              });
            }
          }
        </script>
    <?php endif; ?>
      </div>
    </div>
</section>

<section id="sec-settings">
    <div class="container">
      <div class="div-settings">
    <?php if (isset($_SESSION['admin'])) : ?>
        <ul class="nav nav-tabs" role="tablist">
          <li class="active" role="presentation"><a id="tab-academic_era" href="#academic_era" aria-controls="academic_era" role="tab" data-toggle="tab">填報期間</a></li>
          <li role="presentation"><a id="tab-academic_class" href="#academic_class" aria-controls="academic_class" role="tab" data-toggle="tab">績效認列</a></li>
        </ul>    
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="academic_era">
            <script type="text/x-kendo-template" id="template-academic_era">
              <div class="createBtnContainer">
                <a href="\\#" class="k-button" id="btn-academic_era-add"><span class="fa fa-plus"></span>&nbsp;增加新年度</a>
              </div>
              <div class="toolbar"></div>
            </script>
            <div id="dialog-academic_era"></div>
            <div id="grid-academic_era"></div>
        <?php if (isset($academic_era)) : ?>
            <script>
              var era_data = JSON.parse('<?php echo json_encode($academic_era); ?>');
              var quarter_data = JSON.parse('<?php echo json_encode($academic_era_quarter); ?>');
              $('#grid-academic_era').kendoGrid({
                pageable: false,
                columns: [
                  { title: '', field: 'id' },
                  { title: '', field: 'era_id' },
                  { title: '', field: 'quarter' },
                  { title: '季度', field: 'cname' },
                  { title: '開放填報', field: 'online' },
                  { title: '結束填報', field: 'offline' },
                  { title: '', field: 'state' },
                  {
                    title: '', 
                    width: '100px',
                    command: [{
                      name: '編輯',
                      template: '<a class="k-button k-blank k-grid-edit btn-academic_era-mod" title="編輯"><i class="fa fa-edit"></i></a>'
                    }]
                  }
                ],
                toolbar: kendo.template($('#template-academic_era').html())
              });
              $('#grid-academic_era').data('kendoGrid').hideColumn(0);
              $('#grid-academic_era').data('kendoGrid').hideColumn(1);
              $('#grid-academic_era').data('kendoGrid').hideColumn(2);
              $('#grid-academic_era').data('kendoGrid').hideColumn(6);
              // set quarter
              $('#grid-academic_era').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: quarter_data }));
            </script>
        <?php endif; ?>
          </div>
          <div role="tabpanel" class="tab-pane" id="academic_class">
            <script type="text/x-kendo-template" id="template-academic_class">
              <div class="createBtnContainer">
                <select id="select-academic_class-era"></select>
                <select id="select-academic_class-taken"><option value="0">不公開</option><option value="1">公開</option></select>
                <a href="\\#" class="k-button" id="btn-academic_class-add"><span class="fa fa-plus"></span>&nbsp;新增類別</a>
                <a href="\\#" class="k-button" id="btn-academic_class-save"><span class="fa fa-save"></span>&nbsp;儲存設定</a>
              </div>
              <div><label>請勾選不納入績效認列之類別</label></div>
              <div class="toolbar"></div>
            </script>
            <div id="dialog-academic_class"></div>
            <div id="grid-academic_class"></div>
            <div id="grid-academic_class-a" class="col-xs-4"></div>
            <div id="grid-academic_class-b" class="col-xs-4"></div>
            <div id="grid-academic_class-c" class="col-xs-4"></div>
        <?php if (isset($academic_class)) : ?>
            <script>
              $('#grid-academic_class').kendoGrid({
                pageable: false,
                height: 0,
                toolbar: kendo.template($('#template-academic_class').html())
              });
              // set academic_era
              for (var i=0; i<era_data.length; i++) {
                if (i == 0) {
                  $('#select-academic_class-era').append('<option value="' + era_data[i]['id'] +'" selected>' + era_data[i]['cname'] + '</option>');
                  $('#select-academic_class-taken').val(era_data[i]['taken']);
                } else 
                  $('#select-academic_class-era').append('<option value="' + era_data[i]['id'] +'">' + era_data[i]['cname'] + '</option>');
              }
              
              //$('#select-academic_class-era option:first-child').attr('selected', true);
              $('#grid-academic_class-a').kendoGrid({
                pageable: false,
                columns: [
                  { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
                  { title: '', field: 'id' },
                  { title: '', field: 'era_id' },
                  { title: '', field: 'major_code' },
                  { title: '', field: 'minor_code' },
                  { title: '第一類', field: 'cname' },
                  { title: '', field: 'state' }
                ]
              });
              $('#grid-academic_class-a').data('kendoGrid').hideColumn(1);
              $('#grid-academic_class-a').data('kendoGrid').hideColumn(2);
              $('#grid-academic_class-a').data('kendoGrid').hideColumn(3);
              $('#grid-academic_class-a').data('kendoGrid').hideColumn(4);
              $('#grid-academic_class-a').data('kendoGrid').hideColumn(6);
  
              $('#grid-academic_class-b').kendoGrid({
                pageable: false,
                columns: [
                  { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
                  { title: '', field: 'id' },
                  { title: '', field: 'era_id' },
                  { title: '', field: 'major_code' },
                  { title: '', field: 'minor_code' },
                  { title: '第二類', field: 'cname' },
                  { title: '', field: 'state' }
                ]
              });
              $('#grid-academic_class-b').data('kendoGrid').hideColumn(1);
              $('#grid-academic_class-b').data('kendoGrid').hideColumn(2);
              $('#grid-academic_class-b').data('kendoGrid').hideColumn(3);
              $('#grid-academic_class-b').data('kendoGrid').hideColumn(4);
              $('#grid-academic_class-b').data('kendoGrid').hideColumn(6);
  
              $('#grid-academic_class-c').kendoGrid({
                pageable: false,
                columns: [
                  { template: "<input type='checkbox' class='checkbox' />", width: '50px' },
                  { title: '', field: 'id' },
                  { title: '', field: 'era_id' },
                  { title: '', field: 'major_code' },
                  { title: '', field: 'minor_code' },
                  { title: '第三類', field: 'cname' },
                  { title: '', field: 'state' }
                ]
              });
              $('#grid-academic_class-c').data('kendoGrid').hideColumn(1);
              $('#grid-academic_class-c').data('kendoGrid').hideColumn(2);
              $('#grid-academic_class-c').data('kendoGrid').hideColumn(3);
              $('#grid-academic_class-c').data('kendoGrid').hideColumn(4);
              $('#grid-academic_class-c').data('kendoGrid').hideColumn(6);
  
              mojo.data.academic_class_data = JSON.parse('<?php echo json_encode($academic_class); ?>');
              mojo.data.academic_class_data_a = [];
              mojo.data.academic_class_data_b = [];
              mojo.data.academic_class_data_c = [];
              for (var i=0; i<mojo.data.academic_class_data.length; i++) {
                switch(mojo.data.academic_class_data[i]['major_code'])
                {
                case 'A':
                  mojo.data.academic_class_data_a.push(mojo.data.academic_class_data[i]);
                  break;
                case 'B':
                  mojo.data.academic_class_data_b.push(mojo.data.academic_class_data[i]);
                  break;
                case 'C':
                  mojo.data.academic_class_data_c.push(mojo.data.academic_class_data[i]);
                  break;
                } 
              }
              $('#grid-academic_class-a').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_class_data_a }));
              $('#grid-academic_class-b').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_class_data_b }));
              $('#grid-academic_class-c').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_class_data_c }));
            </script>
        <?php endif; ?>
          </div>
        </div>
    <?php endif; ?>
      </div>
    </div>
</section>

<section id="sec-info">
    <div class="container">
    <?php if ($_SESSION['agent']) : ?>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active" role="presentation"><a id="tab-academic_agency" href="#academic_agency" aria-controls="academic_agency" role="tab" data-toggle="tab">教育機構通訊資料</a></li>
        <li role="presentation"><a id="tab-academic_agency_hr" href="#academic_agency_hr" aria-controls="academic_agency_hr" role="tab" data-toggle="tab">教學人力</a></li>
        <li role="presentation"><a id="tab-academic_agency_contact" href="#academic_agency_contact" aria-controls="academic_agency_contact" role="tab" data-toggle="tab">聯絡人</a></li>
      </ul>    
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="academic_agency">
          <script type="text/x-kendo-template" id="template-academic_agency">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency-mod"><span class="fa fa-save"></span>&nbsp;更新機構資訊</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="dialog-academic_agency"></div>
          <div id="grid-academic_agency">
            <table role="grid">
              <colgroup><col style="width: 25%"/><col style="width: 15%;" /><col /></colgroup>
              <thead></thead>
              <tbody>
                <tr>
                  <td>學校名稱</td>
                  <td colspan="2"><b id="academic_institution_cname"></b></td>
                </tr>
                <tr>
                  <td>華語文教育機構名稱</td>
                  <td colspan="2"><input type="text" id="academic_agency_cname" class="" /></td>
                </tr>
                <tr>
                  <td>地址</td>
                  <td><input type="text" id="academic_agency_zipcode" placeholder="郵遞區號" class="" /></td>
                  <td><input type="text" id="academic_agency_address" placeholder="機構地址" class="" /></td>
                </tr>
                <tr>
                  <td>成立時間</td>
                  <td><input type="text" id="academic_agency_established" class="" /></td>
                  <td></td>
                </tr>
                <tr>
                  <td>核准自境外招生時間</td>
                  <td><input type="text" id="academic_agency_approval" class="" /></td>
                  <td></td>
                </tr>
                <tr>
                  <td>備註</td>
                  <td colspan="2"><input type="text" id="academic_agency_note" class="" /></td>
                </tr>
              </tbody>
            </table>
            
            <script>
              $('#grid-academic_agency').kendoGrid({
                pageable: false,
                toolbar: kendo.template($('#template-academic_agency').html())
              }); 
              var agency_data = JSON.parse('<?php echo json_encode($academic_agency); ?>');
              if ('object' == typeof(agency_data)) {
                $('#academic_institution_cname').html(agency_data[0].academic_institution_cname);
                $('#academic_agency_cname').val(agency_data[0].cname);
                $('#academic_agency_zipcode').val(agency_data[0].zipcode);
                $('#academic_agency_address').val(agency_data[0].address);
                $('#academic_agency_established').val(agency_data[0].established).kendoDatePicker({format: "yyyy-MM-dd"});
                $('#academic_agency_approval').val(agency_data[0].approval).kendoDatePicker({format: "yyyy-MM-dd"});
                $('#academic_agency_note').val(agency_data[0].note);
              }
            </script>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_agency_hr">
          <script type="text/x-kendo-template" id="template-academic_agency_hr">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_hr-add"><span class="fa fa-plus"></span>&nbsp;新增年度教學人力</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="dialog-academic_agency_hr"></div>
          <div id="grid-academic_agency_hr"></div>
          <script>
            $('#grid-academic_agency_hr').kendoGrid({
              pageable: false,
              resizable: true,
              height: 550,
              columns: [
                { title: '&nbsp;', field: 'agency_id' },
                { title: '&nbsp;', field: 'era_id' },
                { title: '年度', field: 'academic_era_code', width: '100px' },
                { title: '行政人員', field: 'administration', width: '100px' },
                { title: '專任教師', field: 'subject', width: '100px' },
                { title: '兼任教師', field: 'adjunct', width: '100px' },
                { title: '儲備教師', field: 'reserve', width: '100px' },  
                { title: '其他教師', field: 'others', width: '100px' },  
                { title: '&nbsp;', field: 'note' },
                { title: '&nbsp;', width: '120px', 
                  command: [
                    {   
                      name: '編輯',
                      template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_hr-mod" title="編輯"><i class="fa fa-edit"></i></a>'
                    }   
                  ]   
                }   
              ],  
              toolbar: kendo.template($('#template-academic_agency_hr').html())
            }); 
            $('#grid-academic_agency_hr').data('kendoGrid').hideColumn(0);
            $('#grid-academic_agency_hr').data('kendoGrid').hideColumn(1);
            $('#grid-academic_agency_hr').data('kendoGrid').hideColumn(8);
            var hr_data = JSON.parse('<?php echo json_encode($academic_agency_hr); ?>');
            $('#grid-academic_agency_hr').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: hr_data }));
          </script>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_agency_contact">
          <script type="text/x-kendo-template" id="template-academic_agency_contact">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_contact-add"><span class="fa fa-plus"></span>&nbsp;新增聯絡人</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="dialog-academic_agency_contact"></div>
          <div id="grid-academic_agency_contact"></div>
          <script>
            $('#grid-academic_agency_contact').kendoGrid({
              pageable: false,
              resizable: true,
              height: 550,
              columns: [
                { title: '&nbsp;', field: 'id' },
                { title: '&nbsp;', field: 'agency_id' },
                { title: '姓名', field: 'cname', width: '100px' },
                { title: '職稱', field: 'title', width: '100px' },
                { title: '&nbsp;', field: 'manager' },
                { title: '&nbsp;', field: 'staff' },
                { title: '&nbsp;', field: 'role' },
                { title: '&nbsp;', field: 'area_code' },
                { title: '&nbsp;', field: 'phone' },
                { title: '&nbsp;', field: 'ext' },
                { title: '電話', template: "#=area_code + '-' + phone + ' ext:' + ext#", width: '200px' },
                { title: '電子郵件', field: 'email', width: '200px' },
                { title: '&nbsp;', field: 'spare_email' },
                { title: '&nbsp;', field: 'primary' }, 
                { title: '&nbsp;', width: '200px',
                  command: [
                    {
                      name: '編輯',
                      template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_contact-mod" title="編輯"><i class="fa fa-edit"></i></a>'
                    },
                    {
                      name: '刪除',
                      template: '<a class="k-button k-blank k-grid-destroy btn-academic_agency_contact-del" title="刪除"><i class="fa fa-trash"></i></a>'
                    }
                  ]
                } 
              ],
              toolbar: kendo.template($('#template-academic_agency_contact').html())
            });     
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(0);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(1);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(4);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(5);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(6);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(7);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(8);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(9);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(12);
            $('#grid-academic_agency_contact').data('kendoGrid').hideColumn(13);
            var contact_data = JSON.parse('<?php echo json_encode($academic_agency_contact); ?>');
            $('#grid-academic_agency_contact').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: contact_data }));
          </script>
        </div>
      </div>
    <?php endif; ?>
    </div>
</section>

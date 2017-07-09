<section id="sec-maintain">
    <div class="container">
    <?php if (isset($_SESSION['admin'])) : ?>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active" role="presentation"><a id="tab-academic_agency" href="#academic_agency" aria-controls="academic_agency" role="tab" data-toggle="tab">機構列表</a></li>
        <li role="presentation"><a id="tab-academic_agency_agent" href="#academic_agency_agent" aria-controls="academic_agency_agent" role="tab" data-toggle="tab">使用者</a></li>
      </ul>    
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="academic_agency">
          <script type="text/x-kendo-template" id="template-academic_agency">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency-add"><span class="fa fa-plus"></span>&nbsp;新增機構</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="dialog-academic_agency"></div>
          <div id="grid-academic_agency"></div>
          <script>        
            $('#grid-academic_agency').kendoGrid({
              pageable: true,
              columns: [
                { title: '&nbsp;', field: 'id' },
                { title: '學校代碼', field: 'institution_code' },
                { title: '學校名稱', field: 'institution_cname', width: '200px' },
                { title: '華語文機構名稱', field: 'cname', width: '300px' },
                { title: '簡稱', field: 'institution_aka' },  
                { title: '行政人員', field: 'academic_agency_hr_administration' },
                { title: '專任教師', field: 'academic_agency_hr_subject' },
                { title: '兼任教師', field: 'academic_agency_hr_adjunct' },
                { title: '儲備教師', field: 'academic_agency_hr_reserve' },  
                { title: '&nbsp;', width: '200px', 
                  command: [
                    {   
                      name: '編輯',
                      template: '<a class="k-button k-blank k-grid-edit btn-academic_agency-mod" title="編輯"><i class="fa fa-edit"></i></a>'
                    },  
                    {   
                      name: '刪除',
                      template: '<a class="k-button k-blank k-grid-destroy btn-academic_agency-del" title="刪除"><i class="fa fa-trash"></i></a>'
                    }   
                  ]   
                }   
              ],  
              toolbar: kendo.template($('#template-academic_agency').html())
            });
            $('#grid-academic_agency').data('kendoGrid').hideColumn(0);
            mojo.data.academic_agency = JSON.parse('<?php echo json_encode($academic_agency); ?>');
            $('#grid-academic_agency').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency, page: 1, pageSize: 10 }));
          </script>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_agency_agent">
          <script type="text/x-kendo-template" id="template-academic_agency_agent">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_agent-add"><span class="fa fa-plus"></span>&nbsp;新增使用者</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="dialog-academic_agency_agent"></div>
          <div id="grid-academic_agency_agent"></div>
          <script>
            $('#grid-academic_agency_agent').kendoGrid({
              pageable: true,
              height: 550,
              columns: [
                { title: '&nbsp;', field: 'id' },
                { title: '&nbsp;', field: 'agency_id' },
                { title: '使用者ID', field: 'username', width: '150px' },
                { title: '電子郵件', field: 'email' },
                { title: '學校名稱', field: 'academic_institution_cname', width: '200px' },
                { title: '所屬機構', field: 'academic_agency_cname', width: '200px' },
                { title: '&nbsp;', width: '100px',
                  command: [
                    {
                      name: '編輯',
                      template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_agent-mod" title="編輯"><i class="fa fa-edit"></i></a>'
                    },
                    {
                      name: '刪除',
                      template: '<a class="k-button k-blank k-grid-destroy btn-academic_agency_agent-del" title="刪除"><i class="fa fa-trash"></i></a>'
                    }
                  ]
                }
              ],
              toolbar: kendo.template($('#template-academic_agency_agent').html())
            });
            $('#grid-academic_agency_agent').data('kendoGrid').hideColumn(0);
            $('#grid-academic_agency_agent').data('kendoGrid').hideColumn(1);
            mojo.data.academic_agency_agent = JSON.parse('<?php echo json_encode($academic_agency_agent); ?>');
            $('#grid-academic_agency_agent').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_agent, page: 1, pageSize: 10 }));
          </script>    
        </div>
      </div>
    <?php endif; ?>
    </div>
</section>

<section id="sec-admin_unlock">
    <div class="container">
      <div class="div-admin_unlock">
    <?php if (isset($_SESSION['admin'])) : ?>

        <div id="dialog-academic_agency_unlock"></div>
        <div id="grid-academic_agency_unlock"></div>
        <script>
          mojo.data.academic_agency_unlock = JSON.parse('<?php echo json_encode($academic_agency_unlock); ?>');
          $('#grid-academic_agency_unlock').kendoGrid({
            pageable: false,
            columns: [
              { title: '&nbsp;', field: 'agency_id' },
              { title: '&nbsp;', field: 'id' },
              { title: '學校名稱', field: 'academic_institution_cname' },
              { title: '機構名稱', field: 'academic_agency_cname' },
              { title: '申請年度', field: 'academic_era_cname' },
              { title: '申請季度', field: 'quarter' },
              { title: '申請類別', field: 'minors' },
              { title: '申請天數', field: 'work_days' },
              { title: '修改原因', field: 'note' },
              { title: '&nbsp;', field: 'state' },
              { title: '&nbsp;', width: '200px',
                command: [
                  {   
                    name: '同意',
                    template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_unlock-yes" title="同意"><i class="fa fa-thumbs-o-up"></i></a>'
                  },  
                  {   
                    name: '不同意',
                    template: '<a class="k-button k-blank k-grid-destroy btn-academic_agency_unlock-no" title="不同意"><i class="fa fa-thumbs-o-down"></i></a>'
                  }   
                ]
              }
            ]
          });
          $('#grid-academic_agency_unlock').data('kendoGrid').hideColumn(0);
          $('#grid-academic_agency_unlock').data('kendoGrid').hideColumn(1);
          $('#grid-academic_agency_unlock').data('kendoGrid').hideColumn(9);
          $('#grid-academic_agency_unlock').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_unlock, page: 1, pageSize: 10 }));  
        </script>
    <?php endif; ?>
      </div>
    </div>
</section>

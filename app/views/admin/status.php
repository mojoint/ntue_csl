<section id="sec-status">
    <div class="container">
      <div class="div-status">
    <?php if (isset($_SESSION['admin'])) : ?>
        <script type="text/x-kendo-template" id="template-academic_agency_status">
          <div class="createBtnContainer">
            <select id="select-academic_agency_status-era"></select>
            <select id="select-academic_agency_status-quarter"></select>
            <a href="\\#" class="k-button" id="btn-academic_agency_status-search"><span class="fa fa-search"></span>&nbsp;查詢狀況</a>
          </div>
          <div class="toolbar"></div>
            </script>
            <div id="grid-academic_agency_status"></div>
            <script>
              mojo.data.academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
  
              $('#grid-academic_agency_status').kendoGrid({
                pageable: true,
                columns: [
                  { field: 'institution_code', title: '學校代碼' },
                  { field: 'institution_cname', title: '學校名稱'  },
                  { field: 'academic_agency_cname', title: '機構名稱'  },
                  { field: 'state', title: '填報狀態'  },
                  { field: 'offline', title: '延長截止日'  }
                ],
                toolbar: kendo.template($('#template-academic_agency_status').html())
              });

              // set academic_era
              for (var i=0; i<mojo.data.academic_era.length; i++) 
                $('#select-academic_agency_status-era').append('<option value="' + mojo.data.academic_era[i]['id'] +'">' + mojo.data.academic_era[i]['cname'] + '</option>');
              
              // set academic_era_quarter
              for (var i=1; i<=4; i++) 
                $('#select-academic_agency_status-quarter').append('<option value="' + i +'">第 ' + i + ' 季</option>');
            </script>
          </div>
        </div>
    <?php endif; ?>
      </div>
    </div>
</section>

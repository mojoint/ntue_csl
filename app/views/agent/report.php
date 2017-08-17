<section id="sec-agency_report">
    <div class="container">
    <?php if ($_SESSION['agent']) : ?>
      <div id="grid-academic_agency_report_search">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 15%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_agency_report-era"></select></td>
              <td><select id="academic_agency_report-quarter"></select></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_agency_report-search"><span class="fa fa-search"></span>查詢報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active" role="presentation"><a id="tab-academic_agency_report_summary" href="#academic_agency_report_summary" aria-controls="academic_agency_report_summary" role="tab" data-toggle="tab">機構報表-課程統計簡表(四大類)</a></li>
        <li role="presentation"><a id="tab-academic_agency_report_detail" href="#academic_agency_report_detail" aria-controls="academic_agency_report_detail" role="tab" data-toggle="tab">機構報表-課程明細詳表(含國別)</a></li>
        <li role="presentation"><a id="tab-academic_agency_report_pdf" href="#academic_agency_report_pdf" aria-controls="academic_agency_report_pdf" role="tab" data-toggle="tab">機構報表-PDF績效報表</a></li>
      </ul>    
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="academic_agency_report_summary">
          <script type="text/x-kendo-template" id="template-academic_agency_report_summary">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_report_summary-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_agency_report_summary"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_agency_report_detail">
          <script type="text/x-kendo-template" id="template-academic_agency_report_detail">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_report_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_agency_report_detail"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_agency_report_pdf">
          <script type="text/x-kendo-template" id="template-academic_agency_report_pdf">
            <div class="createBtnContainer"><a href="\\#" class="k-button" id="btn-academic_agency_report_pdf-export"><span class="fa fa-file-pdf-o"></span>&nbsp</a></div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_agency_report_pdf"></div>
        </div>
      </div>
      <script>
        mojo.data.academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
        mojo.data.academic_era_quarter = [
          {quarter: 1, cname: '第一季'},
          {quarter: 2, cname: '第二季'}
        ];
        for (var i=0; i<mojo.data.academic_era.length; i++)
          $('#academic_agency_report-era').append('<option value="' + mojo.data.academic_era[i]['id'] + '">' + mojo.data.academic_era[i]['cname'] + '</option>');
        for (var i=0; i<mojo.data.academic_era_quarter.length; i++) 
          $('#academic_agency_report-quarter').append('<option value="' + mojo.data.academic_era_quarter[i]['quarter'] + '">' + mojo.data.academic_era_quarter[i]['cname'] + '</option>');
        
      </script>
    <?php endif; ?>
    </div>
</section>

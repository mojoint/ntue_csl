<section id="sec-admin_report">
    <div class="container">
    <?php if ($_SESSION['admin']) : ?>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active" role="presentation"><a id="tab-academic_admin_report_era_detail" href="#academic_admin_report_era_detail" aria-controls="academic_admin_report_era_detail" role="tab" data-toggle="tab">年度機構詳表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_era_summary" href="#academic_admin_report_era_summary" aria-controls="academic_admin_report_era_summary" role="tab" data-toggle="tab">年度機構簡表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_quarter_detail" href="#academic_admin_report_quarter_detail" aria-controls="academic_admin_report_quarter_detail" role="tab" data-toggle="tab">季度機構詳表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_quarter_summary" href="#academic_admin_report_quarter_summary" aria-controls="academic_admin_report_quarter_summary" role="tab" data-toggle="tab">季度機構簡表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_manager" href="#academic_admin_report_manager" aria-controls="academic_admin_report_manager" role="tab" data-toggle="tab">管理報表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_statistics" href="#academic_admin_report_statistics" aria-controls="academic_admin_report_statistics" role="tab" data-toggle="tab">統計處報表</a></li>
        <li role="presentation"><a id="tab-academic_admin_report_major_b" href="#academic_admin_report_major_b" aria-controls="academic_admin_report_major_b" role="tab" data-toggle="tab">B類年度總表</a></li>
      </ul>    
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="academic_admin_report_era_detail">
          <script type="text/x-kendo-template" id="template-academic_admin_report_era_detail">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_era_detail-era" class="academic_admin_report-era"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_detail-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_era_detail"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_era_summary">
          <script type="text/x-kendo-template" id="template-academic_admin_report_era_summary">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_era_summary-era" class="academic_admin_report-era"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_summary-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_summary-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_era_summary"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_quarter_detail">
          <script type="text/x-kendo-template" id="template-academic_admin_report_quarter_detail">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_quarter_detail-era" class="academic_admin_report-era"></select>
              <select id="select-academic_admin_report_quarter_detail-quarter" class="academic_admin_report-quarter"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_quarter_detail-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_quarter_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_quarter_detail"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_quarter_summary">
          <script type="text/x-kendo-template" id="template-academic_admin_report_quarter_summary">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_quarter_summary-era" class="academic_admin_report-era"></select>
              <select id="select-academic_admin_report_quarter_summary-quarter" class="academic_admin_report-quarter"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_quarter_summary-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_quarter_summary-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_quarter_summary"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_manager">
          <script type="text/x-kendo-template" id="template-academic_admin_report_manager">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_manager-era" class="academic_admin_report-era"></select>
              <select id="select-academic_admin_report_manager-quarter" class="academic_admin_report-quarter"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_manager-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_manager-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_manager"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_statistics">
          <script type="text/x-kendo-template" id="template-academic_admin_report_statistics">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_statistics-era" class="academic_admin_report-era"></select>
              <select id="select-academic_admin_report_statistics-quarter" class="academic_admin_report-quarter"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_statistics-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_statistics-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_major_b"></div>
        </div>
        <div role="tabpanel" class="tab-pane" id="academic_admin_report_major_b">
          <script type="text/x-kendo-template" id="template-academic_admin_report_major_b">
            <div class="createBtnContainer">
              <select id="select-academic_admin_report_major_b-era" class="academic_admin_report-era"></select>
              <select id="select-academic_admin_report_era_detail-quarter" class="academic_admin_report-quarter"></select>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_detail-search"><span class="fa fa-search"></span>&nbsp;</a>
              <a href="\\#" class="k-button" id="btn-academic_admin_report_era_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;</a>
            </div>
            <div class="toolbar"></div>
          </script>
          <div id="grid-academic_admin_report_major_b"></div>
        </div>
      </div>
      <script>
        mojo.data.academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
        mojo.data.academic_era_quarter = [
          {quarter: 1, cname: '第1季'},
          {quarter: 2, cname: '第2季'}
          {quarter: 3, cname: '第3季'}
          {quarter: 4, cname: '第4季'}
        ];
        for (var i=0; i<mojo.data.academic_era.length; i++)
          $('.academic_admin_report-era').append('<option value="' + mojo.data.academic_era[i]['id'] + '">' + mojo.data.academic_era[i]['cname'] + '</option>');
        for (var i=0; i<mojo.data.academic_era_quarter.length; i++) 
          $('.academic_admin_report-quarter').append('<option value="' + mojo.data.academic_era_quarter[i]['quarter'] + '">' + mojo.data.academic_era_quarter[i]['cname'] + '</option>');
        
      </script>
    <?php endif; ?>
    </div>
</section>

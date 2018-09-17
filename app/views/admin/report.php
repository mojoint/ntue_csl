<section id="sec-admin_report">
    <div class="container">
    <?php if ($_SESSION['admin']) : ?>
      <div id="grid-academic_admin_report_era_detail">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_era_detail-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_era_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;年度機構詳表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_era_summary">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_era_summary-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_era_summary-export"><span class="fa fa-file-excel-o"></span>&nbsp;年度機構簡表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_quarter_detail">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_quarter_detail-era" class="academic_admin_report-era"></select></td>
              <td><select id="academic_admin_report_quarter_detail-quarter" class="academic_admin_report-quarter"></select></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_quarter_detail-export"><span class="fa fa-file-excel-o"></span>&nbsp;季度機構詳表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_quarter_summary">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_quarter_summary-era" class="academic_admin_report-era"></select></td>
              <td><select id="academic_admin_report_quarter_summary-quarter" class="academic_admin_report-quarter"></select></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_quarter_summary-export"><span class="fa fa-file-excel-o"></span>&nbsp;季度機構簡表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_manager">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_manager-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_manager-export"><span class="fa fa-file-excel-o"></span>&nbsp;管理報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_statistics">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_statistics-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_statistics-export"><span class="fa fa-file-excel-o"></span>&nbsp;統計處報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_major_b">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_major_b-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_major_b-export"><span class="fa fa-file-excel-o"></span>&nbsp;B類年度總表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_states">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_states-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_states-export"><span class="fa fa-file-excel-o"></span>&nbsp;國籍人數排序報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div id="grid-academic_admin_report_classes">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 35%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_classes-era" class="academic_admin_report-era"></select></td>
              <td><select id="academic_admin_report_classes-class" class="academic_admin_report-class"></select></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_classes-export"><span class="fa fa-file-excel-o"></span>&nbsp;五大洲國家及課程報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!--
      <div id="grid-academic_admin_report_people">
        <table role="grid">
          <colgroup><col style="width: 15%"/><col style="width: 15%;" /><col /></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><select id="academic_admin_report_people-era" class="academic_admin_report-era"></select></td>
              <td></td>
              <td><a href="#" class="btn btn-lg btn-primary" id="btn-academic_admin_report_people-export"><span class="fa fa-file-excel-o"></span>&nbsp;五大洲課程人數統計報表</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      -->
      <script>
        mojo.data.academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
        mojo.data.academic_era_quarter = [
          {quarter: 1, cname: '第1季'},
          {quarter: 2, cname: '第2季'},
          {quarter: 3, cname: '第3季'},
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

<section id="sec-fillmod">
  <div class="container">
  <?php if ($_SESSION['agent']) : ?>
    <div id="academic_agency_class" data-mojo="<?php if (isset($mojo)) { echo $mojo; } ?>" data-era_id="<?php echo $era_id ;?>" data-quarter="<?php echo $quarter ;?>" data-quarter_id="<?php echo $quarter_id ;?>">
      <script type="text/x-kendo-template" id="template-academic_agency_class">
        <div class="createBtnContainer">
          <a href="\\#" class="k-button" id="btn-academic_agency_class-import"><span class="fa fa-copy"></span>&nbsp;自上一季匯入</a>
        </div>
        <div class="toolbar"></div>
      </script>

      <div id="grid-academic_agency_class">
        <table role="grid">
          <colgroup><col/><col/><col/></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td>研習類別名稱</td>
              <td colspan="2"><select id="editor-minor_code"></select></td>
            </tr>
            <tr>
              <td>課程名稱</td>
              <td colspan="2"><input type="text" id="editor-cname" class="form-control" /></td>
            </tr>
            <tr>
              <td>教學時數</td>
              <td><input type="text" id="editor-weekly" placeholder="6.8" />&nbsp;小時/週</td>
              <td>週數&nbsp;<input type="text" id="editor-weeks" placeholder="6.8" /></td>
            </tr>
            <tr>
              <td>招生對象</td>
              <td colspan="2"><select id="editor-target"></select></td>
            </tr>
            <tr>
              <td>教學內容</td>
              <td colspan="2"><select id="editor-content"></select></td>
            </tr>
          </tbody>
        </table>
      </div>
      <script type="text/x-kendo-template" id="template-academic_agency_class_country">
        <div class="createBtnContainer">
          <a href="\\#" class="k-button k-grid-add" id="btn-academic_agency_class_country-add"><span class="fa fa-plus"></span>&nbsp;新增國別明細</a>
          <a href="\\#" class="k-button" id="btn-academic_agency_class_country-import"><span class="fa fa-file-excel-o"></span>&nbsp;自EXCEL匯入</a>
          <a href="\\#" class="k-button" id="btn-academic_agency_class_country-sample"><span class="fa fa-file-excel-o"></span>&nbsp;下載範例EXCEL</a>
          <a href="\\#" class="k-button" id="btn-academic_agency_class_country-country"><span class="fa fa-file-excel-o"></span>&nbsp;下載國別EXCEL</a>
        </div>
        <div class="toolbar"></div>
      </script>
      <div id="spreadsheet"></div>
      <div id="dialog-academic_agency_class_country"></div>
      <div id="grid-academic_agency_class_country"></div>

      <script type="text/x-kendo-template" id="template-academic_agency_class_summary">
        <div class="createBtnContainer">
          <a href="\\#" class="k-button" id="btn-academic_agency_class-save"><span class="fa fa-save"></span>&nbsp;設定完成</a>
        </div>
        <div class="toolbar"></div>
      </script>
      <div id="grid-academic_agency_class_summary">
        <table role="grid">
          <tbody>
            <tr>
              <td>總時數</td>
              <td>總人次</td>
              <td>調整數值</td>
              <td>總人時數</td>
            </tr>
            <tr>
              <td><input type="text" id="editor-hours"></p></td>
              <td><p id="summary-reach"></p></td>
              <td><input type="text" id="editor-adjust" size="4" /></td>
              <td><p id="summary-total_hours"></p></td>
            </tr>
            <tr>
              <td colspan="4"><p><span class="fa fa-calculator"></span>&nbsp;總時數 X 總人次 - 調整值 = 總人時數</p></td>
            </tr>
            <tr>
              <td><input type="text" id="editor-revenue" placeholder="直接營收(元)" /></td>
              <td><input type="text" id="editor-subsidy" placeholder="政府補助(元)" /></td>
              <td>營收額度(元)</td>
              <td><p id="summary-turnover"></p></td>
            </tr>
            <tr>
              <td>其他</td>
              <td colspan="3"><textarea id="editor-note" cols="50" rows="3"></textarea></td>
            </tr>
          </tbody>
        </table>
      </div>
      <script>  
        $('#grid-academic_agency_class').kendoGrid({
          pageable: false,
          toolbar: kendo.template($('#template-academic_agency_class').html())
        });

        $('#grid-academic_agency_class_summary').kendoGrid({
          pageable: false,
          toolbar: kendo.template($('#template-academic_agency_class_summary').html())
        });

        mojo.data.academic_agency_class = JSON.parse('<?php echo json_encode($academic_agency_class); ?>');
        mojo.data.academic_agency_class_country = JSON.parse('<?php echo json_encode($academic_agency_class_country); ?>');
      </script>
    </div>
  <?php endif; ?>
  </div>
</section>

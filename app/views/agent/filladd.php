<section id="sec-filladd">
  <div class="container">
  <?php if ($_SESSION['agent']) : ?>
    <div id="academic_agency_class" data-mojo="<?php echo $mojo; ?>" data-era_id="<?php echo $era_id ;?>" data-quarter="<?php echo $quarter ;?>" data-quarter_id="<?php echo $quarter_id ;?>">
      <script type="text/x-kendo-template" id="template-academic_agency_class">
        <div class="createBtnContainer">
        <?php if ($quarter != 1) :?>
          <a href="\\#" class="k-button" id="btn-academic_agency_class-import"><span class="fa fa-copy"></span>&nbsp;自上一季匯入</a>
        <?php endif; ?>
        </div>
        <div class="toolbar"></div>
      </script>
      <div id="dialog-academic_agency_class"></div>
      <div id="grid-academic_agency_class">
        <table role="grid">
          <colgroup><col/><col/><col/></colgroup>
          <thead></thead>
          <tbody>
            <tr>
              <td><span class="required_field">*</span>研習類別名稱</td>
              <td colspan="2"><select id="editor-minor_code"></select></td>
            </tr>
            <tr>
              <td><span class="required_field">*</span>課程名稱</td>
              <td colspan="2"><input type="text" id="editor-cname" class="form-control" required /></td>
            </tr>
            <tr>
              <td><span class="required_field">*</span>教學時數</td>
              <td><input type="text" id="editor-weekly" placeholder="" required />&nbsp;小時/週</td>
              <td>週數&nbsp;<input type="text" id="editor-weeks" placeholder="" required /></td>
            </tr>
            <tr>
              <td><span class="required_field">*</span>招生對象</td>
              <td colspan="2"><select id="editor-target"></select></td>
            </tr>
            <tr>
              <td><span class="required_field">*</span>教學內容</td>
              <td colspan="2"><select id="editor-content"></select></td>
            </tr>
          </tbody>
        </table>
      </div>
      <script type="text/x-kendo-template" id="template-academic_agency_class_country">
        <div class="createBtnContainer">
          <a href="\\#" class="k-button k-grid-add" id="btn-academic_agency_class_country-add"><span class="fa fa-plus"></span>&nbsp;新增國別明細</a>
          <a href="\\#" class="k-button" id="btn-academic_agency_class_country-import"><span class="fa fa-file-excel-o"></span>&nbsp;自EXCEL匯入</a>
          <a href="/public/template/classdata_sample.xls" class="k-button" id="btn-academic_agency_class_country-sample" target="_blank"><span class="fa fa-file-excel-o"></span>&nbsp;下載範例檔</a>
          <a href="/public/template/country_list.xls" class="k-button" id="btn-academic_agency_class_country-country" target="_blank"><span class="fa fa-file-excel-o"></span>&nbsp;下載國別表</a>
        </div>
        <div class="toolbar"></div>
      </script>
      <div id="spreadsheet"></div>
      <div id="dialog-academic_agency_class_country"></div>
      <div id="grid-academic_agency_class_country"></div>

      <script type="text/x-kendo-template" id="template-academic_agency_class_summary">
        <div class="createBtnContainer"> </div>
        <div class="toolbar"></div>
      </script>
      <div id="grid-academic_agency_class_summary">
        <table role="grid">
          <tbody>
            <tr>
              <td><span class="required_field">*</span>總時數(每期上課時數)</td>
              <td>總人次</td>
              <td>調整數值</td>
              <td>總人時數</td>
            </tr>
            <tr>
              <td><input type="text" id="editor-hours" required ></p></td>
              <td><p id="summary-people"></p></td>
              <td><input type="text" id="editor-adjust" size="4" /></td>
              <td><p id="summary-total_hours"></p></td>
            </tr>
            <tr>
              <td colspan="4"><p><span class="fa fa-calculator"></span>&nbsp;總時數 X 總人次 - 調整值 = 總人時數</p></td>
            </tr>
            <tr>
              <td><span class="required_field">*</span>直接營收(元)</td>
              <td><span class="required_field">*</span>政府補助(元)</td>
              <td colspan="2">營收額度(元)</td>
            </tr>
            <tr>
              <td><input type="text" id="editor-revenue" placeholder="直接營收(元)" required /></td>
              <td><input type="text" id="editor-subsidy" placeholder="政府補助(元)" required /></td>
              <td colspan="2"><p id="summary-turnover"></p></td>
            </tr>
            <tr>
              <td>其他</td>
              <td colspan="3"><textarea id="editor-note" cols="50" rows="3"></textarea></td>
            </tr>
          </tbody>
        </table>
        <div class="row div-academic_agency_class-send"><div class="col-xs-4"></div><div class="col-xs-4"><a href="\\#" class="btn btn-lg btn-block btn-primary" id="btn-academic_agency_class-send"><span class="fa fa-save"></span>&nbsp;儲存設定</a></div><div class="col-xs-4"></div></div>
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
        mojo.data.academic_agency_class_last = [];
        <?php if (isset($academic_agency_class_last)):?> 
          mojo.data.academic_agency_class_last = JSON.parse('<?php echo json_encode($academic_agency_class_last); ?>');
        <?php endif; ?>
        mojo.data.content_list = JSON.parse('<?php echo json_encode($content_list); ?>');
        mojo.refs.content_list = {};
        for (var i=0; i<mojo.data.content_list.length; i++)
          mojo.refs.content_list[mojo.data.content_list[i]['code']] = mojo.data.content_list[i]['cname'];

        mojo.data.country_list = JSON.parse('<?php echo json_encode($country_list); ?>');
        mojo.refs.country_list = {};
        mojo.refs.country_code_list = []; 
        for (var i=0; i<mojo.data.country_list.length; i++) {
          mojo.refs.country_list[mojo.data.country_list[i]['code']] = mojo.data.country_list[i];                                                                                                                                       
          mojo.refs.country_code_list.push({'code': mojo.data.country_list[i].code, 'cname': mojo.data.country_list[i].cname, 'ename': mojo.data.country_list[i].ename, 'select_key': mojo.data.country_list[i].cname + ' ' + mojo.data.country_list[i].ename + ' ' + mojo.data.country_list[i].code });
        }

        mojo.data.major_list = JSON.parse('<?php echo json_encode($major_list); ?>');
        mojo.refs.major_list = {};
        for (var i=0; i<mojo.data.major_list.length; i++)
          mojo.refs.major_list[mojo.data.major_list[i]['code']] = mojo.data.major_list[i];

        mojo.data.minor_list = JSON.parse('<?php echo json_encode($minor_list); ?>');
        mojo.refs.minor_list = {};
        for (var i=0; i<mojo.data.minor_list.length; i++)
          mojo.refs.minor_list[mojo.data.minor_list[i]['minor_code']] = mojo.data.minor_list[i];

        mojo.data.target_list = JSON.parse('<?php echo json_encode($target_list); ?>');
        mojo.refs.target_list = {};
        for (var i=0; i<mojo.data.target_list.length; i++)
          mojo.refs.target_list[mojo.data.target_list[i]['code']] = mojo.data.target_list[i]['cname']; 
      </script>
    </div>
  <?php endif; ?>
  </div>
</section>

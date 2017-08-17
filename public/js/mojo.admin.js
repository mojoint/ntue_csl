/* mojo javascript */
(function($) {
  $(function() {
    /* =========================================== */
    /* ------------------- admin ------------------- */
    /* status */

    /* maintain */
    mojo.dialog_maintain = function(key, val, params) {
      switch(key)
      {
      case 'academic_agency':
        $('#dialog-academic_agency').kendoDialog({
          minWidth: 480,
          minHeight: 120,
          title: "機構列表",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'add':
                mojo.json = { 'id': 0, 'institution_code': $('#dialog-institution_code').val(), 'cname': $('#dialog-cname').val() };
                break;
              case 'del':
                mojo.json = params;
                break;
              case 'mod':
                mojo.json = { 'id': params.id, 'institution_code': $('#dialog-institution_code').val(), 'cname': $('#dialog-cname').val() };
                break;
              }
              mojo.ajax('admin', key, val, mojo.json);
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-institution_code">學校名稱</label><select id="dialog-institution_code" class="form-control"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-cname">機構名稱</label><input type="text" id="dialog-cname" class="form-control" /></div>';
          $('#dialog-academic_agency').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.academic_institution) 
            $('#dialog-institution_code').append('<option value="' + x + '">' + mojo.refs.academic_institution[x].aka + ' ' + mojo.refs.academic_institution[x].cname + '</option>');
          break;
        case 'mod':
          mojo.html  = '<div><label for="dialog-institution_code"><select id="dialog-institution_code" class="form-control"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-cname">機構名稱</label><input type="text" id="dialog-cname" class="form-control" /></div>';
          $('#dialog-academic_agency').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.academic_institution)  {
            if (params.institution_code == x) 
              $('#dialog-institution_code').append('<option value="' + x + '" selected>' + mojo.refs.academic_institution[x].aka + ' ' + mojo.refs.academic_institution[x].cname + '</option>');
            else   
              $('#dialog-institution_code').append('<option value="' + x + '">' + mojo.refs.academic_institution[x].aka + ' ' + mojo.refs.academic_institution[x].cname + '</option>');
          }
          $('#dialog-cname').val(params.cname);
          break;
        case 'del':
          mojo.html = '<div><label class="warning">刪除 ' + params.cname + ' 與 使用者？</label></div>';
          $('#dialog-academic_agency').data('kendoDialog').content(mojo.html).open().center();
          break;
        } 
        break;
      case 'academic_agency_agent':
        $('#dialog-academic_agency_agent').kendoDialog({
          minWidth: 480,
          minHeight: 120,
          title: "使用者列表",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'add':
                mojo.json = { 'id': 0, 'username': $('#dialog-username').val(), 'agency_id': $('#dialog-agency_id').val(), 'email': $('#dialog-email').val() };
                break;
              case 'del':
                mojo.json = params;
                break;
              case 'mod':
                mojo.json = { 'id': params.id, 'username': params.username, 'agency_id': params.agency_id, 'email': $('#dialog-email').val() };
                break;
              }
              mojo.ajax('admin', key, val, mojo.json);
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-agency_id">機構名稱</label><select id="dialog-agency_id" class="form-control"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-username">使用者ID</label><input type="text" id="dialog-username" class="form-control" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email">電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
          $('#dialog-academic_agency_agent').data('kendoDialog').content(mojo.html).open().center();
          $('#dialog-username').on('change',function(){
            if($.trim($(this).val()) != ''){
              mojo.ajax('admin','academic_agency_agent','chk',{'username':$.trim($(this).val())});
            }
          });
          for (var x in mojo.refs.academic_agency) 
            $('#dialog-agency_id').append('<option value="' + x + '">' + mojo.refs.academic_institution[mojo.refs.academic_agency[x].institution_code].aka + '[ ' + mojo.refs.academic_institution[mojo.refs.academic_agency[x].institution_code].cname + ' ] ' + mojo.refs.academic_agency[x].cname + '</option>');
          break;
        case 'mod':
          mojo.html  = '<div><label for="dialog-agency_id">機構名稱</label><select id="dialog-agency_id" class="form-control"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-username">使用者ID</label><input type="text" id="dialog-username" class="form-control" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email">電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right" style="color:red">存檔後，將會重設使用者密碼，並發出電子郵件，通知使用立即更改密碼，才可以登入系統！</div>';
          $('#dialog-academic_agency_agent').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.academic_agency) {
            if (x == params.agency_id)
              $('#dialog-agency_id').append('<option value="' + x + '" selected>' + mojo.refs.academic_institution[mojo.refs.academic_agency[x].institution_code].aka + '[ ' + mojo.refs.academic_institution[mojo.refs.academic_agency[x].institution_code].cname + ' ] ' + mojo.refs.academic_agency[x].cname + '</option>');
            else
              $('#dialog-agency_id').append('<option value="' + x + '">' + mojo.refs.academic_institution[mojo.refs.academic_agency[x].institution_code].aka + ' ' + mojo.refs.academic_agency[x].cname + '</option>');

          }
          $('#dialog-username').val(params.username).prop('disabled', true);
          $('#dialog-email').val(params.email);
          $('#dialog-agency_id').prop('disabled', true);
          break;
        case 'del':
          mojo.html = '<div><label class="warning">刪除使用者 ' + params.username + '？</label></div>';
          $('#dialog-academic_agency_agent').data('kendoDialog').content(mojo.html).open().center();
          break;
        } 
        break;
      }
    };

    mojo.watch_maintain = function() {
      mojo.ajax('refs', 'academic_institution', 'get');
      mojo.ajax('refs', 'academic_agency', 'get');
      $('#btn-academic_agency-add').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_maintain('academic_agency', 'add');
      });
      $('.btn-academic_agency-mod').on('click', function(e) {
          e.preventDefault();
          var tr = $(e.target).closest("tr");
          var tds = $(tr).find("td");
          mojo.dialog_maintain( 'academic_agency', 'mod', {"id": $(tds[0]).html(), "institution_code": $(tds[1]).html(), "cname": $(tds[3]).html()} );
      });
      $('.btn-academic_agency-del').on('click', function(e) {
          e.preventDefault();
          var tr = $(e.target).closest("tr");
          var tds = $(tr).find("td");
          mojo.dialog_maintain( 'academic_agency', 'del', {"id": $(tds[0]).html(), "institution_code": $(tds[1]).html(), "cname": $(tds[3]).html()} );
      });

      $('#btn-academic_agency_agent-add').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_maintain('academic_agency_agent', 'add');
      });
      $('#grid-academic_agency_agent .btn-academic_agency_agent-mod').on('click', function(e) {
          e.preventDefault();
          var tr = $(e.target).closest("tr");
          var tds = $(tr).find("td");
          mojo.dialog_maintain( 'academic_agency_agent', 'mod', {"id": $(tds[0]).html(), "agency_id": $(tds[1]).html(), "username": $(tds[2]).html(), "email": $(tds[3]).html()} );
      });
      $('.btn-academic_agency_agent-del').on('click', function(e) {
          e.preventDefault();
          var tr = $(e.target).closest("tr");
          var tds = $(tr).find("td");
          mojo.dialog_maintain( 'academic_agency_agent', 'del', {"id": $(tds[0]).html(), "agency_id": $(tds[1]).html(), "username": $(tds[2]).html()} );
      });

    };

    if (mojo.mojo_if('sec-maintain'))
      mojo.watch_maintain(); 
    /* unlock */
    mojo.dialog_admin_unlock = function(key, val, params) {
      switch(key)
      {
      case 'academic_agency_unlock':
        var m = moment();
        var online = m.get('year') + '-' + (parseInt(m.get('month')) + 1) + '-' + m.get('date');
        m.add((parseInt(params.work_days) + 1), 'days');
        var offline = m.get('year') + '-' + (parseInt(m.get('month')) + 1) + '-' + m.get('date');
        $('#dialog-academic_agency_unlock').kendoDialog({
          width: 480,
          title: "填報期間",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'yes':
              case 'no':
                mojo.json = { 'agency_id': params.agency_id, 'id': params.id, 'online': online, 'offline': offline };
                break;
              }
              mojo.ajax('admin', key, val, mojo.json);
            }},
            { text: '取消'}
          ]
        });

        switch(val)
        {
        case 'yes':
          mojo.html  = '<div><label>同意 ' + params.cname + ' 申請解鎖</label></div>';
          mojo.html += '<div><label>期間為 ' + online + ' ~ ' + offline + '</label></div>';
          break;
        case 'no':
          mojo.html  = '<div><label>不同意 ' + params.cname + ' 申請解鎖</label></div>';
          break;
        }
        $('#dialog-academic_agency_unlock').data('kendoDialog').content(mojo.html).open().center();
        break;
      }

    };

    mojo.watch_admin_unlock = function() {
      $('.btn-academic_agency_unlock-yes').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_admin_unlock('academic_agency_unlock', 'yes', {'agency_id': $(tds[0]).html(), 'id': $(tds[1]).html(), 'cname': $(tds[3]).html(), 'work_days': $(tds[7]).html()});
      });

      $('.btn-academic_agency_unlock-no').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_admin_unlock('academic_agency_unlock', 'no', {'agency_id': $(tds[0]).html(), 'id': $(tds[1]).html(), 'cname': $(tds[3]).html()});
      });

      $('#grid-academic_agency_unlock .k-grid-content table tbody tr').each(function(index) {
        var tds = $(this).find("td");
        switch(parseInt($(tds[9]).html()))
        {
        case 1:
          $(tds[10]).find('a:eq(0)').addClass("k-state-disabled");
          $(tds[10]).find('a:eq(1)').hide();
          break;
        case 2:
          $(tds[10]).find('a:eq(0)').hide();
          $(tds[10]).find('a:eq(1)').addClass("k-state-disabled");
          break;
        }
      });
    };

    if (mojo.mojo_if('sec-admin_unlock'))
      mojo.watch_admin_unlock(); 

    /* settings */
    mojo.dialog_settings = function(key, val, params) {
      switch(key)
      {
      case 'academic_era':
        $('#dialog-academic_era').kendoDialog({
          width: 240,
          title: "填報期間",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'mod':
                mojo.json = { 'id': params.id, 'era_id': params.era_id, 'quarter': params.quarter, 'online': $('#dialog-online').val(), 'offline': $('#dialog-offline').val() }
                break;
              }
              mojo.ajax('admin', key, val, mojo.json);
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-institution_code">新增學年?</label></div>';
          $('#dialog-academic_era').data('kendoDialog').content(mojo.html).open().center();
          break;
        case 'mod':
          mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-cname">' + params.cname + '</div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-online">開放填報</label><input type="text" id="dialog-online" class="form-control" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-online">結束填報</label><input type="text" id="dialog-offline" class="form-control" /></div>';
          $('#dialog-academic_era').data('kendoDialog').content(mojo.html).open().center();
          $('#dialog-online').val(params.online).kendoDatePicker({format: "yyyy-MM-dd"}); 
          $('#dialog-offline').val(params.offline).kendoDatePicker({format: "yyyy-MM-dd"}); 
          //$('#dialog-online').val(params.online).datepickerTW({dateFormat: "yy-MM-dd"});
          //$('#dialog-offline').val(params.offline).datepickerTW({dateFormat: "yy-MM-dd"});
          break;
        } 
        break;
      case 'academic_class':
        $('#dialog-academic_class').kendoDialog({
          width: 240,
          title: "填報期間",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'add':
                mojo.json = { 'major': $('#dialog-major').val(), 'minro': $('#dialog-minor').val(), 'cname': $('#dialog-cname').val() }
                break;
              }
              mojo.ajax('admin', key, val, mojo.json);
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-major">研習類別</label><select id="dialog-major"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-minor">研習課程類別</label><input type="text" id="dialog-minor" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-cname">課程類別名稱</label><input type="text" id="dialog-cname" /></div>';
          $('#dialog-academic_class').data('kendoDialog').content(mojo.html).open().center();
          break;
        } 
        break;
      } 
    };

    mojo.watch_settings = function() {
      $('#btn-academic_era-add').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_settings('academic_era', 'add');
      });

      $('#sel-academic_era').on('change', function(e) {
        mojo.dialog_settings('academic_era', 'sel', {'id': $(this).val()} );
      });

      $('.btn-academic_era-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_settings( 'academic_era', 'mod', {'id': $(tds[0]).html(), 'era_id': $(tds[1]).html(), 'quarter': $(tds[2]).html(), 'cname': $(tds[3]).html(), 'online': $(tds[4]).html(), 'offline': $(tds[5]).html() } );
      });

      $('#btn-academic_class-add').on('click', function() {
        mojo.dialog_settings('academic_class', 'add');
      });

      $('#btn-academic_class-save').on('click', function() {
        params = {checks:[], picks:{}};
        $('#grid-academic_class-a tr').each( function(index) {
          var tds = $(this).find("td");
          if ($(tds[0]).find('input').hasClass('checkbox')) {
            if ($(tds[0]).find('input').is(':checked')) {
              params.checks.push($(tds[1]).html());
              params.picks[$(tds[1]).html()] = true;
            }
          }
        });
        $('#grid-academic_class-b tr').each( function(index) {
          var tds = $(this).find("td");
          if ($(tds[0]).find('input').hasClass('checkbox')) {
            if ($(tds[0]).find('input').is(':checked')) {
              params.checks.push($(tds[1]).html());
              params.picks[$(tds[1]).html()] = true;
            }
          }
        });
        $('#grid-academic_class-c tr').each( function(index) {
          var tds = $(this).find("td");
          if ($(tds[0]).find('input').hasClass('checkbox')) {
            if ($(tds[0]).find('input').is(':checked')) {
              params.checks.push($(tds[1]).html());
              params.picks[$(tds[1]).html()] = true;
            }
          }
        });
        mojo.ajax('admin', 'academic_class', 'mod', params);
      });

      $('#select-academic_class-era').on('change', function() {
        mojo.ajax('admin', 'academic_class', 'era');
      });

    };

    if (mojo.mojo_if('sec-settings'))
      mojo.watch_settings(); 

    mojo.watch_admin_report = function() {
      
    };

    if (mojo.mojo_if('sec-admin_report'))
      mojo.watch_admin_report();
  })
})(jQuery);

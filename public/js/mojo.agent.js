/* mojo javascript */
(function($) {
  $(function() {
    /* =========================================== */
    /* ------------------- agent ------------------- */
    /* profile */
    mojo.dialog_agent = function(_self) {
      var _type = '';
      if(typeof _self.id != 'undefined' && _self.id.indexOf('admin')>-1){
        _type = 'admin';
      }
      $('#dialog').kendoDialog({
        minWidth: 480,
        title: "帳號維護",
        content: '',
        model: true,
        visible: false,
        closable: true,
        actions: [
          { text: '確定', primary: true, action: function(e) {
            mojo.json = { username: mojo.mojos[0], agency_id: mojo.mojos[2] };
            if (mojo.reg.email.test($('#dialog-email').val()))
              mojo.json.email = $('#dialog-email').val();
            if (mojo.reg.userpass.test($('#dialog-userpass').val()))
              mojo.json.userpass = $('#dialog-userpass').val();
            if(_type == 'admin') {
              mojo.ajax('admin', 'profile', 'mod', mojo.json);
            }else{
              mojo.ajax('agent', 'profile', 'mod', mojo.json);
            }
          }},
          { text: '取消'}
        ]
      });
      mojo.html = '';
      mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email">信箱</label><input type="text" id="dialog-email" placeholder="需要修改的話請填入" /></div>';
      mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-userpass">密碼</label><input type="text" id="dialog-userpass" placeholder="需要修改的話請填入" /></div>';
      $('#dialog').data('kendoDialog').content(mojo.html).open().center();
    }
    
    mojo.watch_agent = function() {
      $('#btn-agent-profile,#btn-admin').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_agent(this);
      });
    }

    if (mojo.mojo_if('btn-admin')) 
      mojo.watch_agent();

    if (mojo.mojo_if('btn-agent-profile')) 
      mojo.watch_agent();
    /* fill */
    mojo.dialog_fill = function(key, val, params) {
        $('#dialog-academic_agency_class').kendoDialog({
          minWidth: 600,
          minHeight: 120,
          title: "績效填報",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'del':
                mojo.json = params;
                break;
              case 'done':
                mojo.json = params;
                break;
              }
              mojo.ajax('agent', key, val, mojo.json);
            }},
            { text: '取消'}
          ]
        });

        switch(val)
        {
        case 'done':
          mojo.html = '<div><label class="warning">送件後該季度資料即不可再更改，是否確定？</div>';
          $('#dialog-academic_agency_class').data('kendoDialog').content(mojo.html).open().center();
          break;
        case 'del':
          mojo.html = '<div><label class="warning">刪除 ' + params.cname + '？</label></div>';
          $('#dialog-academic_agency_class').data('kendoDialog').content(mojo.html).open().center();
          break;
        } 
    };

    mojo.watch_fill = function() {
      mojo.location_fillmod = function(params) {
        window.location = '/agent/fillmod/' + params;
      };
      if (!mojo.data.academic_agency_class) 
        return;
      mojo.grid.academic_agency_class = $('#grid-academic_agency_class');
      mojo.grid.academic_agency_class_a = $('#grid-academic_agency_class-a');
      mojo.grid.academic_agency_class_b = $('#grid-academic_agency_class-b');
      mojo.grid.academic_agency_class_c = $('#grid-academic_agency_class-c');
      mojo.grid.academic_agency_class_summary = $('#grid-academic_agency_class-summary');

      mojo.data.academic_agency_class_a = [];
      mojo.data.academic_agency_class_b = [];
      mojo.data.academic_agency_class_c = [];

      for (var i=0; i<mojo.data.academic_agency_class.length; i++) {
        switch(mojo.data.academic_agency_class[i].major_code)
        {
        case 'A':
          mojo.data.academic_agency_class_a.push(mojo.data.academic_agency_class[i]);
          break;
        case 'B':
          mojo.data.academic_agency_class_b.push(mojo.data.academic_agency_class[i]);
          break;
        case 'C':
          mojo.data.academic_agency_class_c.push(mojo.data.academic_agency_class[i]);
          break;
        }
      }

      mojo.grid.academic_agency_class.kendoGrid({
        pageable: false,
        resizable: true,
        height: 0,
        columns: [
          { field: "cname", title: "研習類別", width: "240px" },
          { field: "people", title: "總人次", width: "80px" },
          { field: "total_hours", title: "總人時數", width: "100px" },
          { field: "turnover", title: "營收額度", width: "100px" },
          { title: "&nbsp;" }
        ],
        toolbar: kendo.template($('#template-academic_agency_class').html())
      });

      mojo.grid.academic_agency_class_a.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_class_a,
          schema: {
            model: {
              id: "id",
              fields: {
                cname: { type: "string" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "people", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" }
          ]    
        },   
        change: function(e) {
        },   
        remove: function(e) {
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "id", title: "&nbsp;" },
          { field: "minor_cname", title: "第一類研習類別", width: "240px", footerTemplate: "第一類研習類別小計" },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-a" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-a" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover-a" } },
          {
            title: '&nbsp;', width: '180px', 
            command: [
              {   
                name: '編輯',
                template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_class-a-mod" title="編輯"><i class="fa fa-edit"></i></a>'
              },  
              {   
                name: '刪除',
                template: '<a class="k-button k-blank k-grid-delete btn-academic_agency_class-a-del" title="刪除"><i class="fa fa-trash"></i></a>'
              }   
            ]
          }
        ]
      });
      mojo.grid.academic_agency_class_a.data('kendoGrid').hideColumn(0);

      mojo.grid.academic_agency_class_b.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_class_b,
          schema: {
            model: {
              id: "id",
              fields: {
                cname: { type: "string" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },   
          aggregate: [
            { field: "people", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" }
          ]    
        },   
        change: function(e) {
        },   
        remove: function(e) {
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "id", title: "&nbsp;" },
          { field: "minor_cname", title: "第二類研習類別", width: "240px", footerTemplate: "第二類研習類別小計" },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-b" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-b" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover-b" } },
          {
            title: '&nbsp;', width: '180px', 
            command: [
              {   
                name: '編輯',
                template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_class-b-mod" title="編輯"><i class="fa fa-edit"></i></a>'
              },  
              {   
                name: '刪除',
                template: '<a class="k-button k-blank k-grid-delete btn-academic_agency_class-b-del" title="刪除"><i class="fa fa-trash"></i></a>'
              }   
            ]
          }
        ]
      });
      mojo.grid.academic_agency_class_b.data('kendoGrid').hideColumn(0);

      mojo.grid.academic_agency_class_c.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_class_c,
          schema: {
            model: {
              id: "id",        
              fields: {
                cname: { type: "string" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },   
          aggregate: [
            { field: "people", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" }
          ]    
        },   
        change: function(e) {
        },   
        remove: function(e) {
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "id", title: "&nbsp;" },
          { field: "minor_cname", title: "第三類研習類別", width: "240px", footerTemplate: "第三類研習類別小計" },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-c" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-c" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover" } },
          {
            title: '&nbsp;', width: '180px', 
            command: [
              {   
                name: '編輯',
                template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_class-c-mod" title="編輯"><i class="fa fa-edit"></i></a>'
              },  
              {   
                name: '刪除',
                template: '<a class="k-button k-blank k-grid-delete btn-academic_agency_class-c-del" title="刪除"><i class="fa fa-trash"></i></a>'
              }   
            ]
          }
        ]
      });
      mojo.grid.academic_agency_class_c.data('kendoGrid').hideColumn(0);

      mojo.grid.academic_agency_class_summary.kendoGrid({
        pageable: false,
        columns: [
          { field: "cname", title: "研習類別統計", width: "240px" },
          { field: "people", title: "總人數", width: "80px", footerAttributes: { "class": "summary-people" } },
          { field: "total_hours", title: "總人時數", width: "100px", footerAttributes: { "class": "summary-hours" } },
          { field: "turnover", title: "營收額度", width: "100px", footerAttributes: { "class": "summary-turnover" } },
          { title: "&nbsp;" }
        ]
      });

      $('#btn-academic_agency_class-done').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_fill('academic_agency_class', 'done', {'agency_id': mojo.mojos[2], 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'quarter_id': mojo.quarter_id});
      });
      
      $('.btn-academic_agency_class-a-del').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_fill('academic_agency_class', 'del', {'id': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'quarter_id': mojo.quarter_id, 'agency_id': mojo.mojos[2]});
      });

      $('.btn-academic_agency_class-b-del').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_fill('academic_agency_class', 'del', {'id': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'quarter_id': mojo.quarter_id, 'agency_id': mojo.mojos[2]});
      });

      $('.btn-academic_agency_class-c-del').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.dialog_fill('academic_agency_class', 'del', {'id': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'quarter_id': mojo.quarter_id, 'agency_id': mojo.mojos[2]});
      });
      
      $('.btn-academic_agency_class-a-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.location_fillmod($(tds[0]).html());
      });

      $('.btn-academic_agency_class-b-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.location_fillmod($(tds[0]).html());
      });

      $('.btn-academic_agency_class-c-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.location_fillmod($(tds[0]).html());
      });
    };

    if (mojo.mojo_if('sec-fill'))
      mojo.watch_fill();

    /* fill add */
    mojo.check_filladd = function() {
      var pass = true;
      mojo.errmsg = '';
      if (!mojo.reg.string255.test($('#editor-cname').val())) {
        mojo.errmsg += '<p>課程名稱為必填 ( 字串 255 )</p>';
        pass = false;
      }
      if (!mojo.reg.float32.test($('#editor-weekly').val())) {
        mojo.errmsg += '<p>教學時數為必填 ( 浮點 3, 2 )</p>';
        pass = false;
      }
      if (!mojo.reg.float32.test($('#editor-weeks').val())) {
        mojo.errmsg += '<p>教學週數為必填 ( 浮點 3, 2 )</p>';
        pass = false;
      }
      if (!mojo.reg.int11.test($('#editor-revenue').val())) {
        mojo.errmsg += '<p>直接營收為必填 ( 整數 >= 0 )</p>';
        pass = false;
      }
      if (!mojo.reg.int11.test($('#editor-subsidy').val())) {
        mojo.errmsg += '<p>政府補助為必填 ( 整數 >= 0 )</p>';
        pass = false;
      }
      return pass;
    }

    mojo.dialog_filladd = function(key, val, params) {
      $('#dialog-academic_agency_class_country').kendoDialog({
        width: 480,
        minHeight: 120,
        title: "國別資料",
        content: '',
        model: true,
        visible: false,
        closable: true,
        actions: [
          { text: '確定', primary: true, action: function(e) {
            switch(val) 
            {
            case 'add':
              var html = '<tr role="row"><td style="display:none" role="gridcell">' + $('#dialog-country_code').val() + '</td><td role="girdcell">' + mojo.refs.country_list[$('#dialog-country_code').val()]['cname'] + '</td><td class="country_male" role="gridcell">' + (parseInt($('#dialog-male').val()) || 0) + '</td><td class="country_female" role="gridcell">' + (parseInt($('#dialog-female').val()) || 0) + '</td><td class="country_new_male" role="gridcell">' + (parseInt($('#dialog-new_male').val()) || 0) + '</td><td class="country_new_female" role="gridcell">' + (parseInt($('#dialog-new_female').val()) || 0) + '</td><td role="gridcell">' + $('#dialog-note').val() + '</td><td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td></tr>';
              $('#grid-academic_agency_class_country .k-grid-content table tbody').append(html);
        
              $('.btn-academic_agency_class_country-mod').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'mod', mojo.json);
              });    
        
              $('.btn-academic_agency_class_country-del').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'country_cname': $(tds[1]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'del', mojo.json);
              });
              break;
            case 'import':
              var html = '';
              $('#grid-academic_agency_class_country .k-grid-content table tbody').empty();
              for (var x in mojo.country ) {
                for (var i=0; i<mojo.country[x].length; i++) {
                  if (typeof(mojo.country[x][i]) === 'object') {
                    var country = mojo.country[x][i]['國別'];
                    var male = mojo.country[x][i]['男舊生'];
                    var female = mojo.country[x][i]['女舊生'];
                    var new_male = mojo.country[x][i]['男新生'];
                    var new_female = mojo.country[x][i]['女新生'];
                    var note = (mojo.country[x][i]['其他'])? mojo.country[x][i]['其他'] : "";
                    var country_code = '';
                    for (var j=0; j<mojo.refs.country_code_list.length; j++) {
                      if (country == mojo.refs.country_code_list[j].code) {
                        country_code = mojo.refs.country_code_list[j].code;
                      } else if (country == mojo.refs.country_code_list[j].cname) {
                        country_code = mojo.refs.country_code_list[j].code;
                      } else if (country.toLowerCase() == mojo.refs.country_code_list[j].ename.toLowerCase()) {
                        country_code = mojo.refs.country_code_list[j].code;
                      }
                    }
                    if (country_code != "") {
                      html  = '<tr role="row">';
                      html +=   '<td style="display:none" role="gridcell">' + country_code + '</td>';
                      html +=   '<td role="girdcell">';
                      html +=     mojo.refs.country_list[country_code].cname;
                      html +=   '</td>';
                      html +=   '<td role="girdcell" class="country_male">' + male + '</td>';
                      html +=   '<td role="girdcell" class="country_female">' + female + '</td>';
                      html +=   '<td role="girdcell" class="country_new_male">' + new_male + '</td>';
                      html +=   '<td role="girdcell" class="country_new_female">' + new_female + '</td>';
                      html +=   '<td role="girdcell" >' + note + '</td>';
                      html +=   '<td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td>';
                      html += '</tr>';
                      $('#grid-academic_agency_class_country .k-grid-content table tbody').append(html);
                    }
                  }
                }
              }
        
              $('.btn-academic_agency_class_country-mod').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'mod', mojo.json);
              });    
        
              $('.btn-academic_agency_class_country-del').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'country_cname': $(tds[1]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'del', mojo.json);
              });
              break;
            case 'mod':
              var tds = $(params.tr).find("td");
              $(tds[0]).html($('#dialog-country_code').val());
              $(tds[1]).html(mojo.refs.country_list[$('#dialog-country_code').val()]['cname']);
              $(tds[2]).html( (parseInt($('#dialog-male').val()) || 0) );
              $(tds[3]).html( (parseInt($('#dialog-female').val()) || 0) );
              $(tds[4]).html( (parseInt($('#dialog-new_male').val()) || 0) );
              $(tds[5]).html( (parseInt($('#dialog-new_female').val()) || 0) );
              $(tds[6]).html($('#dialog-note').val());
              break;
            case 'del':
              params.tr.remove();
              break;
            }
            mojo.summaryPeople();
          }},
          { text: '取消'}
        ]
      });

      switch(val)
      {
      case 'add':
        mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-country_code">國別</label><select id="dialog-country_code"></select></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-male">男舊生</label><input type="text" id="dialog-male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-female">女舊生</label><input type="text" id="dialog-female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_male">男新生</label><input type="text" id="dialog-new_male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_female">女新生</label><input type="text" id="dialog-new_female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-note">其他</label><input type="text" id="dialog-note" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        break;
      case 'import':
        mojo.html  = '<div><label>EXCEL 匯入將會清除現有的國別資料</label></div>';
        mojo.html  += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-file">EXCEL 檔案</label><input type="file" id="dialog-file" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        $('#dialog-file').on('change', mojo.from_excel);
        break;
      case 'del':
        mojo.html = '<div><label>刪除 ' + params.country_cname + ' 資料?</label></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        break;
      case 'mod':
        mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-country_code">國別</label><select id="dialog-country_code"></select></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-male">男舊生</label><input type="text" id="dialog-male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-female">女舊生</label><input type="text" id="dialog-female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_male">男新生</label><input type="text" id="dialog-new_male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_female">女新生</label><input type="text" id="dialog-new_female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-note">其他</label><input type="text" id="dialog-note" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        $('#dialog-country_code').append('<option value="' + params.country_code + '">' + mojo.refs.country_list[params.country_code]['code'] + ' ' + mojo.refs.country_list[params.country_code]['cname'] + ' ' + mojo.refs.country_list[params.country_code]['ename'] + '</option>');
        $('#dialog-country_code').val(params.country_code);
        $('#dialog-male').val(params.male);
        $('#dialog-female').val(params.female);
        $('#dialog-new_male').val(params.new_male);
        $('#dialog-new_female').val(params.new_female);
        $('#dialog-note').val(params.note);
        break;
      }

      var country_code_list = {};
      $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
        var tds = $(this).find("td");
        country_code_list[$(tds[0]).html()] = $(tds[1]).html();
      });

      options = '';
      for (var x in mojo.refs.country_list) {
        if (!country_code_list[x])
          options += '<option value="' + x + '">' + mojo.refs.country_list[x]['code'] + ' ' + mojo.refs.country_list[x]['cname'] + ' ' + mojo.refs.country_list[x]['ename'] + '</option>';
      }
      $('#dialog-country_code').append(options);
      $('#dialog-country_code').select2();
    };

    mojo.watch_filladd = function() {
         
      mojo.major = $('#academic_agency_class').attr('data-mojo');
      for (var x in mojo.refs.minor_list) {
        if (mojo.refs.minor_list[x].major_code == mojo.major) 
          $('#editor-minor_code').append('<option value="' + x + '">' + mojo.refs.minor_list[x].cname + '</option>');
      }

      for (var x in mojo.refs.content_list) 
        $('#editor-content').append('<option value="' + x + '">' + mojo.refs.content_list[x] + '</option>');

      for (var x in mojo.refs.target_list) 
        $('#editor-target').append('<option value="' + x + '">' + mojo.refs.target_list[x] + '</option>');
      
      mojo.summary = {
        adjust: 0,
        hours: 0,
        female: 0,
        male: 0,
        people: 0,
        new_female: 0,
        new_male: 0,
        new_people: 0,
        revenue: 0,
        subsidy: 0,
        total_hours: 0,
        turnover: 0,
        weekly: 0,
        weeks: 0
      };

      mojo.summaryPeople = function() {
        mojo.summary.female = 0;
        mojo.summary.male = 0;
        mojo.summary.people = 0;
        mojo.summary.new_female = 0;
        mojo.summary.new_male = 0;
        mojo.summary.new_people = 0;
        $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
          var tds = $(this).find('td');
          mojo.summary.male += parseInt($(tds[2]).html()) || 0;
          mojo.summary.female += parseInt($(tds[3]).html()) || 0;
          mojo.summary.new_male += parseInt($(tds[4]).html()) || 0;
          mojo.summary.new_female += parseInt($(tds[5]).html()) || 0;
        });
        mojo.summary.new_people = mojo.summary.new_male + mojo.summary.new_female;
        mojo.summary.people = mojo.summary.male + mojo.summary.female + mojo.summary.new_male + mojo.summary.new_female;
        
        $('.summary-country_male').html(mojo.summary.male);
        $('.summary-country_female').html(mojo.summary.female);
        $('.summary-country_new_male').html(mojo.summary.new_male);
        $('.summary-country_new_female').html(mojo.summary.new_female);
        $('#summary-people').html(mojo.summary.people);
        mojo.summaryHours();
      }

      mojo.summaryHours = function() {
        mojo.summary.hours = mojo.summary.weekly * mojo.summary.weeks;
        $('#editor-hours').val(mojo.summary.hours);
        mojo.summaryTotalHours();
      }

      mojo.summaryTotalHours = function() {
        mojo.summary.hours = parseFloat($('#editor-hours').val()) || 0;
        mojo.summary.total_hours = mojo.summary.hours * mojo.summary.people - mojo.summary.adjust;
        mojo.summary.total_hours = (mojo.summary.total_hours > 0)? mojo.summary.total_hours : 0;
        $('#summary-total_hours').html(mojo.summary.total_hours);
      }

      $('#editor-weekly').on('keyup', function(e) {
        mojo.summary.weekly = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.weekly = parseFloat($(this).val());
        $('#summary-weekly').html(mojo.summary.weekly);
        mojo.summaryHours();
      });
        
      $('#editor-weeks').on('keyup', function(e) {
        mojo.summary.weeks = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.weeks = parseFloat($(this).val());
        $('#summary-weeks').html(mojo.summary.weeks);
        mojo.summaryHours();
      });

      $('#editor-hours').on('keyup', function(e) {
        mojo.summary.hours = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.hours = parseFloat($(this).val());
        mojo.summaryTotalHours();
      });

      $('#editor-adjust').on('keyup', function(e) {
        mojo.summary.adjust = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.adjust = parseFloat($(this).val());
        mojo.summaryTotalHours();
      });

      $('#editor-revenue').on('keyup', function(e) {
        mojo.summary.turnover = 0;
        if (!isNaN(parseInt($(this).val())))
          mojo.summary.turnover = parseFloat($(this).val());
        if (!isNaN(parseInt($('#editor-subsidy').val())))
          mojo.summary.turnover += parseFloat($('#editor-subsidy').val());
        $('#summary-turnover').html(mojo.summary.turnover);
      });

      $('#editor-subsidy').on('keyup', function(e) {
        mojo.summary.turnover = 0;
        if (!isNaN(parseInt($(this).val())))
          mojo.summary.turnover = parseFloat($(this).val());
        if (!isNaN(parseInt($('#editor-revenue').val())))
          mojo.summary.turnover += parseFloat($('#editor-revenue').val());
        $('#summary-turnover').html(mojo.summary.turnover);
      });

      $('#btn-academic_agency_class-send').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_filladd()) {
          var adjust = 0,
              people = 0,
              new_people = 0,
              hours = 0,
              total_hours = 0,
              country = [];
          if (!isNaN(parseInt($('#editor-adjust').val())))
            adjust = $('#editor-adjust').val();
          if (!isNaN(parseInt($('#editor-hours').val())))
            hours = $('#editor-hours').val();
          if (!isNaN(parseInt($('#summary-total_hours').html())))
            total_hours = $('#summary-total_hours').html();
          mojo.json = {'agency_id': mojo.mojos[2], 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'major_code': mojo.major, 'minor_code': $('#editor-minor_code').val(), 'cname': $('#editor-cname').val(), 'weekly': $('#editor-weekly').val(), 'weeks': $('#editor-weeks').val(), 'adjust': adjust, 'content_code': $('#editor-content').val(), 'target_code': $('#editor-target').val(), 'hours': hours, 'total_hours': total_hours, 'revenue': $('#editor-revenue').val(), 'subsidy': $('#editor-subsidy').val(), 'turnover': $('#summary-turnover').html(), 'note': Base64.encode($('#editor-note').val()), 'country': []}; 
    
          $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
            var tds = $(this).find('td');
            var male = parseInt($(tds[2]).html()) || 0;
            var female = parseInt($(tds[3]).html()) || 0;
            var new_male = parseInt($(tds[4]).html()) || 0;
            var new_female = parseInt($(tds[5]).html()) || 0;
            new_people += new_male + new_female;
            people += male + female + new_male + new_female;
            country.push({'country_code': $(tds[0]).html(), 'male': male, 'female': female, 'new_male': new_male, 'new_female': new_female, 'note': Base64.encode($(tds[6]).html())});
          });
          
          mojo.json.country = country;
          mojo.json.new_people = new_people;
          mojo.json.people = people;
          mojo.ajax('agent', 'academic_agency_class', 'add', mojo.json);
        } else 
          mojo.dialog_error('academic_agency_class', mojo.errmsg);
      });

      mojo.data.academic_agency_class_country = [];
      
      mojo.grid.academic_agency_class_country = $('#grid-academic_agency_class_country');
      mojo.grid.academic_agency_class_country.kendoGrid({
        pageable: false,
        resizable: false,
        columns: [
          { field: "country_code", title: "&nbsp;" },
          { field: "country_cname", title: "國別", width: "200px", footerTemplate: "人數小計" },
          { field: "male", title: "男舊生", attributes: { "class": "country_male" }, footerAttributes: { "class": "summary-country_male" }, width: "90px" },
          { field: "female", title: "女舊生", attributes: { "class": "country_female" }, footerAttributes: { "class": "summary-country_female" }, width: "90px" },
          { field: "new_male", title: "男新生", attributes: { "class": "country_new_male" }, footerAttributes: { "class": "summary-country_new_male" }, width: "90px" },
          { field: "new_female", title: "女新生", attributes: { "class": "country_new_female" }, footerAttributes: { "class": "summary-country_new_female" }, width: "90px" },
          { field: "note", title: "其他" }, 
          { title: "&nbsp;", width: "200px", 
            command: [
              {   
                name: '編輯',
                template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a>'
              },  
              {   
                name: '刪除',
                template: '<a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a>'
              } 
            ]
          }
        ],
        toolbar: kendo.template($('#template-academic_agency_class_country').html())
      });
      $('#grid-academic_agency_class_country').data('kendoGrid').hideColumn(0);

      $('#btn-academic_agency_class_country-add').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_filladd('academic_agency_class_country', 'add', {});
      });

      $('.btn-academic_agency_class_country-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'tr': tr};
        mojo.dialog_filladd('academic_agency_class_country', 'mod', mojo.json);
      });

      $('.btn-academic_agency_class_country-del').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'country_code': $(tds[0]).html(), 'country_cname': $(tds[1]).html(), 'tr': tr};
        mojo.dialog_filladd('academic_agency_class_country', 'del', mojo.json);
      });

      /* excel */
      $("#btn-academic_agency_class_country-import").on('click', function(e) {
        e.preventDefault();
        mojo.dialog_filladd('academic_agency_class_country', 'import', {});
      });
    };
      
    if (mojo.mojo_if('sec-filladd'))
      mojo.watch_filladd();

    /* fill mod */
    mojo.check_fillmod = function() {
      var pass = true;
      mojo.errmsg = '';
      if (!mojo.reg.string255.test($('#editor-cname').val())) {
        mojo.errmsg += '<p>課程名稱為必填 ( 字串 255 )</p>';
        pass = false;
      }
      if (!mojo.reg.float32.test($('#editor-weekly').val())) {
        mojo.errmsg += '<p>教學時數為必填 ( 浮點 3, 2 )</p>';
        pass = false;
      }
      if (!mojo.reg.float32.test($('#editor-weeks').val())) {
        mojo.errmsg += '<p>教學週數為必填 ( 浮點 3, 2 )</p>';
        pass = false;
      }
      if (!mojo.reg.int11.test($('#editor-revenue').val())) {
        mojo.errmsg += '<p>直接營收為必填 ( 整數 >= 0 )</p>';
        pass = false;
      }
      if (!mojo.reg.int11.test($('#editor-subsidy').val())) {
        mojo.errmsg += '<p>政府補助為必填 ( 整數 >= 0 )</p>';
        pass = false;
      }
      return pass;
    }

    mojo.dialog_fillmod = function(key, val, params) {
      $('#dialog-academic_agency_class_country').kendoDialog({
        width: 480,
        minHeight: 120,
        title: "國別資料",
        content: '',
        model: true,
        visible: false,
        closable: true,
        actions: [
          { text: '確定', primary: true, action: function(e) {
            switch(val) 
            {
            case 'add':
              var html = '<tr role="row"><td style="display:none" role="gridcell">' + $('#dialog-country_code').val() + '</td><td role="girdcell">' + mojo.refs.country_list[$('#dialog-country_code').val()]['cname'] + '</td><td class="country_male" role="gridcell">' + (parseInt($('#dialog-male').val()) || 0) + '</td><td class="country_female" role="gridcell">' + (parseInt($('#dialog-female').val()) || 0) + '</td><td class="country_new_male" role="gridcell">' + (parseInt($('#dialog-new_male').val()) || 0) + '</td><td class="country_new_female" role="gridcell">' + (parseInt($('#dialog-new_female').val()) || 0) + '</td><td role="gridcell">' + $('#dialog-note').val() + '</td><td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td></tr>';
              $('#grid-academic_agency_class_country .k-grid-content table tbody').append(html);
              break;
            case 'import':
              var html = '';
              $('#grid-academic_agency_class_country .k-grid-content table tbody').empty();
              for (var x in mojo.country ) {
                for (var i=0; i<mojo.country[x].length; i++) {
                  if (typeof(mojo.country[x][i]) === 'object') {
                    var country = mojo.country[x][i]['國別'];
                    var male = parseInt(mojo.country[x][i]['男舊生']) || 0;
                    
                    var female = parseInt(mojo.country[x][i]['女舊生']) || 0;
                    var new_male = parseInt(mojo.country[x][i]['男新生']) || 0;
                    var new_female = parseInt(mojo.country[x][i]['女新生']) || 0;
                    var note = (mojo.country[x][i]['其他'])? mojo.country[x][i]['其他'] : "";
                    var country_code = '';
                    for (var j=0; j<mojo.refs.country_code_list.length; j++) {
                      if (country == mojo.refs.country_code_list[j].code) {
                        country_code = mojo.refs.country_code_list[j].code;
                      } else if (country == mojo.refs.country_code_list[j].cname) {
                        country_code = mojo.refs.country_code_list[j].code;
                      } else if (country.toLowerCase() == mojo.refs.country_code_list[j].ename.toLowerCase()) {
                        country_code = mojo.refs.country_code_list[j].code;
                      }
                    }
                    html  = '<tr role="row">';
                    html +=   '<td style="display:none" role="gridcell">' + country_code + '</td>';
                    html +=   '<td role="girdcell">';
                    html +=     mojo.refs.country_list[country_code].cname;
                    html +=   '</td>';
                    html +=   '<td role="girdcell" class="country_male">' + male + '</td>';
                    html +=   '<td role="girdcell" class="country_female">' + female + '</td>';
                    html +=   '<td role="girdcell" class="country_new_male">' + new_male + '</td>';
                    html +=   '<td role="girdcell" class="country_new_female">' + new_female + '</td>';
                    html +=   '<td role="girdcell" >' + note + '</td>';
                    html +=   '<td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td>';
                    html += '</tr>';
                    $('#grid-academic_agency_class_country .k-grid-content table tbody').append(html);
                  }
                }
              }
        
              $('.btn-academic_agency_class_country-mod').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'mod', mojo.json);
              });    
        
              $('.btn-academic_agency_class_country-del').on('click', function(e) {
                e.preventDefault();
                var tr = $(e.target).closest("tr");
                var tds = $(tr).find("td");
                mojo.json = {'country_code': $(tds[0]).html(), 'country_cname': $(tds[1]).html(), 'tr': tr};
                mojo.dialog_filladd('academic_agency_class_country', 'del', mojo.json);
              });
              break;
            case 'mod':
              var tds = $(params.tr).find("td");
              $(tds[0]).html($('#dialog-country_code').val());
              $(tds[1]).html(mojo.refs.country_list[$('#dialog-country_code').val()]['cname']);
              $(tds[2]).html( (parseInt($('#dialog-male').val()) || 0) );
              $(tds[3]).html( (parseInt($('#dialog-female').val()) || 0) );
              $(tds[4]).html( (parseInt($('#dialog-new_male').val()) || 0) );
              $(tds[5]).html( (parseInt($('#dialog-new_female').val()) || 0) );
              $(tds[6]).html($('#dialog-note').val());
              break;
            case 'del':
              params.tr.remove();
              break;
            }
            mojo.summaryPeople(0);
          }},
          { text: '取消'}
        ]
      });

      switch(val)
      {
      case 'add':
        mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-country_code">國別</label><select id="dialog-country_code"></select></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-male">男舊生</label><input type="text" id="dialog-male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-female">女舊生</label><input type="text" id="dialog-female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_male">男新生</label><input type="text" id="dialog-new_male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_female">女新生</label><input type="text" id="dialog-new_female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-note">其他</label><input type="text" id="dialog-note" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        break;
      case 'del':
        mojo.html = '<div><label>刪除 ' + params.country_cname + ' 資料?</label></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        break;
      case 'import':
        mojo.html  = '<div><label>EXCEL 匯入將會清除現有的國別資料</label></div>';
        mojo.html  += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-file">EXCEL 檔案</label><input type="file" id="dialog-file" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        $('#dialog-file').on('change', mojo.from_excel);
        break;
      case 'mod':
        mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-country_code">國別</label><select id="dialog-country_code"></select></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-male">男舊生</label><input type="text" id="dialog-male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-female">女舊生</label><input type="text" id="dialog-female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_male">男新生</label><input type="text" id="dialog-new_male" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-new_female">女新生</label><input type="text" id="dialog-new_female" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-note">其他</label><input type="text" id="dialog-note" /></div>';
        $('#dialog-academic_agency_class_country').data('kendoDialog').content(mojo.html).open().center();
        $('#dialog-country_code').append('<option value="' + params.country_code + '">' + mojo.refs.country_list[params.country_code]['code'] + ' ' + mojo.refs.country_list[params.country_code]['cname'] + ' ' + mojo.refs.country_list[params.country_code]['ename'] + '</option>');
        $('#dialog-country_code').val(params.country_code);
        $('#dialog-male').val(params.male);
        $('#dialog-female').val(params.female);
        $('#dialog-new_male').val(params.new_male);
        $('#dialog-new_female').val(params.new_female);
        $('#dialog-note').val(params.note);
        break;
      }

      var country_code_list = {};
      $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
        var tds = $(this).find("td");
        country_code_list[$(tds[0]).html()] = $(tds[1]).html();
      });

      options = '';
      for (var x in mojo.refs.country_list) {
        if (!country_code_list[x])
          options += '<option value="' + x + '">' + mojo.refs.country_list[x]['code'] + ' ' + mojo.refs.country_list[x]['cname'] + ' ' + mojo.refs.country_list[x]['ename'] + '</option>';
      }
      $('#dialog-country_code').append(options);
      $('#dialog-country_code').select2();
    };

    mojo.watch_fillmod = function() {
         
      mojo.class_id = $('#academic_agency_class').attr('data-mojo');

      for (var x in mojo.refs.minor_list) {
        if (mojo.data.academic_agency_class && mojo.refs.minor_list[x].major_code ==  mojo.data.academic_agency_class[0].major_code)
          $('#editor-minor_code').append('<option value="' + x + '">' + mojo.refs.minor_list[x].cname + '</option>');
      }
      $('#editor-minor_code').val(mojo.data.academic_agency_class[0].minor_code);

      for (var x in mojo.refs.content_list) 
        $('#editor-content').append('<option value="' + x + '">' + mojo.refs.content_list[x] + '</option>');
      $('#editor-content').val(mojo.data.academic_agency_class[0].content_code);

      for (var x in mojo.refs.target_list) 
        $('#editor-target').append('<option value="' + x + '">' + mojo.refs.target_list[x] + '</option>');
      $('#editor-target').val(mojo.data.academic_agency_class[0].target_code);

      mojo.summary = {
        adjust: 0,
        hours: 0,
        female: 0,
        male: 0,
        people: 0,
        new_female: 0,
        new_male: 0,
        new_people: 0,
        revenue: 0,
        subsidy: 0,
        total_hours: 0,
        turnover: 0,
        weekly: 0,
        weeks: 0
      };

      mojo.summaryPeople = function(tag) {
        mojo.summary.female = 0;
        mojo.summary.male = 0;
        mojo.summary.people = 0;
        mojo.summary.new_female = 0;
        mojo.summary.new_male = 0;
        mojo.summary.new_people = 0;
        $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
          var tds = $(this).find('td');
          mojo.summary.male += parseInt($(tds[2]).html()) || 0;
          mojo.summary.female += parseInt($(tds[3]).html()) || 0;
          mojo.summary.new_male += parseInt($(tds[4]).html()) || 0;
          mojo.summary.new_female += parseInt($(tds[5]).html()) || 0;
        });
        
        mojo.summary.new_people = mojo.summary.new_male + mojo.summary.new_female;
        mojo.summary.people = mojo.summary.male + mojo.summary.female + mojo.summary.new_male + mojo.summary.new_female;
        $('.summary-country_male').html(mojo.summary.male);
        $('.summary-country_female').html(mojo.summary.female);
        $('.summary-country_new_male').html(mojo.summary.new_male);
        $('.summary-country_new_female').html(mojo.summary.new_female);
        $('#summary-people').html(mojo.summary.people);
        if (!tag)
          mojo.summaryHours();
      }

      mojo.summaryHours = function() {
        mojo.summary.hours = mojo.summary.weekly * mojo.summary.weeks;
        $('#editor-hours').val(mojo.summary.hours);
        mojo.summaryTotalHours();
      }

      mojo.summaryTotalHours = function() {
        mojo.summary.hours = $('#editor-hours').val();
        mojo.summary.total_hours = mojo.summary.hours * mojo.summary.people - mojo.summary.adjust;
        mojo.summary.total_hours = (mojo.summary.total_hours > 0)? mojo.summary.total_hours : 0;
        $('#summary-total_hours').html(mojo.summary.total_hours);
      }

      $('#editor-weekly').on('keyup', function(e) {
        mojo.summary.weekly = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.weekly = parseFloat($(this).val());
        $('#summary-weekly').html(mojo.summary.weekly);
        mojo.summaryHours();
      });
        
      $('#editor-weeks').on('keyup', function(e) {
        mojo.summary.weeks = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.weeks = parseFloat($(this).val());
        $('#summary-weeks').html(mojo.summary.weeks);
        mojo.summaryHours();
      });

      $('#editor-hours').on('keyup', function(e) {
        mojo.summary.hours = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.hours = parseFloat($(this).val());
        mojo.summaryTotalHours();
      });

      $('#editor-adjust').on('keyup', function(e) {
        mojo.summary.adjust = 0;
        if (!isNaN(parseInt($(this).val()))) 
          mojo.summary.adjust = parseFloat($(this).val());
        mojo.summaryTotalHours();
      });

      $('#editor-revenue').on('keyup', function(e) {
        mojo.summary.turnover = 0;
        if (!isNaN(parseInt($(this).val())))
          mojo.summary.turnover = parseFloat($(this).val());
        if (!isNaN(parseInt($('#editor-subsidy').val())))
          mojo.summary.turnover += parseFloat($('#editor-subsidy').val());
        $('#summary-turnover').html(mojo.summary.turnover);
      });

      $('#editor-subsidy').on('keyup', function(e) {
        mojo.summary.turnover = 0;
        if (!isNaN(parseInt($(this).val())))
          mojo.summary.turnover = parseFloat($(this).val());
        if (!isNaN(parseInt($('#editor-revenue').val())))
          mojo.summary.turnover += parseFloat($('#editor-revenue').val());
        $('#summary-turnover').html(mojo.summary.turnover);
      });

      $('#btn-academic_agency_class-send').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_fillmod()) {
          var adjust = 0,
              people = 0,
              new_people = 0,
              hours = 0,
              total_hours = 0,
              country = [];
          if (!isNaN(parseInt($('#editor-adjust').val())))
            adjust = $('#editor-adjust').val();
          if (!isNaN(parseInt($('#editor-hours').val())))
            hours = $('#editor-hours').val();
          if (!isNaN(parseInt($('#summary-total_hours').html())))
            total_hours = $('#summary-total_hours').html();
          mojo.json = {'agency_id': mojo.mojos[2], 'class_id': mojo.class_id, 'era_id': mojo.era_id, 'quarter': mojo.quarter, 'minor_code': $('#editor-minor_code').val(), 'cname': $('#editor-cname').val(), 'weekly': $('#editor-weekly').val(), 'weeks': $('#editor-weeks').val(), 'adjust': adjust, 'content_code': $('#editor-content').val(), 'target_code': $('#editor-target').val(), 'hours': hours, 'total_hours': total_hours, 'revenue': $('#editor-revenue').val(), 'subsidy': $('#editor-subsidy').val(), 'turnover': $('#summary-turnover').html(), 'note': Base64.encode($('#editor-note').val())}; 
    
          $('#grid-academic_agency_class_country .k-grid-content table tbody tr').each(function(e) {
            var tds = $(this).find('td');
            var male = parseInt($(tds[2]).html()) || 0;
            var female = parseInt($(tds[3]).html()) || 0;
            var new_male = parseInt($(tds[4]).html()) || 0;
            var new_female = parseInt($(tds[5]).html()) || 0;
            new_people += new_male + new_female;
            people += male + female + new_male + new_female;
            country.push({'country_code': $(tds[0]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'note': Base64.encode($(tds[6]).html())});
          });
          mojo.json.country = country;
          mojo.json.new_people = new_people;
          mojo.json.people = people;
          mojo.ajax('agent', 'academic_agency_class', 'mod', mojo.json);
        } else 
          mojo.dialog_error('academic_agency_class', mojo.errmsg);
      });

      mojo.grid.academic_agency_class_country = $('#grid-academic_agency_class_country').kendoGrid({ 
        pageable: false,
        columns: [
          { field: "country_code", title: "&nbsp;" },
          { field: "country_cname", title: "國別", width: "200px", footerTemplate: "人數小計" },
          { field: "male", title: "男舊生", attributes: { "class": "country_male" }, footerAttributes: { "class": "summary-country_male" }, width: "90px" },
          { field: "female", title: "女舊生", attributes: { "class": "country_female" }, footerAttributes: { "class": "summary-country_female" }, width: "90px" },
          { field: "new_male", title: "男新生", attributes: { "class": "country_new_male" }, footerAttributes: { "class": "summary-country_new_male" }, width: "90px" },
          { field: "new_female", title: "女新生", attributes: { "class": "country_new_female" }, footerAttributes: { "class": "summary-country_new_female" }, width: "90px" },
          { field: "note", title: "其他" }, 
          { title: "&nbsp;", width: "200px", 
            command: [
              {   
                name: '編輯',
                template: '<a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a>'
              },  
              {   
                name: '刪除',
                template: '<a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a>'
              } 
            ]
          }
        ],
        toolbar: kendo.template($('#template-academic_agency_class_country').html())
      });
      $('#grid-academic_agency_class_country').data('kendoGrid').hideColumn(0);

      for (var i=0; i<mojo.data.academic_agency_class_country.lenght; i++)
        mojo.data.academic_agency_class_country[i]['note'] = Base64.decode(mojo.data.academic_agency_class_country[i]['note']);
      $('#grid-academic_agency_class_country').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_class_country }));
      if (mojo.data.academic_agency_class) {
        $('#editor-minor_code').val(mojo.data.academic_agency_class[0].minor_code);
        $('#editor-cname').val(mojo.data.academic_agency_class[0].cname);
        $('#editor-weekly').val(mojo.data.academic_agency_class[0].weekly);
        $('#editor-weeks').val(mojo.data.academic_agency_class[0].weeks);
        $('#editor-adjust').val(mojo.data.academic_agency_class[0].adjust);
        $('#editor-revenue').val(mojo.data.academic_agency_class[0].revenue);
        $('#editor-subsidy').val(mojo.data.academic_agency_class[0].subsidy);
        $('#editor-note').val(Base64.decode(mojo.data.academic_agency_class[0].note));
        $('#editor-hours').val(mojo.data.academic_agency_class[0].hours);
        $('#summary-people').html(mojo.data.academic_agency_class[0].people);
        $('#summary-total_hours').html(mojo.data.academic_agency_class[0].total_hours);
        $('#summary-turnover').html(mojo.data.academic_agency_class[0].turnover);
        mojo.summary.weekly = mojo.data.academic_agency_class[0].weekly;
        mojo.summary.weeks = mojo.data.academic_agency_class[0].weeks;
        mojo.summary.adjust = mojo.data.academic_agency_class[0].adjust;
        mojo.summary.hours = mojo.data.academic_agency_class[0].hours;
        mojo.summary.total_hours = mojo.data.academic_agency_class[0].total_hours;
        mojo.summary.revenue = mojo.data.academic_agency_class[0].revenue;
        mojo.summary.subsidy = mojo.data.academic_agency_class[0].subsidy;
        mojo.summary.turnover = mojo.data.academic_agency_class[0].turnover;
        mojo.summaryPeople(1);
      }

      $('#btn-academic_agency_class_country-add').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_fillmod('academic_agency_class_country', 'add', {});
      });

      $('.btn-academic_agency_class_country-mod').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'note': $(tds[6]).html(), 'tr': tr};
        mojo.dialog_fillmod('academic_agency_class_country', 'mod', mojo.json);
      });

      $('.btn-academic_agency_class_country-del').on('click', function(e) {
        e.preventDefault();
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'country_code': $(tds[0]).html(), 'country_cname': $(tds[1]).html(), 'tr': tr};
        mojo.dialog_fillmod('academic_agency_class_country', 'del', mojo.json);
      });

      /* excel */
      $("#btn-academic_agency_class_country-import").on('click', function(e) {
        e.preventDefault();
        mojo.dialog_fillmod('academic_agency_class_country', 'import', {});
      });

    };

    if (mojo.mojo_if('sec-fillmod'))
      mojo.watch_fillmod();
    /* info */
    mojo.dialog_info = function(key, val, params) {
      switch(key) 
      {   
      case 'academic_agency_hr':
        $('#dialog-academic_agency_hr').kendoDialog({
          width: 480,
          title: params.academic_era_code + " 教學人力",
          content: '', 
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {   
              case 'add':
                mojo.json = {'agency_id': mojo.mojos[2], 'administration': $('#dialog-administration').val(), 'subject': $('#dialog-subject').val(), 'adjunct': $('#dialog-adjunct').val(), 'reserve': $('#dialog-reserve').val(), 'others': $('#dialog-others').val(), 'note': $('#dialog-note').val()};
                break;
              case 'mod':
                mojo.json = {'agency_id': mojo.mojos[2], 'era_id': params.era_id, 'academic_era_code': params.academic_era_code, 'administration': $('#dialog-administration').val(), 'subject': $('#dialog-subject').val(), 'adjunct': $('#dialog-adjunct').val(), 'reserve': $('#dialog-reserve').val(), 'others': $('#dialog-others').val(), 'note': $('#dialog-note').val()};
                break;
              }   
              mojo.ajax('agent', key, val, mojo.json);
            }}, 
            { text: '取消'}
          ],  
        }); 

        mojo.html  = '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-administration">行政人員</label><input type="text" id="dialog-administration" class="form-control" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-subject">專任教師</label><input type="text" id="dialog-subject" class="form-control" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-adjunct">兼任教師</label><input type="text" id="dialog-adjunct" class="form-control" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-reserve">儲備教師</label><input type="text" id="dialog-reserve" class="form-control" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-others">其他教師</label><input type="text" id="dialog-others" class="form-control" /></div>';
        mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-note">備註</label><input type="text" id="dialog-note" class="form-control" /></div>';
        $('#dialog-academic_agency_hr').data('kendoDialog').content(mojo.html).open().center();
        switch(val)
        {   
        case 'mod':
          $('#dialog-administration').val(params.administration);
          $('#dialog-subject').val(params.subject);
          $('#dialog-adjunct').val(params.adjunct);
          $('#dialog-reserve').val(params.reserve);
          $('#dialog-others').val(params.others);
          $('#dialog-note').val(params.note);
          break;
        }   
        break;
      case 'academic_agency_contact':
        $('#dialog-academic_agency_contact').kendoDialog({
          width: 600,
          title: "聯絡人",
          content: '', 
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {   
              case 'add':
              case 'mod':
                mojo.json = {'agency_id': mojo.mojos[2], 'id': params.id, 'cname': $('#dialog-cname').val(), 'title': $('#dialog-title').val(), 'manager': ($('#dialog-manager').is(':checked'))? 1 : 0, 'staff': ($('#dialog-staff').is(':checked'))? 1 : 0, 'role': $('#dialog-role').val(), 'area_code': $('#dialog-area_code').val(), 'phone': $('#dialog-phone').val(), 'ext': $('#dialog-ext').val(), 'email': $('#dialog-email').val(), 'spare_email': $('#dialog-spare_email').val(), 'primary': ($('#dialog-primary').is(':checked'))? 1 : 0};  
                break;
              case 'del':
                mojo.json = {'agency_id': mojo.mojos[2], 'id': params.id};
                break;
              }   
              mojo.ajax('agent', key, val, mojo.json);
            }}, 
            { text: '取消'}
          ],  
        }); 

        switch(val)
        {   
        case 'add':
          mojo.html  = '<div class="col-xs-12" ><label for="dialog-cname">姓名</label><input type="text" id="dialog-cname" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-title">職稱</label><input type="text" id="dialog-title" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-manager" class="form-control mini-chkbox" /><label for="dialog-manager">單位主管</label></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-staff" class="form-control mini-chkbox" /><label for="dialog-staff">單位職員</label></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-role">聘用身份</label><input type="text" id="dialog-role" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label>電話</label>&nbsp;<select id="dialog-area_code"></select>&nbsp;<input type="text" id="dialog-phone" class="" placeholder="電話" size="10" />&nbsp;<input type="text" id="dialog-ext" class="" placeholder="分機" size="6" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-email">信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-spare_email">備用信箱</label><input type="text" id="dialog-spare_email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-primary" class="form-control mini-chkbox" /><label for="dialog-primary">主要聯絡人</label></div>';
          $('#dialog-academic_agency_contact').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.area_list)
            $('#dialog-area_code').append('<option value="' + x + '">' + x + '(' + mojo.refs.area_list[x] + ')</option>');
          break;
        case 'mod':
          mojo.html  = '<div class="col-xs-12" ><label for="dialog-cname">姓名</label><input type="text" id="dialog-cname" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-title">職稱</label><input type="text" id="dialog-title" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-manager" class="form-control mini-chkbox" /><label for="dialog-manager">單位主管</label></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-staff" class="form-control mini-chkbox" /><label for="dialog-staff">單位職員</label></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-role">聘用身份</label><input type="text" id="dialog-role" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label>電話</label>&nbsp;<select id="dialog-area_code"></select>&nbsp;<input type="text" id="dialog-phone" class="" placeholder="電話" size="10" />&nbsp;<input type="text" id="dialog-ext" class="" placeholder="分機" size="6" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-email">信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-spare_email">備用信箱</label><input type="text" id="dialog-spare_email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-primary" class="form-control mini-chkbox" /><label for="dialog-primary">主要聯絡人</label></div>';
          $('#dialog-academic_agency_contact').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.area_list)
            $('#dialog-area_code').append('<option value="' + x + '">' + x + '(' + mojo.refs.area_list[x] + ')</option>');
          $('#dialog-cname').val(params.cname);
          $('#dialog-title').val(params.title);
          if (1 == params.manager)
            $('#dialog-manager').prop('checked', true);
          if (1 == params.staff)
            $('#dialog-staff').prop('checked', true);
          $('#dialog-role').val(params.role);
          $('#dialog-area_code').val(params.area_code);
          $('#dialog-phone').val(params.phone);
          $('#dialog-ext').val(params.ext);
          $('#dialog-email').val(params.email);
          $('#dialog-spare_email').val(params.spare_email);
          if (1 == params.primary)
            $('#dialog-primary').prop('checked', true);
          break;
        case 'del':
          mojo.html = '<div class="col-xs-12"><label>刪除 聯絡人 ' + params.cname + '?</label></div>';
          $('#dialog-academic_agency_contact').data('kendoDialog').content(mojo.html).open().center();
          break;
        }   
        break;
      }
    };

    mojo.watch_info = function() {
      mojo.ajax('refs', 'area_list', 'get');
      $('#btn-academic_agency-mod').on('click', function(e) {
        e.preventDefault(); 
        mojo.json = {'id': mojo.mojos[2], 'cname': $('#academic_agency_cname').val(), 'zipcode': $('#academic_agency_zipcode').val(), 'address': $('#academic_agency_address').val(), 'established': $('#academic_agency_established').val(), 'approval': $('#academic_agency_approval').val(), 'note': $('#academic_agency_note').val()};
        mojo.ajax('agent', 'academic_agency', 'mod', mojo.json);
      });

      $('#btn-academic_agency_hr-add').on('click', function(e) {
        e.preventDefault(); 
        mojo.ajax('agent', 'academic_agency_hr', 'add', {'agency_id': mojo.mojos[2]});
      });

      $('.btn-academic_agency_hr-mod').on('click', function(e) {
        e.preventDefault(); 
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'agency_id': $(tds[0]).html(), 'era_id': $(tds[1]).html(), 'academic_era_code': $(tds[2]).html(), 'administration': $(tds[3]).html(), 'subject': $(tds[4]).html(), 'adjunct': $(tds[5]).html(), 'reserve': $(tds[6]).html(), 'others': $(tds[7]).html(), 'note': $(tds[8]).html()};
        mojo.dialog_info('academic_agency_hr', 'mod', mojo.json);
      });

      $('#btn-academic_agency_contact-add').on('click', function(e) {
        e.preventDefault(); 
        
        mojo.dialog_info('academic_agency_contact', 'add', {'id': 0});
      });

      $('.btn-academic_agency_contact-mod').on('click', function(e) {
        e.preventDefault(); 
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'id': $(tds[0]).html(), 'agency_id': $(tds[1]).html(), 'cname': $(tds[2]).html(), 'title': $(tds[3]).html(), 'manager': $(tds[4]).html(), 'staff': $(tds[5]).html(), 'role': $(tds[6]).html(), 'area_code': $(tds[7]).html(), 'phone': $(tds[8]).html(), 'ext': $(tds[9]).html(), 'email': $(tds[11]).html(), 'spare_email': $(tds[12]).html(), 'primary': $(tds[13]).html()};
        mojo.dialog_info('academic_agency_contact', 'mod', mojo.json);
      });

      $('.btn-academic_agency_contact-del').on('click', function(e) {
        e.preventDefault(); 
        var tr = $(e.target).closest("tr");
        var tds = $(tr).find("td");
        mojo.json = {'id': $(tds[0]).html(), 'agency_id': $(tds[1]).html(), 'cname': $(tds[2]).html()};
        mojo.dialog_info('academic_agency_contact', 'del', mojo.json);
      });
    };

    if (mojo.mojo_if('sec-info'))
      mojo.watch_info();

    /* unlock */
    mojo.dialog_agency_unlock = function(key, val, params) {

    }

    mojo.watch_agency_unlock = function() {
      if (mojo.data.academic_agency_unlock.length) {
        $('#editor-academic_era').val(mojo.data.academic_agency_unlock[0]['era_id']);
        $('#editor-academic_era_quarter').val(mojo.data.academic_agency_unlock[0]['quarter']);
        $('#editor-academic_class-note').val(mojo.data.academic_agency_unlock[0]['note']);
        $('#editor-academic_class-work_days').val(mojo.data.academic_agency_unlock[0]['work_days']);

        var minors = mojo.data.academic_agency_unlock[0]['minors'].split(',');
        $('#grid-academic_class-a table tbody tr').each(function(e) {
          if (minors.indexOf($(this).find('td:eq(1)').html()) != -1) {
            $(this).addClass('k-state-selected');
            $(this).find('td:eq(0) input:checkbox').prop('checked', true);
          }
        });
        $('#grid-academic_class-b table tbody tr').each(function(e) {
          if (minors.indexOf($(this).find('td:eq(1)').html()) != -1) {
            $(this).addClass('k-state-selected');
            $(this).find('td:eq(0) input:checkbox').prop('checked', true);
          }
        });
        $('#grid-academic_class-c table tbody tr').each(function(e) {
          if (minors.indexOf($(this).find('td:eq(1)').html()) != -1) {
            $(this).addClass('k-state-selected');
            $(this).find('td:eq(0) input:checkbox').prop('checked', true);
          }
        });
      }

      $('#grid-academic_class-a table tbody tr').on('click', function(e) {
        if ($(this).hasClass('k-state-selected')) {
          $(this).removeClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', false);
        } else {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });

      $('#grid-academic_class-b table tbody tr').on('click', function(e) {
        if ($(this).hasClass('k-state-selected')) {
          $(this).removeClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', false);
        } else {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });

      $('#grid-academic_class-c table tbody tr').on('click', function(e) {
        if ($(this).hasClass('k-state-selected')) {
          $(this).removeClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', false);
        } else {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });

      $('#btn-academic_class-unlock').on('click', function(e) {
        e.preventDefault();
        mojo.json = {'agency_id': mojo.mojos[2], 'era_id': $('#editor-academic_era').val(), 'quarter': $('#editor-academic_era_quarter').val(), 'note': $('#editor-academic_class-note').val(), 'work_days': $('#editor-academic_class-work_days').val()};
        var minors = [];
        $('#grid-academic_class-a table tbody tr').each(function( index ) {
          if ($(this).hasClass('k-state-selected'))
            minors.push($(this).find('td:eq(1)').html());
        });
        $('#grid-academic_class-b table tbody tr').each(function( index ) {
          if ($(this).hasClass('k-state-selected'))
            minors.push($(this).find('td:eq(1)').html());
        });
        $('#grid-academic_class-c table tbody tr').each(function( index ) {
          if ($(this).hasClass('k-state-selected'))
            minors.push($(this).find('td:eq(1)').html());
        });
        mojo.json.minors = minors.join(',');
        mojo.ajax('agent', 'academic_agency_unlock', 'mod', mojo.json);
      });
    };

    if (mojo.mojo_if('sec-agency_unlock'))
      mojo.watch_agency_unlock();

  });
})(jQuery);

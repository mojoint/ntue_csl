/* mojo javascript */
(function($) {
  $(function() {
    /* =========================================== */
    /* ------------------ common ------------------ */
    mojo.mojo =  $('body').attr('data-mojo');
    mojo.dialog = $('#dialog');
    mojo.errcode = $('body').attr('data-error');

    if (mojo.mojo != "") {
        mojo.mojoint = parseInt(mojo.mojo.substr(0,1));
        mojo.mojo = Base64.decode(mojo.mojo.substr(1, mojo.mojo.length));
        mojo.mojos = mojo.mojo.split('@@@'); 
        mojo.era_id = $('#academic_agency_class').attr('data-era_id'); 
        mojo.quarter = $('#academic_agency_class').attr('data-quarter');
        mojo.quarter_id = $('#academic_agency_class').attr('data-quarter_id');
        mojo.sec = $('section').attr('id');
    }

    //console.log( mojo.errcode );

    /* ajax */
    mojo.ajax = function(key, val, params, data) {
      data = (!data)? {} : data;
      mojo.ajaxurl = '/ajax/' + key + '/' + val + '/' + params + '/';
console.log(mojo.ajaxurl);
console.log(data);
      $.ajax({
        url: mojo.ajaxurl,
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(res) {
          if (1 == parseInt(res.code)) {
console.log(res);
            switch(key)
            {
            case 'admin':
              switch(val)
              {
              case 'academic_agency':
                switch(params)
                {
                case 'add':
                case 'mod':
                case 'del':
                  $('#grid-academic_agency').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data, page: 1, pageSize: 10 }));
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
                  mojo.ajax('refs', 'academic_agency', 'get');
                  break;
                }
                break;
              case 'academic_agency_agent':
                switch(params)
                {
                case 'add':
                case 'mod':
                    kendo.alert('已寄出密碼重設通之信，敬請提醒使用者！');
                    break;
                case 'del':
                  $('#grid-academic_agency_agent').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data, page: 1, pageSize: 10 }));
                  $('.btn-academic_agency_agent-mod').on('click', function(e) {
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
                  break;
                case 'chk':
                    if(0 < parseInt(res.data[0].cnt)) {
                        kendo.alert('使用者 ID ['+ $('#dialog-username').val()+ '] 已經有人使用，請修改！');
                        $('#dialog-username').val('');
                        $('#dialog-username').focus();
                    }
                    break;
                }
                break;
              case 'academic_agency_unlock':
                switch(params)
                {
                case 'yes':
                case 'no':
                  window.location = "/admin/unlock/";  
                  break;
                }
                break;
              case 'academic_class':
                switch(params)
                {
                case 'add':
                  break;
                case 'sel':
                  break;
                case 'mod':
                  break;
                }
                break;
              case 'academic_era':
                switch(params)
                {
                case 'add':
                case 'mod':
                  $('#grid-academic_era').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data, page: 1, pageSize: 12 }));
                  $('.btn-academic_era-mod').on('click', function(e) {
                    e.preventDefault();
                    var tr = $(e.target).closest("tr");
                    var tds = $(tr).find("td");
                    mojo.dialog_settings( 'academic_era', 'mod', {'id': $(tds[0]).html(), 'era_id': $(tds[1]).html(), 'quarter': $(tds[2]).html(), 'cname': $(tds[3]).html(), 'online': $(tds[4]).html(), 'offline': $(tds[5]).html() } );
                  });
                }
                break;
              }
              case 'profile':
                switch(params) 
                {
                case 'mod':
                    /*這邊可以異動 email 及pwd，如果資料有異動成功的話，則res.data 會等於 1
                      如果異動的資料是一樣，則res.data 會等於 0，等於沒修改！
                      modi by thucop
                    */
                    if( 0 < parseInt(res.data)){
                        kendo.alert('資料異動成功！');
                    }else{
                        kendo.alert('資料無異動！');

                    }
                    break;
                }
                break;
              break;
            case 'agent':
              switch(val)
              {
              case 'academic_agency_class':
                case 'add':
                case 'del':
                case 'done':
                case 'mod':
                  window.location = "/agent/fill/";  
                  break;
                break;
              case 'academic_agency_contact':
                switch(params)
                {
                case 'add':
                case 'del':
                case 'mod':
                  $('#grid-academic_agency_contact').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data, page: 1, pageSize: 10 }));
                  $('.btn-academic_agency_contact-del').on('click', function(e) {
                    e.preventDefault(); 
                    var tr = $(e.target).closest("tr");
                    var tds = $(tr).find("td");
                    mojo.json = {'id': $(tds[0]).html(), 'agency_id': $(tds[1]).html(), 'cname': $(tds[2]).html()};
                    mojo.dialog_info('academic_agency_contact', 'del', mojo.json);
                  }); 
                  $('.btn-academic_agency_contact-mod').on('click', function(e) {
                    e.preventDefault(); 
                    var tr = $(e.target).closest("tr");
                    var tds = $(tr).find("td");
                    mojo.json = {'id': $(tds[0]).html(), 'agency_id': $(tds[1]).html(), 'cname': $(tds[2]).html(), 'title': $(tds[3]).html(), 'manager': $(tds[4]).html(), 'staff': $(tds[5]).html(), 'role': $(tds[6]).html(), 'area_code': $(tds[7]).html(), 'phone': $(tds[8]).html(), 'ext': $(tds[9]).html(), 'email': $(tds[11]).html(), 'spare_email': $(tds[12]).html(), 'primary': $(tds[13]).html()};
                    mojo.dialog_info('academic_agency_contact', 'mod', mojo.json);
                  }); 
                  break;
                }
                break;
              case 'academic_agency_hr':
                switch(params)
                {
                case 'add':
                case 'mod':
                  $('#grid-academic_agency_hr').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data, page: 1, pageSize: 10 }));
                  $('.btn-academic_agency_hr-mod').on('click', function(e) {
                    e.preventDefault(); 
                    var tr = $(e.target).closest("tr");
                    var tds = $(tr).find("td");
                    mojo.json = {'agency_id': $(tds[0]).html(), 'era_id': $(tds[1]).html(), 'academic_era_code': $(tds[2]).html(), 'administration': $(tds[3]).html(), 'subject': $(tds[4]).html(), 'adjunct': $(tds[5]).html(), 'reserve': $(tds[6]).html(), 'others': $(tds[7]).html(), 'note': $(tds[8]).html()};                                                                                                                                                                      
                    mojo.dialog_info('academic_agency_hr', 'mod', mojo.json);
                  }); 
                  break;
                }
                break;
              case 'academic_agency_report':
                switch(params)
                {
                case 'search':
                  if (res.data.summary) {
      mojo.data.academic_agency_report_summary = res.data.summary;
      mojo.data.academic_agency_report_summary_a = [];
      mojo.data.academic_agency_report_summary_b = [];
      mojo.data.academic_agency_report_summary_c = [];

      for (var i=0; i<mojo.data.academic_agency_report_summary.length; i++) {
        switch(mojo.data.academic_agency_report_summary[i].major_code)
        {    
        case 'A': 
          mojo.data.academic_agency_report_summary_a.push(mojo.data.academic_agency_report_summary[i]);
          break;
        case 'B': 
          mojo.data.academic_agency_report_summary_b.push(mojo.data.academic_agency_report_summary[i]);
          break;
        case 'C': 
          mojo.data.academic_agency_report_summary_c.push(mojo.data.academic_agency_report_summary[i]);
          break;
        }    
      }  
      mojo.grid.academic_agency_report_summary_a.data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_report_summary_a }));
      mojo.grid.academic_agency_report_summary_b.data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_report_summary_b }));
      mojo.grid.academic_agency_report_summary_c.data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: mojo.data.academic_agency_report_summary_c }));
                  }
                  break;
                }
                break;
              case 'academic_agency_unlock':
                switch(params)
                {
                case 'mod':
                  window.location = "/agent/unlock/";  
                  break;
                }
                break;
              case 'profile':
                switch(params) 
                {
                case 'mod':
                    /*這邊可以異動 email 及pwd，如果資料有異動成功的話，則res.data 會等於 1
                      如果異動的資料是一樣，則res.data 會等於 0，等於沒修改！
                      modi by thucop
                    */
                    if( 0 < parseInt(res.data)){
                        kendo.alert('資料異動成功！');
                    }else{
                        kendo.alert('資料無異動！');
                    }
                    break;
                }
                break;
              case 'academic_agency':
                switch(params) 
                {
                case 'mod':
                    kendo.alert('資料已儲存！');
                    break;
                }
                break;
            }
            break;
            case 'profile':
                switch(val) 
                {
                case 'mod':
                    break;
                }
                break;
            case 'refs':
              switch(val)
              {
              case 'academic_institution':
                mojo.refs.academic_institution = {};
                for (var i=0; i<res.data.length; i++)
                  mojo.refs.academic_institution[res.data[i]['code']] = res.data[i];
                break;
              case 'academic_agency':
                mojo.refs.academic_agency = {};
                for (var i=0; i<res.data.length; i++)
                  mojo.refs.academic_agency[res.data[i]['id']] = res.data[i];
                break;
              case 'area_list':
                mojo.refs.area_list = {};
                for (var i=0; i<res.data.length; i++)
                  mojo.refs.area_list[res.data[i]['code']] = res.data[i]['cname'];
                break;
              }
              break;
            }
          }
        }
      });
    };

    mojo.mojo_if = function(id) {
      return $('#' + id).length;
    };

    mojo.dialog_error = function(key, val) {
      $('#dialog').kendoDialog({
        minWidth: 480,
        minHeight: 120,
        title: "錯誤提醒",
        content: '',
        model: true,
        visible: false,
        closable: true,
        actions: [
          { text: '確定', warning: true, action: function(e) { } }
        ]
      });
      mojo.html = '<div>' + val + '</div>';
      $('#dialog').data('kendoDialog').content(mojo.html).open().center();
    };

    mojo.to_json = function(wb) {
      var result = {};
      wb.SheetNames.forEach(function(sheetName) {
        var roa = XLS.utils.sheet_to_row_object_array(wb.Sheets[sheetName]);
        if (roa.length) 
          result[sheetName] = roa;
      });
      return result;
    };

    mojo.process_wb = function(wb) {
      output = JSON.stringify(mojo.to_json(wb), 2, 2);
      mojo.country = JSON.parse(output);
    };

    mojo.from_excel = function(e) {
      var files = e.target.files;
      var i, f;
      for (i = 0, f = files[i]; i != files.length; ++i) {
        var reader = new FileReader();
        var name = f.name;
        reader.onload = function(e) {
          var data = e.target.result;
          var workbook = XLSX.read(data, {
              type: 'binary'
          });

          /* DO SOMETHING WITH workbook HERE */
          mojo.process_wb(workbook);
        };
        reader.readAsBinaryString(f);
      }  
    };

    mojo.to_excel = function(grid_id) {
/*
      var uri = 'data:application/vnd.ms-excel;base64,', 
      template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><?xml version="1.0" encoding="UTF-8" standalone="yes"?><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
      base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) },
      format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    
      var table_div = document.getElementById('grid-' + grid_id);
      var table_div_a = document.getElementById('grid-' + grid_id + '-a');
      var table_div_b = document.getElementById('grid-' + grid_id + '-a');
      var table_div_c = document.getElementById('grid-' + grid_id + '-a');
      var table_html = table_div.outerHTML.replace(/ /g, '%20');
      table_html += table_div_a.outerHTML.replace(/ /g, '%20');
      table_html += table_div_b.outerHTML.replace(/ /g, '%20');
      table_html += table_div_c.outerHTML.replace(/ /g, '%20');

      $('body').append('<div id="toExcel"></div>');
      $('#toExcel').html(table_html);
      $('#toExcel').find("thead > tr > th:last-child").remove();
      $('#toExcel').find("tbody > tr > td:last-child").remove();
      var toExcel = $('#toExcel').html();
      var ctx = {
          worksheet: grid_id || '',
          table: toExcel
      };
      $('#toExcel').remove();
      window.open(uri + base64(format(template, ctx)));
*/



      var data_type = 'data:application/vnd.ms-excel';
      var table_div = document.getElementById('grid-' + grid_id);
      var table_div_a = document.getElementById('grid-' + grid_id + '-a');
      var table_div_b = document.getElementById('grid-' + grid_id + '-a');
      var table_div_c = document.getElementById('grid-' + grid_id + '-a');
      var table_html = table_div.outerHTML.replace(/ /g, '%20');
      table_html += table_div_a.outerHTML.replace(/ /g, '%20');
      table_html += table_div_b.outerHTML.replace(/ /g, '%20');
      table_html += table_div_c.outerHTML.replace(/ /g, '%20');
      var a = document.createElement('a');
      a.href = data_type + ', ' + table_html;
      a.download = 'exported_table_' + Math.floor((Math.random() * 9999999) + 1000000) + '.xls';
      a.click();

    };

    /* =========================================== */
    /* ------------------ login ------------------ */
    /* check input */
    mojo.check_login = function() {
      var pass = true;
      if (!mojo.reg.username.test($('#username').val()) || !mojo.reg.userpass.test($('#userpass').val())) {
        pass = false;
        kendo.alert('請確認您的帳號與密碼！');
      }   
      return pass;
    };

    mojo.watch_login = function() {
      $('#username').on('focus', function(e) {
        $(this).val("");
      });

      $('#userpass').on('focus', function(e) {
        $(this).val("");
      });

      $('#btn-login-agent').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_login()) {
          $('#form-login').attr('action', $(this).attr('href'));
          $('#form-login').submit();
        }
      });

      $('#btn-login-admin').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_login()) {
          $('#form-login').attr('action', $(this).attr('href'));
          $('#form-login').submit();
        }
      });

      if (mojo.errcode == 'login')
        kendo.alert('請確認您的帳號與密碼！');
    };

    if (mojo.mojo_if('sec-login'))
      mojo.watch_login();
    /* =========================================== */
    /* ----------------- activate ----------------- */
    mojo.check_activate = function() {
      var pass = true;
      if (!mojo.reg.userpass.test($('#userpass').val()) || !mojo.reg.userpass.test($('#checkpass').val()) || $('#userpass').val() != $('#checkpass').val()) {
        pass = false;
        kendo.alert('請確認您的密碼！');
      }
      return pass;
    }

    mojo.watch_activate = function() {
      $('#userpass').on('focus', function(e) {
        $(this).val("");
      });

      $('#checkpass').on('focus', function(e) {
        $(this).val("");
      });

      $('i.fa.fa-eye').on('click', function(e) {
        if ('icon-userpass' == $(this).attr('id'))
          $('#userpass').attr('type', 'text');
        else 
          $('#checkpass').attr('type', 'text');
      });

      $('#btn-activate-agent').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_activate()) {
          $('#form-activate').attr('action', $(this).attr('href'));
          $('#form-activate').submit();
        }
      });
    };

    if (mojo.mojo_if('sec-activate'))
      mojo.watch_activate();

  });
})(jQuery);

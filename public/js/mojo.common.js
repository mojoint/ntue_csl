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
              //}
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
              case 'message':
                switch(params)
                {
                case 'noReplyMsgQry':
                  $.showUnReplyQuest(res.data);
                  break;
                case 'replyMsgSave':
                  $.showReplySave(res.data.cnt);
                  break;
                }
              break;
              }
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

                    var html_a = '<tr><th colspan="11">第一研習類別</th></tr>';
                    var html_b = '<tr><th colspan="11">第二研習類別</th></tr>';
                    var html_c = '<tr><th colspan="11">第三研習類別</th></tr>';

                    var pdf_a = html_a;
                    var pdf_b = html_b;
                    var pdf_c = html_c;

                    mojo.summary = {
                      a: {
                        'new_people': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'hours': 0,
                        'total_hours': 0,
                        'turnover': 0,
                        'classes': 0,
                      },
  
                      b: {
                        'new_people': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'hours': 0,
                        'total_hours': 0,
                        'turnover': 0,
                        'classes': 0,
                      },
  
                      c: {
                        'new_people': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'hours': 0,
                        'total_hours': 0,
                        'turnover': 0,
                        'classes': 0,
                      }
  
                    }
                    for (var i=0; i<mojo.data.academic_agency_report_summary.length; i++) {
                      switch(mojo.data.academic_agency_report_summary[i].major_code)
                      {    
                      case 'A': 
                        html_a += '<tr>';
                        html_a += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        html_a += '<td class="new_people">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        html_a += '<td class="people">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        html_a += '<td class="weekly">' + mojo.data.academic_agency_report_summary[i]['weekly'] + '</td>';
                        html_a += '<td class="avg_weekly">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        html_a += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        html_a += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        html_a += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        html_a += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        html_a += '<td class="info">' + (mojo.data.academic_agency_report_summary[i]['info'] || '') + '</td>';
                        html_a += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        html_a += '</tr>';
                        
                        pdf_a += '<tr>';
                        pdf_a += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        pdf_a += '<td class="new_people">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        pdf_a += '<td class="people">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        pdf_a += '<td class="avg_weekly">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        pdf_a += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        pdf_a += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        pdf_a += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        pdf_a += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        pdf_a += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        pdf_a += '</tr>';

                        mojo.summary.a.new_people += parseInt(mojo.data.academic_agency_report_summary[i]['new_people']);
                        mojo.summary.a.people += parseInt(mojo.data.academic_agency_report_summary[i]['people']);
                        mojo.summary.a.weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['weekly']);
                        mojo.summary.a.avg_weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['avg_weekly']);
                        mojo.summary.a.hours += parseFloat(mojo.data.academic_agency_report_summary[i]['hours']);
                        mojo.summary.a.total_hours += parseFloat(mojo.data.academic_agency_report_summary[i]['total_hours']);
                        mojo.summary.a.turnover += parseInt(mojo.data.academic_agency_report_summary[i]['turnover']);
                        mojo.summary.a.classes += parseInt(mojo.data.academic_agency_report_summary[i]['classes']);
                        break;
                      case 'B': 
                        html_b += '<tr>';
                        html_b += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        html_b += '<td class="new_people">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        html_b += '<td class="people">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        html_b += '<td class="weekly">' + mojo.data.academic_agency_report_summary[i]['weekly'] + '</td>';
                        html_b += '<td class="avg_weekly">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        html_b += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        html_b += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        html_b += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        html_b += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        html_b += '<td class="info">' + (mojo.data.academic_agency_report_summary[i]['info'] || '') + '</td>';
                        html_b += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        html_b += '</tr>';

                        pdf_b += '<tr>';
                        pdf_b += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        pdf_b += '<td class="new_people">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        pdf_b += '<td class="people">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        pdf_b += '<td class="avg_weekly">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        pdf_b += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        pdf_b += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        pdf_b += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        pdf_b += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        pdf_b += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        pdf_b += '</tr>';

                        mojo.summary.b.new_people += parseInt(mojo.data.academic_agency_report_summary[i]['new_people']);
                        mojo.summary.b.people += parseInt(mojo.data.academic_agency_report_summary[i]['people']);
                        mojo.summary.b.weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['weekly']);
                        mojo.summary.b.avg_weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['avg_weekly']);
                        mojo.summary.b.hours += parseFloat(mojo.data.academic_agency_report_summary[i]['hours']);
                        mojo.summary.b.total_hours += parseFloat(mojo.data.academic_agency_report_summary[i]['total_hours']);
                        mojo.summary.b.turnover += parseInt(mojo.data.academic_agency_report_summary[i]['turnover']);
                        mojo.summary.b.classes += parseInt(mojo.data.academic_agency_report_summary[i]['classes']);
                        break;
                      case 'C': 
                        html_c += '<tr>';
                        html_c += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        html_c += '<td class="new_people_a">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        html_c += '<td class="people_a">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        html_c += '<td class="weekly_a">' + mojo.data.academic_agency_report_summary[i]['weekly'] + '</td>';
                        html_c += '<td class="avg_weekly_a">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        html_c += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        html_c += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        html_c += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        html_c += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        html_c += '<td class="info">' + (mojo.data.academic_agency_report_summary[i]['info'] || '') + '</td>';
                        html_c += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        html_c += '</tr>';

                        pdf_c += '<tr>';
                        pdf_c += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
                        pdf_c += '<td class="new_people">' + mojo.data.academic_agency_report_summary[i]['new_people'] + '</td>';
                        pdf_c += '<td class="people">' + mojo.data.academic_agency_report_summary[i]['people'] + '</td>';
                        pdf_c += '<td class="avg_weekly">' + mojo.data.academic_agency_report_summary[i]['avg_weekly'] + '</td>';
                        pdf_c += '<td class="hours">' + mojo.data.academic_agency_report_summary[i]['hours'] + '</td>';
                        pdf_c += '<td class="total_hours">' + mojo.data.academic_agency_report_summary[i]['total_hours'] + '</td>';
                        pdf_c += '<td class="turnover">' + mojo.data.academic_agency_report_summary[i]['turnover'] + '</td>';
                        pdf_c += '<td class="classes">' + mojo.data.academic_agency_report_summary[i]['classes'] + '</td>';
                        pdf_c += '<td class="note">' + (mojo.data.academic_agency_report_summary[i]['note'] || '') + '</td>';
                        pdf_c += '</tr>';

                        mojo.summary.c.new_people += parseInt(mojo.data.academic_agency_report_summary[i]['new_people']);
                        mojo.summary.c.people += parseInt(mojo.data.academic_agency_report_summary[i]['people']);
                        mojo.summary.c.weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['weekly']);
                        mojo.summary.c.avg_weekly += parseFloat(mojo.data.academic_agency_report_summary[i]['avg_weekly']);
                        mojo.summary.c.hours += parseFloat(mojo.data.academic_agency_report_summary[i]['hours']);
                        mojo.summary.c.total_hours += parseFloat(mojo.data.academic_agency_report_summary[i]['total_hours']);
                        mojo.summary.c.turnover += parseInt(mojo.data.academic_agency_report_summary[i]['turnover']);
                        mojo.summary.c.classes += parseInt(mojo.data.academic_agency_report_summary[i]['classes']);
                        break;
                      }
                    }

                    html_a += '<tr>';
                    html_a += '<th>第一研習類別小計</th>';
                    html_a += '<th>' + mojo.summary.a.new_people + '</th>';
                    html_a += '<th>' + mojo.summary.a.people + '</th>';
                    html_a += '<th>' + mojo.summary.a.weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.avg_weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.total_hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.turnover + '</th>';
                    html_a += '<th>' + mojo.summary.a.classes + '</th>';
                    html_a += '<th></th><th></th>';
                    html_a += '</tr>';

                    html_a += '<tr>';
                    pdf_a += '<th>第一研習類別小計</th>';
                    pdf_a += '<th>' + mojo.summary.a.new_people + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.people + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.weekly + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.avg_weekly + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.hours + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.total_hours + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.turnover + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.classes + '</th>';
                    pdf_a += '<th></th><th></th>';
                    pdf_a += '</tr>';

                    html_b += '<tr>';
                    html_b += '<th>第二研習類別小計</th>';
                    html_b += '<th>' + mojo.summary.b.new_people + '</th>';
                    html_b += '<th>' + mojo.summary.b.people + '</th>';
                    html_b += '<th>' + mojo.summary.b.weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.avg_weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.total_hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.turnover + '</th>';
                    html_b += '<th>' + mojo.summary.b.classes + '</th>';
                    html_b += '<th></th><th></th>';
                    html_b += '</tr>';

                    pdf_b += '<tr>';
                    pdf_b += '<th>第二研習類別小計</th>';
                    pdf_b += '<th>' + mojo.summary.b.new_people + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.people + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.weekly + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.avg_weekly + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.hours + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.total_hours + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.turnover + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.classes + '</th>';
                    pdf_b += '<th></th><th></th>';
                    pdf_b += '</tr>';

                    html_c += '<tr>';
                    html_c += '<th>第三研習類別小計</th>';
                    html_c += '<th>' + mojo.summary.c.new_people + '</th>';
                    html_c += '<th>' + mojo.summary.c.people + '</th>';
                    html_c += '<th>' + mojo.summary.c.weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.avg_weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.total_hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.turnover + '</th>';
                    html_c += '<th>' + mojo.summary.c.classes + '</th>';
                    html_c += '<th></th><th></th>';
                    html_c += '</tr>';

                    pdf_c += '<tr>';
                    pdf_c += '<th>第三研習類別小計</th>';
                    pdf_c += '<th>' + mojo.summary.c.new_people + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.people + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.weekly + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.avg_weekly + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.hours + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.total_hours + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.turnover + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.classes + '</th>';
                    pdf_c += '<th></th><th></th>';
                    pdf_c += '</tr>';

                    mojo.html += html_a;
                    mojo.html += html_b;
                    mojo.html += html_c;
                    $('#grid-academic_agency_report_summary table tbody').html(mojo.html);
                  }

                  if (res.data.detail) {
                    mojo.data.academic_agency_report_detail = res.data.detail;
                    var html_a = '<tr><th colspan="11">第一研習類別</th></tr>';
                    var html_b = '<tr><th colspan="11">第二研習類別</th></tr>';
                    var html_c = '<tr><th colspan="11">第三研習類別</th></tr>';
                    mojo.detail = {
                      a: {
                        'country': 0,
                        'new_male': 0,
                        'new_female': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'total_hours': 0,
                        'turnover': 0
                      },
      
                      b: {
                        'new_people': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'total_hours': 0,
                        'turnover': 0
                      },
      
                      c: {
                        'new_people': 0,
                        'people': 0,
                        'weekly': 0,
                        'avg_weekly': 0,
                        'total_hours': 0,
                        'turnover': 0
                      }
                    }
   
                    mojo.data.academic_agency_report_detail_a = []; 
                    mojo.data.academic_agency_report_detail_b = []; 
                    mojo.data.academic_agency_report_detail_c = []; 
  
                    for (var i=0; i<mojo.data.academic_agency_report_detail.length; i++) {
                      switch(mojo.data.academic_agency_report_detail[i].major_code)
                      {    
                      case 'A': 
                        mojo.data.academic_agency_report_detail_a.push(mojo.data.academic_agency_report_detail[i]);
                        break;
                      case 'B': 
                        mojo.data.academic_agency_report_detail_b.push(mojo.data.academic_agency_report_detail[i]);
                        break;
                       case 'C': 
                         mojo.data.academic_agency_report_detail_c.push(mojo.data.academic_agency_report_detail[i]);
                         break;
                      }
                    }
    
                    for (var i=0; i<mojo.data.academic_agency_report_detail_a.length; i++) {
                      for (var j=0, jsize=mojo.data.academic_agency_report_detail_a[i]['country'].length-1; j<mojo.data.academic_agency_report_detail_a[i]['country'].length; j++) {
                        html_a += '<tr>';
                        html_a += '<td class="minor_code_cname">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['minor_code_cname'];
                        html_a += '</td>';
                        html_a += '<td class="country_code_cname">' + mojo.data.academic_agency_report_detail_a[i]['country'][j]['country_code_cname'] + '</td>';
                        html_a += '<td class="country_new_male">' + mojo.data.academic_agency_report_detail_a[i]['country'][j]['new_male'] + '</td>';
                        html_a += '<td class="country_new_female">' + mojo.data.academic_agency_report_detail_a[i]['country'][j]['new_female'] + '</td>';
                        html_a += '<td class="new_people">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['new_people'];
                        html_a += '</td>';
                        html_a += '<td class="country_people">' + mojo.data.academic_agency_report_detail_a[i]['country'][j]['people'] + '</td>';
                        html_a += '<td class="people">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['people'];
                        html_a += '</td>';
                        html_a += '<td class="weekly">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['weekly'];
                        html_a += '</td>';
                        html_a += '<td class="avg_weekly">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['avg_weekly'];
                        html_a += '</td>';
                        html_a += '<td class="total_hours">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['total_hours'];
                        html_a += '</td>';
                        html_a += '<td class="turnover">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['turnover'];
                        html_a += '</td>';
                        html_a += '<td class="info">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['info'];
                        html_a += '</td>';
                        html_a += '<td class="note">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['note'];
                        html_a += '</td>';
                        html_a += '<td class="latest">';
                        if (j == jsize)
                          html_a += mojo.data.academic_agency_report_detail_a[i]['latest'];
                        html_a += '</td>';
                        html_a += '</tr>';
                      }
                    }
                           
                    for (var i=0; i<mojo.data.academic_agency_report_detail_b.length; i++) {
                      for (var j=0, jsize=mojo.data.academic_agency_report_detail_b[i]['country'].length-1; j<mojo.data.academic_agency_report_detail_b[i]['country'].length; j++) {
                        html_b += '<tr>';
                        html_b += '<td class="minor_code_cname">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['minor_code_cname'];
                        html_b += '</td>';
                        html_b += '<td class="country_code_cname">' + mojo.data.academic_agency_report_detail_b[i]['country'][j]['country_code_cname'] + '</td>';
                        html_b += '<td class="country_new_male">' + mojo.data.academic_agency_report_detail_b[i]['country'][j]['new_male'] + '</td>';
                        html_b += '<td class="country_new_female">' + mojo.data.academic_agency_report_detail_b[i]['country'][j]['new_female'] + '</td>';
                        html_b += '<td class="new_people">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['new_people'];
                        html_b += '</td>';
                        html_b += '<td class="country_people">' + mojo.data.academic_agency_report_detail_b[i]['country'][j]['people'] + '</td>';
                        html_b += '<td class="people">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['people'];
                        html_b += '</td>';
                        html_b += '<td class="weekly">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['weekly'];
                        html_b += '</td>';
                        html_b += '<td class="avg_weekly">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['avg_weekly'];
                        html_b += '</td>';
                        html_b += '<td class="total_hours">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['total_hours'];
                        html_b += '</td>';
                        html_b += '<td class="turnover">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['turnover'];
                        html_b += '</td>';
                        html_b += '<td class="info">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['info'];
                        html_b += '</td>';
                        html_b += '<td class="note">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['note'];
                        html_b += '</td>';
                        html_b += '<td class="latest">';
                        if (j == jsize)
                          html_b += mojo.data.academic_agency_report_detail_b[i]['latest'];
                        html_b += '</td>';
                        html_b += '</tr>';
                      }
                    }
                        
                    for (var i=0; i<mojo.data.academic_agency_report_detail_c.length; i++) {
                      for (var j=0, jsize=mojo.data.academic_agency_report_detail_c[i]['country'].length-1; j<mojo.data.academic_agency_report_detail_c[i]['country'].length; j++) {
                        html_c += '<tr>';
                        html_c += '<td class="minor_code_cname">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['minor_code_cname'];
                        html_c += '</td>';
                        html_c += '<td class="country_code_cname">' + mojo.data.academic_agency_report_detail_c[i]['country'][j]['country_code_cname'] + '</td>';
                        html_c += '<td class="country_new_male">' + mojo.data.academic_agency_report_detail_c[i]['country'][j]['new_male'] + '</td>';
                        html_c += '<td class="country_new_female">' + mojo.data.academic_agency_report_detail_c[i]['country'][j]['new_female'] + '</td>';
                        html_c += '<td class="new_people">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['new_people'];
                        html_c += '</td>';
                        html_c += '<td class="country_people">' + mojo.data.academic_agency_report_detail_c[i]['country'][j]['people'] + '</td>';
                        html_c += '<td class="people">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['people'];
                        html_c += '</td>';
                        html_c += '<td class="weekly">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['weekly'];
                        html_c += '</td>';
                        html_c += '<td class="avg_weekly">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['avg_weekly'];
                        html_c += '</td>';
                        html_c += '<td class="total_hours">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['total_hours'];
                        html_c += '</td>';
                        html_c += '<td class="turnover">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['turnover'];
                        html_c += '</td>';
                        html_c += '<td class="info">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['info'];
                        html_c += '</td>';
                        html_c += '<td class="note">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['note'];
                        html_c += '</td>';
                        html_c += '<td class="latest">';
                        if (j == jsize)
                          html_c += mojo.data.academic_agency_report_detail_c[i]['latest'];
                        html_c += '</td>';
                        html_c += '</tr>';
                      }
                    }
                           
                    mojo.html  = '';
  
                    html_a += '<th>第一研習類別小計</th>';
                    html_a += '<th>' + mojo.summary.a.new_people + '</th>';
                    html_a += '<th>' + mojo.summary.a.people + '</th>';
                    html_a += '<th>' + mojo.summary.a.weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.avg_weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.total_hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.turnover + '</th>';
                    html_a += '<th></th><th></th>';
                    html_b += '<th>第二研習類別小計</th>';
                    html_b += '<th>' + mojo.summary.b.new_people + '</th>';
                    html_b += '<th>' + mojo.summary.b.people + '</th>';
                    html_b += '<th>' + mojo.summary.b.weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.avg_weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.total_hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.turnover + '</th>';
                    html_b += '<th></th><th></th>';
                    html_c += '<th>第三研習類別小計</th>';
                    html_c += '<th>' + mojo.summary.c.new_people + '</th>';
                    html_c += '<th>' + mojo.summary.c.people + '</th>';
                    html_c += '<th>' + mojo.summary.c.weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.avg_weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.total_hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.turnover + '</th>';
                    html_c += '<th></th><th></th>';
                    mojo.html += html_a;
                    mojo.html += html_b;
                    mojo.html += html_c;
                    $('#grid-academic_agency_report_detail table tbody').html(mojo.html);
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
              case 'message':
                switch(params)
                {
                case 'histMsgQry':
                  $.showHisQuest(res.data);
                  break;
                case 'quesSave':
                  $.showQuesSave(res.data);
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
      var filename = (grid_id == 'academic_agency_report_summary')? '機構報表-課程統計簡表(四大類)' : '機構報表-課程明細詳表(含國別)';
      $('body').append('<table id="table_export"></table>');
      $('#table_export').html($('#grid-' + grid_id).html());
      mojo.export_table_to_excel('table_export', filename);
      $('#table_export').remove();
    };

     mojo.s2ab = function(s) {
       if(typeof ArrayBuffer !== 'undefined') {
         var buf = new ArrayBuffer(s.length);
         var view = new Uint8Array(buf);
         for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
         return buf;
       } else {
         var buf = new Array(s.length);
         for (var i=0; i!=s.length; ++i) buf[i] = s.charCodeAt(i) & 0xFF;
         return buf;
       }
     };
 
     mojo.export_table_to_excel = function(id, fn) {
       var wb = XLSX.utils.table_to_book(document.getElementById(id), {sheet:"Sheet JS"});
       var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});
       var fname = fn + '.xlsx';
       try {
         saveAs(new Blob([mojo.s2ab(wbout)],{type:"application/octet-stream"}), fname);
       } catch(e) { if(typeof console != 'undefined') console.log(e, wbout); }
       return wbout;
     }

    mojo.to_pdf = function(grid_id) {

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

/* mojo javascript */
(function($) {
  $(function() {
    /* =========================================== */
    /* ------------------ common ------------------ */
    mojo.mojo =  $('body').attr('data-mojo');
    mojo.dialog = $('#dialog');
    mojo.errcode = $('body').attr('data-error');
    mojo.quarters = {1: '第1季', 2: '第2季', 3: '第3季', 4: '第4季'};

    if (mojo.mojo != "") {
        mojo.mojoint = parseInt(mojo.mojo.substr(0,1));
        mojo.mojo = Base64.decode(mojo.mojo.substr(1, mojo.mojo.length));
        mojo.mojos = mojo.mojo.split('@@@'); 
        mojo.era_id = $('#academic_agency_class').attr('data-era_id'); 
        mojo.quarter = $('#academic_agency_class').attr('data-quarter');
        mojo.quarter_id = $('#academic_agency_class').attr('data-quarter_id');
        mojo.sec = $('section').attr('id');
    }

    /* ajax */
    mojo.ajax = function(key, val, params, data) {
      data = (!data)? {} : data;
      mojo.ajaxurl = '/ajax/' + key + '/' + val + '/' + params + '/';
console.log( mojo.ajaxurl );
console.log( data );
      $.ajax({
        url: mojo.ajaxurl,
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(res) {
          if (1 == parseInt(res.code)) {
console.log( res );
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
                  $('#grid-academic_agency').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
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
                  $('#grid-academic_agency_agent').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
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
              case 'academic_agency_status':
                switch(params)
                {
                case 'list':
                  $('#grid-academic_agency_status').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
                  break;
                case 'agency':
                  console.log( res.data );
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
                  window.location = "/admin/settings/";  
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
                  $('#grid-academic_era').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
                  $('.btn-academic_era-mod').on('click', function(e) {
                    e.preventDefault();
                    var tr = $(e.target).closest("tr");
                    var tds = $(tr).find("td");
                    mojo.dialog_settings( 'academic_era', 'mod', {'id': $(tds[0]).html(), 'era_id': $(tds[1]).html(), 'quarter': $(tds[2]).html(), 'cname': $(tds[3]).html(), 'online': $(tds[4]).html(), 'offline': $(tds[5]).html() } );
                  });
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
              case 'postman':
                switch(params)
                {
                case 'emailSend':
                  $.postmanSend(res.data);
                  break;
                }
              break;
              }
            case 'agent':
              switch(val)
              {
              case 'academic_agency_class':
                switch(params)
                {
                case 'add':
                case 'del':
                case 'done':
                case 'mod':
                  window.location = "/agent/fill/";  
                  break;
                case 'import':
                  //window.location = "/agent/fillmod/" + res.data;  
                  break;
                }
                break;
              case 'academic_agency_contact':
                switch(params)
                {
                case 'add':
                case 'del':
                case 'mod':
                  $('#grid-academic_agency_contact').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
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
                  $('#grid-academic_agency_hr').data('kendoGrid').setDataSource(new kendo.data.DataSource({ data: res.data }));
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

                    var html_a = '<tr class="active"><th  colspan="11">第一研習類別</th></tr>';
                    var html_b = '<tr class="active"><th  colspan="11">第二研習類別</th></tr>';
                    var html_c = '<tr class="active"><th  colspan="11">第三研習類別</th></tr>';

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
                        html_a += '<td class="minor_code_cname">' + mojo.quarters[ mojo.data.academic_agency_report_summary[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
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
                        html_b += '<td class="minor_code_cname">' + mojo.quarters[ mojo.data.academic_agency_report_summary[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
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
                        html_c += '<td class="minor_code_cname">' + mojo.quarters[ mojo.data.academic_agency_report_summary[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_summary[i]['minor_code_cname'] + '</td>';
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

                    html_a += '<tr class="active">';
                    html_a += '<th >第一研習類別小計</th>';
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

                    html_b += '<tr class="active">';
                    html_b += '<th >第二研習類別小計</th>';
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

                    html_c += '<tr class="active">';
                    html_c += '<th >第三研習類別小計</th>';
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

                    mojo.html  = html_a;
                    mojo.html += html_b;
                    mojo.html += html_c;
                    $('#grid-academic_agency_report_summary table tbody').html(mojo.html);
                  }

                  if (res.data.detail) {
                    mojo.data.academic_agency_report_detail = res.data.detail;
                    var html_a = '<tr class="active"><th  colspan="14">第一研習類別</th></tr>';
                    var html_b = '<tr class="active"><th  colspan="14">第二研習類別</th></tr>';
                    var html_c = '<tr class="active"><th  colspan="14">第三研習類別</th></tr>';
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
console.log( mojo.data.academic_agency_report_detail_a );    
console.log( mojo.data.academic_agency_report_detail_b );    
console.log( mojo.data.academic_agency_report_detail_c );    
                    for (var i=0; i<mojo.data.academic_agency_report_detail_a.length; i++) {
                      for (var j=0, jsize=mojo.data.academic_agency_report_detail_a[i]['country'].length-1; j<mojo.data.academic_agency_report_detail_a[i]['country'].length; j++) {
                        html_a += '<tr>';
                        html_a += '<td class="minor_code_cname">';
                        if (j == jsize)
                          html_a += mojo.quarters[ mojo.data.academic_agency_report_detail[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_detail[i]['minor_code_cname'];
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
console.log ( mojo.data.academic_agency_report_detail_b[i] );
                      for (var j=0, jsize=mojo.data.academic_agency_report_detail_b[i]['country'].length-1; j<mojo.data.academic_agency_report_detail_b[i]['country'].length; j++) {
                        html_b += '<tr>';
                        html_b += '<td class="minor_code_cname">';
                        if (j == jsize)
                          html_b += mojo.quarters[ mojo.data.academic_agency_report_detail[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_detail[i]['minor_code_cname'];
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
                          html_c += mojo.quarters[ mojo.data.academic_agency_report_detail[i]['quarter'] ] + ':' + mojo.data.academic_agency_report_detail[i]['minor_code_cname'];
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
                           
                    html_a += '<tr class="active">';
                    html_a += '<th >第一研習類別小計</th>';
                    html_a += '<th>' + mojo.summary.a.new_people + '</th>';
                    html_a += '<th>' + mojo.summary.a.people + '</th>';
                    html_a += '<th>' + mojo.summary.a.weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.avg_weekly + '</th>';
                    html_a += '<th>' + mojo.summary.a.hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.total_hours + '</th>';
                    html_a += '<th>' + mojo.summary.a.turnover + '</th>';
                    html_a += '<th></th>';
                    html_a += '<th></th>';
                    html_a += '<th></th>';
                    html_a += '<th></th>';
                    html_a += '<th></th>';
                    html_a += '<th></th>';
                    html_a += '</tr>';
                    html_b += '<tr class="active">';
                    html_b += '<th >第二研習類別小計</th>';
                    html_b += '<th>' + mojo.summary.b.new_people + '</th>';
                    html_b += '<th>' + mojo.summary.b.people + '</th>';
                    html_b += '<th>' + mojo.summary.b.weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.avg_weekly + '</th>';
                    html_b += '<th>' + mojo.summary.b.hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.total_hours + '</th>';
                    html_b += '<th>' + mojo.summary.b.turnover + '</th>';
                    html_b += '<th></th>';
                    html_b += '<th></th>';
                    html_b += '<th></th>';
                    html_b += '<th></th>';
                    html_b += '<th></th>';
                    html_b += '<th></th>';
                    html_b += '</tr>';
                    html_c += '<tr class="active">';
                    html_c += '<th >第三研習類別小計</th>';
                    html_c += '<th>' + mojo.summary.c.new_people + '</th>';
                    html_c += '<th>' + mojo.summary.c.people + '</th>';
                    html_c += '<th>' + mojo.summary.c.weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.avg_weekly + '</th>';
                    html_c += '<th>' + mojo.summary.c.hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.total_hours + '</th>';
                    html_c += '<th>' + mojo.summary.c.turnover + '</th>';
                    html_c += '<th></th>';
                    html_c += '<th></th>';
                    html_c += '<th></th>';
                    html_c += '<th></th>';
                    html_c += '<th></th>';
                    html_c += '<th></th>';
                    html_c += '</tr>';

                    mojo.html  = html_a;
                    mojo.html += html_b;
                    mojo.html += html_c;
                    $('#grid-academic_agency_report_detail table tbody').html(mojo.html);
                  }

                  if (res.data.pdf) {
                    mojo.data.academic_agency_report_pdf = res.data.pdf;

                    var pdf_a = '<tr class="active"><th  colspan="8">第一研習類別</th></tr>';
                    var pdf_b = '<tr class="active"><th  colspan="8">第二研習類別</th></tr>';
                    var pdf_c = '<tr class="active"><th  colspan="8">第三研習類別</th></tr>';

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

                    for (var i=0; i<mojo.data.academic_agency_report_pdf.length; i++) {
                      switch(mojo.data.academic_agency_report_pdf[i].major_code)
                      {    
                      case 'A': 
                        pdf_a += '<tr>';
                        pdf_a += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_pdf[i]['minor_code_cname'] + '</td>';
                        pdf_a += '<td class="new_people">' + mojo.data.academic_agency_report_pdf[i]['new_people'] + '</td>';
                        pdf_a += '<td class="people">' + mojo.data.academic_agency_report_pdf[i]['people'] + '</td>';
                        pdf_a += '<td class="avg_weekly">' + mojo.data.academic_agency_report_pdf[i]['avg_weekly'] + '</td>';
                        pdf_a += '<td class="hours">' + mojo.data.academic_agency_report_pdf[i]['hours'] + '</td>';
                        pdf_a += '<td class="total_hours">' + mojo.data.academic_agency_report_pdf[i]['total_hours'] + '</td>';
                        pdf_a += '<td class="turnover">' + mojo.data.academic_agency_report_pdf[i]['turnover'] + '</td>';
                        pdf_a += '<td class="classes">' + mojo.data.academic_agency_report_pdf[i]['classes'] + '</td>';
                        pdf_a += '</tr>';

                        mojo.summary.a.new_people += parseInt(mojo.data.academic_agency_report_pdf[i]['new_people']);
                        mojo.summary.a.people += parseInt(mojo.data.academic_agency_report_pdf[i]['people']);
                        mojo.summary.a.weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['weekly']);
                        mojo.summary.a.avg_weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['avg_weekly']);
                        mojo.summary.a.hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['hours']);
                        mojo.summary.a.total_hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['total_hours']);
                        mojo.summary.a.turnover += parseInt(mojo.data.academic_agency_report_pdf[i]['turnover']);
                        mojo.summary.a.classes += parseInt(mojo.data.academic_agency_report_pdf[i]['classes']);
                        break;
                      case 'B': 
                        pdf_b += '<tr>';
                        pdf_b += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_pdf[i]['minor_code_cname'] + '</td>';
                        pdf_b += '<td class="new_people">' + mojo.data.academic_agency_report_pdf[i]['new_people'] + '</td>';
                        pdf_b += '<td class="people">' + mojo.data.academic_agency_report_pdf[i]['people'] + '</td>';
                        pdf_b += '<td class="avg_weekly">' + mojo.data.academic_agency_report_pdf[i]['avg_weekly'] + '</td>';
                        pdf_b += '<td class="hours">' + mojo.data.academic_agency_report_pdf[i]['hours'] + '</td>';
                        pdf_b += '<td class="total_hours">' + mojo.data.academic_agency_report_pdf[i]['total_hours'] + '</td>';
                        pdf_b += '<td class="turnover">' + mojo.data.academic_agency_report_pdf[i]['turnover'] + '</td>';
                        pdf_b += '<td class="classes">' + mojo.data.academic_agency_report_pdf[i]['classes'] + '</td>';
                        pdf_b += '</tr>';

                        mojo.summary.b.new_people += parseInt(mojo.data.academic_agency_report_pdf[i]['new_people']);
                        mojo.summary.b.people += parseInt(mojo.data.academic_agency_report_pdf[i]['people']);
                        mojo.summary.b.weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['weekly']);
                        mojo.summary.b.avg_weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['avg_weekly']);
                        mojo.summary.b.hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['hours']);
                        mojo.summary.b.total_hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['total_hours']);
                        mojo.summary.b.turnover += parseInt(mojo.data.academic_agency_report_pdf[i]['turnover']);
                        mojo.summary.b.classes += parseInt(mojo.data.academic_agency_report_pdf[i]['classes']);
                        break;
                      case 'C': 
                        pdf_c += '<tr>';
                        pdf_c += '<td class="minor_code_cname">' + mojo.data.academic_agency_report_pdf[i]['minor_code_cname'] + '</td>';
                        pdf_c += '<td class="new_people">' + mojo.data.academic_agency_report_pdf[i]['new_people'] + '</td>';
                        pdf_c += '<td class="people">' + mojo.data.academic_agency_report_pdf[i]['people'] + '</td>';
                        pdf_c += '<td class="avg_weekly">' + mojo.data.academic_agency_report_pdf[i]['avg_weekly'] + '</td>';
                        pdf_c += '<td class="hours">' + mojo.data.academic_agency_report_pdf[i]['hours'] + '</td>';
                        pdf_c += '<td class="total_hours">' + mojo.data.academic_agency_report_pdf[i]['total_hours'] + '</td>';
                        pdf_c += '<td class="turnover">' + mojo.data.academic_agency_report_pdf[i]['turnover'] + '</td>';
                        pdf_c += '<td class="classes">' + mojo.data.academic_agency_report_pdf[i]['classes'] + '</td>';
                        pdf_c += '</tr>';

                        mojo.summary.c.new_people += parseInt(mojo.data.academic_agency_report_pdf[i]['new_people']);
                        mojo.summary.c.people += parseInt(mojo.data.academic_agency_report_pdf[i]['people']);
                        mojo.summary.c.weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['weekly']);
                        mojo.summary.c.avg_weekly += parseFloat(mojo.data.academic_agency_report_pdf[i]['avg_weekly']);
                        mojo.summary.c.hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['hours']);
                        mojo.summary.c.total_hours += parseFloat(mojo.data.academic_agency_report_pdf[i]['total_hours']);
                        mojo.summary.c.turnover += parseInt(mojo.data.academic_agency_report_pdf[i]['turnover']);
                        mojo.summary.c.classes += parseInt(mojo.data.academic_agency_report_pdf[i]['classes']);
                        break;
                      }
                    }

                    pdf_a += '<tr class="active">';
                    pdf_a += '<th>第一研習類別小計</th>';
                    pdf_a += '<th>' + mojo.summary.a.new_people + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.people + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.avg_weekly.toFixed(2) + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.hours.toFixed(2) + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.total_hours.toFixed(2) + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.turnover + '</th>';
                    pdf_a += '<th>' + mojo.summary.a.classes + '</th>';
                    pdf_a += '</tr>';

                    pdf_b += '<tr class="active">';
                    pdf_b += '<th>第二研習類別小計</th>';
                    pdf_b += '<th>' + mojo.summary.b.new_people + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.people + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.avg_weekly.toFixed(2) + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.hours.toFixed(2) + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.total_hours.toFixed(2) + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.turnover + '</th>';
                    pdf_b += '<th>' + mojo.summary.b.classes + '</th>';
                    pdf_b += '</tr>';

                    pdf_c += '<th>第三研習類別小計</th>';
                    pdf_c += '<th>' + mojo.summary.c.new_people + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.people + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.avg_weekly.toFixed(2) + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.hours.toFixed(2) + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.total_hours.toFixed(2) + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.turnover + '</th>';
                    pdf_c += '<th>' + mojo.summary.c.classes + '</th>';
                    pdf_c += '</tr>';

                    mojo.html  = pdf_a;
                    mojo.html += pdf_b;
                    mojo.html += pdf_c;
                    $('#grid-academic_agency_report_pdf table tbody').html(mojo.html);
                  }

                  if (res.data.taken) {
                    mojo.data.academic_agency_report_taken = res.data.taken;
                    if (res.data.taken[0]['taken']) {
                      mojo.html = '<div class="row">';
                      var html_a = '<div class="col-xs-4"><table class="major_a">';
                      var html_b = '<div class="col-xs-4"><table class="major_b">';
                      var html_c = '<div class="col-xs-4"><table class="major_c">';
                      html_a += '<thead><tr><th>第一類</th></tr></thead><tbody>';
                      html_b += '<thead><tr><th>第二類</th></tr></thead><tbody>';
                      html_c += '<thead><tr><th>第三類</th></tr></thead><tbody>';
                    
                      for (var i=0; i<mojo.data.academic_agency_report_taken.length; i++) {
                        switch(mojo.data.academic_agency_report_taken[i]['major_code'])
                        {
                        case 'A':
                          html_a += '<tr><td>' + mojo.data.academic_agency_report_taken[i]['cname'] + '</td></tr>';
                          break;
                        case 'B':
                          html_b += '<tr><td>' + mojo.data.academic_agency_report_taken[i]['cname'] + '</td></tr>';
                          break;
                        case 'C':
                          html_c += '<tr><td>' + mojo.data.academic_agency_report_taken[i]['cname'] + '</td></tr>';
                          break;
                        }
                      }

                      html_a += '</tbody></table></div>';
                      html_b += '</tbody></table></div>';
                      html_c += '</tbody></table></div>';
                      mojo.html += html_a + html_b + html_c;
                    } else {
                      mojo.html = '<h2>未定案</h2>';
                    }
                    $('#grid-academic_agency_report_taken').html(mojo.html);
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
                    //kendo.alert('資料已儲存！');
                    window.location = "/agent/info/";
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

    /*
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
    };
    */

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
        $('#form-login').attr('action', "");
        if (mojo.check_login()) {
          $('#form-login').attr('action', $(this).attr('href'));
          grecaptcha.execute();
        }
      });

      $('#btn-login-admin').on('click', function(e) {
        e.preventDefault();
        if (mojo.check_login()) {
          $('#form-login').attr('action', $(this).attr('href'));
          $('#form-login').submit();
        }
      });

      if (mojo.errcode == 'error_login')
        kendo.alert('請確認您的帳號與密碼！');
      else if (mojo.errcode == 'error_activate')
        kendo.alert('請確認您的帳號與密碼！');
      else if (mojo.errcode == 'activated')
        kendo.alert('啟用完成，請用設定的帳號密碼登入!');

      
      $('body').attr('data-error', "");

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

    /* manual download */
    $('#btn-agent-manual').on('click', function(e) {
      e.preventDefault();
      window.open('/ajax/downloader/pdf/user_manual/agent', '_blank');
    });

    $('#btn-admin-manual').on('click', function(e) {
      e.preventDefault();
      window.open('/ajax/downloader/pdf/admin_manual/admin', '_blank');
    });

    /* qa download */
    $('#btn-agent-help').on('click', function(e) {
      e.preventDefault();
      window.open('/ajax/downloader/pdf/qa/agent', '_blank');
    });

    /* qa download */
    $('#btn-admin-help').on('click', function(e) {
      e.preventDefault();
      window.open('/ajax/downloader/pdf/qa/admin', '_blank');
    });
    /* =========================================== */
    /* ------------------- admin ------------------- */
    /* status */

    mojo.watch_status = function() {
      $('#btn-academic_agency_status-search').on('click', function(e) {
        e.preventDefault();
        mojo.json = {'era_id': $('#select-academic_agency_status-era').val(), 'quarter': $('#select-academic_agency_status-quarter').val()};
        mojo.ajax('admin', 'academic_agency_status', 'list', mojo.json);
      });
    };

    if (mojo.mojo_if('sec-status'))
      mojo.watch_status();

    /* maintain */
    mojo.check_maintain = function() {
      var pass = true;
      mojo.errmsg = '';
      if (!mojo.reg.email.test($('#editor-email').val())) {
        mojo.errmsg += '<p>請輸入電子信箱</p>';
        pass = false;
      }
      return pass;
    };

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
                if (mojo.check_maintain('email', $('#dialog-email').val())) { 
                  mojo.json = { 'id': 0, 'username': $('#dialog-username').val(), 'agency_id': $('#dialog-agency_id').val(), 'email': $('#dialog-email').val() };
                  mojo.ajax('admin', key, val, mojo.json);
                } else 
                  mojo.dialog_error('academic_agency_agent', mojo.errmsg);
                break;
              case 'del':
                mojo.json = params;
                mojo.ajax('admin', key, val, mojo.json);
                break;
              case 'mod':
                if (mojo.check_maintain('email', $('#dialog-email').val())) { 
                  mojo.json = { 'id': params.id, 'username': params.username, 'agency_id': params.agency_id, 'email': $('#dialog-email').val() };
                  mojo.ajax('admin', key, val, mojo.json);
                } else 
                  mojo.dialog_error('academic_agency_agent', mojo.errmsg);
                break;
              }
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-agency_id">機構名稱</label><select id="dialog-agency_id" class="form-control"></select></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-username">使用者ID</label><input type="text" id="dialog-username" class="form-control" /></div>';
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email"><b style="color:red">*</b>電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
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
          mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email"><b style="color:red">*</b>電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
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
        mojo.dialog_admin_unlock('academic_agency_unlock', 'yes', {'agency_id': $(tds[0]).html(), 'id': $(tds[1]).html(), 'cname': $(tds[3]).html(), 'work_days': $(tds[8]).html()});
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
          mojo.html  = '<div><label for="dialog-institution_code">新增下一年度?</label></div>';
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
                mojo.json = { 'major': $('#dialog-major').val(), 'minor': $('#dialog-minor').val(), 'cname': $('#dialog-cname').val(), 'era_id': $('#select-academic_class-era').val() };
                mojo.ajax('admin', key, val, mojo.json);
                break;
              }
            }},
            { text: '取消'}
          ],
        });

        switch(val)
        {
        case 'add':
          mojo.html  = '<div><label for="dialog-major">研習類別</label><select id="dialog-major"><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></div>';
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
        var params = {checks:[], picks:{}, taken: $('#select-academic_class-taken').val(), era_id: $('#select-academic_class-era').val()};
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
      
      var minors = {};
      for (var i=0; i<mojo.data.academic_class_data.length; i++)
        minors[mojo.data.academic_class_data[i].id] = mojo.data.academic_class_data[i].state;
      $('#grid-academic_class-a table tbody tr').each(function(e) {
        if (minors[$(this).find('td:eq(1)').html()]) {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });
      $('#grid-academic_class-b table tbody tr').each(function(e) {
        if (minors[$(this).find('td:eq(1)').html()]) {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });
      $('#grid-academic_class-c table tbody tr').each(function(e) {
        if (minors[$(this).find('td:eq(1)').html()]) {
          $(this).addClass('k-state-selected');
          $(this).find('td:eq(0) input:checkbox').prop('checked', true);
        }
      });

      $('#select-academic_class-era').on('change', function() {
        mojo.ajax('admin', 'academic_class', 'era');
      });
    };

    if (mojo.mojo_if('sec-settings'))
      mojo.watch_settings(); 

    mojo.watch_admin_report = function() {
      $('#btn-academic_admin_report_era_detail-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/era_detail/' + $('#academic_admin_report_era_detail-era').val());
      });

      $('#btn-academic_admin_report_era_summary-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/era_summary/' + $('#academic_admin_report_era_summary-era').val());
      });

      $('#btn-academic_admin_report_quarter_detail-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/quarter_detail/' + $('#academic_admin_report_quarter_detail-era').val() + '/' + $('#academic_admin_report_quarter_detail-quarter').val());
      });

      $('#btn-academic_admin_report_quarter_summary-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/quarter_summary/' + $('#academic_admin_report_quarter_summary-era').val() + '/' + $('#academic_admin_report_quarter_summary-quarter').val());
      });

      $('#btn-academic_admin_report_manager-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/manager/' + $('#academic_admin_report_manager-era').val());
      });

      $('#btn-academic_admin_report_statistics-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/statistics/' + $('#academic_admin_report_statistics-era').val());
      });

      $('#btn-academic_admin_report_major_b-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_admin_report/major_b/' + $('#academic_admin_report_major_b-era').val());
      });

    };

    if (mojo.mojo_if('sec-admin_report'))
      mojo.watch_admin_report();

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
      mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-email">信箱(需要修改才需填入)</label><input type="text" id="dialog-email" placeholder="規則: email " /></div>';
      mojo.html += '<div class="k-textbox k-textbox-full k-space-right"><label for="dialog-userpass">密碼(需要修改才需填入)</label><input type="text" id="dialog-userpass" placeholder="規則: 長度3~80 的 大小寫英數字或`~!@#$%^&*-_=+" /></div>';
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
            { text: '確定', action: function(e) {
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
            { text: '取消', primary: true}
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
          { field: "new_people", title: "總人數", width: "80px" },
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
                new_people: { type: "number" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_people", aggregate: "sum" },
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
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-a" } },
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
                new_people: { type: "number" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },   
          aggregate: [
            { field: "new_people", aggregate: "sum" },
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
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-b" } },
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
                new_people: { type: "number" },
                people: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" }
              }    
            }    
          },   
          aggregate: [
            { field: "new_people", aggregate: "sum" },
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
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-c" } },
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
          { field: "new_people", title: "總人數", width: "80px", footerAttributes: { "class": "summary-new_people" } },
          { field: "people", title: "總人次", width: "80px", footerAttributes: { "class": "summary-people" } },
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
      switch( key )
      {
      case 'academic_agency_class':
        $('#dialog-academic_agency_class').kendoDialog({
          width: 480,
          minHeight: 120,
          title: "課程資料",
          content: '',
          model: true,
          visible: false,
          closable: true,
          actions: [
            { text: '確定', primary: true, action: function(e) {
              switch(val) 
              {
              case 'import':
                mojo.json = {'agency_id': mojo.mojos[2], 'id': $('#editor-academic_agency_class_import').val()};
                mojo.ajax('agent', key, val, mojo.json);
                break;
              }
            }},
            { text: '取消'}
          ]
        });
        break;
      case 'academic_agency_class_country':
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
                var male = (parseInt($('#dialog-male').val()) || 0);
                var female = (parseInt($('#dialog-female').val()) || 0);
                var new_male = (parseInt($('#dialog-new_male').val()) || 0);
                var new_female = (parseInt($('#dialog-new_female').val()) || 0);
                var people = male + female + new_male + new_female;
              
                var html = '<tr role="row"><td style="display:none" role="gridcell">' + $('#dialog-country_code').val() + '</td><td role="girdcell">' + mojo.refs.country_list[$('#dialog-country_code').val()]['cname'] + '</td><td class="country_male" role="gridcell">' + male + '</td><td class="country_female" role="gridcell">' + female + '</td><td class="country_new_male" role="gridcell">' + new_male + '</td><td class="country_new_female" role="gridcell">' + new_female + '</td><td role="gridcell">' + people + '</td><td role="gridcell">' + $('#dialog-note').val() + '</td><td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td></tr>';
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
                var errmsg = '';
                for (var x in mojo.country ) {
                  for (var i=0; i<mojo.country[x].length; i++) {
                    if (typeof(mojo.country[x][i]) === 'object') {
                      var country = mojo.country[x][i]['國別'];
                      var male = mojo.country[x][i]['男舊生'];
                      var female = mojo.country[x][i]['女舊生'];
                      var new_male = mojo.country[x][i]['男新生'];
                      var new_female = mojo.country[x][i]['女新生'];
                      var note = (mojo.country[x][i]['其他'])? mojo.country[x][i]['其他'] : "";
                      var people = male + female + new_male + new_female;
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
                        html +=   '<td role="gridcell" class="country_people">' + people + '</td>';
                        html +=   '<td role="girdcell" >' + note + '</td>';
                        html +=   '<td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td>';
                        html += '</tr>';
                        $('#grid-academic_agency_class_country .k-grid-content table tbody').append(html);
                      } else {
                          errmsg += country + ' ';
                      }
                    }
                  }
                  if (errmsg.length) {
                    kendo.alert( errmsg + ' 系統無此國別(地區)');
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
                var male = (parseInt($('#dialog-male').val()) || 0);
                var female = (parseInt($('#dialog-female').val()) || 0);
                var new_male = (parseInt($('#dialog-new_male').val()) || 0);
                var new_female = (parseInt($('#dialog-new_female').val()) || 0);
                var people = male + female + new_male + new_female;
                var tds = $(params.tr).find("td");
                $(tds[0]).html($('#dialog-country_code').val());
                $(tds[1]).html(mojo.refs.country_list[$('#dialog-country_code').val()]['cname']);
                $(tds[2]).html( male );
                $(tds[3]).html( female );
                $(tds[4]).html( new_male );
                $(tds[5]).html( new_female );
                $(tds[6]).html( people );
                $(tds[7]).html($('#dialog-note').val());
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
        break;
      }

      switch(key)
      {
      case 'academic_agency_class':
        switch(val)
        {
        case 'import':
          mojo.html = '<div>上季課程: ';
          mojo.html += '<select id="editor-academic_agency_class_import">';
          for (var i=0; i<mojo.data.academic_agency_class_last.length; i++)
            mojo.html += '<option value="' + mojo.data.academic_agency_class_last[i]['id'] + '">' + mojo.data.academic_agency_class_last[i]['minor_cname'] + ' - ' + mojo.data.academic_agency_class_last[i]['cname'] + ' - ' + mojo.data.academic_agency_class_last[i]['people'] + ' - ' + mojo.data.academic_agency_class_last[i]['turnover'] + '</option>';
          mojo.html += '</select>';
          mojo.html += '<div><label>自上季匯入將會清除現有的課程資料</label></div>';
          mojo.html += '</div>';
          $('#dialog-academic_agency_class').data('kendoDialog').content(mojo.html).open().center();
          break;
        }
        break;
      case 'academic_agency_class_country':
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
        break;
      }

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
          var male = parseInt($(tds[2]).html()) || 0;
          var female = parseInt($(tds[3]).html()) || 0;
          var new_male = parseInt($(tds[4]).html()) || 0;
          var new_female = parseInt($(tds[5]).html()) || 0;
          var people = male + female + new_male + new_female;
          mojo.summary.male += male;
          mojo.summary.female += female;
          mojo.summary.new_male += new_male;
          mojo.summary.new_female += new_female;
          $(tds[6]).html(people);
        });
        mojo.summary.new_people = mojo.summary.new_male + mojo.summary.new_female;
        mojo.summary.people = mojo.summary.male + mojo.summary.female + mojo.summary.new_male + mojo.summary.new_female;
        
        $('.summary-country_male').html(mojo.summary.male);
        $('.summary-country_female').html(mojo.summary.female);
        $('.summary-country_new_male').html(mojo.summary.new_male);
        $('.summary-country_new_female').html(mojo.summary.new_female);
        $('.summary-country_people').html(mojo.summary.people);
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
            country.push({'country_code': $(tds[0]).html(), 'male': male, 'female': female, 'new_male': new_male, 'new_female': new_female, 'note': Base64.encode($(tds[7]).html())});
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
          { title: "人次", attributes: { "class": "country_people" }, footerAttributes: { "class": "summary-country_people" }, width: "90px" },
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

      $('#btn-academic_agency_class-import').on('click', function(e) {
        e.preventDefault();
        mojo.dialog_filladd('academic_agency_class', 'import', {});
      });

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
              var male = (parseInt($('#dialog-male').val()) || 0);
              var female = (parseInt($('#dialog-female').val()) || 0);
              var new_male = (parseInt($('#dialog-new_male').val()) || 0);
              var new_female = (parseInt($('#dialog-new_female').val()) || 0);
              var people = male + female + new_male + new_female;
              var html = '<tr role="row"><td style="display:none" role="gridcell">' + $('#dialog-country_code').val() + '</td><td role="girdcell">' + mojo.refs.country_list[$('#dialog-country_code').val()]['cname'] + '</td><td class="country_male" role="gridcell">' + male + '</td><td class="country_female" role="gridcell">' + female + '</td><td class="country_new_male" role="gridcell">' + new_male + '</td><td class="country_new_female" role="gridcell">' + new_female + '</td><td role="gridcell">' + people + '</td><td role="gridcell">' + $('#dialog-note').val() + '</td><td role="gridcell"><a class="k-button k-blank k-grid-edit btn-academic_agency_class_country-mod" title="修改"><i class="fa fa-edit"></i></a><a class="k-button k-blank k-grid-delete btn-academic_agency_class_country-del" title="刪除"><i class="fa fa-trash"></i></a></td></tr>';
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
                    html +=   '<td role="girdcell" >' + people + '</td>';
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
              var male = (parseInt($('#dialog-male').val()) || 0);
              var female = (parseInt($('#dialog-female').val()) || 0);
              var new_male = (parseInt($('#dialog-new_male').val()) || 0);
              var new_female = (parseInt($('#dialog-new_female').val()) || 0);
              var people = male + female + new_male + new_female;
              var tds = $(params.tr).find("td");
              $(tds[0]).html($('#dialog-country_code').val());
              $(tds[1]).html(mojo.refs.country_list[$('#dialog-country_code').val()]['cname']);
              $(tds[2]).html( male );
              $(tds[3]).html( female );
              $(tds[4]).html( new_male );
              $(tds[5]).html( new_female );
              $(tds[6]).html( people );
              $(tds[7]).html($('#dialog-note').val());
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
          var male = parseInt($(tds[2]).html()) || 0;
          var female = parseInt($(tds[3]).html()) || 0;
          var new_male = parseInt($(tds[4]).html()) || 0;
          var new_female = parseInt($(tds[5]).html()) || 0;
          var people = male + female + new_male + new_female;
          mojo.summary.male += male;
          mojo.summary.female += female;
          mojo.summary.new_male += new_male;
          mojo.summary.new_female += new_female;
          $(tds[6]).html( people );
        });
        
        mojo.summary.new_people = mojo.summary.new_male + mojo.summary.new_female;
        mojo.summary.people = mojo.summary.male + mojo.summary.female + mojo.summary.new_male + mojo.summary.new_female;
        $('.summary-country_male').html(mojo.summary.male);
        $('.summary-country_female').html(mojo.summary.female);
        $('.summary-country_new_male').html(mojo.summary.new_male);
        $('.summary-country_new_female').html(mojo.summary.new_female);
        $('.summary-country_people').html(mojo.summary.people);
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
            country.push({'country_code': $(tds[0]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'note': Base64.encode($(tds[7]).html())});
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
          { field: "people", title: "人次", attributes: { "class": "country_people" }, footerAttributes: { "class": "summary-country_people" }, width: "90px" },
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
      for (var i=0; i<mojo.data.academic_agency_class_country.length; i++) 
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
        mojo.json = {'country_code': $(tds[0]).html(), 'cname': $(tds[1]).html(), 'male': $(tds[2]).html(), 'female': $(tds[3]).html(), 'new_male': $(tds[4]).html(), 'new_female': $(tds[5]).html(), 'note': $(tds[7]).html(), 'tr': tr};
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
    mojo.check_contact = function() {
      var pass = true;
      mojo.errmsg = '';
      if (!mojo.reg.phone.test($('#dialog-phone').val()) && !mojo.reg.tel.test($('#dialog-phone').val())) {
        mojo.errmsg += '<p>請填寫聯絡電話</p>';
        pass = false;
      }

      if (!mojo.reg.email.test($('#dialog-email').val())) {
        mojo.errmsg += '<p>請填寫電子郵件信箱</p>';
        pass = false;
      }

      return pass;
    }

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
              var administration = mojo.reg.float32.test($('#dialog-administration').val())? $('#dialog-administration').val() : 0;
              var subject = mojo.reg.float32.test($('#dialog-subject').val())? $('#dialog-subject').val() : 0;
              var adjunct = mojo.reg.float32.test($('#dialog-adjunct').val())? $('#dialog-adjunct').val() : 0;
              var reserve = mojo.reg.float32.test($('#dialog-reserve').val())? $('#dialog-reserve').val() : 0;
              var others = mojo.reg.float32.test($('#dialog-others').val())? $('#dialog-others').val() : 0;
              switch(val) 
              {   
              case 'add':
                mojo.json = {'agency_id': mojo.mojos[2], 'administration': administration, 'subject': subject, 'adjunct': adjunct, 'reserve': reserve, 'others': others, 'note': $('#dialog-note').val()};
                break;
              case 'mod':
                mojo.json = {'agency_id': mojo.mojos[2], 'era_id': params.era_id, 'academic_era_code': params.academic_era_code, 'administration': administration, 'subject': subject, 'adjunct': adjunct, 'reserve': reserve, 'others': others, 'note': $('#dialog-note').val()};
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
                if (mojo.check_contact()) {
                  mojo.json = {'agency_id': mojo.mojos[2], 'id': params.id, 'cname': $('#dialog-cname').val(), 'title': $('#dialog-title').val(), 'manager': ($('#dialog-manager').is(':checked'))? 1 : 0, 'staff': ($('#dialog-staff').is(':checked'))? 1 : 0, 'role': $('#dialog-role').val(), 'area_code': $('#dialog-area_code').val(), 'phone': $('#dialog-phone').val(), 'ext': $('#dialog-ext').val(), 'email': $('#dialog-email').val(), 'spare_email': $('#dialog-spare_email').val(), 'primary': ($('#dialog-primary').is(':checked'))? 1 : 0};  
                  mojo.ajax('agent', key, val, mojo.json);
                } else {
                    kendo.alert( mojo.errmsg );
                    return false;
                }
                break;
              case 'del':
                mojo.json = {'agency_id': mojo.mojos[2], 'id': params.id};
                mojo.ajax('agent', key, val, mojo.json);
                break;
              }   
            }}, 
            { text: '取消'}
          ],  
        }); 

        switch(val)
        {   
        case 'add':
          mojo.html  = '<div class="col-xs-12" ><label for="dialog-cname">姓名</label><input type="text" id="dialog-cname" class="form-control" required /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-title">職稱</label><input type="text" id="dialog-title" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-manager" class="form-control mini-chkbox" /><label for="dialog-manager">單位主管</label></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-staff" class="form-control mini-chkbox" /><label for="dialog-staff">單位職員</label></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-role">聘用身份</label><input type="text" id="dialog-role" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label>電話</label>&nbsp;<select id="dialog-area_code"></select>&nbsp;<input type="text" id="dialog-phone" class="" placeholder="電話" size="10" required />&nbsp;<input type="text" id="dialog-ext" class="" placeholder="分機" size="6" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-email">電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" required /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-spare_email">備用電子郵件信箱</label><input type="text" id="dialog-spare_email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-primary" class="form-control mini-chkbox" /><label for="dialog-primary">主要聯絡人</label></div>';
          $('#dialog-academic_agency_contact').data('kendoDialog').content(mojo.html).open().center();
          for (var x in mojo.refs.area_list)
            $('#dialog-area_code').append('<option value="' + x + '">' + x + '(' + mojo.refs.area_list[x] + ')</option>');
          $('#dialog-primary').prop('checked', true);
          break;
        case 'mod':
          mojo.html  = '<div class="col-xs-12" ><label for="dialog-cname">姓名</label><input type="text" id="dialog-cname" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-title">職稱</label><input type="text" id="dialog-title" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-manager" class="form-control mini-chkbox" /><label for="dialog-manager">單位主管</label></div>';
          mojo.html += '<div class="col-xs-12" ><input type="checkbox" id="dialog-staff" class="form-control mini-chkbox" /><label for="dialog-staff">單位職員</label></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-role">聘用身份</label><input type="text" id="dialog-role" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label>電話</label>&nbsp;<select id="dialog-area_code"></select>&nbsp;<input type="text" id="dialog-phone" class="" placeholder="電話" size="10" />&nbsp;<input type="text" id="dialog-ext" class="" placeholder="分機" size="6" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-email">電子郵件信箱</label><input type="text" id="dialog-email" class="form-control" /></div>';
          mojo.html += '<div class="col-xs-12" ><label for="dialog-spare_email">備用電子郵件信箱</label><input type="text" id="dialog-spare_email" class="form-control" /></div>';
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
        var zipcode = mojo.reg.zipcode.test($('#academic_agency_zipcode').val())? $('#academic_agency_zipcode').val() : '';
        mojo.json = {'id': mojo.mojos[2], 'cname': $('#academic_agency_cname').val(), 'zipcode': zipcode, 'address': $('#academic_agency_address').val(), 'established': $('#academic_agency_established').val(), 'approval': $('#academic_agency_approval').val(), 'note': $('#academic_agency_note').val()};
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
    mojo.check_unlock = function() {
      var pass = true;
      if (!mojo.reg.string255.test($('#editor-academic_class-note').val())) {
        pass = false;
        kendo.alert('請填寫修改理由說明！');
      }   
      return pass;
    }

    mojo.watch_agency_unlock = function() {
      for (var i=1; i<15; i++) 
        $('#editor-academic_class-work_days').append('<option value="' + i + '">' + i + '天</option>');
      if (mojo.if_unlock) {
        $('#editor-academic_era').val(mojo.is_unlock['era_id']).prop('disabled', true);
        $('#editor-academic_era_quarter').val(mojo.is_unlock['quarter']).prop('disabled', true);
        $('#editor-academic_class-note').val(mojo.is_unlock['note']).prop('disabled', true);
        $('#editor-academic_class-work_days').val(mojo.is_unlock['work_days']).prop('disabled', true);

        var minors = mojo.is_unlock['minors'].split(',');
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
      /*
      if (mojo.data.academic_agency_unlock && mojo.data.academic_agency_unlock.length) {
        if (!mojo.data.academic_agency_unlock['state']) {
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
      }
      */

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

    /* =========================================== */
    /* ------------------ admin report ------------------ */

    /* ------------------ agent report ------------------ */

    mojo.watch_agency_report = function() {

      /* summary tab */

      mojo.grid.academic_agency_report_summary = $('#grid-academic_agency_report_summary');
      mojo.grid.academic_agency_report_summary_a = $('#grid-academic_agency_report_summary-a');
      mojo.grid.academic_agency_report_summary_b = $('#grid-academic_agency_report_summary-b');
      mojo.grid.academic_agency_report_summary_c = $('#grid-academic_agency_report_summary-c');
      mojo.grid.academic_agency_report_summary_summary = $('#grid-academic_agency_report_summary-summary');

      mojo.grid.academic_agency_report_summary.kendoGrid({
        pageable: false,
        resizable: true,
        height: 0,
        columns: [
          { title: "研習類別", width: "240px" },
          { title: "總人數", width: "80px" },
          { title: "總人次", width: "80px" },
          { title: "每週平均上課時數", width: "100px" },
          { title: "每週平均上課時數(每班平均)", width: "100px" },
          { title: "每期上課時數", width: "100px" },
          { title: "總人時數", width: "100px" },
          { title: "營收額度", width: "100px" },
          { title: "已組合班數", width: "100px" },
          { title: "小註(課程名稱)", width: "100px" },
          { title: "備註", width: "100px" }
        ],
        toolbar: kendo.template($('#template-academic_agency_report_summary').html())
      });

      $('#grid-academic_agency_report_summary table').addClass('table');
      $('#btn-academic_agency_report_summary-export').on('click', function(e) {
        e.preventDefault();
        //mojo.to_excel('academic_agency_report_summary');
        window.open('/ajax/reporter/academic_agency_report/summary/' + $('#academic_agency_report-era').val() + '/' + $('#academic_agency_report-quarter').val() + '/' + mojo.mojos[2]);
      });

      mojo.data.academic_agency_report_summary_a = []; 
      mojo.data.academic_agency_report_summary_b = []; 
      mojo.data.academic_agency_report_summary_c = []; 

      /* detail tab */
      mojo.grid.academic_agency_report_detail = $('#grid-academic_agency_report_detail');
      mojo.grid.academic_agency_report_detail_a = $('#grid-academic_agency_report_detail-a');
      mojo.grid.academic_agency_report_detail_b = $('#grid-academic_agency_report_detail-b');
      mojo.grid.academic_agency_report_detail_c = $('#grid-academic_agency_report_detail-c');
      mojo.grid.academic_agency_report_detail_detail = $('#grid-academic_agency_report_detail-detail');

      mojo.data.academic_agency_report_detail_a = []; 
      mojo.data.academic_agency_report_detail_b = []; 
      mojo.data.academic_agency_report_detail_c = []; 

      mojo.grid.academic_agency_report_detail.kendoGrid({
        pageable: false,
        resizable: true,
        height: 0,
        columns: [
          { title: "研習類別", width: "200px" },
          { title: "國別", width: "120px" },
          { title: "男新生人數", width: "80px" },
          { title: "女新生人數", width: "80px" },
          { title: "總人數", width: "100px" },
          { title: "人次", width: "80px" },
          { title: "總人次", width: "100px" },
          { title: "每期上課時數", width: "100px" },
          { title: "每週平均時數", width: "100px" },
          { title: "總人時數", width: "100px" },
          { title: "營收額度", width: "100px" },
          { title: "小註(課程名稱)", width: "100px" },
          { title: "備註", width: "100px" },
          { title: "最後修改時間", width: "100px" }
        ],
        toolbar: kendo.template($('#template-academic_agency_report_detail').html())
      });
      $('#grid-academic_agency_report_detail table').addClass('table');

      $('#btn-academic_agency_report_detail-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_agency_report/detail/' + $('#academic_agency_report-era').val() + '/' + $('#academic_agency_report-quarter').val() + '/' + mojo.mojos[2]);
      });

      /* pdf tab */

      mojo.grid.academic_agency_report_pdf = $('#grid-academic_agency_report_pdf');
      mojo.grid.academic_agency_report_pdf_a = $('#grid-academic_agency_report_pdf-a');
      mojo.grid.academic_agency_report_pdf_b = $('#grid-academic_agency_report_pdf-b');
      mojo.grid.academic_agency_report_pdf_c = $('#grid-academic_agency_report_pdf-c');
      mojo.grid.academic_agency_report_pdf_pdf = $('#grid-academic_agency_report_pdf-pdf');

      mojo.data.academic_agency_report_pdf_a = []; 
      mojo.data.academic_agency_report_pdf_b = []; 
      mojo.data.academic_agency_report_pdf_c = []; 

      mojo.grid.academic_agency_report_pdf.kendoGrid({
        pageable: false,
        resizable: true,
        height: 0,
        columns: [
          { title: "研習類別", width: "200px" },
          { title: "總人數", width: "100px" },
          { title: "總人次", width: "100px" },
          { title: "每週平均上課時數(班平均)", width: "100px" },
          { title: "每期上課時數", width: "150px" },
          { title: "總人時數", width: "200px" },
          { title: "營收額度", width: "200px" },
          { title: "已組合班數", width: "150px" }
        ],
        toolbar: kendo.template($('#template-academic_agency_report_pdf').html())
      });
      $('#grid-academic_agency_report_pdf table').addClass('table');

      $('#btn-academic_agency_report_pdf-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/reporter/academic_agency_report/pdf/' + $('#academic_agency_report-era').val() + '/' + $('#academic_agency_report-quarter').val() + '/' + mojo.mojos[2]);
      });

      mojo.tags.report = false;
      $('#btn-academic_agency_report-search').on('click', function(e) {
        e.preventDefault();
        mojo.tags.report = false;
        mojo.json = {'agency_id': mojo.mojos[2], 'era_id': $('#academic_agency_report-era').val(), 'quarter': $('#academic_agency_report-quarter').val()};
        mojo.ajax('agent', 'academic_agency_report', 'search', mojo.json);
      });
    };

    if (mojo.mojo_if('sec-agency_report'))
      mojo.watch_agency_report();

    mojo.watch_y105 = function() {
      $('#btn-academic_agency_report_history_summary-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/downloader/y105/summary/' + mojo.data.institution_code, '_blank');
      });

      $('#btn-academic_agency_report_history_detail-export').on('click', function(e) {
        e.preventDefault();
        window.open('/ajax/downloader/y105/detail/' + mojo.data.institution_code, '_blank');
      });
    };

    if (mojo.mojo_if('sec-y105')) 
      mojo.watch_y105();

  });
})(jQuery);

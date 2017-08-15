/* mojo javascript */
(function($) {
  $(function() {
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
          { title: "每週平均上課時數", width: "100px" },
          { title: "每期上課時數", width: "100px" },
          { title: "總人時數", width: "100px" },
          { title: "營收額度", width: "100px" },
          { title: "已組合班數", width: "100px" },
          { title: "小註(課程名稱)", width: "100px" },
          { title: "備註", width: "100px" }
        ],
        toolbar: kendo.template($('#template-academic_agency_report_summary').html())
      });

      $('#btn-academic_agency_report_summary-export').on('click', function(e) {
        e.preventDefault();
        mojo.to_excel('academic_agency_report_summary');
      });

      mojo.data.academic_agency_report_summary_a = []; 
      mojo.data.academic_agency_report_summary_b = []; 
      mojo.data.academic_agency_report_summary_c = []; 

      mojo.grid.academic_agency_report_summary_a.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_summary_a,
          schema: {
            model: {
              id: "minor_code_cname",
              fields: {
                minor_code_cname: { type: "string" },
                new_people: { type: "number" },
                people: { type: "number" },
                weekly: { type: "number" },
                avg_weekly: { type: "number" },
                hours: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" },
                classes: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_people", aggregate: "sum" },
            { field: "people", aggregate: "sum" },
            { field: "weekly", aggregate: "sum" },
            { field: "avg_weekly", aggregate: "sum" },
            { field: "hours", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" },
            { field: "classes", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "minor_code_cname", title: "第一類研習類別", width: "240px", footerTemplate: "第一類研習類別小計" },
          { field: "new_people", title: "&nbsp;", width: "80px" },
          { field: "people", title: "&nbsp;", width: "80px" },
          { field: "weekly", title: "&nbsp;", width: "80px" },
          { field: "avg_weekly", title: "&nbsp;", width: "80px" },
          { field: "hours", title: "&nbsp;", width: "80px" },
          { field: "total_hours", title: "&nbsp;", width: "100px" },
          { field: "turnover", title: "&nbsp;", width: "100px" },
          { field: "classes", title: "&nbsp;", width: "100px" }

/*
          { field: "minor_code_cname", title: "第一類研習類別", width: "240px", footerTemplate: "第一類研習類別小計" },
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-a" } },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-a" } },
          { field: "weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-weekly-a" } },
          { field: "avg_weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-avg_weekly-a" } },
          { field: "hours", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-a" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-total_hours-a" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover-a" } },
          { field: "classes", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-classes-a" } }
*/
        ]
      });

      mojo.grid.academic_agency_report_summary_b.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_summary_b,
          schema: {
            model: {
              id: "minor_code_cname",
              fields: {
                minor_code_cname: { type: "string" },
                new_people: { type: "number" },
                people: { type: "number" },
                weekly: { type: "number" },
                avg_weekly: { type: "number" },
                hours: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" },
                classes: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_people", aggregate: "sum" },
            { field: "people", aggregate: "sum" },
            { field: "weekly", aggregate: "sum" },
            { field: "avg_weekly", aggregate: "sum" },
            { field: "hours", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" },
            { field: "classes", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "minor_code_cname", title: "第二類研習類別", width: "240px", footerTemplate: "第二類研習類別小計" },
          { field: "new_people", title: "&nbsp;", width: "80px" },
          { field: "people", title: "&nbsp;", width: "80px" },
          { field: "weekly", title: "&nbsp;", width: "80px" },
          { field: "avg_weekly", title: "&nbsp;", width: "80px" },
          { field: "hours", title: "&nbsp;", width: "80px" },
          { field: "total_hours", title: "&nbsp;", width: "100px" },
          { field: "turnover", title: "&nbsp;", width: "100px" },
          { field: "classes", title: "&nbsp;", width: "100px" }
/*
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-b" } },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-b" } },
          { field: "weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-weekly-b" } },
          { field: "avg_weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-avg_weekly-b" } },
          { field: "hours", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-b" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-total_hours-b" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover-b" } },
          { field: "classes", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-classes-b" } }
*/
        ]
      });

      mojo.grid.academic_agency_report_summary_c.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_summary_c,
          schema: {
            model: {
              id: "minor_code_cname",
              fields: {
                minor_code_cname: { type: "string" },
                new_people: { type: "number" },
                people: { type: "number" },
                weekly: { type: "number" },
                avg_weekly: { type: "number" },
                hours: { type: "number" },
                total_hours: { type: "number" },
                turnover: { type: "number" },
                classes: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_people", aggregate: "sum" },
            { field: "people", aggregate: "sum" },
            { field: "weekly", aggregate: "sum" },
            { field: "avg_weekly", aggregate: "sum" },
            { field: "hours", aggregate: "sum" },
            { field: "total_hours", aggregate: "sum" },
            { field: "turnover", aggregate: "sum" },
            { field: "classes", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { field: "minor_code_cname", title: "第三類研習類別", width: "240px", footerTemplate: "第三類研習類別小計" },
          { field: "new_people", title: "&nbsp;", width: "80px" },
          { field: "people", title: "&nbsp;", width: "80px" },
          { field: "weekly", title: "&nbsp;", width: "80px" },
          { field: "avg_weekly", title: "&nbsp;", width: "80px" },
          { field: "hours", title: "&nbsp;", width: "80px" },
          { field: "total_hours", title: "&nbsp;", width: "100px" },
          { field: "turnover", title: "&nbsp;", width: "100px" },
          { field: "classes", title: "&nbsp;", width: "100px" }
/*
          { field: "new_people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-new_people-c" } },
          { field: "people", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-people-c" } },
          { field: "weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-weekly-c" } },
          { field: "avg_weekly", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-avg_weekly-c" } },
          { field: "hours", title: "&nbsp;", width: "80px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-hours-c" } },
          { field: "total_hours", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-total_hours-c" } },
          { field: "turnover", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-turnover-c" } },
          { field: "classes", title: "&nbsp;", width: "100px", footerTemplate: "#=sum#", footerAttributes: { "class": "summary-classes-c" } }
*/
        ]
      });

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

      mojo.grid.academic_agency_report_detail_a.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_detail_a,
          schema: {
            model: {
              id: "country_code",
              fields: {
                country_code: { type: "string" },
                new_male: { type: "number" },
                new_female: { type: "number" },
                new_people: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_male", aggregate: "sum" },
            { field: "new_female", aggregate: "sum" },
            { field: "new_people", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { title: "第一類研習類別", width: "200px", footerTemplate: "第一類研習類別小計" },
          { field: "country_code", title: "&nbsp;" },
          { field: "country_cname", title: "&nbsp;", width: "120px" },
          { field: "new_male", title: "&nbsp;", width: "80px" },
          { field: "new_female", title: "&nbsp;", width: "80px" },
          { title: "&nbsp;", width: "100px" },
          { field: "people", title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" }

        ]
      });

      mojo.grid.academic_agency_report_detail_b.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_detail_b,
          schema: {
            model: {
              id: "country_code",
              fields: {
                coountry_code: { type: "string" },
                new_male: { type: "number" },
                new_female: { type: "number" },
                new_people: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_male", aggregate: "sum" },
            { field: "new_female", aggregate: "sum" },
            { field: "new_people", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { title: "第二類研習類別", width: "200px", footerTemplate: "第二類研習類別小計" },
          { field: "country_code", title: "&nbsp;" },
          { field: "country_cname", title: "&nbsp;", width: "120px" },
          { field: "new_male", title: "&nbsp;", width: "80px" },
          { field: "new_female", title: "&nbsp;", width: "80px" },
          { title: "&nbsp;", width: "100px" },
          { field: "people", title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" }

        ]
      });

      mojo.grid.academic_agency_report_detail_c.kendoGrid({
        dataSource: {
          data: mojo.data.academic_agency_report_detail_c,
          schema: {
            model: {
              id: "country_code",
              fields: {
                country_code: { type: "string" },
                new_male: { type: "number" },
                new_female: { type: "number" },
                new_people: { type: "number" }
              }    
            }    
          },
          aggregate: [
            { field: "new_male", aggregate: "sum" },
            { field: "new_female", aggregate: "sum" },
            { field: "new_people", aggregate: "sum" }
          ]    
        },   
        pageable: false,
        resizable: true,
        columns: [
          { title: "第三類研習類別", width: "200px", footerTemplate: "第三類研習類別小計" },
          { field: "country_code", title: "&nbsp;" },
          { field: "country_cname", title: "&nbsp;", width: "120px" },
          { field: "new_male", title: "&nbsp;", width: "80px" },
          { field: "new_female", title: "&nbsp;", width: "80px" },
          { title: "&nbsp;", width: "100px" },
          { field: "people", title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" },
          { title: "&nbsp;", width: "100px" }

        ]
      });

      /* pdf tab */

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

    

  });
})(jQuery);

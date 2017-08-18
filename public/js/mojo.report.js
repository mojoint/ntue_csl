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

      $('#btn-academic_agency_report_summary-export').on('click', function(e) {
        e.preventDefault();
        mojo.to_excel('academic_agency_report_summary');
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

      $('#btn-academic_agency_report_detail-export').on('click', function(e) {
        e.preventDefault();
        mojo.to_excel('academic_agency_report_detail');
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
          { title: "每期上課時數", width: "100px" },
          { title: "總人時數", width: "100px" },
          { title: "營收額度", width: "100px" },
          { title: "已組合班數", width: "100px" },
          { title: "備註", width: "100px" },
        ],
        toolbar: kendo.template($('#template-academic_agency_report_pdf').html())
      });

      $('#btn-academic_agency_report_pdf-export').on('click', function(e) {
        e.preventDefault();
        mojo.to_pdf('academic_agency_report_pdf');
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

    

  });
})(jQuery);

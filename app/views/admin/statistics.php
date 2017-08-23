<section id="sec-statistics">
    <div class="container">
      <div class="div-statistics">
    <?php if (isset($_SESSION['admin'])) : ?>
        <script type="text/x-kendo-template" id="template-academic_agency_class_statistics">
          <div class="createBtnContainer">
            <select id="select-academic_agency_class_statistics-era"></select>
            <a href="\\#" class="k-button" id="btn-academic_agency_class_statistics-search"><span class="fa fa-plus"></span>&nbsp;查詢狀況</a>
          </div>
          <div class="toolbar"></div>
            </script>
            <div id="grid-academic_agency_class_statistics"></div>
            <script>
              mojo.data.academic_era = JSON.parse('<?php echo json_encode($academic_era); ?>');
              mojo.data.academic_agency_class_statistics = JSON.parse('<?php echo json_encode($academic_agency_class_statistics); ?>');
              $('#grid-academic_class').kendoGrid({
                pageable: false,
                height: 0,
                toolbar: kendo.template($('#template-academic_agency_class_statistics').html())
              });
              // set academic_era
              for (var i=0; i<mojo.data.academic_era.length; i++) {
                $('#select-academic_agency_class_statistics-era').append('<option value="' + mojo.data.academic_era[i]['id'] +'">' + mojo.data.academic_era[i]['cname'] + '</option>');
              }
  
              $('#grid-academic_agency_class_statistics').kendoGrid({
                pageable: false,
                columns: [
                  { title: '機構代號'  },
                  { title: '機構名稱'  },
                  { title: '課程數量'  },
                  { title: '完成送件'  }
                ]
              });
            </script>
          </div>
        </div>
    <?php endif; ?>
      </div>
    </div>
</section>

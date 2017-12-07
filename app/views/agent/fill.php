<section id="sec-fill">
  <div class="container">
    <?php if ($_SESSION['agent']) : ?>
    <script>
      <?php if (isset($academic_agency_fill)) : ?>
        mojo.data.academic_agency_fill = JSON.parse('<?php echo json_encode($academic_agency_fill); ?>');
console.log( mojo.data.academic_agency_fill );
      <?php endif; ?>
      <?php if (isset($academic_agency_class)) : ?>
      <?php
        foreach( $academic_agency_class as $key=>$val ) {
            $academic_agency_class[$key]['cname'] = base64_encode( $val['cname'] );
        }
      ?>
        mojo.data.academic_agency_class = JSON.parse('<?php echo json_encode($academic_agency_class); ?>');
      <?php endif; ?>
    </script>
        <?php if (0 == $quarter_id) : ?>
    <div><p>目前未開放填報</p></div>      
        <?php else: ?>
    <div id="academic_agency_class" data-era_id="<?php echo $era_id ;?>" data-quarter="<?php echo $quarter ;?>" data-quarter_id="<?php echo $quarter_id ;?>" data-classes="<?php echo $classes ;?>" >
      <script type="text/x-kendo-template" id="template-academic_agency_class">
        <div class="createBtnContainer">
          <div id="academic_era_quarter"><?php echo '目前填報 : ' . $academic_era_quarter; ?></div>
          <div>
          <a href="/agent/filladd/A/" class="k-button" id="btn-academic_agency_class-a-add"><span class="fa fa-plus"></span>&nbsp;新增第一類</a>
          <a href="/agent/filladd/B/" class="k-button" id="btn-academic_agency_class-b-add"><span class="fa fa-plus"></span>&nbsp;新增第二類</a>
          <a href="/agent/filladd/C/" class="k-button" id="btn-academic_agency_class-c-add"><span class="fa fa-plus"></span>&nbsp;新增第三類</a>
          <?php if ($classes) :?>
            <a href="\\#" class="k-button" id="btn-academic_agency_class-done"><span class="fa fa-save"></span>&nbsp;完成送件</a>
          <?php else: ?>
            <label class="btn-disabled" id="btn-academic_agency_class-done"><span class="fa fa-save"></span>&nbsp;完成送件(需有課程)</label>
          <?php endif;?>
          </div>
        </div>
        <div class="toolbar"></div>
      </script>
      <div id="dialog-academic_agency_class"></div>
      <div id="grid-academic_agency_class"></div>
      <div id="grid-academic_agency_class-a"></div>
      <div id="grid-academic_agency_class-b"></div>
      <div id="grid-academic_agency_class-c"></div>
      <div id="grid-academic_agency_class-summary"></div>
      
    </div>
        <?php endif;?>
    <?php endif;?>
  </div>
</section>


<?php
  $data_link = (object)array(
    'link'  => cn('faq'),
    'name'  => lang('FAQ')
  );
?>
<?php echo Modules::run("blocks/user_header_top", $data_link); ?>
<section class="faq">
  <div class="container">
    <div class="row" id="result_ajaxSearch">
      

      <div class="col-md-12">
        <div class="faq-header text-white">
          <div class="title">
            <h1 class="title-name"><?php echo lang("frequently_asked_questions"); ?></h1>
          </div>
          <span><?php echo lang("quickly_find_out_if_weve_already_addressed_your_query"); ?></span>
        </div>
      </div>

      <!-- FAQ -->
      <?php
        if ($faqs) {
          $faqs = (array)$faqs;
          if (count($faqs) >= 2 ) {
            list($faqs1, $faqs2) = array_chunk($faqs, ceil(count($faqs) / 2));
          }else{
            $faqs1 = $faqs;
            $faqs2 = [];
          }
      ?>
      <div class="row package-faq">
        <div class="col-md-12">
          
          
          <div class="row">
            <div class="col-md-6">
              <?php
              foreach ($faqs1 as $key => $row) {
                if ($row->question && $row->answer) {
              ?>
              <div class="item">
                <div class="title">
                  <i class="fe fe-plus plus-icon"></i>
                  <h5><?php echo strip_tags($row->question); ?></h5>
                </div>
                <div class="body"><?php echo html_entity_decode($row->answer, ENT_QUOTES); ?></div>
              </div>
              <?php }} ?>
            </div>

            <div class="col-md-6">
              <?php
              foreach ($faqs2 as $key => $row) {
                if ($row->question && $row->answer) {
              ?>
              <div class="item">
                <div class="title">
                  <i class="fe fe-plus plus-icon"></i>
                  <h5><?php echo strip_tags($row->question); ?></h5>
                </div>
                <div class="body"><?php echo html_entity_decode($row->answer, ENT_QUOTES); ?></div>
              </div>
              <?php }} ?>
            </div>
            
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</section>

<script>
  $(document).ready(function(){
    $(".package-faq .item").click(function(){
      $(this).toggleClass("active");
    });
  });
</script>


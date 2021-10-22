<div class="footer footer_top dark">
  <div class="container m-t-60 m-b-50">
    <div class="row">
      <div class="col-lg-12">
        <div class="site-logo m-b-30">
          <a href="<?php echo cn(); ?>" class="m-r-20">
            <img src="<?php echo get_option('website_logo_white', BASE."assets/images/logo-white.png"); ?>" alt="Website logo">
          </a>
          <?php
            $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          ?>
          <?php 
            if (!empty($languages)) {
          ?>
          <select class="footer-lang-selector ajaxChangeLanguage" name="ids" data-url="<?php echo cn('language/set_language/'); ?>" data-redirect="<?php echo strip_tags($redirect); ?>">
            <?php 
              foreach ($languages as $key => $row) {
            ?>
            <option value="<?php echo strip_tags($row->ids); ?>" <?php echo (!empty($lang_current) && $lang_current->code == $row->code) ? 'selected' : '' ?> ><?php echo language_codes($row->code); ?></option>
            <?php }?>
          </select>
          <?php }?>
        </div>
      </div>
      <div class="col-lg-8 m-t-30  mt-lg-0">
        <h4 class="title"><?php echo lang("Quick_links"); ?></h4>
        <div class="row">
          <div class="col-6 col-md-3  mt-lg-0">
            <ul class="list-unstyled quick-link mb-0">
              <li><a href="<?php echo cn(); ?>"><?php echo lang("Home"); ?></a></li>
              <li><a href="<?php echo cn('faq'); ?>"><?php echo lang("FAQs"); ?></a></li>
              <li><a href="<?php echo cn('blog'); ?>"><?php echo lang('Blog'); ?></a></li>
            </ul>
          </div>
          <div class="col-6 col-md-3">
            <ul class="list-unstyled quick-link mb-0">
              <li><a href="<?php echo cn('terms'); ?>"><?php echo lang("terms__conditions"); ?></a></li>
              <li><a href="<?php echo cn('contact'); ?>"><?php echo lang('Contact'); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-4 m-t-30 mt-lg-0">
        <h4 class="title"><?php echo lang("contact_informations"); ?></h4>
        <ul class="list-unstyled">
          <li><?php echo lang("Tel"); ?>: <?php echo get_option('contact_tel',"+12345678"); ?> </li>
          <li><?php echo lang("Email"); ?>: <?php echo get_option('contact_email',"do-not-reply@smartpanel.com"); ?> </li>
          <li><?php echo lang("working_hour"); ?>: <?php echo get_option('contact_work_hour',"Mon - Sat 09 am - 10 pm"); ?> </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<footer class="footer footer_bottom dark">
  <div class="container">
    <div class="row align-items-center flex-row-reverse">
      <div class="col-auto ml-lg-auto">
        <div class="row align-items-center">
          <div class="col-auto">
            <ul class="list-inline mb-0">
              <?php 
                if (get_option('social_facebook_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_facebook_link',''); ?>" target="_blank" class="btn btn-icon btn-facebook"><i class="fa fa-facebook"></i></a></li>
              <?php }?>
              <?php 
                if (get_option('social_twitter_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_twitter_link',''); ?>" target="_blank" class="btn btn-icon btn-twitter"><i class="fa fa-twitter"></i></a></li>
              <?php }?>
              <?php 
                if (get_option('social_instagram_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_instagram_link',''); ?>" target="_blank" class="btn btn-icon btn-instagram"><i class="fa fa-instagram"></i></a></li>
              <?php }?>

              <?php 
                if (get_option('social_pinterest_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_pinterest_link',''); ?>" target="_blank" class="btn btn-icon btn-pinterest"><i class="fa fa-pinterest"></i></a></li>
              <?php }?>

              <?php 
                if (get_option('social_tumblr_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_tumblr_link',''); ?>" target="_blank" class="btn btn-icon btn-vk"><i class="fa fa-tumblr"></i></a></li>
              <?php }?>

              <?php 
                if (get_option('social_youtube_link','') != '') {
              ?>
              <li class="list-inline-item"><a href="<?php echo get_option('social_youtube_link',''); ?>" target="_blank" class="btn btn-icon btn-youtube"><i class="fa fa-youtube"></i></a></li>
              <?php }?>

            </ul>
          </div>
        </div>
      </div>
      
      <?php
        $version = get_field(PURCHASE, ['pid' => 24815787], 'version');
        $version = 'Ver'.$version;
      ?>
      <div class="col-12 col-lg-auto mt-3 mt-lg-0 text-center">
        <?php echo get_option('copy_right_content',"Copyright &copy; 2021 - SmartStore"); ?> <?php if(get_role("admin")) echo strip_tags($version); else echo ""; ?>
      </div>
    </div>
  </div>
</footer>


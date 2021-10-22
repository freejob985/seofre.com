<section class="checkout-form">
  <div class="container">
    <div class="row justify-content-md-center justify-content-xl-center content  ">
      <div class="col-md-12 text-center">
        <div class="checkout-header">
          <div class="title">
            <h1 class="title-name"><?php echo lang("checkout_form"); ?></h1>
          </div>
          <span class="text-muted"><?php echo lang('please_review_the_order_summary_again_before_entering_checkout_information'); ?></span>
        </div>
      </div>
      <div class="col-md-10">
        <div class="row checkout-wrap">
          <div class="col-sm-7 col-xs-12 checkout-left">
            <div class="checkout-left-title">
              <?php echo lang("checkout_information"); ?>
            </div>
            <div class="checkout-left-content form-content">

              <form class="actionCheckoutForm" method="POST" action>
                <fieldset class="form-fieldset m-t-20">
                  <!-- get alert html -->
                  <div id="alert-message"></div>
                  <div class="form-group">
                    <label class="form-label"><?php echo strip_tags($item->required_field)?></label>
                    <input type="text" name="link" class="form-control" placeholder="<?php echo strip_tags($item->required_field); ?>" required>
                  </div>

                  <div class="form-group">
                    <label class="form-label"><?php echo lang("email_address"); ?> <span class="form-required">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="<?php echo lang('please_enter_your_email_address'); ?>" required>
                  </div>
                  <?php 
                    if(!$item->is_free){
                  ?>
                  <div class="form-group">
                    <label class="form-label"><?php echo lang('payment_methods'); ?></label>
                    <select name="payment_id" id="select-payments" class="form-control custom-select">
                      <?php 
                        if ($payment_methods) {
                          foreach ($payment_methods as $key => $row) {
                      ?>
                      <option value="<?php echo $row->id; ?>" data-data='{"image": "<?php echo BASE; ?>assets/images/payments/<?php echo $row->type; ?>.png"}'><?php echo $row->name; ?></option>
                      <?php }}else{ ?>
                      <option value="empty" data-data='{"image": "<?php echo BASE; ?>assets/images/payments/empty.png"}'><?php echo lang('empty'); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <p><?php echo lang('note'); ?> <?php echo lang("clicking_return_to_shop_merchant_after_payment_successfully_completed"); ?></p>
                  </div>
                  <?php } ?>
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="agree">
                      <input type="hidden"  name="item_ids" value="<?php echo strip_tags($item->ids)?>">
                      <span class="custom-control-label"><?php echo lang('by_clicking_next_you_agree_to_our_terms_of_services'); ?></span>
                    </label>
                  </div>

                </fieldset>
                <div class="card-footer text-left">
                  <button type="submit" class="btn btn-pill btn-submit btn-gradient btn-block mr-1 mb-1">
                    <?php echo lang("place_order"); ?>
                  </button>
                </div>
              </form>

            </div>

          </div>

          <?php
            $features = (isset($item->features) && $item->features) ? json_decode($item->features): "";
          ?>
          <div class="col-sm-5 col-xs-12 checkout-right">
            <div class="checkout-right-title"><?php echo lang("order_summary"); ?></div>
            <div class="checkout-right-content">
              <div class="card-body">
                <ul class="list-unstyled leading-loose">
                  <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("package_name"); ?> <strong><?php echo strip_tags($item->name)?></strong></li>
                  <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("amount"); ?> <strong><?php echo strip_tags($item->quantity)?></strong></li>
                  <?php
                  if ($features) {
                      foreach ($features as $key => $feature) {
                        if ($feature->icon && $feature->content) {
                          echo '<li><i class="fe fe-'. $feature->icon .' text-icon" aria-hidden="true"></i> '. $feature->content .'</li>';
                        }
                      }
                    }else{
                  ?>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("high_quality"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("all_real__active"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("lifetime_guaranteed"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("100_safe"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("no_password_required"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("214_days_delivery"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("247_support"); ?></li>
                    <li><i class="fa fa-check-square-o text-icon mr-2" aria-hidden="true"></i> <?php echo lang("Profile_must_be_public"); ?></li>
                  <?php }?>     
                </ul>
              </div>
              <?php
                $setting_number = get_setting_number_format();
              ?>
              <div class="card-footer text-right">
                <div class="d-flex">
                  <p class=""><?php echo lang('total_vat_included'); ?></p>
                  <h3 class=" ml-auto"><?php echo get_option('currency_symbol', '$').currency_format($item->price, $setting_number->decimal_places, $setting_number->decimal_separator, $setting_number->thousand_separator); ?></h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://js.stripe.com/v3"></script>


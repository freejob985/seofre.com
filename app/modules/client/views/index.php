<!-- get Header top menu -->
<?php
  $data_link = (object)array(
    'link'  => cn($module),
    'name'  => lang("manage_your_orders")
  );
?>
<?php echo Modules::run("blocks/user_header_top", $data_link); ?>

<section class="client">
  <div class="container">
    <div class="row justify-content-md-center">

      <div class="col-md-8">
        <div class="client-header text-white">
          <div class="title">
            <h1 class="title-name"><?php echo lang("manage_your_orders"); ?></h1>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card client_form" id="resultActionForm">

          <div class="card-header">
            <h3 class="card-title"><?php echo lang("please_enter_the_email_address_associated_with_your_orders"); ?></h3>
          </div>
          <div class="card-body">
            <form class="form" action="<?php echo cn($module); ?>" data-html="required" method="GET">
              <div class="form-body" id="client-form">
                <div class="row justify-content-md-center">
                  <div class="col-md-12">
                    <div id="alert-message"></div>
                  </div>
                  <div class="col-12 col-sm-12 col-md-12">
                    <div class="form-group">
                      <label class="form-label"><?php echo lang('Email'); ?> <span class="form-required">*</span></label>
                      <div class="row gutters-xs">
                        <div class="col">
                          <input type="text" name="query" class="form-control email-input" placeholder="Enter your email">
                        </div>
                        <span class="col-auto">
                          <button type="submit" class="btn btn-pill btn-submit btn-gradient"><?php echo lang('Submit'); ?></button>
                        </span>
                      </div>
                    </div>
                    <?php 
                      if(isset($error) && isset($_GET['query'])){
                    ?>
                    <div class="alert alert-warning" role="alert">
                      <h4 class="alert-title">Warming!</h4>
                      <div class="text-muted"><?php echo strip_tags($error_ms); ?></div>
                    </div>
                    <?php } ?>
                  </div> 
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
      
    </div>
  </div>
</section>

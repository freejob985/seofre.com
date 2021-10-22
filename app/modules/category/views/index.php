<form class="actionForm"  method="POST">
  <div class="page-header">
    <?php 
    if(get_role("admin")  || get_role("supporter")) {
    ?>
    <h1 class="page-title d-none d-lg-block">
      <a class="btn-add-new" href="<?php echo cn("$module/add"); ?>">
        <span class="add-new"><i class="fa fa-plus-square text-primary" aria-hidden="true"></i></span>
        <?php echo lang("add_new"); ?>
      </a>
    </h1>

    <h1 class="page-title d-md-none">
      <a class="" href="<?php echo cn("$module/add"); ?>">
        <span class="add-new" data-toggle="tooltip" data-placement="bottom" data-original-title="<?php echo lang("add_new"); ?>"><i class="fa fa-plus-square text-primary" aria-hidden="true"></i></span>
      </a>
      <?php echo lang("Category"); ?>
    </h1>
    <?php }?>

    <div class="page-options d-flex">
      <div class="mr-2">
        <select  name="status" class="form-control order_by ajaxChange h-7" data-url="<?php echo cn($module."/ajax_sort_by/"); ?>">
          <option value="all"> <?php echo lang("sort_by"); ?></option>
          <?php 
            if (!empty($social_networks)) {
              foreach ($social_networks as $key => $social_network) {
          ?>
          <option value="<?php echo strip_tags($social_network[0]->main_sn_id); ?>"><?php echo strip_tags($key); ?></option>
          <?php }}?>
        </select>
      </div>

      <div>
        <div class="item-action dropdown action-options">
          <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
             <i class="fe fe-menu mr-2"></i> <?php echo lang("Action"); ?>
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item ajaxActionOptions" href="<?php echo cn($module.'/ajax_actions_option'); ?>" data-type="delete"><i class="fe fe-trash-2 text-danger mr-2"></i> <?php echo lang("Delele"); ?></a>
            <a class="dropdown-item ajaxActionOptions" href="<?php echo cn($module.'/ajax_actions_option'); ?>" data-type="all_deactive"><i class="fe fe-trash-2 text-danger mr-2"></i> <?php echo lang("all_deactivated_services"); ?></a>
            <a class="dropdown-item ajaxActionOptions" href="<?php echo cn($module.'/ajax_actions_option'); ?>" data-type="deactive"><i class="fe fe-x-square text-danger mr-2"></i> <?php echo lang("Deactive"); ?></a>   
            <a class="dropdown-item ajaxActionOptions" href="<?php echo cn($module.'/ajax_actions_option'); ?>" data-type="active"><i class="fe fe-check-square text-success mr-2"></i> <?php echo lang("Active"); ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row m-t-5" id="result_ajaxSearch">
    <?php if(!empty($all_social_networks)){
      foreach ($all_social_networks as $key => $social_network_row) {
    ?>
    <div class="col-md-12 col-xl-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><?php echo strip_tags($key); ?></h3>
          <div class="card-options">
            <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
            <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
          </div>
        </div>

        <div class="table-responsive">
          <?php
            $data = array(
              "module"     => $module,
              "columns"    => $columns,
              "categories" => $social_network_row,
              "cate_id"    => $social_network_row[0]->main_sn_id,
            );
            $this->load->view("ajax/load_services_by_cate", $data);
          ?>
        </div>
      </div>
    </div>
    <?php }}else{?>
      <?php echo Modules::run("blocks/empty_data"); ?>
    <?php } ?>
  </div>
</form>
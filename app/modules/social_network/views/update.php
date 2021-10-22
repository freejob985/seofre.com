<?php
  $ids = (!empty($category->ids))? $category->ids: '';
  if ($ids != "") {
    $url = cn($module."/ajax_update/$ids");
  }else{
    $url = cn($module."/ajax_update");
  }
?>

<div class="page-header">
  <h1>
    <?php echo ($category) ? 'Edit: ' .$category->name : 'Add New'; ?>
  </h1>
</div>
<div class="row c-update-form">
  <div class="col-md-6">
    <form class="form actionForm" action="<?php echo strip_tags($url); ?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
      <div class="card">
        <div class="card-body">
          <div class="form-body">
            <div class="row justify-content-md-center">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label ><?php echo lang('Name'); ?></label>
                  <input type="text" class="form-control square"  name="name" value="<?php echo (!empty($category->name)) ? $category->name : '' ?>">
                </div>
              </div> 
              <div class="col-md-12">
                <div class="form-group">
                  <label> Image icon path
                    <span class="form-required">*</span>
                    <i class="fa fa-question-circle" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Display the image icon of category on home page. Use format 255px * 255px. You can insert the third party url" data-title="Details"></i> 
                  </label>
                  <div class="input-group">
                    <input type="text" name="icon_path" class="form-control" value="<?=(!empty($category->image))? $category->image: ''?>">
                    <span class="input-group-append btn-elFinder">
                      <button class="btn btn-info" type="button">
                        <i class="fe fe-image">
                        </i>
                      </button>
                    </span>
                  </div>
                </div> 
              </div>
              
              <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label for="eventRegInput1"><?php echo lang("Default_sorting_number"); ?></label>
                  <input type="number" class="form-control square" name="sort"  value="<?php echo (!empty($category->sort)) ? $category->sort : ''; ?>">
                </div>
              </div>

              <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label><?php echo lang("Status"); ?></label>
                  <select name="status" class="form-control square">
                    <option value="1" <?php echo (!empty($category->status) && $category->status == 1) ? 'selected' : '' ?>><?php echo lang("Active"); ?></option>
                    <option value="0" <?php echo (isset($category->status) && $category->status != 1) ? 'selected': ''?>><?php echo lang("Deactive"); ?></option>
                  </select>
                </div>
              </div> 

              <div class="col-md-12 col-sm-12 col-xs-12 d-none">
                <div class="form-group">
                  <textarea rows="3" class="form-control square editor" name="" placeholder="About Project">
                  </textarea>
                </div>
              </div>
             
            </div>
          </div>
        </div>
        <div class="card-footer m-t-20">
          <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1"><?php echo lang('Save'); ?></button>
        </div>
      </div>
      </form>
  </div>
</div>

<script>
  "use strict";
  $(document).ready(function() {
    plugin_editor('.editor');
    $(document).on('click','.btn-elFinder', function(){
      var _that = $(this);
      getPathMediaByelFinderBrowser(_that);
    });

  });
</script>

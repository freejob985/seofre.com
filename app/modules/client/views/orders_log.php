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

      <div class="col-md-10">
        <div class="client-header text-white">
          <div class="title">
            <h1 class="title-name"><?php echo lang("manage_your_orders"); ?></h1>
          </div>
        </div>
      </div>

      <div class="col-md-10">
        <div class="card client_form" id="resultActionForm">
          <div class="card-header">
            <h3 class="card-title"><?php echo lang('Orders_List'); ?><?php echo (isset($_GET['query']) && get('query')) ? ' - '.get('query') : ''; ?></h3>
          </div>
          <?php
            if ($orders) {
          ?>
          <div class="table-responsive">
            <table class="table card-table table-striped table-vcenter">
              <thead>
                <tr>
                  <th><?php echo lang('No_'); ?></th>
                  <th><?php echo lang('order_id'); ?></th>
                  <th><?php echo lang('Package_Name'); ?></th>
                  <th><?php echo lang('Price'); ?></th>
                  <th><?php echo lang('Status'); ?></th>
                  <th><?php echo lang('Order_on'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $currency_symbol = get_option("currency_symbol", "$");
                  $i = 0;
                  foreach ($orders as $key => $row) {
                    ++$i;
                ?>
                <tr>
                  <td class="w-1"><?php echo $i; ?></td>
                  <td class="w-1"><?php echo $row->id; ?></td>
                  <td><strong><?php echo strip_tags($row->quantity . ' '. $row->service_name); ?></strong></td>
                  <td><?php echo strip_tags($currency_symbol . (double)$row->charge); ?></td>
                  <td><?php echo order_status_title($row->status); ?></td>
                  <td class="text-muted"><?php echo date("F jS, Y", strtotime($row->created)); ?></td>
                </tr>
                <?php }?>
              </tbody>
            </table>
          </div>

          <div class="col-md-12">
            <div class="float-right">
              <?php echo $pagination; ?>
            </div>
          </div>
          <?php
            }else{
          ?>
          <style>
            .data-empty img.img {
              max-height: 120px;
            }
          </style>
          <div class="p-t-20 p-b-20">
            <?php echo Modules::run("blocks/empty_data"); ?>
          </div>
          <?php }?>
         
        </div>
      </div>
      
    </div>
  </div>
</section>

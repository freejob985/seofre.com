<?php
  $data_orders_log  = $data_log->data_orders;
  $currency_decimal = get_option('currency_decimal', 2);
  switch (get_option('currency_decimal_separator', 'dot')) {
    case 'dot':
      $decimalpoint = '.';
      break;
    case 'comma':
      $decimalpoint = ',';
      break;
    default:
      $decimalpoint = '';
      break;
  } 

  switch (get_option('currency_thousand_separator', 'comma')) {
    case 'dot':
      $separator = '.';
      break;
    case 'comma':
      $separator = ',';
      break;
    case 'space':
      $separator = ' ';
      break;
    default:
      $separator = '';
      break;
  }
  $currency_symbol = get_option('currency_symbol',"$");
?>
<div class="row justify-content-center row-card statistics">

  <div class="col-sm-12">
    <div class="row">

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-success-gradient text-white mr-3">
              <i class="fe fe-users"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_log->total_users); ?></h4>
                <small class="text-muted "><?php echo lang("total_users"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-info-gradient text-white mr-3">
              <i class="fe fe-dollar-sign"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo $currency_symbol; ?><?php echo (!empty($data_log->total_spent_receive)) ? currency_format($data_log->total_spent_receive, $currency_decimal, $decimalpoint, $separator) : 0; ?></h4>
                <small class="text-muted ">
                  <?php
                    echo lang("total_amount_recieved");
                  ?>
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-warning-gradient text-white mr-3">
              <i class="fe fe-shopping-cart"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->total); ?></h4>
                <small class="text-muted "><?php echo lang("total_orders"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-danger-gradient text-white mr-3">
              <i class="fa fa-balance-scale"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo $currency_symbol; ?><?php echo (!empty($data_log->providers_balance)) ? currency_format($data_log->providers_balance, $currency_decimal, $decimalpoint, $separator) : 0?></h4>
                <small class="text-muted"><?php echo lang('Balance_providers'); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-success-gradient text-white mr-3">
              <i class="icon-fa fa fa-calendar"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo $currency_symbol . number_format($data_log->total_profit, $currency_decimal, '.', ','); ?></h4>
                <small class="text-muted ">Total Profit</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-info-gradient text-white mr-3">
              <i class="icon-fa fa fa-calculator"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo $currency_symbol.number_format($data_log->last_profit_30_days, $currency_decimal, $decimalpoint, $separator); ?></h4>
                <small class="text-muted ">
                  Total Profit (last 30 days)
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-warning-gradient text-white mr-3">
              <i class="icon-fa fa fa-calculator"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo $currency_symbol.number_format($data_log->profit_today, $currency_decimal, $decimalpoint, $separator); ?></h4>
                <small class="text-muted ">Total Profit Today</small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="row">
      
      <!-- Order -->
      <div class="col-sm-12 charts">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?php echo lang("recent_orders"); ?></h3>
          </div>
          <div class="row">
            <div class="col-sm-8">
              <div class="p-4 card">
                <div id="orders_chart_spline" class="h-18"></div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="p-4 card">
                <div id="orders_chart_pie" class="h-18"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-list"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->total); ?></h4>
                <small class="text-muted "><?php echo lang("total_orders"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-check"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 number"><?php echo strip_tags($data_orders_log->completed); ?></h4>
                <small class="text-muted"><?php echo lang("Completed"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-trending-up"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->processing); ?></h4>
                <small class="text-muted "><?php echo lang("Processing"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-loader"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->inprogress); ?></h4>
                <small class="text-muted "><?php echo lang("In_progress"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-pie-chart"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->pending); ?></h4>
                <small class="text-muted "><?php echo lang("Pending"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fa fa-hourglass-half"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->partial); ?></h4>
                <small class="text-muted "><?php echo lang("Partial"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>    

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-x-square"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->canceled); ?></h4>
                <small class="text-muted "><?php echo lang("Canceled"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div> 

      <div class="col-sm-6 col-md-3 item">
        <div class="card p-4">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md mr-3 text-info">
              <i class="fe fe-rotate-ccw"></i>
            </span>
            <div class="d-flex order-lg-2 ml-auto">
              <div class="ml-2 d-lg-block text-right">
                <h4 class="m-0 text-right number"><?php echo strip_tags($data_orders_log->refunded)?></h4>
                <small class="text-muted "><?php echo lang("Refunded"); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>    
      
    </div>
  </div>
</div>

<script>
  "use strict";
  $(document).ready(function(){
    Chart_template.chart_spline('#orders_chart_spline', <?php echo strip_tags($data_orders_log->data_orders_chart_spline); ?>);
    Chart_template.chart_pie('#orders_chart_pie', <?php echo strip_tags($data_orders_log->data_orders_chart_pie); ?>);
  });
</script>


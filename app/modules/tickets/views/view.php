<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            <?php echo $ticket->subject?>
            <?php
              $title = $ticket->from . ' - ' . convert_timezone($ticket->created, "user");
            ?>
            <small class="text-muted"> [<?php echo $title; ?>]</small>
          </h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          </button>
        </div>
        <div class="modal-body">
          <div class="form-body">
            <div class="row justify-content-md-center">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <div class="content">
                    <?php
                      if ($ticket->description) {
                        $desc = html_entity_decode($ticket->description, ENT_QUOTES);
                        $desc = str_replace("\n", "<br>", $desc);
                        echo $desc;
                      }else{
                        echo Modules::run("blocks/empty_data");
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary mr-1 mb-1" data-dismiss="modal"><?=lang("Close")?></button>
        </div>
      </div>
    </div>
  </div>
</div>

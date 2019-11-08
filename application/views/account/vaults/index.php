<script>
$( document ).ready(function() {


  $('#cancel_delete').click(function(){
    location.reload();
  });
 
 $('#do_delete').click(function(){
    
    var vault = $(this).attr('data-vault');
    
    $.post( "/ajax/delete_vault", { vault: vault })
      .done(function( data ) {
        
         $('#msg_vault_deleted').show();
           setTimeout(function(){ 
                    $('#msg_vault_deleted').hide();
                }, 3000);
       
       var tr_id = 'tr_' + vault;
       
       $("#" + tr_id).remove();
       
       
      });
  });
 
   $('.delete-vault').click(function(){
    $('#do_delete').attr("data-vault",$(this).attr('data-vault'));
    $('#delete_sum').html($(this).attr('data-sum'));
  });
 
});
  
</script>

<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_vault_deleted">
    <?php echo lang('users vaults delete_alert_1'); ?> <span id="delete_sum">sum</span> <?php echo lang('users vaults delete_alert_2'); ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>

<!-- Modal -->
  <div class="modal fade" id="deletemodal" role="dialog" style="z-index: 99999; display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <?php /* <button type="button" class="close" data-dismiss="modal">&times;</button> */ ?>
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> <?php echo lang('users vaults delete_title'); ?></h4>
          <button type="button" class="close" data-dismiss="modal" id="close_delete_modal">&times;</button>
        </div>
        <div class="modal-body">
            <?php echo lang('users vaults delete_vault_1'); ?><br>
            <?php echo lang('users vaults delete_vault_2'); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="cancel_delete" class="close" data-dismiss="modal" ><?php echo lang('users vaults cancel_button'); ?></button>
          <button type="button" class="btn btn-danger" id="do_delete" data-vault="" data-dismiss="modal"><?php echo lang('users vaults delete_button'); ?></button>
        </div>
      </div>
    </div>
  </div> 

<div class="row">
  <div class="col-md-8 mb-2">
    <h5><?php echo lang('users vaults all'); ?></h5>
  </div>
  <div class="col-md-4 mb-2 text-right">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="<?php echo base_url('account/vaults/new_vault'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-plus icons"></i> <?php echo lang('users vault create_v'); ?></a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">

      <div class="col-md-12">
        <table class="table table-hover table-responsive-lg">
          <thead>
            <th><?php echo lang('users vaults name'); ?></th>
            <th><?php echo lang('users vaults amount'); ?></th>
            <th><?php echo lang('users vaults collected'); ?></th>
            <th></th>
          </thead>
          <tbody>
            <?php if ($total) : ?>
              <?php foreach ($vaults as $view) : ?>
              <tr id="tr_<?php echo $view['id']; ?>">

                <td><?php echo $view['vault_name']; ?></td>
                <td><?php echo $view['vault_total']; ?> <?if($view['vault_currency']=='debit_base'){ $cur_index = $this->currencys->display->base_code ; ?>
                                  <?php echo $this->currencys->display->base_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra1'){ $cur_index = $this->currencys->display->extra1_code ; ?>
                                  <?php echo $this->currencys->display->extra1_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra2'){ $cur_index = $this->currencys->display->extra2_code ; ?>
                                  <?php echo $this->currencys->display->extra2_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra3'){ $cur_index = $this->currencys->display->extra3_code ; ?>
                                  <?php echo $this->currencys->display->extra3_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra4'){ $cur_index = $this->currencys->display->extra4_code ; ?>
                                  <?php echo $this->currencys->display->extra4_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra5'){ $cur_index = $this->currencys->display->extra5_code ; ?>
                                  <?php echo $this->currencys->display->extra5_code ?>
                              <?}else{?>
                              <?}?></td>
                <td><?php print $view['vault_current']; ?> <?if($view['vault_currency']=='debit_base'){?>
                                  <?php echo $this->currencys->display->base_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra1'){?>
                                  <?php echo $this->currencys->display->extra1_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra2'){?>
                                  <?php echo $this->currencys->display->extra2_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra3'){?>
                                  <?php echo $this->currencys->display->extra3_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra4'){?>
                                  <?php echo $this->currencys->display->extra4_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($view['currency']=='debit_extra5'){?>
                                  <?php echo $this->currencys->display->extra5_code ?>
                              <?}else{?>
                              <?}?></td>
                <td class="text-center"><a class="btn DismissBtn delete-vault" href="#"  data-vault="<?php echo $view['id']; ?>" data-sum="<?php echo $view['vault_current'].' '.$cur_index; ?>" data-toggle="modal" data-target="#deletemodal"  ><i class="icon-close icons"></i></a><a class="btn btn-outline-secondary btn-sm" href="<?php echo base_url();?>account/vaults/detail/<?php echo $view['id']; ?>"><i class="icon-eye icons"></i></a></td>
              </tr>

            <?php endforeach; ?>
              <?php else : ?>
                  <tr>
                      <td colspan="4">
                          <?php echo lang('core error no_results'); ?>
                      </td>
                  </tr>
              <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="card-footer text-right">
      <div class="row">
        <div class="col-md-3 text-left">
            <small><?php echo lang('users vaults total'); ?> <?php echo $total; ?></small>
        </div>
        <div class="col-md-9">
            <?php echo $pagination; ?>
        </div>
      </div>
  </div>
</div>

<div class="container" style="margin-top:50px;margin-bottom:50px;">
    <div class="row">
        <div class="col" style="margin-bottom:15px"><center><h4><?php echo lang('users description title'); ?></h4></center>
        </div>
    </div>
    <div class="row">
        <div class="col"><center><i class="fas fa-calendar-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vaults_1'); ?></center>
        </div>
          <div class="col"><center><i class="far fa-credit-card fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vaults_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-undo fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vaults_3'); ?></center>
        </div>
      </div>
</div> 
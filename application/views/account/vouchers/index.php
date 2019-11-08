<script>
$(document).ready(function(){
  
    $(".btcaddress").click(function(){
      console.log($(this).attr('data-id'));
      window.location.href = "/account/cryptowallets/view/" + $(this).attr('data-id');
    });

  //$('.delete-address').confirm_action();
  $('#cancel_delete').click(function(){
    location.reload();
  });

  $('#do_delete').click(function(){
    
    var vid = $(this).attr('data-vid');
    console.log(vid);
     
    
    $.post( "/ajax/delete_voucher", { vid: vid })
      .done(function( data ) {
        
         $('#msg_vid_deleted').show();
           setTimeout(function(){ 
                    $('#msg_vid_deleted').hide();
                }, 3000);
       
       var tr_id = 'tr_' + vid;
       
       $("#" + tr_id).remove();
       
       data = jQuery.parseJSON(data);
       console.log(data);
       
       if (data.status == 1) {
         var show_cur = $("#current_balance").attr("data-cur");
         
         if (data.currency == show_cur) {
           $("#current_balance").html(data.newbalance);
         } else {
           console.log(data.currency);
           console.log('avb_' + data.currency);
           $("#avb_" + data.currency).text(data.newbalance);
         }
         
       }
       
      });
      
  });
  
  $('.delete-address').click(function(){
    $('#do_delete').attr("data-vid",$(this).attr('data-vid'));
  });
  
  <?php if (isset($show_msg_new)) { ?>
      $('#msg_vid_deleted').show();
           setTimeout(function(){ 
                    $('#msg_vid_deleted').hide();
                }, 3000);
  <?php } ?>


  $(".voucher_row").click(function(){
      window.location.href = "/account/vouchers/view/" + $(this).attr('data-id');
    });
});
</script>
<!-- Modal -->
  <div class="modal fade" id="deletemodal" role="dialog" style="z-index: 99999; display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <?php /* <button type="button" class="close" data-dismiss="modal">&times;</button> */ ?>
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Address delete</h4>
          <button type="button" class="close" data-dismiss="modal" id="close_delete_modal">&times;</button>
        </div>
        <div class="modal-body">
            Do you really want to remove this Voucher?<br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="cancel_delete" class="close" data-dismiss="modal" >Cancel</button>
          <button type="button" class="btn btn-danger" id="do_delete" data-vid="" data-dismiss="modal">Remove</button>
        </div>
      </div>
    </div>
  </div> 

<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_vid_deleted">
  Voucher has been deleted!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>

<div class="row">
  <div class="col-md-8 mb-2">
    <h5><?php echo lang('users vouchers all'); ?></h5>
  </div>
  <div class="col-md-4 mb-2 text-right">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="<?php echo base_url('account/vouchers/activate_code'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-key icons"></i> <?php echo lang('users vouchers ac'); ?></a>
      <a href="<?php echo base_url('account/vouchers/new_voucher'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-plus icons"></i> <?php echo lang('users vouchers new'); ?></a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <table class="table table-hover table-responsive-lg">
          <thead>
            <th><?php echo lang('users vouchers date_created'); ?></th>
            <th><?php echo lang('users vouchers code'); ?></th>
            <th><?php echo lang('users trans amount'); ?></th>
            <th><?php echo lang('users disputes status'); ?></th>
            <th><?php echo lang('users vouchers action'); ?></th>
          </thead>
          <tbody>
            <?php if ($total) : ?>
              <?php foreach ($vouchers as $view) : ?>
              <tr id="tr_<?php print $view['id']; ?>" style="cursor:pointer">

                <td class="voucher_row" data-id="<?php print $view['id']; ?>"><?php echo $view['date_creature']; ?></td>
                <td class="voucher_row" data-id="<?php print $view['id']; ?>"><?php echo $view['code']; ?></td>
                <td class="voucher_row" data-id="<?php print $view['id']; ?>"><?php echo $view['amount']; ?> <?if($view['currency']=='debit_base'){?>
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
                <td class="voucher_row" data-id="<?php print $view['id']; ?>">
                  <?if($view['status']==1){?>
                                <span class="badge badge-primary"> <?php echo lang('users trans pending'); ?> </span>
                              <?}else{?>
                              <?}?>
                              <?if($view['status']==2){?>
                                <span class="badge badge-success"> <?php echo lang('users vouchers activated'); ?> </span>
                              <?}else{?>
                              <?}?>
                              <?if($view['status']==3){?>
                                <span class="badge badge-secondary"> <?php echo lang('users trans blocked'); ?> </span>
                              <?}else{?>
                              <?}?>

                </td>
                <td><a class="btn DismissBtn delete-address" href="#"  data-vid="<?php echo $view['id']; ?>" data-toggle="modal" data-target="#deletemodal"  ><i class="icon-close icons"></i></a></td>

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
            <small><?php echo lang('users vouchers total'); ?> <?php echo $total; ?></small>
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
        <div class="col"><center><i class="fas fa-gift fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vouchers_1'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-share-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vouchers_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-money-bill-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description vouchers_3'); ?></center>
        </div>
      </div>
</div> 
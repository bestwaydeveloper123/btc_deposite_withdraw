<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

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
    
    var address = $(this).attr('data-address');
    
    $.post( "/ajax/delete_address", { address: address })
      .done(function( data ) {
        
         $('#msg_address_deleted').show();
           setTimeout(function(){ 
                    $('#msg_address_deleted').hide();
                }, 3000);
        /*
        $('#close_delete_modal').trigger('click');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        */
       
       var tr_id = 'tr_' + address;
       
       $("#" + tr_id).remove();
       
       
      });
  });
  
  $('.delete-address').click(function(){
    $('#do_delete').attr("data-address",$(this).attr('data-address'));
  });
  
  <?php if (isset($show_msg_new)) { ?>
      $('#msg_address_addedd').show();
           setTimeout(function(){ 
                    $('#msg_address_addedd').hide();
                }, 3000);
  <?php } ?>

});
</script>

<!-- Modal -->
  <div class="modal fade" id="deletemodal" role="dialog" style="z-index: 99999; display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <?php /* <button type="button" class="close" data-dismiss="modal">&times;</button> */ ?>
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span><?php echo lang('users cryptowallets delete_title'); ?></h4>
          <button type="button" class="close" data-dismiss="modal" id="close_delete_modal">&times;</button>
        </div>
        <div class="modal-body">
            <?php echo lang('users cryptowallets delete_1'); ?><br>
            <?php echo lang('users cryptowallets delete_2'); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="cancel_delete" class="close" data-dismiss="modal" ><?php echo lang('users cryptowallets cancel_button'); ?></button>
          <button type="button" class="btn btn-danger" id="do_delete" data-address="" data-dismiss="modal"><?php echo lang('users cryptowallets delete_button'); ?></button>
        </div>
      </div>
    </div>
  </div> 

<!-- Modal -->
  <div class="modal fade" id="btcmodal" role="dialog" style="z-index: 99999; ">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Login</h4>
        </div>
        <div class="modal-body">
          <?php print("MODAL"); ?>
        </div>
        <div class="modal-footer">
         FOOTER
        </div>
      </div>
    </div>
  </div> 

<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_address_deleted">
  BTC address has been removed!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>

<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_address_addedd">
  New BTC address generated!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>

<div class="card">
    <div class="card-body">
    <div class="row">
      <div class="col-md-12">
         <table class="table table-hover table-responsive-lg">
           <thead>
            <th style="width:100px"><?php echo lang('users trans id'); ?></th>
            <th><?php echo lang('users cryptowallets wallet'); ?></th>
            <th><?php echo lang('users cryptowallets label'); ?></th>
            <th><?php echo lang('users cryptowallets action'); ?></th>
          </thead>
           <tbody>
              <?php if ($user_wallets['count'] > 0) : ?>
                <?php foreach ($user_wallets['results'] as $id=>$wallet) : ?>
                <tr data-id="<?php print($wallet['id']); ?>" id="tr_<?php echo $wallet['address']; ?>" style="cursor:pointer">
                  <td class="btcaddress" data-id="<?php print($wallet['id']); ?>"><?php echo ($id+1); ?></td>
                  <td class="btcaddress" data-id="<?php print($wallet['id']); ?>"><?php echo $wallet['address']; ?></td>
                  <td class="btcaddress" data-id="<?php print($wallet['id']); ?>"><?php echo $wallet['label']; ?></td>
                  <?php /* 
                  <td><a class="btn DismissBtn" href="/account/cryptowallets/delete/<?php echo $wallet['address']; ?>"><i class="icon-close icons"></i></a></td>
                   */ ?>
                  <td><a class="btn DismissBtn delete-address" href="#"  data-address="<?php echo $wallet['address']; ?>" data-toggle="modal" data-target="#deletemodal"  ><i class="icon-close icons"></i></a></td>
                </tr>

              <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">
                            <?php echo lang('core error no_results'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
         </table>
      </div>
      <div class="col-md-12 text-right">
          <a href="/account/cryptowallets/generate"><button type="button" class="btn btn-success"><?php echo lang('users cryptowallets generate'); ?></button></a>
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
        <div class="col"><center><i class="fab fa-btc fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description cryptowallets_1'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-share-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description cryptowallets_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-list-ol fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description cryptowallets_3'); ?></center>
        </div>
      </div>
</div> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
$(document).ready(function(){
    
    $(".loan_row").click(function(){
      console.log($(this).attr('data-id'));
      window.location.href = "/account/credits/detail/" + $(this).attr('data-id');
    });

  //$('.delete-address').confirm_action();
  $('#cancel_delete').click(function(){
    location.reload();
  });

  $('#do_delete').click(function(){
    
    var loan_id = $(this).attr('data-loan_id');
     
    $.post( "/ajax/delete_loan", { loan_id: loan_id })
      .done(function( data ) {
        
         $('#msg_loan_id_deleted').show();
           setTimeout(function(){ 
                    $('#msg_loan_id_deleted').hide();
                }, 3000);
        /*
        $('#close_delete_modal').trigger('click');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        */
        
       var tr_id = 'tr_' + loan_id;
       
       $("#" + tr_id).remove();
       
       
      });
  });
  
  $('.delete-address').click(function(){
    $('#do_delete').attr("data-loan_id",$(this).attr('data-loan_id'));
  });
  
  <?php if (isset($show_msg_new)) { ?>
      $('#msg_loan_id_deleted').show();
           setTimeout(function(){ 
                    $('#msg_loan_id_deleted').hide();
                }, 3000);
  <?php } ?>

  $(document).on('keyup', '#give', function(event) {
            event.preventDefault();
              credit_recalc(); 
        });
   
     $(document).on('change', '#get_cur, #myRange', function(event) {
            event.preventDefault();
              credit_recalc(); 
        });
     
     $(document).on('keyup', '#loan_days', function(event) {
            event.preventDefault();
              if ($(this).val() >= 7 && $(this).val() <= 365) {
                $('#myRange').val($(this).val());
                credit_recalc();
            } 
        });

    function credit_recalc() {
         console.log('credit recalc');
         //give sum 
         
          var give_sum = $('#give').val();
          
          //term
          var term = $('#loan_days').val();
          
          //get currency
          var get_cur = $('#get_cur').val();
          
          $.post( "/ajax/credit_recalc", { give: give_sum, get_cur: get_cur, term: term})
            .done(function( data ) {
              data = $.parseJSON(data);
              console.log(data);
              
              $("#get").val(data.credut_sum);
              $("#apr_value").html(data.apr_rate + ' %');
              
              
              $("#loan_amount").html(data.loan_total + ' ' + $('#get_cur option:selected').text());
              //$("#give").val(data.give);
            });
          
          //console.log(get_cur);
         
      }
      
      $(document).on('change', '#get', function(event) {
            event.preventDefault();
              reverse_credit_recalc(); 
        });
      
      function reverse_credit_recalc() {
        
          var get_sum = $('#get').val();
          var get_cur = $('#get_cur').val();
          var term    = $('#loan_days').val();
          
          //get currency
          
          
          $.post( "/ajax/credit_recalcr", { get: get_sum, get_cur: get_cur, term: term})
            .done(function( data ) {
              data = $.parseJSON(data);
              console.log(data);
              $('#give').val(data.btc);

              //$("#credit_get").val(data.credut_sum);
              $("#apr_value").html(data.apr_rate + ' %');
              $("#loan_amount").html(data.loan_total + ' ' + $('#credit_get_option_selected').text());
            });
        
      }
      
      var slider = document.getElementById("myRange");
        var output = document.getElementById("loan_days");
        output.innerHTML = slider.value;

        slider.oninput = function() {
          output.value = this.value;
        }
});
  
</script>
<style>
  
.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 15px;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #7867a7;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: #7867a7;
    cursor: pointer;
}
    
    
</style>
<!-- Modal -->
  <div class="modal fade" id="deletemodal" role="dialog" style="z-index: 99999; display:none">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <?php /* <button type="button" class="close" data-dismiss="modal">&times;</button> */ ?>
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Loan delete</h4>
          <button type="button" class="close" data-dismiss="modal" id="close_delete_modal">&times;</button>
        </div>
        <div class="modal-body">
            Do you really want to remove this loan? This cannot be undone and you will not be able to get information about this loan anymore!<br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="cancel_delete" class="close" data-dismiss="modal" >Cancel</button>
          <button type="button" class="btn btn-danger" id="do_delete" data-loan_id="" data-dismiss="modal">Remove</button>
        </div>
      </div>
    </div>
  </div> 

<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_loan_id_deleted">
  Loan has been deleted!
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>

<div class="row">
  <div class="col-md-12 mb-2">
    <h5><?php echo lang('users loans new_loan'); ?></h5>
    <p><?php echo lang('users loans about_loan'); ?></p>
  </div>
  <?php /*
  <div class="col-md-4 mb-2 text-right">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="<?php echo base_url('account/credits/new_loan'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-plus icons"></i> New loan</a>
    </div>
  </div>
   * 
   */ ?>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
        
        <?php 
        //dump($credit_settings);
        ?>

      <div class="col-md-12">
          <?php echo form_open(site_url("account/credits/get_loan/"), array("" => "")) ?>
        <div class="row">
         
          <div class="form-group col-md-4">
            <label for="title"><?php echo lang('users loans crypto_sum'); ?></label>
            <input type="text" class="form-control " id="give" name="give" placeholder="0.00000000">
           </div>
           <div class="form-group col-md-2">
                <label><?php echo lang('users trans cyr'); ?></label>
                      <select class="form-control" name="giv_cur" id="giv_cur" disabled>
                        <option value="debit_extra5"><?php print $this->currencys->display->extra5_code; ?></option>
                      </select>
           </div>
           <div class="form-group col-md-4">
                <label for="title"><?php echo lang('users loans loan_get'); ?></label>
                <input type="text" class="form-control " id="get" name="amount" placeholder="0.00">
           </div>
           <div class="form-group col-md-2">
                <label><?php echo lang('users trans cyr'); ?></label>
                      <select class="form-control" name="get_cur" id="get_cur">
                        <option value="0"><?php print $this->currencys->display->base_code; ?></option>
                        <option value="1"><?php print $this->currencys->display->extra1_code; ?></option>
                        <option value="2"><?php print $this->currencys->display->extra2_code; ?></option>
                        <option value="3"><?php print $this->currencys->display->extra3_code; ?></option>
                        <option value="4"><?php print $this->currencys->display->extra4_code; ?></option>
                      </select>
           </div>
            
                <div class="form-group col-md-9">
                    <label for="title"><?php echo lang('users loans term_days'); ?></label>
                    
                    <input class="slider" id="myRange" max="<?php print $credit_settings[2]['value']; ?>" min="<?php print $credit_settings[1]['value']; ?>" style="height:15px !important; width:100%" type="range" value="30" />
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                <td align="left" id="days_min"><?php print $credit_settings[1]['value']; ?></td>
                                <td>&nbsp;</td>
                                <td align="right" id="days_max"><?php print $credit_settings[2]['value']; ?></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <div class="form-group col-md-3">
                <label for="title"><?php echo lang('users loans term_days'); ?></label>
                <input type="text" class="form-control " id="loan_days" name="loan_days" placeholder="30" value="30">
           </div>
            
            <div class="form-group col-md-12">
              <label for="title"><?php echo lang('users loans total_amount'); ?> <span id="loan_amount" style="font-weight: bold">0 EUR</span> / <?php echo lang('users loans loan_apr'); ?> <span id="apr_value" style="font-weight: bold">0 %</span></label>
              
            </div>
                    
                    

      </div>
      
       <div class="col-md-12 text-right">
          <button type="submit" class="btn btn-success"><?php echo lang('users loans get_loan'); ?></button>
       </div>
       <?php echo form_close(); ?> 
    </div>
          
        <table class="table table-hover table-responsive-lg">
          <thead>
            <th>#</th>
            <th><?php echo lang('users loans loan_sum'); ?></th>
            <th><?php echo lang('users loans crypto_sum'); ?></th>
            <th><?php echo lang('users loans status'); ?></th>
            <th><?php echo lang('users loans action'); ?></th>
          </thead>
          <tbody>
            <?php if ($total) : ?>
              <?php foreach ($loans as $id=>$loan) : ?>
              
              <tr id="tr_<?php echo $loan['id']; ?>" style="cursor:pointer">
                <td class="loan_row" data-id="<?php print $loan['id']; ?>"><?php print($id+1);?></td>
                <td class="loan_row" data-id="<?php print $loan['id']; ?>"><?php echo $loan['get_sum']; ?> <?php if($loan['get_currency']==0){ $cur_index = $this->currencys->display->base_code ; ?>
                                  <?php echo $this->currencys->display->base_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['get_currency']==1){ $cur_index = $this->currencys->display->extra1_code ; ?>
                                  <?php echo $this->currencys->display->extra1_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['get_currency']==2){ $cur_index = $this->currencys->display->extra2_code ; ?>
                                  <?php echo $this->currencys->display->extra2_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['get_currency']==3){ $cur_index = $this->currencys->display->extra3_code ; ?>
                                  <?php echo $this->currencys->display->extra3_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['get_currency']==4){ $cur_index = $this->currencys->display->extra4_code ; ?>
                                  <?php echo $this->currencys->display->extra4_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['get_currency']==5){ $cur_index = $this->currencys->display->extra5_code ; ?>
                                  <?php echo $this->currencys->display->extra5_code ?>
                              <?}else{?>
                              <?}?></td>
                <td class="loan_row" data-id="<?php print $loan['id']; ?>"><?php echo $loan['give_sum']; ?> <?php if($loan['give_currency']==0){ $cur_index = $this->currencys->display->base_code ; ?>
                                  <?php echo $this->currencys->display->base_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['give_currency']==1){ $cur_index = $this->currencys->display->extra1_code ; ?>
                                  <?php echo $this->currencys->display->extra1_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['give_currency']==2){ $cur_index = $this->currencys->display->extra2_code ; ?>
                                  <?php echo $this->currencys->display->extra2_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['give_currency']==3){ $cur_index = $this->currencys->display->extra3_code ; ?>
                                  <?php echo $this->currencys->display->extra3_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['give_currency']==4){ $cur_index = $this->currencys->display->extra4_code ; ?>
                                  <?php echo $this->currencys->display->extra4_code ?>
                              <?}else{?>
                              <?}?>
                              <?if($loan['give_currency']==5){ $cur_index = $this->currencys->display->extra5_code ; ?>
                                  <?php echo $this->currencys->display->extra5_code ?>
                              <?}else{?>
                              <?}?></td>
                <td class="loan_row" data-id="<?php print $loan['id']; ?>"><?php 
                
                if ( $loan['status'] == 0) { $loan_status = 'Not paid'; }
                elseif ($loan['status'] == 1) { $loan_status = 'Paid'; }
                elseif ($loan['status'] == 2) { $loan_status = 'Active'; }
                elseif ($loan['status'] == 3) { $loan_status = 'Closed'; }
                
                ?>
                <?php print $loan_status; ?>
                
                    
                </td>
                <?php /*
                <td>
                  <?php if ($loan['status'] == 0 && $this->notice->hold_balance($user['username'], "debit_extra5", 1) > $loan['give_sum']) { ?>
                    <a href="/account/credits/pay/<?php print($loan['id']); ?>"><span class="badge badge-success"> Pay from balance </span></a>
                  <?php } ?>
                    
                  <?php 
                  
                  if ($loan['get_currency'] == 0) { $loan['cur'] = 'debit_base'; } 
                  if ($loan['get_currency'] == 1) { $loan['cur'] = 'debit_extra1'; } 
                  if ($loan['get_currency'] == 2) { $loan['cur'] = 'debit_extra2'; } 
                  if ($loan['get_currency'] == 3) { $loan['cur'] = 'debit_extra3'; } 
                  if ($loan['get_currency'] == 4) { $loan['cur'] = 'debit_extra4'; } 
                  
                  //print $loan['cur'];
                  
                  if ($loan['status'] == 1 ) { ?>
                    <a href="/account/credits/withdraw/<?php print($loan['id']); ?>"><span class="badge badge-success"> Withdraw loan </span></a>
                    
                  <?php } ?>
                  
                  <?php 
                    if ($loan['status'] == 2 && ( $this->notice->hold_balance($user['username'], $loan['cur'], 1) > $loan['total_amount'] ) ) { ?>
                    <a href="/account/credits/repay/<?php print($loan['id']); ?>"><span class="badge badge-warning"> Repay loan </span></a>
                    
                  <?php } ?>
                    
                    
                </td>
                 * 
                 */?>
                <?php if ($loan['status'] == 3) { ?>
                <td><a class="btn DismissBtn delete-address" href="#"  data-loan_id="<?php echo $loan['id']; ?>" data-toggle="modal" data-target="#deletemodal"  ><i class="icon-close icons"></i></a></td>
                <?}else{?>
                <td class="loan_row" data-id="<?php print $loan['id']; ?>"></td>
                <?php } ?>
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
            <small><?php echo lang('users loans total'); ?> <?php echo $total; ?></small>
        </div>
        <div class="col-md-9">
            <?php echo $pagination; ?>
        </div>
      </div>
  </div>

<div class="container" style="margin-top:50px;margin-bottom:50px;">
    <div class="row">
        <div class="col" style="margin-bottom:15px"><center><h4><?php echo lang('users description title'); ?></h4></center>
        </div>
    </div>
    <div class="row">
        <div class="col"><center><i class="far fa-credit-card fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description loans_1'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-rocket fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description loans_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-dollar-sign fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description loans_3'); ?></center>
        </div>
      </div>
</div>
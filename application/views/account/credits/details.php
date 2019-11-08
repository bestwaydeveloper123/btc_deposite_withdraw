<script>
$( document ).ready(function() {
  <?php if (isset($loan['data']['id'])) { ?>
  jQuery.fn.selectText = function(){
    var doc = document;
    var element = this[0];
    console.log(this, element);
    if (doc.body.createTextRange) {
        var range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        var selection = window.getSelection();        
        var range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
  };

  document.getElementById("copy_link").addEventListener("click", function() {
      $("#btc_address").selectText();
      document.execCommand("copy")
      $('#copy_link').text('Copied');
      
      if (window.getSelection) {
        if (window.getSelection().empty) {  // Chrome
          window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {  // Firefox
          window.getSelection().removeAllRanges();
        }
      } else if (document.selection) {  // IE?
        document.selection.empty();
      }
      
  }, false);
  $(document).on("click","#btn_close",function(event) { 
    window.location.href = "/account/credits/";
    
  });
  
  <?php } ?>
  
  });
  
</script>
<div class="card">
  <div class="card-body">
      
     <?php if (isset($loan['data']['id'])) { 
       
       if ($loan['data']['status'] == 0) {
       ?>
    <div class="row">
        <div class="form-group col-md-12 text-center">
            <label>Please transfer <b><?php print $loan['data']['give_sum']; ?> BTC</b>  to this address to get the Bankaero Loan:</label><br>
       </div>
        <div class="form-group col-md-12 text-center" style="font-weight:bold">
         <img src="https://chart.googleapis.com/chart?chs=300x300&amp;cht=qr&amp;chl=bitcoin:<?php print($loan['data']['btc_address']); ?>" class="walletAddressInfoModal-qr" width="200" height="200">
       </div>
        <div class="form-group col-md-12 text-center" style="font-weight:bold">
            <span id="btc_address"><?php print($loan['data']['btc_address']); ?></span>&nbsp;&nbsp;<span id="copy_link" style="cursor:pointer"><i class="fas fa-copy" style="color: #423e6f;"></i></span>
       </div>
        <div class="form-group col-md-12 text-center">
             <label><?php echo lang('users loans loan_sum'); ?>: <b><?php print $loan['data']['get_sum']; ?> <?php print $loan['data']['get_currency_text']; ?></b></label>
       </div>
        <div class="form-group col-md-12 text-center">
             <label>The funds will be available after 4 network confirmations.</label>
       </div>
        
        <div class="form-group col-md-12 text-center">
             
       </div>
       
      <div class="col-md-12 text-center">
          <a class="btn btn-success" href="/account/credits/pay/<?php print($loan['data']['id']); ?>">Pay from balance </a>
        <button type="button" class="btn btn-secondary" id="btn_close">Close</button>
      </div>
    </div>
     <?php } ?>
     
     <?php if ($loan['data']['status'] == 1) { ?>
       
       <div class="row">
        <div class="form-group col-md-12 text-center">
            <label>You can withdraw a loan to your available balance according to these details:</label><br>
        </div>
        <div class="form-group col-md-12 text-center">
            <label><?php echo lang('users loans loan_sum'); ?>: <?php print $loan['data']['get_sum'].' '.$loan['data']['get_currency_text']; ?> </label><br>
        </div>
        <div class="form-group col-md-12 text-center">
            <label>Term: <?php print $loan['data']['days']; ?> Day(s)</label><br>
        </div>
        <div class="form-group col-md-12 text-center">
            <label>Total amount: <?php print $loan['data']['total_amount'].' '.$loan['data']['get_currency_text'];; ?> </label><br>
        </div>
        <div class="form-group col-md-12 text-center">
            <label>Annual percentage rate (APR): <?php print $credit_settings[3]['value']; ?> %</label><br>
        </div>
               
      <div class="col-md-12 text-center">
          <a class="btn btn-success" href="/account/credits/withdraw/<?php print($loan['data']['id']); ?>">Withdraw loan</a>
          <a href="/account/credits/"><button type="button" class="btn btn-secondary" id="btn_close">Close</button></a>
      </div>
    </div>
       
     <?php } ?>
     
     <?php if ($loan['data']['status'] == 2) { ?>
       
       <div class="row">
        <div class="form-group col-md-12 text-center">
            <label>Once your loan is paid off, all of your crypto collateral will be released to your secure BANKAERO cryptowallet.</label><br>
       </div>
               
      <div class="col-md-12 text-center">
          <a class="btn btn-success" href="/account/credits/repay/<?php print($loan['data']['id']); ?>">Repay loan</a>
          <a href="/account/credits/"><button type="button" class="btn btn-secondary" id="btn_close">Close</button></a>
      </div>
    </div>
       
     <?php } ?>
      
       <?php if ($loan['data']['status'] == 3) { ?>
       
       <div class="row">
        <div class="form-group col-md-12 text-center">
            <label>You have repaid this loan successfully.</label><br>
       </div>
           
       <div class="form-group col-md-12 text-center">
            <label>Loan repaid on <?php print($loan['data']['date_repaid']); ?>.</label><br>
       </div>
               
      <div class="col-md-12 text-center">
          <a href="/account/credits/"><button type="button" class="btn btn-secondary" id="btn_close">Close</button></a>
      </div>
    </div>
       
     <?php }
     
     
     
       }else { ?>
      <div class="row">
          <h3><?php echo lang('users cryptowallets noaddress'); ?></h3>
      </div>
      
     <?php 
     
     
     
     
     } ?>
  </div>
<?php //dump($loan); ?>
</div>

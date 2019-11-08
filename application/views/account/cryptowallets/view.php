<script>
$( document ).ready(function() {
  <?php if (isset($wallet['results']['address'])) { ?>
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
    window.location.href = "/account/cryptowallets/";
    
  });
  
  <?php } ?>
  
  });
  
</script>
<div class="card">
  <div class="card-body">
     <?php if (isset($wallet['results']['address'])) { ?>
    <div class="row">
        <div class="form-group col-md-12 text-center">
             <label><?php echo lang('users cryptowallets btc'); ?></label>
       </div>

        <div class="form-group col-md-12 text-center" style="font-weight:bold">
         <img src="https://chart.googleapis.com/chart?chs=300x300&amp;cht=qr&amp;chl=bitcoin:<?php print($wallet['results']['address']); ?>" class="walletAddressInfoModal-qr" width="200" height="200">
       </div>
        <div class="form-group col-md-12 text-center" style="font-weight:bold">
            <span id="btc_address"><?php print($wallet['results']['address']); ?></span>&nbsp;&nbsp;<span id="copy_link" style="cursor:pointer"><i class="fas fa-copy" style="color: #423e6f;"></i></span>
       </div>
       <div class="form-group col-md-12 text-center">
             <label>Label</label>
             <?php print($wallet['results']['label']); ?>
       </div>
      <div class="col-md-12 text-center">
        <button type="button" class="btn btn-success" id="btn_close">Close</button>
      </div>
    </div>
     <?php } else { ?>
      <div class="row">
          <h3><?php echo lang('users cryptowallets noaddress'); ?></h3>
      </div>
      
      <?php } ?>
  </div>

</div>

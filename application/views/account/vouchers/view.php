<script>
$( document ).ready(function() {
  <?php if (isset($voucher)) { ?>
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
    console.log('close');
    window.location.href = "/account/vouchers/";
    
  });
  
  <?php } ?>
  
  });
  
</script>
<div class="card">
  <div class="card-body">
     <?php if (isset($voucher)) { ?>
    <div class="row">
        <div class="form-group col-md-12 text-center">
             <label>Voucher is a special code to transfer funds from your balance. Voucher code can be sent to a friend, family, to pay for goods or services, or simply donate.</label>
       </div>
       
       <div class="form-group col-md-12 text-center"">
                  <label class="title-label">
                    <?php if ($voucher['status'] == 1) {
                        
                        print('Voucher status: Pending');
                    } elseif ($voucher['status'] == 2) {
                        print('Voucher status: Activated by') . ' ' . ($voucher['activator']);
                    } elseif ($voucher['status'] == 3) {
                        print('Voucher status: Blocked');
                    }
                    
                    ?>
                  </label>
              </div>

        <div class="form-group col-md-12 text-center" style="font-weight:bold">
            <span id="btc_address"><?php print($voucher['code']); ?></span>&nbsp;&nbsp;<span id="copy_link" style="cursor:pointer"><i class="fas fa-copy" style="color: #423e6f;"></i></span>
       </div>
      <div class="col-md-12 text-center">
        <button type="button" class="btn btn-success" id="btn_close">Close</button>
      </div>
    </div>
     <?php } else { ?>
      <div class="row">
          <h3></h3>
      </div>
      
      <?php } ?>
  </div>

</div>

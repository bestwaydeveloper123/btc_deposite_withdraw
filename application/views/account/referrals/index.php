<script>
$( document ).ready(function() {

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

  document.getElementById("copy_link_div").addEventListener("click", function() {
      $("#ref_link").selectText();
      document.execCommand("copy")
      $('#copy_link').text('Copied!');
      
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
  
  $(document).on("click", "#send_refinvite", function (event) {
    $('#row_user_error').hide();
    $('#row_user_error_email').hide();

            var email = $('#email').val();
            var reflink = '<?php print $reflink; ?>';
						
						if (email.length > 0) {
							function validateEmail(email) {
								var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
								if( !emailReg.test( email ) ) {
										return false;
								} else {
										return true;
								}
							}
							var check_email = validateEmail(email);
							
						} else {
							var check_email = false;
						}
						
						if (check_email == false) {
							//setTimeout(function(){ location.reload();  }, 1000);
                            $('#row_user_error_email').show();
						} else {
							//add
								$.post("/ajax/send_refinvite", {email: email, reflink: reflink})
                    .done(function (data) {
												console.log(data);
                        data = $.parseJSON(data);
                        if (data.status == 1) {
                          $('#row_user_success').show();
                          //location.reload();
                          setTimeout(function(){ location.reload();  }, 1000);
                        } else {
                          //setTimeout(function(){ location.reload();  }, 1000);
                          $('#row_user_error').show();
                        }
                });
						}
						



        });

 
  
  <?php if (isset($_GET['user'])) { ?>
    $('#row_user_error').show();
   <?php } ?>
  
});
  
</script>
  <div class="alert alert-danger" role="alert" id="row_user_error" style="display:none"> 
	User with this email address is already registered!
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">×</span>
	</button>
  </div>
  <div class="alert alert-danger" role="alert" id="row_user_error_email" style="display:none"> 
	Incorrect Email address!
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">×</span>
	</button>
  </div>
  <div class="alert alert-success" role="alert" id="row_user_success" style="display:none"> 
	Invite sent!
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">×</span>
	</button>
  </div>
<div class="row">
  <div class="col-md-8 mb-2">
    <h5><?php echo lang('users referrals start'); ?></h5>
  </div>
    <div class="col-md-8 mb-2">
    <h6><?php echo lang('users referrals start5'); ?></h6>
  </div>
</div>

    <div class="row">
        <div class="form-group col-md-5">
            <label for="title"><?php echo lang('users referrals invite2'); ?></label>
            <input type="text" class="form-control" id="email" name="email"  placeholder="your@friend">
       </div>
        <div class="form-group col-md-2">
          <label for="title">&nbsp;</label>
            <button type="button" class="btn btn-success" id="send_refinvite"><?php echo lang('users referrals button'); ?></button>
        </div>
    </div>
<div class="row">
  <div class="col-md-8 mb-2">
    <h6><?php echo lang('users referrals sharelink'); ?></h6>
  </div>
</div>    
<div class="row">
  <div class="form-group col-md-6 d-none d-xl-block" style="border: 1px solid #423e6f; padding-top:10px; padding-bottom:10px;">
        <span id="ref_link"><nobr><center>https://bankaero.com/user/register/<?php print $reflink; ?></center></nobr></span>
  </div>

  <div class="form-group col-md-6 d-md-none" style="border: 1px solid #423e6f; padding-top:10px; padding-bottom:10px;">
        <span id="ref_link" style="font-size:13px"><nobr><center>https://bankaero.com/user/register/<?php print $reflink; ?></center></nobr></span>
  </div>
  
  <div class="form-group col-md-8 d-none d-md-block d-lg-none" style="border: 1px solid #423e6f; padding-top:10px; padding-bottom:10px;">
        <span id="ref_link" style="font-size:13px"><nobr><center>https://bankaero.com/user/register/<?php print $reflink; ?></center></nobr></span>
  </div>
    
  <div class="form-group col-md-8 d-none d-lg-block d-xl-none" style="border: 1px solid #423e6f; padding-top:10px; padding-bottom:10px;">
        <span id="ref_link" style="font-size:13px"><nobr><center>https://bankaero.com/user/register/<?php print $reflink; ?></center></nobr></span>
  </div>
    
  <div class="form-group col-md-2" style="border: 1px solid #423e6f; padding-top:10px; padding-bottom:10px;cursor:pointer;" id="copy_link_div">
      <center><span id="copy_link" style="cursor:pointer"><i class="fas fa-copy"></i> Copy</span></center>
  </div>
</div>
<div class="row">
  <div class="col-md-6 mb-2" style="margin-top:30px">
    <h6><?php echo lang('users referrals payouts'); ?><?php print($payouts); ?> <?php echo $this->currencys->display->base_code ?></h6>
  </div>
  
</div>

<div class="container" style="margin-top:50px;margin-bottom:50px;">
    <div class="row">
        <div class="col" style="margin-bottom:15px"><center><h4><?php echo lang('users description title'); ?></h4></center>
        </div>
    </div>
    <div class="row">
        <div class="col"><center><i class="fas fa-share-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description referrals_1'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-rocket fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description referrals_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-money-bill-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description referrals_3'); ?></center>
        </div>
      </div>
</div> 

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <table class="table table-hover table-responsive-lg">
          <thead>
            <th><?php echo lang('users referrals date_created'); ?></th>
            <th><?php echo lang('users referrals email'); ?></th>
            <th><?php echo lang('users referrals status'); ?></th>
          </thead>
          <tbody>
            <?php if ($total_ref) : ?>
              <?php foreach ($referrals as $view) : ?>
              <tr>
                <td><?php echo $view['date_added']; ?></td>
                <td><?php echo $view['email']; ?></td>
                <td>
                  <?if($view['status']==0){?>
                                <span class="badge badge-secondary"> <?php echo lang('users referrals status0'); ?> </span>
                              <?}else{?>
                              <?}?>
                              <?if($view['status']==1){?>
                                <span class="badge badge-primary "> <?php echo lang('users referrals status1'); ?> </span>
                              <?}else{?>
                              <?}?>
                              <?if($view['status']==2){?>
                                <span class="badge badge-success"> <?php echo lang('users referrals status2'); ?> </span>
                              <?}else{?>
                              <?}?>

                </td>

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

   
  <?php /*
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
   * 
   * 
   */ ?>
</div>


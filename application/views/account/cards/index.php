<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
$( document ).ready(function() {
 $(document).on('click', '#order_card', function(event) {
   $('#msg_order').show();
           setTimeout(function(){ 
                    $('#msg_order').hide();
                }, 3000);
  });
  
  $(document).on('click', '.card_cur_el', function(event) {
    $('.card_cur_el').removeClass('card_cur_select_active');
    $(this).addClass('card_cur_select_active');
  });
  
  
 });
</script>


<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none" id="msg_order">
  <?php echo lang('users cards card_ordered'); ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
          <h3><?php echo lang('users cards header'); ?></h3>
          <p><?php echo lang('users cards text_1'); ?><br><?php echo lang('users cards text_2'); ?></p>
          
          <div class="credit_card">
              <div class="credit_card__number">4111 1111 1111 1111</div>
              <div class="credit_card__expiry-date">10/22</div>
              <div class="credit_card__owner"><?php print($this->session->userdata['logged_in']['first_name'].' '.$this->session->userdata['logged_in']['last_name']); ?></div>
              <img src="/assets/themes/account/img/visa-logo.png" class="credit_card__logo">
              <img src="/assets/themes/account/img/main-logo.png" class="credit_card__logo2">
          </div>
          
      </div>
        
        <div class="col-md-1 text-center card_cur_el card_cur_select card_cur_select_active" style="">
          € EUR
        </div>
        <div class="col-md-1 text-center card_cur_el card_cur_select">
          $ USD
        </div>
        <div class="col-md-1 text-center card_cur_el card_cur_select">
          £ GBP
        </div>
       
        <div class="col-md-12">
          <br><br><button class="btn btn-success" id="order_card"><?php echo lang('users cards button'); ?></button>
        </div>
        
    </div>
  </div>

    
</div>


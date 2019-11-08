<script >
<?php if (isset($_GET['invite'])) { ?>
$( document ).ready(function() {
    $('#row_invite').show();
    var get_email = '';
    <?php if (isset($_GET['email'])) { ?>
    get_email = '<?php print $_GET['email']; ?>';
    <?php } ?>
    $(document).on("click","#send_invite",function() {
    
      $.post( "/ajax/send_invite", { email: get_email })
      .done(function( data ) {
        $('#row_invite').hide();
      });
    
    });
    $(document).on("click","#send_invite", function(event) { $('.alert').hide(); $('#invice_success').show(); });
});
<?php } ?>

  function shrink() {
    var cur = $( "#currency option:selected" ).val();
    if (cur != 'debit_extra5') {
    var str = $('#amount').val();
    var n = str.indexOf(".");
    if (n>0) {
        var m = str.length;
        delta = m-n;
        if (delta > 3) { 
          slice = delta - 3
          newstr = str.toString();
          newstr = newstr.slice(0, m-slice);
          //console.log(newstr);
          $('#amount').val(newstr);
        };
      }
    }
  }
  
  $(document).on("keyup","#amount",function(event) { shrink(); });

</script>


    <script>
$( document ).ready(function() {

    $('.datepicker-invoice').datepicker({
      minDate: new Date()
      });
   
   //Select2 for FAV
   /*
    $('#name').select2({
     tags: true
   });
   */
});  
</script>
<div class="alert alert-success" role="alert" id="invice_success" style="display:none"> 
	Invite sent!
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">×</span>
	</button>
  </div>
<div class="row" style="display:none" id="row_invite">
  <div class="col-md-12 mb-2 text-center">
      <button type="button" class="btn btn-success" id="send_invite">Send invite!</button>
  </div>
</div>
<div class="row">
  <div class="col-md-4 mb-2">
    <h5><?php echo lang('users invoices create'); ?></h5>
  </div>
  <div class="col-md-8 mb-2 text-right">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="#search" data-toggle="collapse" href="#search" aria-expanded="false" aria-controls="search" class="btn btn-outline-secondary btn-sm"><i class="icon-magnifier icons"></i> <?php echo lang('users trans search'); ?></a>
      <a href="<?php echo base_url('account/invoices/inbox'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-drawer icons"></i> <?php echo lang('users invoices inbox'); ?></a>
      <a href="<?php echo base_url('account/invoices/sent'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-paper-plane icons"></i> <?php echo lang('users invoices sent'); ?></a>
      <a href="<?php echo base_url('account/invoices/new_invoice'); ?>" class="btn btn-outline-secondary btn-sm active"><i class="icon-plus icons"></i> <?php echo lang('users invoices create'); ?></a>
    </div>
  </div>
</div>

<?php echo form_open(site_url("account/invoices/start_invoice/"), array("" => "")) ?>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="form-group col-md-8">
            <label for="title"><?php echo lang('users invoices name'); ?></label>
            <?Php /* <select class="form-control" id="name" name="name"></select> */ ?>
            <input type="text" class="form-control" name="name" id="name">
            
            
      </div>
        <div class="form-group col-md-4">
            <label for="title"><?php echo lang('users invoices due_date'); ?></label>
            <input type="text" class="form-control datepicker-invoice" name="due_date" id="due_date" value="<?php print date('Y-m-d', strtotime('+1 day')); ?>" placeholder="">
            
            
      </div>
      <div class="form-group col-md-6">
             <label><?php echo lang('users invoices username'); ?></label>
             <?php /* <input type="text" class="form-control" name="receiver"> */ ?>
             <input list="fav_users" name="receiver" style="display: block" class="form-control" id="receiver"></label>
              <datalist id="fav_users">
                <?php foreach ($favlist as $k=>$v) { ?>
                <option value="<?php print $v['username']; ?>">
                <?php } ?>    
              </datalist>
              <small class="form-text text-muted"><?php echo lang('users invoices username_info'); ?></small>
      </div>
      <div class="form-group col-md-4">
            <label for="title"><?php echo lang('users transfer amount'); ?></label>
            <input type="text" class="form-control <?php echo form_error('title') ? ' is-invalid' : ''; ?>" name="amount" id="amount" placeholder="0.00">
       </div>
       <div class="form-group col-md-2">
            <label><?php echo lang('users trans cyr'); ?></label>
                  <select class="form-control" name="currency" id="currency">
                    <option value="debit_base">
                    <?php echo $this->currencys->display->base_code ?>
                    </option>
                    <?php if($this->currencys->display->extra1_check) : ?>
                    <option value="debit_extra1">
                    <?php echo $this->currencys->display->extra1_code ?>
                    </option>
                    <?php endif; ?>
                    <?php if($this->currencys->display->extra2_check) : ?>
                    <option value="debit_extra2">
                    <?php echo $this->currencys->display->extra2_code ?>
                    </option>
                    <?php endif; ?>
                    <?php if($this->currencys->display->extra3_check) : ?>
                    <option value="debit_extra3">
                    <?php echo $this->currencys->display->extra3_code ?>
                    </option>
                    <?php endif; ?>
                    <?php if($this->currencys->display->extra4_check) : ?>
                    <option value="debit_extra4">
                    <?php echo $this->currencys->display->extra4_code ?>
                    </option>
                    <?php endif; ?>
                    <?php if($this->currencys->display->extra5_check) : ?>
                    <option value="debit_extra5">
                    <?php echo $this->currencys->display->extra5_code ?>
                    </option>
                    <?php endif; ?>
                  </select>
       </div>
      <div class="form-group col-md-12">
             <label><?php echo lang('users invoices description'); ?></label>
             <textarea class="form-control" rows="5" name="info"></textarea>
      </div>
      <div class="col-md-12 text-right">
          <button type="submit" class="btn btn-success"><?php echo lang('users invoices create'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?> 
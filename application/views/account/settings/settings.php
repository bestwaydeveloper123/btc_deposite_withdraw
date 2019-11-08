<script src="https://bankaero.com/assets/themes/modular_just/assets/form.js"></script>
<script>
$( document ).ready(function() {
  var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

  elems.forEach(function(html) {
    var switchery = new Switchery(html);
  });
  
  $(document).on('change', '.notification_checkbox', function(event) {
    var id = $(this).attr("data-id");
    var type = $(this).attr("data-type");
    var status = $(this).is(':checked');
    
    $.post( "/ajax/change_user_notification", { type: type, id: id, status: status })
          .done(function( data ) {
            //location.reload(); 
          });

  });
  
});

</script>
<div class="row">
  <div class="col-md-4 mb-2">
    <h5><?php echo lang('users settings profile'); ?></h5>
  </div>
  <div class="col-md-8 mb-2 text-right">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="<?php echo base_url('account/settings/logs'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-lock icons"></i> <?php echo lang('users settings logs'); ?></a>
      <a href="<?php echo base_url('account/settings/verification'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-user-following icons"></i> <?php echo lang('users settings verify'); ?></a>
      <a href="<?php echo base_url('account/settings/billing'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-wallet icons"></i> <?php echo lang('users settings billing'); ?></a>
      <a href="<?php echo base_url('account/settings/security'); ?>" class="btn btn-outline-secondary btn-sm"><i class="icon-shield icons"></i> <?php echo lang('users security title'); ?></a>
    </div>
  </div>
</div>
<?php echo form_open('', array('role'=>'form')); ?>
<div class="card">
  <div class="card-body">
    <div class="row">
          <div class="form-group col-md-6">
            <label for="email"><?php echo lang('users settings email'); ?></label>
            <input type="email" class="form-control <?php echo form_error('email') ? ' is-invalid' : ''; ?>" name="email" id="email" value="<?php echo $user['email']; ?>">
          </div>
          <div class="form-group col-md-6">
            <label for="first_name"><?php echo lang('users settings first_name'); ?></label>
            <input type="text" class="form-control <?php echo form_error('first_name') ? ' is-invalid' : ''; ?>" name="first_name" id="first_name" value="<?php echo $user['first_name']; ?>">
          </div>
          <div class="form-group col-md-6">
            <label for="last_name"><?php echo lang('users settings last_name'); ?></label>
            <input type="text" class="form-control <?php echo form_error('last_name') ? ' is-invalid' : ''; ?>" name="last_name" id="last_name" value="<?php echo $user['last_name']; ?>">
          </div>
          <div class="form-group col-md-6">
            <label for="language"><?php echo lang('users settings language'); ?></label>
             <?php echo form_dropdown('language', $this->languages, (isset($user['language']) ? $user['language'] : $this->config->item('language')), 'id="language" class="form-control"'); ?>
          </div>
          
          <?php if ($user['verify_status'] || 2 && $user['verify_status'] == 1 && $check_request == 0) : ?>
          <div class="form-group col-md-6">
            <label for="phone"><?php echo lang('users input phone'); ?></label>
            <input type="tel" class="form-control <?php echo form_error('phone') ? ' is-invalid' : ''; ?>" name="phone" id="phone" value="<?php echo $user['phone']; ?>">
          </div>
          <div class="form-group col-md-6">
            <label for="address"><?php echo lang('users settings address'); ?></label>
            <input disabled type="text" class="form-control <?php echo form_error('address') ? ' is-invalid' : ''; ?>" name="address" id="address" value="<?php echo($user['country'].', '.$user['city'].', '.$user['address_1'].', '.$user['zip']); ?>">
          </div>
          <?php endif; ?>
          
          <div class="form-group col-md-6">
            <label for="password"><?php echo lang('users settings password'); ?></label>
            <input type="password" class="form-control <?php echo form_error('password') ? ' is-invalid' : ''; ?>" name="password" id="password" placeholder="*******">
          </div>
          <div class="form-group col-md-6">
            <label for="password_repeat"><?php echo lang('users settings re_password'); ?></label>
            <input type="password" class="form-control <?php echo form_error('password_repeat') ? ' is-invalid' : ''; ?>" name="password_repeat" id="password_repeat" placeholder="*******">
          </div>
          <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-success"><?php echo lang('users button save'); ?></button>
          </div>
        
    </div>
  </div>
</div>
<div class="row" style="margin-top:10px">
  <div class="col-md-4 mb-2">
    <h5><?php echo lang('users settings email_notifications'); ?></h5>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
        <?php 

        foreach ($notification_list as $k=>$v) { 
          if ($v['type'] == 'email') { 
            if($v['global_status'] == 1) {
            ?>
          <div class="form-group col-md-3">
            <label for="<?php print $v['type'].$v['id']; ?>"><?php print $v['name']; ?></label>
            <input data-type="<?php print $v['type']; ?>" data-id="<?php print $v['id']; ?>" id="<?php print $v['type'].$v['id']; ?>" type="checkbox" class="js-switch primary notification_checkbox" <?php if($v['status'] == 1) echo "checked" ?>  >
          </div>
         <?php } } } ?>
    </div>
  </div>
</div>
<?php /*
<div class="row" style="margin-top:10px">
  <div class="col-md-4 mb-2">
    <h5><?php echo lang('users settings sms_notifications'); ?></h5>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
        <?php 

        foreach ($notification_list as $k=>$v) { 
          if ($v['type'] == 'sms') { 
            if($v['global_status'] == 1) {
            ?>
          <div class="form-group col-md-3">
            <label for="<?php print $v['type'].$v['id']; ?>"><?php print $v['name']; ?></label>
            <input data-type="<?php print $v['type']; ?>" data-id="<?php print $v['id']; ?>" id="<?php print $v['type'].$v['id']; ?>" type="checkbox" class="js-switch primary notification_checkbox" <?php if($v['status'] == 1) echo "checked" ?>  >
          </div>
           <?php } } } ?>
    </div>
  </div>
</div>
*/ ?>
 <?php echo form_close(); ?> 
 
 <div class="container" style="margin-top:50px;margin-bottom:50px;">
    <div class="row">
        <div class="col" style="margin-bottom:15px"><center><h4><?php echo lang('users description title'); ?></h4></center>
        </div>
    </div>
    <div class="row">
        <div class="col"><center><i class="fas fa-clipboard-list fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description settings_1'); ?></center>
        </div>
          <div class="col"><center><i class="far fa-check-circle fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description settings_2'); ?></center>
        </div>
          <div class="col"><center><i class="far fa-credit-card fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description settings_3'); ?></center>
        </div>
      </div>
</div> 
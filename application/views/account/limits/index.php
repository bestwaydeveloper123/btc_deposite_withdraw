<script>
$( document ).ready(function() {



 
});
  
</script>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <?php //print("<pre>");print_r($user_limits); ?>
          <h5><?php print($user_limits['vrf']['text']); ?> Verification</h5>
          <p><?php echo lang('users limits limit_increase'); ?> <a href="/account/settings/verification"><?php echo lang('users limits verification_page'); ?></a></p>
          <br>
          <h5><?php echo lang('users limits limit_used'); ?></h5>
        <?php foreach ($user_limits['limits'] as $id=>$limit) { 
          
          $width = $user_limits['limit_percentage'][$id];
          if ($width < 10) { $width = 5; }
          ?>
        
          <div><?php print(strtoupper($id)); ?></div>
          <div class="progress" style="margin-bottom: 15px;">
            <div class="progress-bar" role="progressbar" style="width: <?php print($width); ?>%;" aria-valuenow="<?php print($user_limits['limit_percentage'][$id]); ?>" aria-valuemin="0" aria-valuemax="100"><?php print($user_limits['limit_percentage'][$id]); ?> % </div>&nbsp; of <?php print($user_limits['limits'][$id].' '.strtoupper($id)); ?>
          </div>
        <?php } ?>
        <br>
        <h5><?php echo lang('users limits different_limits'); ?></h5>
        <p><?php echo lang('users limits ini_limit'); ?> 100 USD / 80 EUR / 75 GBP</a></p>
        <p><?php echo lang('users limits std_limit'); ?> 1000 USD / 800 EUR / 750 GBP</a></p>
        <p><?php echo lang('users limits ext_limit'); ?> 10000 USD / 8000 EUR / 7500 GBP</a></p><br>
        <?php echo lang('users limits crypto_exchange'); ?>
      
    </div>
  </div>

</div>


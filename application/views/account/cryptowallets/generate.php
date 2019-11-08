<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
  
</script>
<?php 

if ($new_address == 1) {
echo form_open(site_url("account/cryptowallets/"), array("" => "")) ?>
<input type="hidden" class="form-control" name="address_id" id="address_id" value="<?php print($new_wallet_data['id']); ?>">
<div class="card">
  <div class="card-body">
    <div class="row">
        <div class="form-group col-md-12 text-center">
             <label><?php echo lang('users cryptowallets btc_address'); ?></label>
       </div>
        <div class="form-group col-md-12 text-center" style="font-weight:bold">
          <?php print($new_wallet_data['address']); ?>
       </div>
       <div class="form-group col-md-12 text-center">
             <label><?php echo lang('users cryptowallets input_label'); ?></label>
             <input type="text" class="form-control" name="address_label" id="address_id">
       </div>
      <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-success">Save</button>
      </div>
    </div>
  </div>

</div>
</form>
<?php } else { 
  
  if ($btc_addon['address_price_currency'] == 'debit_base') {
    $currency_add = $this->currencys->display->base_code;
  } elseif ($btc_addon['address_price_currency'] == 'debit_extra1') {
    $currency_add = $this->currencys->display->extra1_code;
  } elseif ($btc_addon['address_price_currency'] == 'debit_extra2') {
    $currency_add = $this->currencys->display->extra2_code;
  } elseif ($btc_addon['address_price_currency'] == 'debit_extra3') {
    $currency_add = $this->currencys->display->extra3_code;
  } elseif ($btc_addon['address_price_currency'] == 'debit_extra4') {
    $currency_add = $this->currencys->display->extra4_code;
  } elseif ($btc_addon['address_price_currency'] == 'debit_extra5') {
    $currency_add = $this->currencys->display->extra5_code;
  }
  
  

  
  
  ?>
<div class="row">
  Address Limit reached. You can buy additional <?php print($btc_addon['address_addon']); ?> addresses for <?php print($btc_addon['address_price']); ?> <?php print $currency_add; ?>!
</div>
<div class="row">
    <a href="/account/cryptowallets/buyextra"><button type="button" class="btn btn-success">Buy</button></a>
</div>
<?php 

//print_r($btc_addon);
?>
<?php } ?>

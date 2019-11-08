<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
      <?php 
      //print_r($address_limits);
      ?>
    <div class="card">
      <div class="card-title">
        <?php echo lang('admin title alimits'); ?>
      </div>
      <div class="card-body">
        <?php echo form_open('', array('role'=>'form')); ?>
          <?php 
          foreach ($address_limits as $k=>$v) { ?>
          <h5><?php print $v['description']; ?></h5>
          <div class="form-group col-sm-2">
            <label for="site_name" class="control-label">Max free addresses</label>
            <input type="text" name="btc_address_max" value="<?php print($v['address_max']); ?>" id="limit_usd" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-2">
            <label for="site_name" class="control-label">Additional addresses</label>
            <input type="text" name="btc_address_addon" value="<?php print($v['address_addon']); ?>" id="limit_usd" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-2">
            <label for="site_name" class="control-label">Additional addresses price</label>
            <input type="text" name="btc_address_price" value="<?php print($v['address_price']); ?>" id="limit_usd" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-2">
            <label for="site_name" class="control-label">Additional addresses price currency</label>
            <select name="btc_address_price_currency" class="form-control">
              <option <?php $v['address_price_currency'] == 'debit_base' ? print('selected') : ''; ?> value="debit_base">EUR</option>
              <option <?php $v['address_price_currency'] == 'debit_extra1' ? print('selected') : ''; ?> value="debit_extra1">USD</option>
              <option <?php $v['address_price_currency'] == 'debit_extra2' ? print('selected') : ''; ?> value="debit_extra2">GBP</option>
              <option <?php $v['address_price_currency'] == 'debit_extra3' ? print('selected') : ''; ?> value="debit_extra3">JPY</option>
              <option <?php $v['address_price_currency'] == 'debit_extra4' ? print('selected') : ''; ?> value="debit_extra4">CAD</option>
              <option <?php $v['address_price_currency'] == 'debit_extra5' ? print('selected') : ''; ?> value="debit_extra5">BTC</option>
            </select>
          </div>
          <?php } ?>
      </div>
        <div class="card-footer-padding">
        <button type="submit"  class="btn btn-success btn-sm"><?php echo lang('core button save'); ?></button>
        </div>
    </div>
  </div>
</div>


<?php echo form_close(); ?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        <?php echo lang('admin title limits1'); ?>
      </div>
      <div class="card-body">
        <?php echo form_open('', array('role'=>'form')); ?>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">USD</label>
            <input type="text" name="limit_usd" value="<?php print($usd); ?>" id="limit_usd" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">EUR</label>
            <input type="text" name="limit_eur" value="<?php print($eur); ?>" id="limit_eur" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">GBP</label>
            <input type="text" name="limit_gbp" value="<?php print($gbp); ?>" id="limit_gbp" class="form-control form-control-sm">
          </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        <?php echo lang('admin title limits2'); ?>
      </div>
      <div class="card-body">
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">USD</label>
            <input type="text" name="limit_usd_std" value="<?php print($usd_std); ?>" id="limit_usd_std" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">EUR</label>
            <input type="text" name="limit_eur_std" value="<?php print($eur_std); ?>" id="limit_eur_std" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">GBP</label>
            <input type="text" name="limit_gbp_std" value="<?php print($gbp_std); ?>" id="limit_gbp_std" class="form-control form-control-sm">
          </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        <?php echo lang('admin title limits3'); ?>
      </div>
      <div class="card-body">
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">USD</label>
            <input type="text" name="limit_usd_ext" value="<?php print($usd_ext); ?>" id="limit_usd_ext" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">EUR</label>
            <input type="text" name="limit_eur_ext" value="<?php print($eur_ext); ?>" id="limit_eur_ext" class="form-control form-control-sm">
          </div>
          <div class="form-group col-sm-1">
            <label for="site_name" class="control-label">GBP</label>
            <input type="text" name="limit_gbp_ext" value="<?php print($gbp_ext); ?>" id="limit_gbp_ext" class="form-control form-control-sm">
          </div>
      </div>
      <div class="card-footer-padding">
        <button type="submit"  class="btn btn-success btn-sm"><?php echo lang('core button save'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php echo form_close(); ?>
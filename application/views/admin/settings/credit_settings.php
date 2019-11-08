<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>

</script>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        Settings<?php //echo lang('admin title alimits'); ?>
      </div>
      <div class="card-body">
        <?php echo form_open('', array('role'=>'form'));
        
        //dump($credit_settings);
        ?>
          <?php 
          foreach ($credit_settings as $k=>$v) { ?>
          
          <div class="form-group col-sm-2" style="display: inline-block;">
            <label for="site_name" class="control-label"><?php print $v['description']; ?></label>
            <input type="number" name="<?php print $v['param']; ?>" value="<?php print($v['value']); ?>" id="<?php print $v['param']; ?>" class="form-control form-control-sm">
          </div>
          
          <?php } ?>
          </div>
        <div class="card-footer-padding">
        <button type="submit"  class="btn btn-success btn-sm"><?php echo lang('core button save'); ?></button>
        </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
        Loan list
      </div>
      <div class="card-body">
          <table class="table table-responsive-lg table-bordered table-hover" id="loan_table">
              <thead>
                  <tr>
                      <td>ID</td>
                      <td>User</td>
                      <td>Give sum</td>
                      <td>Loan sum</td>
                      <td>Overpay</td>
                      <td>Date start</td>
                      <td>Date end</td>
                      <td>Status</td>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($loans['results'] as $id=>$loan) { ?>
                <tr>
                      <td><?php print $loan['id']; ?></td>
                      <td><?php print $loan['username']; ?></td>
                      <td><?php print $loan['give_sum']; ?> BTC</td>
                      <td><?php print $loan['get_sum'].' '.$loan['cur_text']; ?> </td>
                      <td><?php print $loan['overpay']; ?> EUR</td>
                      <td><?php print $loan['date_start']; ?> EUR</td>
                      <td><?php print $loan['date_end']; ?> EUR</td>
                      <td><?php print $loan['status_text']; ?></td>
                  </tr>
                  <?php } ?>
              </tbody>
          </table>
      </div>
      <div class="card-footer-padding">
          
      </div>
    </div>
  </div>
</div>


<?php echo form_close(); ?>
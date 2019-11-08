<div class="row">
  <div class="col-md-4 mb-2">
    <h5><?php echo lang('users vault detail'); ?></h5>
  </div>
</div>
<?php 
if (isset($_GET['delete'])) {
  //dump('1');
}
?>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults name'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_name'] ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults date_added'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['date_added'] ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults total_sum'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_total'] ?> <?php echo $symbol ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults period_sum'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_paysum'] ?> <?php echo $symbol ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults current_sum'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_current'] ?> <?php echo $symbol ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults last_payment'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_last_payment'] ?></p>
      </div>
      <div class="form-group col-md-6">
         <label for="date"><strong><?php echo lang('users vaults next_payment'); ?></strong></label>
         <p class="form-control-static"><?php echo $vault['data']['vault_next_payment'] ?></p>
      </div>
      <?php 
      if (isset($_GET['delete'])) {?>
        <div class="form-group col-md-12">Text for delete<br><br>
            <a href="/account/vaults/delete/<?php print $vault['data']['id']; ?>"><button type="button" class="btn btn-danger">Delete Vault</button></a>
        </div>
      <?php }
      ?>
        
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('users invoices modal_title1'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo lang('users invoices modal_body1'); ?> <?php echo $invoice['total'] ?> <?php echo $symbol ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?php echo lang('users invoices modal_close'); ?></button>
        <a href="<?php echo base_url();?>account/invoices/pay_invoice/<?php echo $invoice['id']; ?>" class="btn btn-success btn-sm"><?php echo lang('users invoices modal_agree'); ?></a>
      </div>
    </div>
  </div>
</div>
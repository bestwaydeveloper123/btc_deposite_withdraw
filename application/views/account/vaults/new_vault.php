<script >
$(document).ready( function () {
  
    $('.datepicker-vault').datepicker({
      minDate: new Date()
      });
});
</script>

<div class="row">
  <div class="col-md-12 mb-2">
    <h5><?php echo lang('users vaults new_v'); ?></h5>
  </div>
</div>
 <script>
  
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
  
  //$(document).on("keyup","#amount",function(event) { shrink(); });
      
});

</script>

<div class="card">
  <div class="card-body">
    <?php echo form_open(site_url("account/vaults/new_vault"), array("" => "")) ?>
    <div class="row">
        <div class="form-group col-md-12">
         <label for="title"><?php echo lang('users vaults name'); ?></label>
         <input type="text" class="form-control <?php echo form_error('vault_name') ? ' is-invalid' : ''; ?>" id="vault_name" name="vault_name" placeholder="<?php echo lang('users vaults name_placeholder'); ?>" <?php if (isset($form_data['vault_name'])) { print('value="'.$form_data['vault_name'].'"'); } ?> > 
        </div>
      <div class="form-group col-md-9">
         <label for="title"><?php echo lang('users vaults total_sum'); ?></label>
         <input type="text" class="form-control <?php echo form_error('vault_total') ? ' is-invalid' : ''; ?>" id="vault_total" name="vault_total" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00" <?php if (isset($form_data['vault_total'])) { print('value="'.$form_data['vault_total'].'"'); } ?>>
      </div>
      <div class="form-group col-md-3">
            <label><?php echo lang('users trans cyr'); ?></label>
                  <select class="form-control" name="vault_currency" id="vault_currency">
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
        
        <div class="form-group col-md-9">
         <label for="title"><?php echo lang('users vaults period_sum'); ?></label>
         <input type="text" class="form-control <?php echo form_error('vault_paysum') ? ' is-invalid' : ''; ?>" id="vault_paysum" name="vault_paysum" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00" <?php if (isset($form_data['vault_paysum'])) { print('value="'.$form_data['vault_paysum'].'"'); } ?>>
        </div>
        
        <div class="form-group col-md-3">
            <label><?php echo lang('users vaults payment_period'); ?></label>
                  <select class="form-control" name="vault_period" id="vault_period">
                    <option value="1"><?php echo lang('users vaults period_daily'); ?></option>
                    <option value="2"><?php echo lang('users vaults period_weekly'); ?></option>
                    <option value="3"><?php echo lang('users vaults period_monthly'); ?></option>

                  </select>
       </div>

       <div class="form-group col-md-4">
                <label for="id"><?php echo lang('users vaults payment_daystart'); ?></label>
                <input type="text" name="vault_daystart" value="<?php print date('Y-m-d', strtotime('+1 day')); ?>" id="vault_daystart" class="form-control datepicker-vault" placeholder="">
              </div> 
        
       <div class="col-md-12 text-right">
          <button type="submit" class="btn btn-success"><?php echo lang('users vault create_v'); ?></button>
       </div>
      <?php echo form_close(); ?> 
    </div>
  </div>
</div>
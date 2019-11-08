<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>



</script>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
          <?php echo lang('admin btcdash feespd'); ?>
        </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-responsive-lg table-bordered table-hover" id="fee_spd_table" >
              <thead>
                <th class="-text-center" width="33%">
                   Fastest
                  </th>
                  <th class="-text-center" width="33%">
                   ~ 1/2 hour
                  </th>
                  <th class="-text-center" width="33%">
                    ~ 1 hour
                </th>
              </thead>
              <tr>
                  <td  class="-text-center"><?php print $fee_spd['fastestFee']; ?> sat/byte</td>
                  <td class="-text-center"><?php print $fee_spd['halfHourFee']; ?> sat/byte</td>
                  <td class="-text-center"><?php print $fee_spd['hourFee']; ?> sat/byte</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
  //dump($settings);
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
          Settings
        </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-responsive-lg table-bordered table-hover" id="fee_spd_table" >
              <thead>
                <th class="-text-center" width="33%">
                   Setting
                  </th>
                  <th class="-text-center" width="33%">
                   Value
                  </th>
              </thead>
              <tr>
                  <td class="-text-center"><?php print $settings[28]['label']; ?></td>
                  <td class="-text-center"><input style="width:350px !important;display:inline-block;" class="form-control" type="text" id="<?php print $settings[28]['name']; ?>" value="<?php print $settings[28]['value']; ?>">
                      <button style="width:100px !important;display:inline-block;margin-left:15px" class="btn btn-success btn-xs" type="button" id="save_<?php print $settings[28]['name']; ?>">Save</button>
                      <div id="save_<?php print $settings[28]['name']; ?>_result" style="display:none;"><i class="fa fa-check fa-2x" style="margin-left:15px; color:green"></i></div>
                  </td>
              </tr>
              <tr>
                  <td class="-text-center"><?php print $settings[29]['label']; ?></td>
                  <td class="-text-center"><input style="width:100px !important;display:inline-block;" class="form-control" type="text" id="<?php print $settings[29]['name']; ?>" value="<?php print $settings[29]['value']; ?>">
                      <button style="width:100px !important;display:inline-block;margin-left:15px" class="btn btn-success btn-xs" type="button" id="save_<?php print $settings[29]['name']; ?>">Save</button>
                      <div id="save_<?php print $settings[29]['name']; ?>_result" style="display:none;"><i class="fa fa-check fa-2x" style="margin-left:15px; color:green"></i></div>
                  </td>
              </tr>
              <tr>
                  <td class="-text-center"><?php print $settings[26]['label']; ?></td>
                  <td class="-text-center"><input style="width:100px !important;display:inline-block;" class="form-control" type="text" id="<?php print $settings[26]['name']; ?>" value="<?php print $settings[26]['value']; ?>">
                      <button style="width:100px !important;display:inline-block;margin-left:15px" class="btn btn-success btn-xs" type="button" id="save_<?php print $settings[26]['name']; ?>">Save</button>
                      <div id="save_btc_wallet_check_result" style="display:none;"><i class="fa fa-check fa-2x" style="margin-left:15px; color:green"></i></div>
                  </td>
              </tr>
              <tr>
                  <td class="-text-center"><?php print $settings[27]['label']; ?></td>
                  <td class="-text-center">
                      <select class="form-control" id="<?php print $settings[27]['name']; ?>" style="width:100px !important;display:inline-block;">
                          <option value="1" <?php if ($settings[27]['value'] == 1) { print ("selected"); } ?>>Fastest</option>
                          <option value="2" <?php if ($settings[27]['value'] == 2) { print ("selected"); } ?>>~1/2 h</option>
                          <option value="3" <?php if ($settings[27]['value'] == 3) { print ("selected"); } ?>>~1 h</option>
                      </select>
                      <button style="width:100px !important;display:inline-block;margin-left:15px" class="btn btn-success btn-xs" type="button" id="save_<?php print $settings[27]['name']; ?>">Save</button>
                      <div id="save_btc_transaction_speed_result" style="display:none;"><i class="fa fa-check fa-2x" style="margin-left:15px; color:green"></i></div>
                      
                  </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
          <?php echo lang('admin btcdash addresses'); ?>
        </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-responsive-lg table-bordered table-hover" id="btc_address_table">
             <thead>
              <th class="-text-center" width="5%">
                 <?php echo lang('admin btcdash id'); ?>  
                </th>
                <th>
                 <?php echo lang('admin btcdash address'); ?>  
                </th>
                <th class="-text-center" width="10%">
                  <?php echo lang('admin btcdash balance'); ?>  
               </th>
            </thead>
             <tbody>
               <?php if ($address_total > 0) : ?>
               <?php foreach ($address_list as $id=>$view) : ?>
               
               <tr>
              
                  <td class="-text-center">
                      <?php echo $id+1; ?>
                  </td>
                  <td>
                    <?php echo $view['address']; ?>
                  </td>
                  <td class="-text-center">
                      <?php echo number_format($view['balance'], 8, '.', ''); ?>
                      <?php /*<a href="<?php echo $this_url; ?>/edit/<?php echo $view['id']; ?>" class="btn btn-sm btn-primary"><i class="icon-eye icons"></i></a> */ ?>
                  </td>

                </tr>
               <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3">
                            <?php echo lang('core error no_results'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
             </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-title">
          <?php echo lang('admin btcdash transactions'); ?>
        </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-responsive-lg table-bordered table-hover" id="btc_transactions_table">
             <thead>
              <th class="-text-center" width="5%">
                 <?php echo lang('admin btcdash id'); ?>  
                </th>
                <th>
                 <?php echo lang('admin btcdash address'); ?>  
                </th>
                <th>
                 <?php echo lang('admin btcdash txid'); ?>  
                </th>
                <th>
                 Timestamp  
                </th>
                <th class="-text-center" width="10%">
                  <?php echo lang('admin btcdash txsum'); ?>  
               </th>
            </thead>
             <tbody>
               <?php if ($transaction_total) : ?>
               <?php foreach ($transaction_list as $id=>$view) : ?>
               
               <tr>
              
                  <td class="-text-center">
                      <?php echo $id+1; ?>
                  </td>
                  <td>
                    <?php echo $view['address']; ?>
                  </td>
                  <td>
                    <?php echo $view['data']['txid']; ?>
                  </td>
                  <td>
                    <?php echo gmdate("Y-m-d\ H:i:s\ ", $view['data']['timereceived']); ?>
                  </td>
                  <td class="-text-center">
                      <?php echo $view['data']['amount']; ?>
                  </td>

                </tr>
               <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">
                            <?php echo lang('core error no_results'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
             </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
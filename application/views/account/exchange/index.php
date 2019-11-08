<div class="row">
  <div class="col-md-12 mb-2">
    <h5><?php echo lang('users title form_exchange'); ?></h5>
  </div>
</div>

  <script>
    
    $( document ).ready(function() {
      
      function recalc_base() {
        
        var give = $(".exch-base1").val();
        var give_cur = 0;
        var get = 0;
        var get_cur1 = $( ".from_base_select option:selected" ).val();

        if (get_cur1 == 'debit_extra1') {get_cur1 = 1;}
        if (get_cur1 == 'debit_extra2') {get_cur1 = 2;}
        if (get_cur1 == 'debit_extra3') {get_cur1 = 3;}
        if (get_cur1 == 'debit_extra4') {get_cur1 = 4;}
        if (get_cur1 == 'debit_extra5') {get_cur1 = 5;}
      
        $.post( "/ajax/recalc", { give: give, give_cur: give_cur, get: get, get_cur: get_cur1 })
        .done(function( data ) {
          data = $.parseJSON(data);
          $("#get_base").html(data.get); 
        });
        
      }
      
      function recalc_to_base() {
        
        var give = $(".exch-base2").val();
        var give_cur = $( "#get_cur1 option:selected" ).val();
        var get = 0;
        var get_cur = 0;

        if (give_cur == 'debit_extra1') {give_cur = 1;}
        if (give_cur == 'debit_extra2') {give_cur = 2;}
        if (give_cur == 'debit_extra3') {give_cur = 3;}
        if (give_cur == 'debit_extra4') {give_cur = 4;}
        if (give_cur == 'debit_extra5') {give_cur = 5;}
      
        //console.log('give ' + give);
        //console.log('give_cur ' + give_cur);
        //console.log('get ' + get);
        //console.log('get_cur ' + get_cur);
      
        $.post( "/ajax/recalc", { give: give, give_cur: give_cur, get: get, get_cur: get_cur })
        .done(function( data ) {
          //console.log(data);
          data = $.parseJSON(data);
          $("#get_base2").html(data.get); 
        });
      }
      
      function shrink(option) {
        if (option == 1) {
          var cur = $( ".from_base_select option:selected" ).val();
          if (cur != 'debit_extra5') {
          var str = $('.exch-base1').val();
          var n = str.indexOf(".");

          str = str.replace (/^\.|[^\d\.]/g, '');
          $('.exch-base1').val(str);

          if (n>0) {
              var m = str.length;
              delta = m-n;
              if (delta > 3) { 
                slice = delta - 3
                newstr = str.toString();
                newstr = newstr.slice(0, m-slice);
                //console.log(newstr);
                $('.exch-base1').val(newstr);
              };
            }
          }
        }
        
        if (option == 2) {
          var cur = $( "#get_cur1 option:selected" ).val();
          if (cur != 'debit_extra5') {
          var str = $('.exch-base2').val();
          var n = str.indexOf(".");

          str = str.replace (/^\.|[^\d\.]/g, '');
          $('.exch-base2').val(str);

          if (n>0) {
              var m = str.length;
              delta = m-n;
              if (delta > 3) { 
                slice = delta - 3
                newstr = str.toString();
                newstr = newstr.slice(0, m-slice);
                //console.log(newstr);
                $('.exch-base2').val(newstr);
              };
            }
          }
        }
      }
      
      $(document).on("keyup",".exch-base2", function(event) { shrink(2); recalc_to_base(); });
      $(document).on("change","#get_cur1",function() {recalc_to_base();});

      $(document).on("keyup",".exch-base1", function(event) { shrink(1); recalc_base(); });
      $(document).on("change",".from_base_select",function() {  recalc_base();});
      
    });

</script>

<?php echo form_open(site_url("account/exchange/calculation"), array("" => "")) ?>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="form-group col-md-5">
            <label><?php echo lang('users exchange amount'); ?>, <?php echo $this->currencys->display->base_code ?></label>
            <input type="text" class="form-control exch-base1" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00">
      </div>
      <div class="form-group col-md-4">
        <label><?php echo lang('users trans cyr'); ?></label>
                  <select class="form-control from_base_select" name="currency">
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
      <div class="form-group col-md-3">
            <label><?php echo lang('users exchange get'); ?></label><br>
            <span id="get_base"></span>
      </div>
       <div class="col-md-12 text-right mt-3">
          <button type="submit" class="btn btn-success"><?php echo lang('users exchange check_calc'); ?></button>
       </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?> 

<div class="row">
  <div class="col-md-12 mt-3 mb-2">
    <h5><?php echo lang('users title to_form_exchange'); ?></h5>
  </div>
</div>
<?php echo form_open(site_url("account/exchange/calculation_to"), array("" => "")) ?>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="form-group col-md-5">
            <label><?php echo lang('users exchange amount'); ?></label>
            <input type="text" class="form-control exch-base2" name="amount" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00">
      </div>
      
      <div class="form-group col-md-4">
        <label><?php echo lang('users trans cyr'); ?></label>
                  <select class="form-control" name="currency" id="get_cur1">
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
      <div class="form-group col-md-3">
            <label><?php echo lang('users exchange get'); ?>, <?php echo $this->currencys->display->base_code ?></label><br>
            <span id="get_base2"></span>
      </div>
       <div class="col-md-12 text-right mt-3">
          <button type="submit" class="btn btn-success"><?php echo lang('users exchange check_calc'); ?></button>
       </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?> 

<div class="container" style="margin-top:50px;margin-bottom:50px;">
    <div class="row">
        <div class="col" style="margin-bottom:15px"><center><h4><?php echo lang('users description title'); ?></h4></center>
        </div>
    </div>
    <div class="row">
        <div class="col"><center><i class="far fa-keyboard fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description exchange_1'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-exchange-alt fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description exchange_2'); ?></center>
        </div>
          <div class="col"><center><i class="fas fa-percent fa-2x" style="color:#7e57c2"></i></center><br><center><?php echo lang('users description exchange_3'); ?></center>
        </div>
      </div>
</div> 
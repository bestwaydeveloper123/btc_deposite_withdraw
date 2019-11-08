<div class="row">
<div class="col-md-12 mt-5">
<h2 class="text-center">Buy and exchange cryptocurrency</h2>

<p class="text-center">You can buy, hold and exchange Bitcoin and other cryptocurrencies at the best possible exchange rate</p>
</div>

<div class="form-block w-100 d-flex  flex-column align-items-end">
<form class="form-select-block w-100 d-flex  flex-lg-row flex-column flex-sm-column flex-md-column">
<script>    
  $(document).ready(function(){
    $(".cur_option_give").click(function(event){
        event.preventDefault();
        $('#give_option_selected').html($(this).attr('data-text') + ' ');
        $('#give_img_selected').attr("src",($(this).attr('data-img')));
    });
    
    $(".cur_option_get").click(function(event){
        event.preventDefault();
        $('#get_option_selected').html($(this).attr('data-text') + ' ');
        $('#get_img_selected').attr("src",($(this).attr('data-img')));
    });
});
</script>
<div class="deposit d-flex w-100 w-md-50">
  <div class="dropdown ">
  <p>YOU GIVE</p>
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color:#fff; color:black; border:0px; ">
      <span id="give_option_selected">BTC </span><img id="give_img_selected" src="/assets/themes/escrow/img/btc.png" height="16px"><input type="hidden" id="give_cur" value="0">
  </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item cur_option_give" data-id=0 data-text="EUR" href="#" data-img="/assets/themes/escrow/img/euro.png">EUR<img class="float-right" src="/assets/themes/escrow/img/euro.png" height="16px"></a>
      <a class="dropdown-item cur_option_give" data-id=5 data-text="BTC" href="#" data-img="/assets/themes/escrow/img/btc.png">BTC<img class="float-right" src="/assets/themes/escrow/img/btc.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_give" data-id=1 data-text="USD" href="#" data-img="/assets/themes/escrow/img/usd.png">USD<img class="float-right" src="/assets/themes/escrow/img/usd.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_give" data-id=2 data-text="GBP" href="#" data-img="/assets/themes/escrow/img/gbp.png">GBP<img class="float-right" src="/assets/themes/escrow/img/gbp.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_give" data-id=3 data-text="JPY" href="#" data-img="/assets/themes/escrow/img/jpy.png">JPY<img class="float-right" src="/assets/themes/escrow/img/jpy.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_give" data-id=4 data-text="CAD" href="#" data-img="/assets/themes/escrow/img/cad.png">CAD<img class="float-right" src="/assets/themes/escrow/img/cad.png" height="20px" style="padding-top:2px;"></a>
    </div>
    <input id="give" name="give" style="font-size: 18px;" type="text">
  </div>
</div>

<div class="receive w-100 w-md-50 d-flex flex-column">
<div class="dropdown">
<p>YOU GET</p>
<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color:#fff; color:black; border:0px; ">
      <span id="get_option_selected">USD </span><img id="get_img_selected" src="/assets/themes/escrow/img/usd.png" height="16px"><input type="hidden" id="get_cur" value="0">
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item cur_option_get" data-id=0 data-text="EUR" href="#" data-img="/assets/themes/escrow/img/euro.png">EUR<img class="float-right" src="/assets/themes/escrow/img/euro.png" height="16px"></a>
      <a class="dropdown-item cur_option_get" data-id=5 data-text="BTC" href="#" data-img="/assets/themes/escrow/img/btc.png">BTC<img class="float-right" src="/assets/themes/escrow/img/btc.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_get" data-id=1 data-text="USD" href="#" data-img="/assets/themes/escrow/img/usd.png">USD<img class="float-right" src="/assets/themes/escrow/img/usd.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_get" data-id=2 data-text="GBP" href="#" data-img="/assets/themes/escrow/img/gbp.png">GBP<img class="float-right" src="/assets/themes/escrow/img/gbp.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_get" data-id=3 data-text="JPY" href="#" data-img="/assets/themes/escrow/img/jpy.png">JPY<img class="float-right" src="/assets/themes/escrow/img/jpy.png" height="20px" style="padding-top:2px;"></a>
      <a class="dropdown-item cur_option_get" data-id=4 data-text="CAD" href="#" data-img="/assets/themes/escrow/img/cad.png">CAD<img class="float-right" src="/assets/themes/escrow/img/cad.png" height="20px" style="padding-top:2px;"></a>
    </div>
    <input id="give" name="give" style="font-size: 18px;" type="text">
</div>
</form>
<a class="btn btn-success my-2 my-sm-0 my-lg-3" href="https://bankaero.com/user/register" style="width: 200px;margin-left: auto;margin-right: auto;">Exchange with Bankaero</a></div>
</div>
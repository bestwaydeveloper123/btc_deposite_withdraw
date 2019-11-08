<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
 * Default Public Template
 */
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="author" content="BANKAERO">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=<?php echo $this->settings->site_version; ?>">
	  <link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?php echo $this->settings->site_version; ?>">
    <title><?php echo $page_title; ?> - <?php echo $this->settings->site_name; ?></title>
		<meta name="keywords" content="<?php echo $this->settings->meta_keywords; ?>">
    <meta name="description" content="<?php echo $this->settings->meta_description; ?>">

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/themes/escrow/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/themes/escrow/css/escrow.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css">
		<!-- FA -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

		<script src='https://www.google.com/recaptcha/api.js'></script>

    <link rel="stylesheet" type="text/css" href="/assets/themes/escrow/css/bootstrap-datepicker.css" />

	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <?php if ($this->session->language == 'english') : ?>
    
    
	<script>
	window.addEventListener("load", function(){
	window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "#252e39"
			},
			"button": {
				"background": "#14a7d0"
			}
		},
		"theme": "classic",
        "position": "bottom-right",
        "content": {
        "message": "This website uses cookies to ensure you get the best experience on our website.",
        "dismiss": "Got it!",
        "link": "Learn more",
        "href": "https://bankaero.com/privacy"
        }
	})});
	</script>
	<?php endif; ?>
	<?php if ($this->session->language == 'russian') : ?>
    <script>

    window.addEventListener("load", function(){
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#252e39"
            },
            "button": {
                "background": "#14a7d0"
            }
        },
        "theme": "classic",
        "position": "bottom-right",
        "content": {
        "message": "Данный сайт использует cookie-файлы, чтобы расширить возможности и функционал.",
        "dismiss": "Понял!",
        "link": "Узнать больше",
        "href": "https://bankaero.com/privacy"
        }
    })});
</script>
<?php endif; ?>

<!-- Pixel Code for https://oberspot.com/ -->
<script async src="https://oberspot.com/pixel/2b08293a843b3f99ef56469d08692135"></script>
<!-- END Pixel Code -->

    <script>

    $( document ).ready(function() {
      
      $.post( "/ajax/get_credit_defaults", {  })
        .done(function( data ) {
          data = $.parseJSON(data);
          //console.log(data);
          
          var days_min = data[1]['value'];
          var days_max = data[2]['value'];
          
          $('#days_min').html(days_min);
          $('#days_max').html(days_max);
          
          $('#myRange').prop("min", days_min);
          $('#myRange').prop("max", days_max);
          
          //console.log(  );
          
        });
    
      
    <?php 
    if ($this->session->userdata('logged_in')) { ?>
              var exch_link = 'account/exchange';
            <?php } else { ?>
              var exch_link = 'user/register';
            <?php }  
    ?>
        
        <?php 
    if ($this->session->userdata('logged_in')) { ?>
              var credit_link = 'account/credits';
            <?php } else { ?>
              var credit_link = 'user/register';
            <?php }  
    ?>
      //console.log(exch_link);
      var link = "https://bankaero.com/" + exch_link;
      $('.exch_link').attr("href", link);
      
      var link = "https://bankaero.com/" + credit_link;
      $('#credit_link').attr("href", link);
      
      
      
      function recalc() {

        var give = $("#give").val();
        var give_cur = $( "#give_cur" ).val();
        var get = $("#get").val();
        var get_cur = $( "#get_cur" ).val();

        if (give.indexOf('.') != -1) {
          // will not be triggered because str has _..
          if (give[give.length -1] == '.') {
            console.log('last');
            return false;
          }

        }

        if (give_cur == get_cur) {
          if (get_cur == 0) {
            get_cur = 1;
            $("#get_cur").val(get_cur);
          }
          if (get_cur == 5) {
            get_cur = 0;
            $("#get_cur").val(get_cur);
          }
        }

        $.post( "/ajax/recalc", { give: give, give_cur: give_cur, get: get, get_cur: get_cur })
        .done(function( data ) {
          data = $.parseJSON(data);
          //console.log(data);
          $("#get").val(data.get);
          
          
          $("#give").val(data.give);
        });

      }

      $("#give").val('1');
      $("#give_cur").val('5');
      $("#get_cur").val('1');

      recalc();
      credit_recalc();

      $(document).on("keypress","#give, #get",function(event) {

        key = event.keyCode;
        if (key == 0) {
          key = event.which;
        }
        
        if(key == 46 || key == 37 || key == 39 || key == 8 || key == 46 || (key >= 48 && key <= 57) ) { // . / Left / Right / Backspace, Delete keys, numbers

        } else {
          event.preventDefault();
          return false;
        }



      });


      $(document).on("keyup","#give, #get",function(event) { recalc(); });

      $(document).on("change","#give_cur, #get_cur",function() {recalc();});

      $(".cur_option_give").click(function(event){
        event.preventDefault();

        var get_s = $('#get_cur').val();
        var give_s = $('#give_cur').val();

        if ($(this).attr('data-id') != get_s) {
          $('#give_option_selected').html($(this).attr('data-text') + ' ');
          $('#give_img_selected').attr("src",($(this).attr('data-img')));
          $("#give_cur").val($(this).attr('data-id'));
        } else {

          if (get_s != 0) {
            $('#get_option_selected').html('EUR ');
            $('#get_img_selected').attr("src","/assets/themes/escrow/img/euro.png");
            $("#get_cur").val(0);
          }
          else {
            $('#get_option_selected').html('USD ');
            $('#get_img_selected').attr("src","/assets/themes/escrow/img/usd.png");
            $("#get_cur").val(1);
          }

          $('#give_option_selected').html($(this).attr('data-text') + ' ');
          $('#give_img_selected').attr("src",($(this).attr('data-img')));
          $("#give_cur").val($(this).attr('data-id'));
        }

        recalc();

      });
      
      $(".credit_cur_option_get").click(function(event){
          event.preventDefault();
          
          //switch currency
          $('#credit_get_option_selected').html($(this).attr('data-text') + ' ');
          $('#credit_get_img_selected').attr("src",($(this).attr('data-img')));
          $("#credit_get_cur").val($(this).attr('data-id'));
          
          
          credit_recalc();
          
        });
        
        $(document).on('change', '#myRange', function(event) {
            credit_recalc();
        });
        
        $(document).on('keyup', '#demo', function(event) {
            event.preventDefault();
            if ($(this).val() >= 7 && $(this).val() <= 365) {
              $('#myRange').val($(this).val());
              credit_recalc();
            } 
        });
        
        $(document).on('keypress', '#credit_give, #credit_get', function(event) {
          
        key = event.keyCode;
        if (key == 0) {
          key = event.which;
        }
        //console.log(key);
        //console.log(key2);

        if(key == 46 || key == 37 || key == 39 || key == 8 || key == 46 || (key >= 48 && key <= 57) ) { // . / Left / Right / Backspace, Delete keys, numbers

        } else {
          event.preventDefault();
          return false;
        }
        
        
         });
        
        $(document).on('keyup', '#credit_give', function(event) {
          
            event.preventDefault();
              credit_recalc(); 
        });
        
       function credit_recalc() {
         //console.log('credit recalc');
         //give sum 
          var give_sum = $('#credit_give').val();
          
          //term
          var term = $('#demo').val();
          
          //get currency
          var get_cur = $('#credit_get_cur').val();
          
          $.post( "/ajax/credit_recalc", { give: give_sum, get_cur: get_cur, term: term})
            .done(function( data ) {
              data = $.parseJSON(data);
              console.log(data);
              $("#credit_get").val(data.credut_sum);
              $("#apr_value").html(data.apr_rate + ' %');
              
              
              $("#loan_amount").html(data.loan_total + ' ' + $('#credit_get_option_selected').text());
              //$("#give").val(data.give);
            });
          
          //console.log(get_cur);
         
      }

      $(".cur_option_get").click(function(event){
          event.preventDefault();

          var get_s = $('#get_cur').val();
          var give_s = $('#give_cur').val();

          if ($(this).attr('data-id') != give_s) {
            $('#get_option_selected').html($(this).attr('data-text') + ' ');
            $('#get_img_selected').attr("src",($(this).attr('data-img')));
            $("#get_cur").val($(this).attr('data-id'));
          } else {

            if (give_s != 0) {
              $('#give_option_selected').html('EUR ');
              $('#give_img_selected').attr("src","/assets/themes/escrow/img/euro.png");
              $("#give_cur").val(0);
            }
            else {
              $('#give_option_selected').html('USD ');
              $('#give_img_selected').attr("src","/assets/themes/escrow/img/usd.png");
              $("#give_cur").val(1);
            }

            $('#get_option_selected').html($(this).attr('data-text') + ' ');
            $('#get_img_selected').attr("src",($(this).attr('data-img')));
            $("#get_cur").val($(this).attr('data-id'));
          }


          recalc();
      });
      
      
      $(document).on('keyup', '#credit_get', function(event) {
          
            event.preventDefault();
              reverse_credit_recalc(); 
        });
      
      function reverse_credit_recalc() {
        
          var get_sum = $('#credit_get').val();
          var get_cur = $('#credit_get_cur').val();
          var term = $('#demo').val();
          
          //get currency
          
          
          $.post( "/ajax/credit_recalcr", { get: get_sum, get_cur: get_cur, term: term})
            .done(function( data ) {
              data = $.parseJSON(data);
              console.log(data);
              $('#credit_give').val(data.btc);

              //$("#credit_get").val(data.credut_sum);
              $("#apr_value").html(data.apr_rate + ' %');
              $("#loan_amount").html(data.loan_total + ' ' + $('#credit_get_option_selected').text());
            });
        
      }
      

      //pre-order
      $(document).on('click', '#p_email, #p_name', function(event) {
        $('#p_send').removeClass('btn-danger');
        $('#p_send').addClass('btn-success');
        $('#p_send').html('Send!');
      });
      
      
       $(document).on("keyup","#p_name",function(event) { 
            if ($(this).val().length >= 3) {
              $('#p_send').prop('disabled', false);
            }
        });
      

      $(document).on('click', '#p_send', function(event) {
          var email = $('#p_email').val();
          var name = $('#p_name').val();
          check_email = false;
          if (email.length > 0) {
            function validateEmail(email) {
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if( !emailReg.test( email ) ) {
                  return false;
              } else {
                  return true;
              }
            }

            var check_email = validateEmail(email);
          } else {
            var check_email = false;
          }

          if (check_email == false) {
            $('#p_send').removeClass('btn-success');
            $('#p_send').addClass('btn-danger');
            $('#p_send').html('Incorrect email!');
          } else {
            $.post( "/ajax/preorder", { name: name, email:email })
            .done(function( data ) {
              $('#div_preorder').empty();
              var message = '<center><div style="width:150px" class="alert alert-success" role="alert">' +
                'Thank you!' +
                '</div></center>';
              $('#div_preorder').append(message);
            });
          }

        });

      $(document).on('click', '#card_preorder', function(event) {
        $(this).remove();
        $('#div_preorder').empty()
        var pre_form = '<center>Name: <input type="text" name="name" id="p_name" class="form-control" style="width:200px; display:inline-block; margin-left:5px; margin-right:5px;" placeholder="Minimum 3 symbols">' +
          'E-mail: <input type="email" name="email" id="p_email" class="form-control" style="width:200px; display:inline-block; margin-left:5px; margin-right:5px;" placeholder="Valid E-mail">' +
          '<button style="margin-left:15px" type="button" class="btn btn-success " id="p_send" disabled>Send!</button></center>';
        $('#div_preorder').append(pre_form);

        $("#p_name").focus();    


        /*
        if (email == '') {
            $('input.email').addClass('error');
            $('input.email').keypress(function(){
                $('input.email').removeClass('error');
            });
            $('input.email').focusout(function(){
                $('input.email').filter(function(){
                    return this.value.match(/your email regex/);
                }).addClass('error');
            });
        }
        */

      });

        var slider = document.getElementById("myRange");
        var output = document.getElementById("demo");
        output.innerHTML = slider.value;

        slider.oninput = function() {
          output.value = this.value;
        }

    });



</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-147704223-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-147704223-1');
</script>

</head>

<body>

	<nav class="navbar navbar-expand-md navbar-dark">
		<div class="container">
      <a class="navbar-brand" href="/">
				<img src="<?php echo base_url(); ?>assets/themes/account/img/main-logo.png" height="30" alt="">
			</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
<!--          <li class="nav-item <?php echo (uri_string() == '') ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo base_url('#'); ?>"><?php echo lang('core button home'); ?></a>
          </li>  -->
					<li class="nav-item <?php echo (uri_string() == 'pricing') ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo base_url('/pricing'); ?>"><?php echo lang('core button pricing'); ?></a>
          </li>
					<li class="nav-item <?php echo (uri_string() == 'blog') ? 'active' : ''; ?>">
            <a class="nav-link" href="https://blog.bankaero.com"><?php echo lang('core button blog'); ?></a>
          </li>
					<li class="nav-item <?php echo (uri_string() == 'faq') ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo base_url('/faq'); ?>"><?php echo lang('core button faq'); ?></a>
          </li>
          <li class="nav-item <?php echo (uri_string() == 'contact') ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo base_url('/contact'); ?>"><?php echo lang('core button contact'); ?></a>
          </li>

        </ul>
        <div class="form-inline my-2 my-lg-0">
					<?php if ($this->session->userdata('logged_in')): ?>
          <?php if ($this->user['is_admin']): ?>
					<div class="nav-user">
						<a href="<?php echo base_url('admin'); ?>" class="my-2 my-sm-0 mr-3"><?php echo lang('core button admin'); ?></a>
					</div>
					<?php endif; ?>
					<div class="nav-user">
						<a href="<?php echo base_url('logout'); ?>" class="my-2 my-sm-0 mr-3"><?php echo lang('core button logout'); ?></a>
					</div>
					<a href="<?php echo base_url('account/transactions'); ?>" class="btn btn-success my-2 my-sm-0"><?php echo lang('core button my_account'); ?></a>
					<!-- Language switcher logged in -->
					          <div class="col-md-2">
							<div class="dropdown text-right">
								<button id="session-language" class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<?php echo lang('core button language'); ?>
								</button>
								<div id="session-language-dropdown" class="dropdown-menu" aria-labelledby="session-language">
									<?php foreach ($this->languages as $key=>$name) : ?>
									<a class="dropdown-item" href="#" rel="<?php echo $key; ?>">
											<?php if ($key == $this->session->language) : ?>
													<i class="icon-arrow-right icons"></i>
											<?php endif; ?>
											<?php echo $name; ?>
									</a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<!-- End language switcher logged in -->
					<?php else: ?>
          <a href="<?php echo base_url('login'); ?>" class="btn btn-outline-light my-2 my-sm-0 mr-3"><?php echo lang('core button sign_in'); ?></a>
          <a href="<?php echo base_url('user/register'); ?>" class="btn btn-success my-2 my-sm-0"><?php echo lang('core button create'); ?></a>
          <!-- Language switcher -->
          <div class="col-md-2">
							<div class="dropdown text-right">
								<button id="session-language" class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<?php echo lang('core button language'); ?>
								</button>
								<div id="session-language-dropdown" class="dropdown-menu" aria-labelledby="session-language">
									<?php foreach ($this->languages as $key=>$name) : ?>
									<a class="dropdown-item" href="#" rel="<?php echo $key; ?>">
											<?php if ($key == $this->session->language) : ?>
													<i class="icon-arrow-right icons"></i>
											<?php endif; ?>
											<?php echo $name; ?>
									</a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
			<!-- End language switcher -->
					<?php endif; ?>
        </div>

      </div>
		</div>
    </nav>

    <?php // Main body?>
	<main>
    <?php // Main content?>
        <?php echo $content; ?>
	</main>

	<?php // System messages?>
        <?php if ($this->session->flashdata('message')): ?>
				<div class="notify-popup">
					<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
						<div class="container">
						<?php echo $this->session->flashdata('message'); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						</div>
					</div>
				</div>
        <?php elseif ($this->session->flashdata('error')): ?>
					<div class="notify-popup">
						<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
							<div class="container">
							<?php echo $this->session->flashdata('error'); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							</div>
						</div>
					</div>
        <?php elseif (validation_errors()): ?>
					<div class="notify-popup">
						<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
							<div class="container">
							<?php echo validation_errors(); ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							</div>
						</div>
					</div>
        <?php elseif ($this->error): ?>
					<div class="notify-popup">
						<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
							<div class="container">
							 <?php echo $this->error; ?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							</div>
						</div>
					</div>
        <?php endif; ?>

    <?php // Footer?>
    <footer>

			<div class="footer">
				<div class="container">
					<div class="row">
						<div class="col-md-8">
							<ul class="list-inline">
								<li class="list-inline-item mr-4"><a href="<?php echo base_url('agreement'); ?>"><?php echo lang('core button terms'); ?></a></li>
								<li class="list-inline-item mr-4"><a href="<?php echo base_url('privacy'); ?>"><?php echo lang('core button privacy'); ?></a></li>
								<li class="list-inline-item mr-4"><a href="<?php echo base_url('aml'); ?>"><?php echo lang('core button aml_policy'); ?></a></li>
								<li class="list-inline-item mr-4"><a href="<?php echo base_url('/developers'); ?>"><?php echo lang('core button developer'); ?></a></li>
							</ul>
						</div>
						<div class="col-md-4">
							<ul class="list-inline list-icons">
								<li class="list-inline-item mr-4"><a href="https://www.facebook.com/bankaero" target="_blank"><i class="fab fa-facebook-square fa-lg"></i></a></li>
								<li class="list-inline-item mr-4"><a href="https://twitter.com/bankaeroapp" target="_blank"><i class="fab fa-twitter-square fa-lg"></i></a></li>
								<li class="list-inline-item mr-4"><a href="https://www.instagram.com/bankaeroapp" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
								<li class="list-inline-item mr-4"><a href="https://t.me/bankaeroapp" target="_blank"><i class="fab fa-telegram-plane fa-lg"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

    </footer>

	 <!-- Placed at the end of the document so the pages load faster -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

	<?php // Javascript files?>
    <?php if (isset($js_files) && is_array($js_files)): ?>
        <?php foreach ($js_files as $js): ?>
            <?php if (!is_null($js)): ?>
                <?php $separator = (strstr($js, '?')) ? '&' : '?'; ?>
                <?php echo "\n"; ?><script type="text/javascript" src="<?php echo $js; ?><?php echo $separator; ?>v=<?php echo $this->settings->site_version; ?>"></script><?php echo "\n"; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($js_files_i18n) && is_array($js_files_i18n)): ?>
        <?php foreach ($js_files_i18n as $js): ?>
            <?php if (!is_null($js)): ?>
                <?php echo "\n"; ?><script type="text/javascript"><?php echo "\n".$js."\n"; ?></script><?php echo "\n"; ?>
            <?php endif; ?>
        <?php endforeach;?>
    <?php endif;?>

</body>
</html>

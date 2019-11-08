<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Default Public Template
 */
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="BANKAERO">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=<?php echo $this->settings->site_version; ?>">
	  <link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?php echo $this->settings->site_version; ?>">
    <title><?php echo $page_title; ?> - <?php echo $this->settings->site_name; ?></title>
		<meta name="keywords" content="<?php echo $this->settings->meta_keywords; ?>">
    <meta name="description" content="<?php echo $this->settings->meta_description; ?>">

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url();?>assets/themes/account/css/bootstrap.css" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="<?php echo base_url();?>assets/themes/account/css/escrow.css" rel="stylesheet">
		<link href="<?php echo base_url();?>assets/themes/account/css/countrySelect.css" rel="stylesheet">
        
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css">
        
        <link rel="stylesheet" href="<?php echo base_url();?>assets/js/select2/css/select2.min.css">
              
        
        
        <?php 
        //print("<pre>");print_r($this->uri->segments);die;
        if ($this->uri->segments[1] == 'account' && $this->uri->segments[2] == 'settings' && $this->uri->segments[3] == 'verification') { ?>
          <link href="<?php echo base_url();?>/assets/themes/escrow/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css" />
        <?php } else { ?>
          <link href="<?php echo base_url();?>assets/themes/account/css/datepicker.css" rel="stylesheet" type="text/css">
        <?php } ?>
        
          <link href="<?php echo base_url();?>assets/js/card/card.css" rel="stylesheet" type="text/css">
	
		<!-- FA -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
		
		
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5a9be2e82326af0013ae4037&product=inline-share-buttons"></script>
		<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
		<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>
		
		<?php /* <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> */?>
        <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
  
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
        
        <script src="/assets/js/select2/js/select2.min.js"></script>

        <script src="<?php echo base_url();?>assets/js/card/jquery.card.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-147704223-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-147704223-1');
</script>

</head>
	
	<?php // Notify invoice
		$info_invoices = $this->notice->sum_user_invoices($user['username']);
		if ($info_invoices > 0) {
			$sample_invoice = TRUE;
		} else {
			$sample_invoice = FALSE;
		}
	?>
	
	<?php // Notify support
		$info_support = $this->notice->sum_user_support($user['username']);
		if ($info_invoices > 0) {
			$sample_support = TRUE;
		} else {
			$sample_support = FALSE;
		}
	?>

    <?php // Notify disputes
		$info_disputes = $this->notice->sum_user_disputes($user['username']);
		if ($info_disputes > 0) {
			$sample_dispute = TRUE;
		} else {
			$sample_dispute = FALSE;
		}
	?>

<body>
    <script>
      $(document).ready(function(){
         $(document).on('click', '.change-viewbalance-currency', function(event) {
           
           var show_cur = $(this).attr('data-val');
           $.post( "/ajax/set_show_cur", { show_cur: show_cur })
          .done(function( data ) {
            location.reload(); 
          });
        });
      });
    </script>

	
	<nav class="navbar navbar-expand-md navbar-dark">
		<div class="container">
      <a class="navbar-brand" href="/">
				<img src="<?php echo base_url();?>assets/themes/account/img/main-logo.png" height="30" alt="">
			</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
<!--          <li class="nav-item <?php echo (uri_string() == '') ? 'active' : ''; ?>">
            <a class="nav-link" href="<?php echo base_url('#'); ?>"><?php echo lang('core button home'); ?></a>
          </li> -->
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
					<?php if ($this->session->userdata('logged_in')) : ?>
          <?php if ($this->user['is_admin']) : ?>
					<div class="nav-user">
						<a href="<?php echo base_url('admin'); ?>" class="my-2 my-sm-0 mr-3"><?php echo lang('core button admin'); ?></a>
					</div>
					<?php endif; ?>
					<div class="nav-user">
						<a href="<?php echo base_url('logout'); ?>" class="my-2 my-sm-0 mr-3"><?php echo lang('core button logout'); ?></a>
					</div>
					<a href="<?php echo base_url('account/transactions'); ?>" class="btn btn-success my-2 my-sm-0"><?php echo lang('users menu my_account'); ?></a>
					<!-- Language switcher user dashboard -->
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
					<!-- End language switcher user dashboard -->
					<?php else : ?>
          <a href="<?php echo base_url('login'); ?>" class="btn btn-outline-light my-2 my-sm-0 mr-3"><?php echo lang('core button sign_in'); ?></a>
          <a href="<?php echo base_url('user/register'); ?>" class="btn btn-success my-2 my-sm-0"><?php echo lang('core button create'); ?></a>
					<?php endif; ?>
        </div>
				
      </div>
		</div>
    </nav>
	
		<div class="header-st">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-sm-6 col-xs-6 mt-0">
						<div class="row">
							<div class="col-md-4">
								<small><?php echo lang('users balanve total'); ?></small>
            		<div class="dropdown">
									<button class="btn-ballance btn-link dropdown-toggle btn-block-head" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php 
                                        $view_b = $this->notice->user_view_balance($user['username']);
                                        //print_r($this->notice->user_view_balance($user['username']));
                                        if ($view_b == 0) {
                                          echo '<span id="current_balance" data-cur="debit_base">'.$this->notice->hold_balance($user['username'], "debit_base", 1).' '.$this->currencys->display->base_code; 
                                        } elseif ($view_b == 1) {
                                          echo '<span id="current_balance" data-cur="debit_extra1">'.$this->notice->hold_balance($user['username'], "debit_extra1", 1).' '.$this->currencys->display->extra1_code; 
                                        } elseif ($view_b == 2) {
                                          echo '<span id="current_balance" data-cur="debit_extra2">'.$this->notice->hold_balance($user['username'], "debit_extra2", 1).' '.$this->currencys->display->extra2_code; 
                                        } elseif ($view_b == 3) {
                                          echo '<span id="current_balance" data-cur="debit_extra3">'.$this->notice->hold_balance($user['username'], "debit_extra3", 1).' '.$this->currencys->display->extra3_code; 
                                        } elseif ($view_b == 4) {
                                          echo '<span id="current_balance" data-cur="debit_extra4">'.$this->notice->hold_balance($user['username'], "debit_extra4", 1).' '.$this->currencys->display->extra4_code; 
                                        } elseif ($view_b == 5) {
                                          echo '<span id="current_balance" data-cur="debit_extra5">'.$this->notice->hold_balance($user['username'], "debit_extra5", 1).' '.$this->currencys->display->extra5_code; 
                                        }
                                        
                                        ?></span>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?php 
                                        $cur_show = array();
                                        
                                        $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=0 id="avb_debit_base">'.$this->notice->hold_balance($user['username'], "debit_base", 1).' '.$this->currencys->display->base_code.'</a>';  
                                        
                                        if($this->currencys->display->extra1_check) {
                                            $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=1 id="avb_debit_extra1">'.$this->notice->hold_balance($user['username'], "debit_extra1", 1).' '.$this->currencys->display->extra1_code.'</a>';
                                          }
                                          if($this->currencys->display->extra2_check) {
                                            $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=2 id="avb_debit_extra2">'.$this->notice->hold_balance($user['username'], "debit_extra2", 1).' '.$this->currencys->display->extra2_code.'</a>';
                                          }
                                          if($this->currencys->display->extra3_check) {
                                            $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=3 id="avb_debit_extra3">'.$this->notice->hold_balance($user['username'], "debit_extra3", 1).' '.$this->currencys->display->extra3_code.'</a>';
                                          }
                                          if($this->currencys->display->extra4_check) {
                                            $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=4 id="avb_debit_extra4">'.$this->notice->hold_balance($user['username'], "debit_extra4", 1).' '.$this->currencys->display->extra4_code.'</a>';
                                          }
                                          if($this->currencys->display->extra5_check) {
                                            $cur_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=5 id="avb_debit_extra5">'.$this->notice->hold_balance($user['username'], "debit_extra5", 1).' '.$this->currencys->display->extra5_code.'</a>';
                                          }
                                          $exclude_val = $view_b;
                                          foreach ($cur_show as $val_id=>$string) {
                                            if ($val_id != $exclude_val) {
                                              print($string);
                                            }
                                          }

                                        /*
                                         * old code 
                                        if($this->currencys->display->extra1_check) : ?>
										<a class="dropdown-item change-viewbalance-currency" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra1", 1).' '.$this->currencys->display->extra1_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra2_check) : ?>
										<a class="dropdown-item change-viewbalance-currency" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra2", 1).' '.$this->currencys->display->extra2_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra3_check) : ?>
										<a class="dropdown-item change-viewbalance-currency" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra3", 1).' '.$this->currencys->display->extra3_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra4_check) : ?>
										<a class="dropdown-item change-viewbalance-currency" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra4", 1).' '.$this->currencys->display->extra4_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra5_check) : ?>
										<a class="dropdown-item change-viewbalance-currency" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra5", 1).' '.$this->currencys->display->extra5_code ?></a>
										<?php endif;
                                         * 
                                         */ ?>
									</div>
								</div>
							</div>
							<div class="col-md-4" style="margin-left: 25px;">
								<small><?php echo lang('users balanve hold'); ?></small>
            		<div class="dropdown">
									<button class="btn-ballance btn-link dropdown-toggle btn-block-head" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php 
                                        
                                        //dump_exit($this->currencys->display->base_code);
                                        
                                        $view_b = $this->notice->user_view_balance($user['username']);
                                        //print($temp);
                                        if ($view_b == 0) {
                                          echo $this->notice->hold_balance($user['username'], "debit_base", 2).' '.$this->currencys->display->base_code;
                                        } elseif ($view_b == 1) {
                                          echo $this->notice->hold_balance($user['username'], "debit_extra1", 2).' '.$this->currencys->display->extra1_code; 
                                        } elseif ($view_b == 2) {
                                          echo $this->notice->hold_balance($user['username'], "debit_extra2", 2).' '.$this->currencys->display->extra2_code; 
                                        } elseif ($view_b == 3) {
                                          echo $this->notice->hold_balance($user['username'], "debit_extra3", 2).' '.$this->currencys->display->extra3_code; 
                                        } elseif ($view_b == 4) {
                                          echo $this->notice->hold_balance($user['username'], "debit_extra4", 2).' '.$this->currencys->display->extra4_code; 
                                        } elseif ($view_b == 5) {
                                          echo $this->notice->hold_balance($user['username'], "debit_extra5", 2).' '.$this->currencys->display->extra5_code; 
                                        }
                                       //echo $this->notice->hold_balance($user['username'], "debit_base", 2).' '.echo $this->currencys->display->base_code; ?>
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?Php 
                                        $cur_hold_show = array();
                                        
                                        $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=0>'.$this->notice->hold_balance($user['username'], "debit_base", 2).' '.$this->currencys->display->base_code.'</a>';  
                                        
                                        if($this->currencys->display->extra1_check) {
                                            $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=1>'.$this->notice->hold_balance($user['username'], "debit_extra1", 2).' '.$this->currencys->display->extra1_code.'</a>';
                                          }
                                          if($this->currencys->display->extra2_check) {
                                            $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=2>'.$this->notice->hold_balance($user['username'], "debit_extra2", 2).' '.$this->currencys->display->extra2_code.'</a>';
                                          }
                                          if($this->currencys->display->extra3_check) {
                                            $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=3>'.$this->notice->hold_balance($user['username'], "debit_extra3", 2).' '.$this->currencys->display->extra3_code.'</a>';
                                          }
                                          if($this->currencys->display->extra4_check) {
                                            $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=4>'.$this->notice->hold_balance($user['username'], "debit_extra4", 2).' '.$this->currencys->display->extra4_code.'</a>';
                                          }
                                          if($this->currencys->display->extra5_check) {
                                            $cur_hold_show[] = '<a class="dropdown-item change-viewbalance-currency" href="#" data-val=5>'.number_format($this->notice->hold_balance($user['username'], "debit_extra5", 2), 8, '.', '').' '.$this->currencys->display->extra5_code.'</a>';
                                          }
                                          $exclude_val = $view_b;
                                          foreach ($cur_hold_show as $val_id=>$string) {
                                            if ($val_id != $exclude_val) {
                                              print($string);
                                            }
                                          }
                                        ?>
										<?php 
                                        /*
                                        if($this->currencys->display->extra1_check) : ?>
										<a class="dropdown-item" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra1", 2); ?> <?php echo $this->currencys->display->extra1_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra2_check) : ?>
										<a class="dropdown-item" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra2", 2); ?> <?php echo $this->currencys->display->extra2_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra3_check) : ?>
										<a class="dropdown-item" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra3", 2); ?> <?php echo $this->currencys->display->extra3_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra4_check) : ?>
										<a class="dropdown-item" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra4", 2); ?> <?php echo $this->currencys->display->extra4_code ?></a>
										<?php endif; ?>
										<?php if($this->currencys->display->extra5_check) : ?>
										<a class="dropdown-item" href="#"><?php echo $this->notice->hold_balance($user['username'], "debit_extra5", 2); ?> <?php echo $this->currencys->display->extra5_code ?></a>
										<?php endif; */ ?>
									</div>
								</div>
							</div>
						</div>

					</div>
					
					<div class="col-md-4 col-sm-4 col-xs-4 text-right">
						<div class="btn-group header-bar" role="group" aria-label="Deposit">
							<a href="<?php echo base_url('/account/deposit'); ?>" class="btn btn-outline-light btn-lg min-width-120"><?php echo lang('users dashboard deposit'); ?></a>
							<a href="<?php echo base_url('account/withdrawal'); ?>" class="btn btn-outline-light btn-lg min-width-120"><?php echo lang('users dashboard withdrawal'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>

    <?php // Main body ?>
	<main>
    <div class="container theme-showcase" role="main">
			<div class="row">
				<div class="col-md-6 mt-4">
					<h3><?php echo $page_header; ?></h3>
				</div>
				<div class="col-md-6 mt-4 text-right">
					<div class="btn-group" role="group" aria-label="Basic example">
						<a href="<?php echo base_url('/account/orders'); ?>" class="btn btn-light"><i class="icon-badge icons"></i> <?php echo lang('users orders title'); ?></a>
						<a href="<?php echo base_url('/account/cart'); ?>" class="btn btn-light"><i class="icon-basket icons"></i> <?php echo lang('users cart title'); ?> <span class="badge badge-pill badge-danger"><?php echo $this->notice->sum_items_cart($user['username']); ?></span></a>
					</div>
				</div>
				<div class="col-md-12">
				<hr>
				</div>
				<div class="col-md-3 mt-3">
					<div class="list-group">
                        <a href="<?php echo base_url('account/cryptowallets'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/cryptowallets') ? 'active' : ''; ?>">
							<?php echo lang('users title cryptowallets'); ?><span class="text-right"><i class="fab fa-bitcoin"></i></i>
						</a>
						<a href="<?php echo base_url('account/transactions'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/transactions') ? 'active' : ''; ?>">
							<?php echo lang('users title history'); ?><span class="text-right"><i class="icon-hourglass icons"></i>
						</a>
						<a href="<?php echo base_url('account/money_transfer'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/money_transfer') ? 'active' : ''; ?>">
							<?php echo lang('users menu transfer'); ?><span class="text-right"><i class="icon-paper-plane icons"></i>
						</a>
							<a href="<?php echo base_url('account/shops'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/shops') ? 'active' : ''; ?>">
							<?php echo lang('users shops title'); ?><span class="text-right"><i class="icon-handbag icons"></i>
						  </a>
						 <a href="<?php echo base_url('account/exchange'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/exchange') ? 'active' : ''; ?>">
							<?php echo lang('users menu exchange'); ?><span class="text-right"><i class="icon-refresh icons"></i>
						 </a>
							<a href="<?php echo base_url('account/invoices'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/invoices') ? 'active' : ''; ?>">
								<?php echo lang('users invoices menu'); ?> <span class="text-right">
								<?if($info_invoices == TRUE){?>
								<span class="notification-badge">
									<?php echo $this->notice->sum_user_invoices($user['username']); ?>
								</span>
								<?}else{?>
								<i class="icon-credit-card icons"></i>
  							<?}?>
							</a>
							<a href="<?php echo base_url('account/vouchers'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/vouchers') ? 'active' : ''; ?>">
							<?php echo lang('users vouchers menu'); ?><span class="text-right"><i class="icon-diamond icons"></i>
							</a>
							<a href="<?php echo base_url('account/disputes'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/disputes') ? 'active' : ''; ?>">
							<?php echo lang('users menu dispute'); ?><span class="text-right">
                                <?if($info_disputes == TRUE){?>
								<span class="notification-badge">
									<?php echo $this->notice->sum_user_disputes($user['username']); ?>
								</span>
								<?}else{?>
								<i class="icon-shield icons"></i>
  							<?}?>
                                
                                
							</a>
							<a href="<?php echo base_url('account/merchants'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/merchants') ? 'active' : ''; ?>">
							<?php echo lang('users shops merchant'); ?><span class="text-right"><i class="icon-basket icons"></i>
							</a>
							<a href="<?php echo base_url('account/support'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/support') ? 'active' : ''; ?>">
								<?php echo lang('users support title_1'); ?> <span class="text-right">
								<?if($info_support == TRUE){?>
								<span class="notification-badge">
									<?php echo $this->notice->sum_user_support($user['username']); ?>
								</span>
								<?}else{?>
								<i class="icon-support icons"></i>
  							<?}?>
						</a>
						<a href="<?php echo base_url('account/settings'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/settings') ? 'active' : ''; ?>">
							<?php echo lang('users settings title'); ?><span class="text-right"><i class="icon-settings icons"></i>
						</a>
                        <a href="<?php echo base_url('account/referrals'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/referal') ? 'active' : ''; ?>">
							<?php echo lang('users settings referal'); ?><span class="text-right"><i class="fas fa-users"></i>
						</a>
                        <a href="<?php echo base_url('account/limits'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/limits') ? 'active' : ''; ?>">
							<?php echo lang('users settings limits'); ?><span class="text-right"><i class="fas fa-sliders-h"></i>
						</a>
                        <?php 
                        $cards_section = $this->settings_model->get_cards_section_vis();
                        if ($cards_section == 1) {
                        ?>
                        <a href="<?php echo base_url('account/cards'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/cards') ? 'active' : ''; ?>">
							<?php echo lang('users settings cards'); ?><span class="text-right"><i class="far fa-credit-card"></i>
						</a>
                        <?php } ?>
                        <a href="<?php echo base_url('account/vaults'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/vaults') ? 'active' : ''; ?>">
							<?php echo lang('users settings vaults'); ?><span class="text-right"><i class="fas fa-calendar-alt"></i>
						</a>
                        
                        <a href="<?php echo base_url('account/credits'); ?>" class="list-group-item  d-flex justify-content-between align-items-center list-group-item-action <?php echo (uri_string() == 'account/credits') ? 'active' : ''; ?>">
							<?php echo lang('users settings credits'); ?><span class="text-right"><i class="fas fa-dollar-sign"></i>
						</a>
					</div>
				</div>
				<div class="col-md-9 mt-3">
						<?php // System messages ?>
						<?php if ($this->session->flashdata('message')) : ?>
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<?php echo $this->session->flashdata('message'); ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php elseif ($this->session->flashdata('error')) : ?>
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									<?php echo $this->session->flashdata('error'); ?>
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
						<?php elseif (validation_errors()) : ?>
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									<?php echo validation_errors(); ?>
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
						<?php elseif ($this->error) : ?>
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									 <?php echo $this->error; ?>
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
						<?php endif; ?>
						<?php // Main content ?>
        		<?php echo $content; ?>
				</div>
			</div>
        

    </div>
	</main>
								

    <?php // Footer ?>
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
	
    
    
	 <!-- Placed at the end of the document so the pages load faster 
	
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
<script src="<?php echo base_url();?>assets/themes/account/js/countrySelect.min.js"></script>

  
  
<script>
  $("#country").countrySelect();
</script>

<script>
	var position = 0;
	var scrollHeight = Math.max(
  document.body.scrollHeight, document.documentElement.scrollHeight,
  document.body.offsetHeight, document.documentElement.offsetHeight,
  document.body.clientHeight, document.documentElement.clientHeight
);

$(window).scroll(function(e) {
  var $element = $('.header-st');
  var scrollTop = $(this).scrollTop();
  if( scrollTop <= 10 ) { 
    $element.removeClass('hide').removeClass('scrolling');
  } else if( scrollTop < position ) {
    $element.removeClass('hide');
  } else if( scrollTop > position && scrollHeight > 1000) {
    $element.addClass('scrolling');
    if( scrollTop + $(window).height() >=  $(document).height() - $element.height() ){
      $element.removeClass('hide');
    } else if(Math.abs($element.position().top) < $element.height()) {
      $element.addClass('hide');
    }
  }
  position = scrollTop;
})			
</script>
-->	
							
	

<script>
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
<script src="<?php echo base_url();?>assets/themes/account/js/countrySelect.min.js"></script>
<script>
  $("#country").countrySelect();
	//autoselect country by ip
	$("#country").countrySelect("selectCountry", "<?php print($user_country); ?>");

<?php if ($this->uri->segments[1] != 'account' && $this->uri->segments[2] != 'settings' ) { ?>
$("#phone").val('<?php if ($return['phone'] == '') { print($country_phonecode); } else { print $return['phone']; } ?>');
<?php } ?>
</script>
<?php 
if ($this->uri->segments[1] == 'account' && $this->uri->segments[2] == 'settings' && $this->uri->segments[3] == 'verification') { ?>

<?php } ?>
<script src="<?php echo base_url();?>assets/themes/account/js/datepicker.js"></script>
<script>

$.fn.datepicker.language['ru'] = {days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    months: ['January','February','March','April','May','June', 'July','August','September','October','November','December'],
    monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    today: 'Today',
    clear: 'Clear',
    dateFormat: 'yyyy-mm-dd',
    timeFormat: 'hh:ii',
    firstDay: 0}
</script>

<?php // Javascript files ?>
    <?php if (isset($js_files) && is_array($js_files)) : ?>
        <?php foreach ($js_files as $js) : ?>
            <?php if ( ! is_null($js)) : ?>
                <?php $separator = (strstr($js, '?')) ? '&' : '?'; ?>
                <?php echo "\n"; ?><script type="text/javascript" src="<?php echo $js; ?><?php echo $separator; ?>v=<?php echo $this->settings->site_version; ?>"></script><?php echo "\n"; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($js_files_i18n) && is_array($js_files_i18n)) : ?>
        <?php foreach ($js_files_i18n as $js) : ?>
            <?php if ( ! is_null($js)) : ?>
                <?php echo "\n"; ?><script type="text/javascript"><?php echo "\n" . $js . "\n"; ?></script><?php echo "\n"; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

         
   
</body>
</html>

<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/third_party/yandex/api.php';
require_once APPPATH . '/third_party/yandex/external_payment.php';

use \YandexMoney\API;

class Deposit extends Private_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // load the language file
        $this->lang->load('users');

        // load the users model
        $this->load->model('users_model');
        $this->load->model('transactions_model');
        $this->load->model('cryptapi_model');
	    $this->load->library('fixer');
        
        
    }
  
    function epay() {
      print('Epay1 page');
      if ($this->input->post()) {
        $data = $this->input->post();
        //print_r($data);
        $json = json_encode($data);
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `temp` (`id`,`method`, `datetime`, `data`) VALUES (NULL, 'epay', '".$date."', '".$json."');";
        $this->db->query($sql);
      }
    }
    
    function epay2() {
      
      $data2['success'] = $_POST["STATUS"];
      $data2['invoiceId'] = $_POST["PAYMENT_ID"];
      $data2['transactionId'] = $_POST["ORDER_NUM"];
      $data2['paymentAmount'] = $_POST["PAYMENT_AMOUNT"];
      $data2['paymentFee'] = 0;//$_POST["x_fee"];
      $data2['hash'] = $_POST["V2_HASH"];
      
      $json = json_encode($data2);
      $date = date("Y-m-d H:i:s");
      $sql = "INSERT INTO `temp` (`id`,`method`, `datetime`, `data`) VALUES (NULL, 'epay2', '".$date."', '".$json."');";
      $this->db->query($sql);
      
      //print('Epay2 page');
      if ($this->input->post()) {
        $data = $this->input->post();
        //print_r($data);
        $json = json_encode($data);
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `temp` (`id`,`method`, `datetime`, `data`) VALUES (NULL, 'epay2', '".$date."', '".$json."');";
        $this->db->query($sql);
      }
      
      if ($this->input->get()) {
        $data = $this->input->get();
        //print_r($data);
        $json = json_encode($data);
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `temp` (`id`,`method`, `datetime`, `data`) VALUES (NULL, 'epay2', '".$date."', '".$json."');";
        $this->db->query($sql);
      }
      
      //update transaction 
      
      //update wallet
      //print("<pre>");
      //print_r($this->session->userdata['logged_in']);
      //print("<pre>");
      //print_r($_SERVER);
      if (isset($_SERVER['HTTP_ORIGIN'])) {
        if ($_SERVER['HTTP_ORIGIN'] == 'https://api.epay.com') {
          //add balance 
          $this->transactions_model->temp_epay($this->session->userdata['logged_in']['id'], $this->session->userdata['logged_in']['username']);
          
          
          
        }
      }
      
      redirect('/account/transactions', 'refresh');
    }
    
    function epay3() {
      print('Epay3 page');
      if ($this->input->post()) {
        $data = $this->input->post();
        //print_r($data);
        $json = json_encode($data);
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `temp` (`id`,`method`, `datetime`, `data`) VALUES (NULL, 'epay3', '".$date."', '".$json."');";
        $this->db->query($sql);
      }
    }
    
    /**
	*  Main page
    */
	function index()
	{
		// setup page header data
    $this->set_title(sprintf(lang('users dashboard deposit'), $this->settings->site_name));
		// reload the new user data and store in session
    $user = $this->users_model->get_user($this->user['id']);
    
    $paypal = $this->settings_model->get_dep_method(1);
		$perfect_m = $this->settings_model->get_dep_method(2);
		$advcash = $this->settings_model->get_dep_method(3);
		$payeer = $this->settings_model->get_dep_method(4);
		$skrill = $this->settings_model->get_dep_method(5);
		$paygol = $this->settings_model->get_dep_method(6);
		$swift = $this->settings_model->get_dep_method(7);
		$yandex = $this->settings_model->get_dep_method(8);
		$epay = $this->settings_model->get_dep_method(9);
		$blockchain = $this->settings_model->get_dep_method(10);
    
    if ($paypal['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_paypal = TRUE;
		} elseif ($paypal['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_paypal = TRUE;
		} elseif ($paypal['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_paypal = TRUE;
		} else {
			$enabled_paypal = FALSE;
		}
		
		// Check enabled method Perfect Money
		if ($perfect_m['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_perfect_m = TRUE;
		} elseif ($perfect_m['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_perfect_m = TRUE;
		} elseif ($perfect_m['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_perfect_m = TRUE;
		} else {
			$enabled_perfect_m = FALSE;
		}
		
		// Check enabled method Advcash
		if ($advcash['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_advcash = TRUE;
		} elseif ($advcash['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_advcash = TRUE;
		} elseif ($advcash['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_advcash = TRUE;
		} else {
			$enabled_advcash = FALSE;
		}
		
		// Check enabled method Payeer
		if ($payeer['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_payeer = TRUE;
		} elseif ($payeer['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_payeer = TRUE;
		} elseif ($payeer['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_payeer = TRUE;
		} else {
			$enabled_payeer = FALSE;
		}
		
		// Check enabled method Skrill
		if ($skrill['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_skrill = TRUE;
		} elseif ($skrill['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_skrill = TRUE;
		} elseif ($skrill['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_skrill = TRUE;
		} else {
			$enabled_skrill = FALSE;
		}
		
		// Check enabled method Paygol
		if ($paygol['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_paygol = TRUE;
		} elseif ($paygol['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_paygol = TRUE;
		} elseif ($paygol['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_paygol = TRUE;
		} else {
			$enabled_paygol = FALSE;
		}
		
		// Check enabled method SWIFT
		if ($swift['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_swift = TRUE;
		} elseif ($swift['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_swift = TRUE;
		} elseif ($swift['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_swift = TRUE;
		} else {
			$enabled_swift = FALSE;
		}
		
		// Check enabled method Local Bank
		if ($local_bank['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_local_bank = TRUE;
		} elseif ($local_bank['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_local_bank = TRUE;
		} elseif ($local_bank['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_local_bank = TRUE;
		} else {
			$enabled_local_bank = FALSE;
		}
		
		// Check enabled method EPAY
		if ($epay['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_epay = TRUE;
		} elseif ($epay['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_epay = TRUE;
		} elseif ($epay['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_epay = TRUE;
		} else {
			$enabled_epay = FALSE;
		}
        
        // Check enabled method EPAY
		if ($yandex['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_yandex = TRUE;
		} elseif ($yandex['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_yandex = TRUE;
		} elseif ($yandex['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_yandex = TRUE;
		} else {
			$enabled_yandex = FALSE;
		}
		
		// Check enabled method Coinpayments
		if ($blockchain['start_verify'] == "1" && $user['verify_status'] == 0) {
			$enabled_blockchain = TRUE;
		} elseif ($blockchain['standart_verify'] == "1" && $user['verify_status'] == 1) {
			$enabled_blockchain = TRUE;
		} elseif ($blockchain['expanded_verify'] == "1" && $user['verify_status'] == 2) {
			$enabled_blockchain = TRUE;
		} else {
			$enabled_blockchain = FALSE;
		}
    
    $data = $this->includes;
    
    // set content data
    $content_data = array(
			'user'    => $user,
      'paypal'            => $paypal,
			'enabled_paypal'    => $enabled_paypal,
			'perfect_m'         => $perfect_m,
			'enabled_perfect_m' => $enabled_perfect_m,
			'advcash'           => $advcash,
			'enabled_advcash'   => $enabled_advcash,
			'payeer'            => $payeer,
			'enabled_payeer'    => $enabled_payeer,
			'skrill'             => $skrill,
			'enabled_skrill'     => $enabled_skrill,
			'paygol'             => $paygol,
			'enabled_paygol'     => $enabled_paygol,
			'swift'             => $swift,
			'enabled_swift'     => $enabled_swift,
			'local_bank'             => $local_bank,
			'enabled_local_bank'     => $enabled_local_bank,
			'epay'             => $epay,
			'enabled_epay'     => $enabled_epay,
			'blockchain'             => $blockchain,
			'enabled_blockchain'     => $enabled_blockchain,
            'yandex' => $yandex,
            'enabled_yandex' => $enabled_yandex,
    );
    
    // load views
    $data['content'] = $this->load->view('account/deposit/index', $content_data, TRUE);
		$this->load->view($this->template, $data);
    
  }
  
  function test() {
    
    //$client_id = "Bankaero.com";
    $app_id = '9FDB8D34616031D1BE8EDF2EEBD260C887AFBD8820F84DAEA9D606944C523178';
    
    $scope[] = "account-info";
    //$scope[] = "operation-details";
    $auth_url = API::buildObtainTokenUrl($app_id, '', $scope);
    print($auth_url);
  }
	
	function confirm()
	{
        //print_r($this->input->post());die;
      
      
		$user = $this->users_model->get_user($this->user['id']);
		
		$paypal = $this->settings_model->get_dep_method(1);
		$perfect_m = $this->settings_model->get_dep_method(2);
		$advcash = $this->settings_model->get_dep_method(3);
		$payeer = $this->settings_model->get_dep_method(4);
		$skrill = $this->settings_model->get_dep_method(5);
		$paygol = $this->settings_model->get_dep_method(6);
		$swift = $this->settings_model->get_dep_method(7);
		$yandex = $this->settings_model->get_dep_method(8);
        //dump_exit($yandex);
		$epay = $this->settings_model->get_dep_method(9);
		$blockchain = $this->settings_model->get_dep_method(10);
		
		$this->form_validation->set_rules('amount', lang('users transfer amount'), 'required|trim|numeric');
		$this->form_validation->set_rules('method', lang('users withdrawal method'), 'required|trim|in_list[paypal,perfect_m,advcash,payeer,skrill,paygol,swift,local_bank,epay,blockchain,yandex]');
		$this->form_validation->set_rules('currency', lang('users trans cyr'), 'required|trim|in_list[debit_base,debit_extra1,debit_extra2,debit_extra3,debit_extra4,debit_extra5]');
		
        
		if ($this->form_validation->run() == FALSE)
		{
          //echo validation_errors();die;
          
		  if (!isset($this->input->post()['method'])) {
				$this->session->set_flashdata('error', lang('users withdrawal error_0 deposit'));
			} else {
				$this->session->set_flashdata('error', lang('users withdrawal error_1'));	
			}
			redirect(site_url("account/deposit"));

		} else {
		  
          
          //CHECK LIMITS
          $limit_check = $this->users_model->check_user_limit($this->user['id'], $this->input->post("currency", TRUE), $this->input->post("amount", TRUE));
          
          //print_r($limit_check);die;
          
          if ($limit_check['status'] == 0) {
            $this->session->set_flashdata('error', lang('users limits exceeded'));
            redirect(site_url("account/deposit"));
          }
          
          $currency = $this->input->post("currency", TRUE);
		  $method = $this->input->post("method", TRUE);
          
          if ($method == 'blockchain') {
            $amount = number_format($this->input->post("amount", TRUE), 8, '.', '');
          } else {
            $amount = number_format($this->input->post("amount", TRUE), 2, '.', '');
          }

            $wallet_data = array();
            $wallet_data['count'] = 0;
          
			if ($method == "paypal") {
				
				$method = $paypal['name'];
				$fee = $paypal['fee'];
				$fee_fix = $paypal['fee_fix'];
				$account = $user['paypal'];
				$terms = $paypal['terms'];
				$minimum = $paypal['minimum_'.$currency.''];
				$maximum = $paypal['maximum_'.$currency.''];
				$code_method = "paypal";
				
				// check verify level
				if ($paypal['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($paypal['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($paypal['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $paypal['debit_base'] == "1") {
					$merchant_account = $paypal['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $paypal['debit_extra1'] == "1") {
					$merchant_account = $paypal['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $paypal['debit_extra2']) {
					$merchant_account = $paypal['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $paypal['debit_extra3']) {
					$merchant_account = $paypal['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $paypal['debit_extra4']) {
					$merchant_account = $paypal['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $paypal['debit_extra5']) {
					$merchant_account = $paypal['ac_debit_extra5'];
				} else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "perfect_m") {
				
				$method = $perfect_m['name'];
				$fee = $perfect_m['fee'];
				$fee_fix = $perfect_m['fee_fix'];
				$account = $user['perfect_m'];
				$minimum = $perfect_m['minimum_'.$currency.''];
				$maximum = $perfect_m['maximum_'.$currency.''];
				$code_method = "perfect_m";
				
				if ($perfect_m['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($perfect_m['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($perfect_m['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $perfect_m['debit_base'] == "1") {
					$merchant_account = $perfect_m['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $perfect_m['debit_extra1'] == "1") {
					$merchant_account = $perfect_m['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $perfect_m['debit_extra2']) {
					$merchant_account = $perfect_m['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $perfect_m['debit_extra3']) {
					$merchant_account = $perfect_m['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $perfect_m['debit_extra4']) {
					$merchant_account = $perfect_m['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $perfect_m['debit_extra5']) {
					$merchant_account = $perfect_m['ac_debit_extra5'];
				} else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "advcash") {
				
				$method = $advcash['name'];
				$fee = $advcash['fee'];
				$fee_fix = $advcash['fee_fix'];
				$minimum = $advcash['minimum_'.$currency.''];
				$maximum = $advcash['maximum_'.$currency.''];
				$code_method = "advcash";
				
				if ($advcash['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($advcash['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($advcash['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $advcash['debit_base'] == "1") {
					$merchant_account = $advcash['ac_debit_base'];
					$symbol = $this->currencys->display->base_code;
				} elseif ($currency == "debit_extra1" && $advcash['debit_extra1'] == "1") {
					$merchant_account = $advcash['ac_debit_extra1'];
					$symbol = $this->currencys->display->extra1_code;
				} elseif ($currency == "debit_extra2" && $advcash['debit_extra2'] == "1") {
					$merchant_account = $advcash['ac_debit_extra2'];
					$symbol = $this->currencys->display->extra2_code;
				} elseif ($currency == "debit_extra3" && $advcash['debit_extra3'] == "1") {
					$merchant_account = $advcash['ac_debit_extra3'];
					$symbol = $this->currencys->display->extra3_code;
				} elseif ($currency =="debit_extra4" && $advcash['debit_extra4'] == "1") {
					$merchant_account = $advcash['ac_debit_extra4'];
					$symbol = $this->currencys->display->extra4_code;
				} elseif ($currency =="debit_extra5" && $advcash['debit_extra5'] == "1") {
					$merchant_account = $advcash['ac_debit_extra5'];
					$symbol = $this->currencys->display->extra5_code;
				} else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "payeer") {
				
				$method = $payeer['name'];
				$fee = $payeer['fee'];
				$fee_fix = $payeer['fee_fix'];
				$minimum = $payeer['minimum_'.$currency.''];
				$maximum = $payeer['maximum_'.$currency.''];
				$code_method = "payeer";
				
				if ($payeer['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($payeer['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($payeer['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $payeer['debit_base'] == "1") {
					$merchant_account = $payeer['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $payeer['debit_extra1'] == "1") {
					$merchant_account = $payeer['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $payeer['debit_extra2'] == "1") {
					$merchant_account = $payeer['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $payeer['debit_extra3'] == "1") {
					$merchant_account = $payeer['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $payeer['debit_extra4'] == "1") {
					$merchant_account = $payeer['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $payeer['debit_extra5'] == "1") {
					$merchant_account = $payeer['ac_debit_extra5'];
				} else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "skrill") {
				
				$method = $skrill['name'];
				$fee = $skrill['fee'];
				$fee_fix = $skrill['fee_fix'];
				$minimum = $skrill['minimum_'.$currency.''];
				$maximum = $skrill['maximum_'.$currency.''];
				$code_method = "skrill";
				
				if ($skrill['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($skrill['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($skrill['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $skrill['debit_base'] == "1") {
					$merchant_account = $skrill['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $skrill['debit_extra1'] == "1") {
					$merchant_account = $skrill['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $skrill['debit_extra2'] == "1") {
					$merchant_account = $skrill['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $skrill['debit_extra3'] == "1") {
					$merchant_account = $skrill['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $skrill['debit_extra4'] == "1") {
					$merchant_account = $skrill['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $skrill['debit_extra5'] == "1") {
					$merchant_account = $skrill['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "paygol") {
				
				$method = $paygol['name'];
				$fee = $paygol['fee'];
				$fee_fix = $paygol['fee_fix'];
				$minimum = $paygol['minimum_'.$currency.''];
				$maximum = $paygol['maximum_'.$currency.''];
				$code_method = "paygol";
				
				if ($paygol['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($paygol['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($paygol['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $paygol['debit_base'] == "1") {
					$merchant_account = $paygol['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $paygol['debit_extra1'] == "1") {
					$merchant_account = $paygol['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $paygol['debit_extra2'] == "1") {
					$merchant_account = $paygol['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $paygol['debit_extra3'] == "1") {
					$merchant_account = $paygol['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $paygol['debit_extra4'] == "1") {
					$merchant_account = $paygol['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $paygol['debit_extra5'] == "1") {
					$merchant_account = $paygol['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "swift") {
				
				$method = $swift['name'];
				$fee = $swift['fee'];
				$fee_fix = $swift['fee_fix'];
				$minimum = $swift['minimum_'.$currency.''];
				$maximum = $swift['maximum_'.$currency.''];
				$code_method = "swift";
				
				if ($swift['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($swift['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($swift['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $swift['debit_base'] == "1") {
					$merchant_account = $swift['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $swift['debit_extra1'] == "1") {
					$merchant_account = $swift['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $swift['debit_extra2'] == "1") {
					$merchant_account = $swift['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $swift['debit_extra3'] == "1") {
					$merchant_account = $swift['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $swift['debit_extra4'] == "1") {
					$merchant_account = $swift['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $swift['debit_extra5'] == "1") {
					$merchant_account = $swift['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "local_bank") {
				
				$method = $local_bank['name'];
				$fee = $local_bank['fee'];
				$fee_fix = $local_bank['fee_fix'];
				$minimum = $local_bank['minimum_'.$currency.''];
				$maximum = $local_bank['maximum_'.$currency.''];
				$code_method = "local_bank";
				
				if ($local_bank['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($local_bank['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($local_bank['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $local_bank['debit_base'] == "1") {
					$merchant_account = $local_bank['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $local_bank['debit_extra1'] == "1") {
					$merchant_account = $local_bank['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $local_bank['debit_extra2'] == "1") {
					$merchant_account = $local_bank['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $local_bank['debit_extra3'] == "1") {
					$merchant_account = $local_bank['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $local_bank['debit_extra4'] == "1") {
					$merchant_account = $local_bank['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $local_bank['debit_extra5'] == "1") {
					$merchant_account = $local_bank['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			} elseif ($method == "epay") {
				
                //print("<pre>");
                //print_r($epay);die;
              
				$method = $epay['name'];
				$fee = $epay['fee'];
				$fee_fix = $epay['fee_fix'];
				$minimum = $epay['minimum_'.$currency.''];
				$maximum = $epay['maximum_'.$currency.''];
				$code_method = "epay";
				
				if ($epay['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($epay['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($epay['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $epay['debit_base'] == "1") {
					$merchant_account = $epay['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $epay['debit_extra1'] == "1") {
					$merchant_account = $epay['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $epay['debit_extra2'] == "1") {
					$merchant_account = $epay['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $epay['debit_extra3'] == "1") {
					$merchant_account = $epay['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $epay['debit_extra4'] == "1") {
					$merchant_account = $epay['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $epay['debit_extra5'] == "1") {
					$merchant_account = $epay['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
                
                
				
			} 
            
            elseif ($method == "yandex") {
				
                //print("<pre>");
                //print_r($epay);die;
              
				//$method  = $yandex['name'];
                $method = 'yandex';
				$fee     = $yandex['fee'];
				$fee_fix = $yandex['fee_fix'];
				$minimum = $yandex['minimum_'.$currency.''];
				$maximum = $yandex['maximum_'.$currency.''];
				$code_method = "yandex";
				
				if ($yandex['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($yandex['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($yandex['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $yandex['debit_base'] == "1") {
					$merchant_account = $yandex['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $yandex['debit_extra1'] == "1") {
					$merchant_account = $yandex['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $yandex['debit_extra2'] == "1") {
					$merchant_account = $yandex['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $yandex['debit_extra3'] == "1") {
					$merchant_account = $yandex['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $yandex['debit_extra4'] == "1") {
					$merchant_account = $yandex['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $yandex['debit_extra5'] == "1") {
					$merchant_account = $yandex['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
                
                //dump_exit($method);
                
                
				
			}
            
            elseif ($method == "blockchain") {
              
               $wallet_data = $this->cryptapi_model->get_user_wallets($this->user['id']);
				
				$method = $blockchain['name'];
				$fee = $blockchain['fee'];
				$fee_fix = $blockchain['fee_fix'];
				$minimum = $blockchain['minimum_'.$currency.''];
				$maximum = $blockchain['maximum_'.$currency.''];
				$code_method = "blockchain";
				
				if ($blockchain['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($blockchain['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($blockchain['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}
				
				// Check currency and account for receiving deposits
				if ($currency == "debit_base" && $blockchain['debit_base'] == "1") {
					$merchant_account = $blockchain['ac_debit_base'];
				} elseif ($currency == "debit_extra1" && $blockchain['debit_extra1'] == "1") {
					$merchant_account = $blockchain['ac_debit_extra1'];
				} elseif ($currency == "debit_extra2" && $blockchain['debit_extra2'] == "1") {
					$merchant_account = $blockchain['ac_debit_extra2'];
				} elseif ($currency == "debit_extra3" && $blockchain['debit_extra3'] == "1") {
					$merchant_account = $blockchain['ac_debit_extra3'];
				} elseif ($currency =="debit_extra4" && $blockchain['debit_extra4'] == "1") {
					$merchant_account = $blockchain['ac_debit_extra4'];
				} elseif ($currency =="debit_extra5" && $blockchain['debit_extra5'] == "1") {
					$merchant_account = $blockchain['ac_debit_extra5'];
				}	else {
					
					$this->session->set_flashdata('error', lang('users deposit error_5'));
					redirect(site_url("account/deposit"));
					
				}
				
			}
			
			// Check currency
			if ($currency == "debit_base") {
				$symbol = $this->currencys->display->base_code;
			} elseif ($currency == "debit_extra1") {
				$symbol = $this->currencys->display->extra1_code;
			} elseif ($currency == "debit_extra2") {
				$symbol = $this->currencys->display->extra2_code;
			} elseif ($currency == "debit_extra3") {
				$symbol = $this->currencys->display->extra3_code;
			} elseif ($currency =="debit_extra4") {
				$symbol = $this->currencys->display->extra4_code;
			} elseif ($currency =="debit_extra5") {
				$symbol = $this->currencys->display->extra5_code;
			}
			
			// Calculation of the commission and total sum
			$percent = $fee/"100";
			$percent_fee = $amount * $percent;
			$total_fee_calc = $percent_fee + $fee_fix;
            
            if ($currency == 'debit_extra5') {
              $total_fee = number_format($total_fee_calc, 8, '.', '');
            } else {
              $total_fee = number_format($total_fee_calc, 2, '.', '');
            }
            
			
			$total_amount_calc = $amount + $total_fee;
            
            if ($currency == 'debit_extra5') {
              $total_amount = number_format($total_amount_calc, 8, '.', '');
            } else {
              $total_amount = number_format($total_amount_calc, 2, '.', '');
            }
			
			
			// Check verify status
			if ($verify_status == FALSE) {
				
				$this->session->set_flashdata('error', lang('users deposit error_4'));
				redirect(site_url("account/deposit"));
				
			}
			
			// Check amount for minimum and maximum limits
			if ($minimum > $amount) {
				
				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));
				
			} elseif ($maximum < $amount) {
				
				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));
				
			}
			//print("EPAYZZZ");
            //print($method);
            //EPAY insert PENDING transaction
            if ($method == "ePay") {
                $label = uniqid("epay_");
                $transactions = $this->transactions_model->add_transaction(array(
												"type" 				=> "1",
												"sum"  				=> $amount,
												"fee"    			=> 0,
												"amount" 			=> $amount,
												"currency"		    => $currency,
												"status" 			=> "1",
												"sender" 			=> 'system',
												"receiver" 		    => $user['username'],
												"time"          	=> date('Y-m-d H:i:s'),
												"label" 	        => $label,
												"admin_comment" 	=> 'epay',
												"user_comment" 	    => 'epay',
												"ip_address" 	    => "none",
												"protect" 	        => "none",
												)
				);
                
            }
            //.EPAY INSERT PENDING
			
			// setup page header data
			$this->set_title(sprintf(lang('users dashboard deposit'), $this->settings->site_name));

			$data = $this->includes;

			// set content data
			$content_data = array(
				'user'              => $user,
				'total_amount'              => $total_amount,
				'total_fee'              => $total_fee,
				'amount'              => $amount,
				'currency'              => $currency,
				'method'              => $method,
				'account'              => $account,
				'terms'              => $terms,
				'code_method'              => $code_method,
				'account'              => $account,
				'merchant_account'     => $merchant_account,
				'advcash'     => $advcash,
				'payeer'     => $payeer,
				'skrill'     => $skrill,
				'swift'     => $swift,
				'local_bank'     => $local_bank,
				'coinpayments'     => $coinpayments,
				'blockchain'     => $blockchain,
				'symbol'     => $symbol,
                'wallet_data' => $wallet_data,
                'epay' => $epay,
                'yandex' => $yandex
			 );

			// load views
			$data['content'] = $this->load->view('account/deposit/confirm', $content_data, TRUE);
			$this->load->view($this->template, $data);
			
		}
		
	}
	
	function credit_card()
	{
		
		$user = $this->users_model->get_user($this->user['id']);
		
		$skrill = $this->settings_model->get_dep_method(5);
		$paygol = $this->settings_model->get_dep_method(6);
		
		$this->form_validation->set_rules('amount', lang('users transfer amount'), 'required|trim|numeric');
		$this->form_validation->set_rules('method', lang('users withdrawal method'), 'required|trim|in_list[skrill,paygol]');
		$this->form_validation->set_rules('currency', lang('users trans cyr'), 'required|trim|in_list[debit_base,debit_extra1,debit_extra2,debit_extra3,debit_extra4,debit_extra5]');
		
		if ($this->form_validation->run() == FALSE)
		{
			
			$this->session->set_flashdata('error', lang('users withdrawal error_1'));
			redirect(site_url("account/deposit"));
			
		} else {
			
			$amount = number_format($this->input->post("amount", TRUE), 2, '.', '');
			$currency = $this->input->post("currency", TRUE);
			$code_method = $this->input->post("method", TRUE);
			
			if ($code_method == "skrill") {
				
				$method = $skrill['name'];
				$fee = $skrill['fee'];
				$fee_fix = $skrill['fee_fix'];
				$minimum = $skrill['minimum_'.$currency.''];
				$maximum = $skrill['maximum_'.$currency.''];;

				$random = rand(100000000000, 900000000000);
				$unic = uniqid();
				$id_transaction = ''.$random.'-'.$unic.'';

				if ($skrill['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($skrill['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($skrill['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}

				// Check currency and account for receiving deposits
				if ($currency == "debit_base") {
					$merchant_account = $skrill['ac_debit_base'];
					$symbol = $this->currencys->display->base_code;
				} elseif ($currency == "debit_extra1") {
					$merchant_account = $skrill['ac_debit_extra1'];
					$symbol = $this->currencys->display->extra1_code;
				} elseif ($currency == "debit_extra2") {
					$merchant_account = $skrill['ac_debit_extra2'];
					$symbol = $this->currencys->display->extra2_code;
				} elseif ($currency == "debit_extra3") {
					$merchant_account = $skrill['ac_debit_extra3'];
					$symbol = $this->currencys->display->extra3_code;
				} elseif ($currency =="debit_extra4") {
					$merchant_account = $skrill['ac_debit_extra4'];
					$symbol = $this->currencys->display->extra4_code;
				} elseif ($currency =="debit_extra5") {
					$merchant_account = $skrill['ac_debit_extra5'];
					$symbol = $this->currencys->display->extra5_code;
				}
				
			} elseif ($code_method == "paygol") {
				
				$method = $paygol['name'];
				$fee = $paygol['fee'];
				$fee_fix = $paygol['fee_fix'];
				$minimum = $paygol['minimum_'.$currency.''];
				$maximum = $paygol['maximum_'.$currency.''];;

				$random = rand(100000000000, 900000000000);
				$unic = uniqid();
				$id_transaction = ''.$random.'-'.$unic.'';

				if ($paygol['start_verify'] == "1" && $user['verify_status'] == 0) {
					$verify_status = TRUE;
				} elseif ($paygol['standart_verify'] == "1" && $user['verify_status'] == 1) {
					$verify_status = TRUE;
				} elseif ($paygol['expanded_verify'] == "1" && $user['verify_status'] == 2) {
					$verify_status = TRUE;
				} else {
					$verify_status = FALSE;
				}

				// Check currency and account for receiving deposits
				if ($currency == "debit_base") {
					$merchant_account = $paygol['ac_debit_base'];
					$symbol = $this->currencys->display->base_code;
				} elseif ($currency == "debit_extra1") {
					$merchant_account = $paygol['ac_debit_extra1'];
					$symbol = $this->currencys->display->extra1_code;
				} elseif ($currency == "debit_extra2") {
					$merchant_account = $paygol['ac_debit_extra2'];
					$symbol = $this->currencys->display->extra2_code;
				} elseif ($currency == "debit_extra3") {
					$merchant_account = $paygol['ac_debit_extra3'];
					$symbol = $this->currencys->display->extra3_code;
				} elseif ($currency =="debit_extra4") {
					$merchant_account = $paygol['ac_debit_extra4'];
					$symbol = $this->currencys->display->extra4_code;
				} elseif ($currency =="debit_extra5") {
					$merchant_account = $paygol['ac_debit_extra5'];
					$symbol = $this->currencys->display->extra5_code;
				}
				
			}
			
			// Calculation of the commission and total sum
			$percent = $fee/"100";
			$percent_fee = $amount * $percent;
			$total_fee_calc = $percent_fee + $fee_fix;
			$total_fee = number_format($total_fee_calc, 2, '.', '');
			$total_amount_calc = $amount + $total_fee;
			$total_amount = number_format($total_amount_calc, 2, '.', '');
			
			// Check verify status
			if ($verify_status == FALSE) {
				
				$this->session->set_flashdata('error', lang('users deposit error_4'));
				redirect(site_url("account/deposit"));
				
			}
			
			// Check amount for minimum and maximum limits
			if ($minimum > $amount) {
				
				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));
				
			} elseif ($maximum < $amount) {
				
				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));
				
			}
			
		}
		
		// setup page header data
		$this->set_title(sprintf(lang('users dashboard deposit'), $this->settings->site_name));

		$data = $this->includes;
		
		// set content data
		$content_data = array(
			'user'    => $user,
			'total_amount'              => $total_amount,
			'total_fee'              => $total_fee,
			'amount'              => $amount,
			'currency'              => $currency,
			'method'              => $method,
			'code_method'              => $code_method,
			'merchant_account'     => $merchant_account,
			'skrill'     => $skrill,
			'paygol'     => $paygol,
			'symbol'     => $symbol,
			'id_transaction'     => $id_transaction,
		);
		
		// load views
		$data['content'] = $this->load->view('account/deposit/credit_card', $content_data, TRUE);
		$this->load->view($this->template, $data);
		
	}
	
	function bank()
	{
		
		$user = $this->users_model->get_user($this->user['id']);

		$swift = $this->settings_model->get_dep_method(7);
		$local_bank = $this->settings_model->get_dep_method(8);
		
		$this->form_validation->set_rules('amount', lang('users transfer amount'), 'required|trim|numeric');
		$this->form_validation->set_rules('method', lang('users withdrawal method'), 'required|trim|in_list[swift,local_bank]');
		$this->form_validation->set_rules('currency', lang('users trans cyr'), 'required|trim|in_list[debit_base,debit_extra1,debit_extra2,debit_extra3,debit_extra4,debit_extra5]');
		
		if ($this->form_validation->run() == FALSE)
		{
			
			$this->session->set_flashdata('error', lang('users withdrawal error_1'));
			redirect(site_url("account/deposit"));
			
		} else {
			
			$amount = number_format($this->input->post("amount", TRUE), 2, '.', '');
			$currency = $this->input->post("currency", TRUE);
			$code_method = $this->input->post("method", TRUE);
			
				if ($code_method == "swift") {

					$method = $swift['name'];
					$fee = $swift['fee'];
					$fee_fix = $swift['fee_fix'];
					$minimum = $swift['minimum_'.$currency.''];
					$maximum = $swift['maximum_'.$currency.''];;

					$random = rand(100000000000, 900000000000);
					$unic = uniqid();
					$id_transaction = ''.$random.'-'.$unic.'';

					if ($swift['start_verify'] == "1" && $user['verify_status'] == 0) {
						$verify_status = TRUE;
					} elseif ($swift['standart_verify'] == "1" && $user['verify_status'] == 1) {
						$verify_status = TRUE;
					} elseif ($swift['expanded_verify'] == "1" && $user['verify_status'] == 2) {
						$verify_status = TRUE;
					} else {
						$verify_status = FALSE;
					}

					// Check currency and account for receiving deposits
					if ($currency == "debit_base") {
						$merchant_account = $swift['ac_debit_base'];
						$symbol = $this->currencys->display->base_code;
					} elseif ($currency == "debit_extra1") {
						$merchant_account = $swift['ac_debit_extra1'];
						$symbol = $this->currencys->display->extra1_code;
					} elseif ($currency == "debit_extra2") {
						$merchant_account = $swift['ac_debit_extra2'];
						$symbol = $this->currencys->display->extra2_code;
					} elseif ($currency == "debit_extra3") {
						$merchant_account = $swift['ac_debit_extra3'];
						$symbol = $this->currencys->display->extra3_code;
					} elseif ($currency =="debit_extra4") {
						$merchant_account = $swift['ac_debit_extra4'];
						$symbol = $this->currencys->display->extra4_code;
					} elseif ($currency =="debit_extra5") {
						$merchant_account = $swift['ac_debit_extra5'];
						$symbol = $this->currencys->display->extra5_code;
					}

				} elseif ($code_method == "local_bank") {
					
					$method = $local_bank['name'];
					$fee = $local_bank['fee'];
					$fee_fix = $local_bank['fee_fix'];
					$minimum = $local_bank['minimum_'.$currency.''];
					$maximum = $local_bank['maximum_'.$currency.''];;

					$random = rand(100000000000, 900000000000);
					$unic = uniqid();
					$id_transaction = ''.$random.'-'.$unic.'';

					if ($local_bank['start_verify'] == "1" && $user['verify_status'] == 0) {
						$verify_status = TRUE;
					} elseif ($local_bank['standart_verify'] == "1" && $user['verify_status'] == 1) {
						$verify_status = TRUE;
					} elseif ($local_bank['expanded_verify'] == "1" && $user['verify_status'] == 2) {
						$verify_status = TRUE;
					} else {
						$verify_status = FALSE;
					}

					// Check currency and account for receiving deposits
					if ($currency == "debit_base") {
						$merchant_account = $local_bank['ac_debit_base'];
						$symbol = $this->currencys->display->base_code;
					} elseif ($currency == "debit_extra1") {
						$merchant_account = $local_bank['ac_debit_extra1'];
						$symbol = $this->currencys->display->extra1_code;
					} elseif ($currency == "debit_extra2") {
						$merchant_account = $local_bank['ac_debit_extra2'];
						$symbol = $this->currencys->display->extra2_code;
					} elseif ($currency == "debit_extra3") {
						$merchant_account = $local_bank['ac_debit_extra3'];
						$symbol = $this->currencys->display->extra3_code;
					} elseif ($currency =="debit_extra4") {
						$merchant_account = $local_bank['ac_debit_extra4'];
						$symbol = $this->currencys->display->extra4_code;
					} elseif ($currency =="debit_extra5") {
						$merchant_account = $local_bank['ac_debit_extra5'];
						$symbol = $this->currencys->display->extra5_code;
					}
					
				}
			
				// Calculation of the commission and total sum
				$percent = $fee/"100";
				$percent_fee = $amount * $percent;
				$total_fee_calc = $percent_fee + $fee_fix;
				$total_fee = number_format($total_fee_calc, 2, '.', '');
				$total_amount_calc = $amount + $total_fee;
				$total_amount = number_format($total_amount_calc, 2, '.', '');
			
				$label = uniqid("bmt_");

				// Check verify status
				if ($verify_status == FALSE) {

					$this->session->set_flashdata('error', lang('users deposit error_4'));
					redirect(site_url("account/deposit"));

				}

				// Check amount for minimum and maximum limits
				if ($minimum > $amount) {

					$this->session->set_flashdata('error', lang('users withdrawal error_3'));
					redirect(site_url("account/deposit"));

				} elseif ($maximum < $amount) {

					$this->session->set_flashdata('error', lang('users withdrawal error_3'));
					redirect(site_url("account/deposit"));

				}
			
				if ($code_method == "swift") {
					
					// add new transaction
					$transactions = $this->transactions_model->add_transaction(array(
						"type" 				=> "1",
						"sum"  				=> $total_amount_calc,
						"fee"    			=> $total_fee_calc,
						"amount" 			=> $amount,
						"currency"		=> $currency,
						"status" 			=> "1",
						"sender" 			=> $swift['name'],
						"receiver" 		=> $user['username'],
						"time"        => date('Y-m-d H:i:s'),
						"label" 	    => $label,
						"admin_comment" 	    => 'none',
						"user_comment" 	    => ''.$merchant_account.'<br><strong> Note for bank transfer:'.$id_transaction.'</strong>',
						"ip_address" 	    =>  $_SERVER["REMOTE_ADDR"],
						"protect" 	    => "none",
						)
					);
					
				} else {
					
					// add new transaction
					$transactions = $this->transactions_model->add_transaction(array(
						"type" 				=> "1",
						"sum"  				=> $total_amount_calc,
						"fee"    			=> $total_fee_calc,
						"amount" 			=> $amount,
						"currency"		=> $currency,
						"status" 			=> "1",
						"sender" 			=> $local_bank['name'],
						"receiver" 		=> $user['username'],
						"time"        => date('Y-m-d H:i:s'),
						"label" 	    => $label,
						"admin_comment" 	    => 'none',
						"user_comment" 	    => ''.$merchant_account.'<br><strong> Note for bank transfer:'.$id_transaction.'</strong>',
						"ip_address" 	    =>  $_SERVER["REMOTE_ADDR"],
						"protect" 	    => "none",
						)
					);
					
				}
			
		}
		
		// setup page header data
		$this->set_title(sprintf(lang('users dashboard deposit'), $this->settings->site_name));

		$data = $this->includes;
		
		// set content data
		$content_data = array(
			'user'    => $user,
			'total_amount'              => $total_amount,
			'total_fee'              => $total_fee,
			'amount'              => $amount,
			'currency'              => $currency,
			'method'              => $method,
			'code_method'              => $code_method,
			'merchant_account'     => $merchant_account,
			'swift'     => $skrill,
			'local_bank'     => $local_bank,
			'symbol'     => $symbol,
			'id_transaction'     => $id_transaction,
		);
		
		// load views
		$data['content'] = $this->load->view('account/deposit/bank', $content_data, TRUE);
		$this->load->view($this->template, $data);
		
	}
	
	/**
    * BlockChain
    */
	function blockchain()
	{
		$user = $this->users_model->get_user($this->user['id']);
		
		$blockchain = $this->settings_model->get_dep_method(10);
		
		$this->form_validation->set_rules('amount', lang('users transfer amount'), 'required|trim|numeric');
		$this->form_validation->set_rules('method', lang('users withdrawal method'), 'required|trim|in_list[blockchain]');
		$this->form_validation->set_rules('currency', lang('users trans cyr'), 'required|trim|in_list[debit_base,debit_extra1,debit_extra2,debit_extra3,debit_extra4,debit_extra5]');
		
		if ($this->form_validation->run() == FALSE)
		{
			
			$this->session->set_flashdata('error', lang('users withdrawal error_1'));
			redirect(site_url("account/deposit"));
			
		} else {
			
			$amount = number_format($this->input->post("amount", TRUE), 2, '.', '');
			$currency = $this->input->post("currency", TRUE);
			$code_method = $this->input->post("method", TRUE);
			
			$method = $blockchain['name'];
			$fee = $blockchain['fee'];
			$fee_fix = $blockchain['fee_fix'];
			$minimum = $blockchain['minimum_'.$currency.''];
			$maximum = $blockchain['maximum_'.$currency.''];;

			$random = rand(100000000000, 900000000000);
			$unic = uniqid();
			$id_transaction = ''.$random.'-'.$unic.'';

			if ($blockchain['start_verify'] == "1" && $user['verify_status'] == 0) {
				$verify_status = TRUE;
			} elseif ($blockchain['standart_verify'] == "1" && $user['verify_status'] == 1) {
				$verify_status = TRUE;
			} elseif ($blockchain['expanded_verify'] == "1" && $user['verify_status'] == 2) {
				$verify_status = TRUE;
			} else {
				$verify_status = FALSE;
			}

			// Check currency and account for receiving deposits
			if ($currency == "debit_base") {
				$merchant_account = $blockchain['ac_debit_base'];
				$symbol = $this->currencys->display->base_code;
			} elseif ($currency == "debit_extra1") {
				$merchant_account = $blockchain['ac_debit_extra1'];
				$symbol = $this->currencys->display->extra1_code;
			} elseif ($currency == "debit_extra2") {
				$merchant_account = $blockchain['ac_debit_extra2'];
				$symbol = $this->currencys->display->extra2_code;
			} elseif ($currency == "debit_extra3") {
				$merchant_account = $blockchain['ac_debit_extra3'];
				$symbol = $this->currencys->display->extra3_code;
			} elseif ($currency =="debit_extra4") {
				$merchant_account = $blockchain['ac_debit_extra4'];
				$symbol = $this->currencys->display->extra4_code;
			} elseif ($currency =="debit_extra5") {
				$merchant_account = $blockchain['ac_debit_extra5'];
				$symbol = $this->currencys->display->extra5_code;
			}
			
			// Calculation of the commission and total sum
			$percent = $fee/"100";
			$percent_fee = $amount * $percent;
			$total_fee_calc = $percent_fee + $fee_fix;

            if ($currency =="debit_extra5") {
              $total_fee = number_format($total_fee_calc, 8, '.', '');
            } else {
              $total_fee = number_format($total_fee_calc, 2, '.', '');
            }
            
			$total_amount_calc = $amount + $total_fee;
            
            if ($currency =="debit_extra5") {
              
              $total_amount = number_format($total_amount_calc, 8, '.', '');
            } else {
              $total_amount = number_format($total_amount_calc, 2, '.', '');
            }
            
			
			
			$label = uniqid("blc_");

			// Check verify status
			if ($verify_status == FALSE) {

				$this->session->set_flashdata('error', lang('users deposit error_4'));
				redirect(site_url("account/deposit"));

			}

			// Check amount for minimum and maximum limits
			if ($minimum > $amount) {

				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));

			} elseif ($maximum < $amount) {

				$this->session->set_flashdata('error', lang('users withdrawal error_3'));
				redirect(site_url("account/deposit"));

			}
			
            /*
            
			$my_callback_url = ''.base_url().'ipn/blockchain?secret='.$blockchain['api_value2'];
				
			$call_url = urlencode($my_callback_url);

			$root_url = 'https://api.blockchain.info/v2/receive';

			$parameters = 'xpub='.$merchant_account.'&callback='.urlencode($my_callback_url).'&key='.$blockchain['api_value1'];

			$response = file_get_contents($root_url.'?'.$parameters);

			$object = json_decode($response);

			$forwarding_address = $object->address;
			
			if ($forwarding_address) {
				
				$qr_img = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=bitcoin:'.$forwarding_address.'';
				
				// check value BTC
				$btc_value = $this->fixer->get_btc_rates($symbol, $total_amount);
				
				// add new transaction
				$transactions = $this->transactions_model->add_transaction(array(
					"type" 				=> "1",
					"sum"  				=> $total_amount_calc,
					"fee"    			=> $total_fee_calc,
					"amount" 			=> $amount,
					"currency"		=> $currency,
					"status" 			=> "1",
					"sender" 			=> $blockchain['name'],
					"receiver" 		=> $user['username'],
					"time"        => date('Y-m-d H:i:s'),
					"label" 	    => $label,
					"admin_comment" 	    => 'none',
					"user_comment" 	    => $forwarding_address,
					"ip_address" 	    =>  $_SERVER["REMOTE_ADDR"],
					"protect" 	    => "none",
					)
				);
				
			} else {
				
				$this->session->set_flashdata('error', lang('users deposit error_6'));
				redirect(site_url("account/deposit"));
				
			}
			*/
		}
		
		// setup page header data
		$this->set_title(sprintf(lang('users dashboard deposit'), $this->settings->site_name));

		$data = $this->includes;
		
		// set content data
        
        $content_data = array(
			'user'    => $user,
			'total_amount'              => $total_amount,
			'total_fee'              => $total_fee,
			'amount'              => $amount,
			'currency'              => $currency,
			'method'              => $method,
			'code_method'              => $code_method,
			'merchant_account'     => $merchant_account,
			//'blockchain'     => $blockchain,
			//'symbol'     => $symbol,
			//'forwarding_address'     => $forwarding_address,
			//'qr_img'     => $qr_img,
			'btc_value'     => $btc_value,
		);
        
        /*
		$content_data = array(
			'user'    => $user,
			'total_amount'              => $total_amount,
			'total_fee'              => $total_fee,
			'amount'              => $amount,
			'currency'              => $currency,
			'method'              => $method,
			'code_method'              => $code_method,
			'merchant_account'     => $merchant_account,
			'blockchain'     => $blockchain,
			'symbol'     => $symbol,
			'forwarding_address'     => $forwarding_address,
			'qr_img'     => $qr_img,
			'btc_value'     => $btc_value,
		);
		*/
		// load views
		$data['content'] = $this->load->view('account/deposit/blockchain2', $content_data, TRUE);
		$this->load->view($this->template, $data);
		
	}
  
}
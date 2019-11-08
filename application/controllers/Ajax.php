<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * All  > PUBLIC <  AJAX functions should go in here
 *
 * CSRF protection has been disabled for this controller in the config file
 *
 * IMPORTANT: DO NOT DO ANY WRITEBACKS FROM HERE!!! For retrieving data only.
 */
class Ajax extends Public_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
    }
    
    public function change_setting() {
      
      if ($this->input->post() && $this->session->userdata['logged_in']['is_admin'] == 1) {
        
        //dump_exit($this->session);
        
        $post_data = $this->security->xss_clean($this->input->post());
        
        $setting_id = $post_data['id'];
        $value      = $post_data['value'];
        
        $sql = "UPDATE `settings` SET `value` = '".$value."' WHERE `settings`.`id` = ".$setting_id.";";
        $this->db->query($sql);
      }

    }
    
    function preorder() {
      $post_data = $this->security->xss_clean($this->input->post());
      $sql = "INSERT INTO `preorder_email` (`id`, `name`, `email`, `timestamp`) VALUES (NULL, '".$post_data['name']."', '".$post_data['email']."', CURRENT_TIMESTAMP);";
      $this->db->query($sql);
      
      $this->load->model('template_model');
      
      $email_template = $this->template_model->get_email_template(34);
									
					if($email_template['status'] == "1") {

						// variables to replace
						$name_user = $post_data['name'];
						$rawstring = $email_template['message'];

						// what will we replace
						$placeholders = array('[NAME]');
						$vals_1 = array($name_user);

						//replace
						$str_1 = str_replace($placeholders, $vals_1, $rawstring);

						$this -> email -> from($this->settings->site_email, $this->settings->site_name);
						$this->email->to($post_data['email']);
						$this -> email -> subject($email_template['title']);

						$this -> email -> message($str_1);

						$this->email->send();
										
					}
      
    }
    
    function change_user_notification () {
      
      $this->load->model('users_model');
      $this->load->model('settings_model');
      
      $post_data = $this->security->xss_clean($this->input->post());
      
      $this->settings_model->update_user_notification($this->session->userdata['logged_in']['id'], $post_data);
    }

    /**
	 * Change session language - user selected 
     */
	function set_session_language()
	{
        $language = $this->input->post('language');
        $this->session->language = $language;
        $results['success'] = TRUE;
        echo json_encode($results);
        die();
	}
    
    function set_show_cur() {
      $this->load->model('users_model');
      $post_data = $this->security->xss_clean($this->input->post());
      if (isset($post_data['show_cur'])) {
        $this->users_model->update_user_showcur($this->session->userdata['logged_in']['id'], $post_data['show_cur']);
      }
    }
  
    
    function send_refinvite() {
      if ($this->input->is_ajax_request()) {
        
        $this->load->model('template_model');
        $this->load->model('users_model');
     
        $post_data = $this->security->xss_clean($this->input->post());
        $invite_email = $post_data['email'];
        $reflink = $post_data['reflink'];
        
        $check_ref = $this->users_model->get_usernamemail($invite_email);
        
        if (isset($check_ref['id'])) {
          $return['status'] = 0;
          $return['error'] = 'User already registered!';
        } else {
        
        
          $this->users_model->add_ref($this->session->userdata['logged_in']['username'], $invite_email);

          $user_name = $this->session->userdata['logged_in']['first_name'] . ' '. $this->session->userdata['logged_in']['last_name'];
          $title = "Bankaero service";
          $msg = "User ".$user_name." invited you to Bankaero service!";
          $msg = wordwrap($msg,70);
          $this -> email -> from($this->settings->site_email, $this->settings->site_name);
          $this->email->to($invite_email);
          $this -> email -> subject($title);

          $email_template = $this->template_model->get_email_template(33);

          $rawstring = $email_template['message'];

          // what will we replace
          $placeholders = array('[USERNAME]');
          $str_1 = str_replace('[USERNAME]', $user_name, $rawstring);

          $placeholders = array('[REFLINK]');
          $str_1 = str_replace('[REFLINK]', $reflink, $str_1);

          $this -> email -> message($str_1);

          $this->email->send();
          
          $return['status'] = 1;
          $return['error'] = 0;
        }
        
        print(json_encode(($return)));
      }
    }
    
    /**
    * Delete wallet
    */
    function delete_address()
    {
      if ($this->input->is_ajax_request()) {
        
        //print_r($this->input->post());
        
        $this->load->model('cryptapi_model');
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->cryptapi_model->delete_address($address, $user_id);
      }
    }
    
    function delete_vault() {
       if ($this->input->is_ajax_request()) {
        
        $vault = $this->input->post('vault', true);
        
        $this->load->model('vaults_model');
        $this->load->model('users_model');
        $this->load->model('transactions_model');
        
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->vaults_model->delete_vault($user_id, $vault);
      }
    }
    
    function delete_loan()
    {
      if ($this->input->is_ajax_request()) {
        
        $this->load->model('credit_model');
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->credit_model->delete_loan($loan_id, $user_id);
      }
    }
      
    
    /**
    * Send invite to user
    */
    
    function send_invite()
    {
      if ($this->input->is_ajax_request()) {
        
        $this->load->model('template_model');
     
        $post_data = $this->security->xss_clean($this->input->post());
        $invite_email = $post_data['email'];
  
        $user_name = $this->session->userdata['logged_in']['first_name'] . ' '. $this->session->userdata['logged_in']['last_name'];
        $title = "Bankaero service";
        $msg = "User ".$user_name." invited you to Bankaero service!";
        $msg = wordwrap($msg,70);
        $this -> email -> from($this->settings->site_email, $this->settings->site_name);
        $this->email->to($invite_email);
        $this -> email -> subject($title);
        
        $email_template = $this->template_model->get_email_template(32);

		$rawstring = $email_template['message'];

		// what will we replace
		$placeholders = array('[USERNAME]');

		//replace
		$str_1 = str_replace('[USERNAME]', $user_name, $rawstring);
        
        $this -> email -> message($str_1);
        
        $this->email->send();
      }
    }
    
    public function test() {
      //print('test');
      /*
      $this->load->model('cryptapi_model');
      $this->load->model('currencys_model');
      
      $result = $this->cryptapi_model->update_btc_balances();
      
      print("<pre>"); print_r($result);print("</pre>");
      */
      $this->load->model('currencys_model');
      $rates = $this->currencys_model->get_currencys();
      $rates = $rates -> row_array();
      
      print_r($rates);
      //return;
    }
    
    public function credit_recalcr() {
      
       if ($this->input->is_ajax_request()) {
        
         $data_update = $this->security->xss_clean($this->input->post());
         
         $tparams['term'] = $data_update['term'];
         $tparams['get'] = $data_update['get'];
         $tparams['get_cur'] = (int)$data_update['get_cur'];
         
         $this->load->model('cryptapi_model');
        $this->load->model('settings_model');
        $this->load->model('currencys_model');
        
        //dump($params);
        
        $btc = round ( (2 * $this->cryptapi_model->calc_rate_r($tparams)), 8);
        
        //General calculation
        
        $credit_settings = $this->settings_model->get_credit_settings();
        
        $ltv = $credit_settings[0]['value'];
        $annual_rate = $credit_settings[3]['value'];
        $daily_rate = round( $annual_rate /365, 3);
        
        $return['btc'] = $btc;
        $return['credut_sum'] = $tparams['get'];
        $return['daily_rate'] = $daily_rate;
        $return['days'] = $tparams['term'];
        $return['loan_total'] = round ( ($return['credut_sum'] + $return['credut_sum'] * $daily_rate / 100 * $tparams['term']), 2);
        
        $return['apr_rate']   = $annual_rate;
        
        print(json_encode(($return)));
        
        //dump($result);
       
       }
    }
    
    public function credit_recalc() {
      if ($this->input->is_ajax_request()) {
        $data_update = $this->security->xss_clean($this->input->post());
        
        //print_r($data_update); die;
        $params['term'] = $data_update['term'];
        
        $params['give'] = (float)$data_update['give'];
        $params['give_cur'] = 5; 

        //$params['get'] = floatval($data_update['get']);
        $params['get_cur'] = (int)$data_update['get_cur'];
        
        
        $this->load->model('cryptapi_model');
        $this->load->model('settings_model');
        $this->load->model('currencys_model');
        $result = $this->cryptapi_model->calc_rate($params);
        
        $credit_settings = $this->settings_model->get_credit_settings();
        
        $ltv = $credit_settings[0]['value'];
        $annual_rate = $credit_settings[3]['value'];
        $daily_rate = round( $annual_rate /365, 3);
        
        
        $return['credut_sum'] = round ( ($result['get'] * $ltv / 100), 2);
        $return['daily_rate'] = $daily_rate;
        $return['days'] = $params['term'];
        $return['loan_total'] = round ( ($return['credut_sum'] + $return['credut_sum'] * $daily_rate / 100 * $params['term']), 2);
        
        $return['apr_rate']   = $annual_rate;
        
        print(json_encode(($return)));

      }
    }
    
    public function recalc() {
      if ($this->input->is_ajax_request()) {
        $data_update = $this->security->xss_clean($this->input->post());
        
        //print_r($data_update); die;
        
        if ($data_update['give_cur'] != 5) {
          $params['give'] = (int)$data_update['give'];
          $params['give_cur'] = (int)$data_update['give_cur']; 
        }
        else {
          $params['give'] = floatval($data_update['give']);
          $params['give_cur'] = (int)$data_update['give_cur'];
        }
        
        if ($data_update['get_cur'] != 5) {
          $params['get'] = (int)$data_update['get'];
          $params['get_cur'] = (int)$data_update['get_cur'];
        } else {
          $params['get'] = floatval($data_update['get']);
          $params['get_cur'] = (int)$data_update['get_cur'];
        }
        
        $this->load->model('cryptapi_model');
        $this->load->model('currencys_model');
        $result = $this->cryptapi_model->calc_rate($params);
        
        print(json_encode(($result)));

      }
    }
    
    public function check_dcur_method() {
      if ($this->input->is_ajax_request()) {
        $data_update = $this->security->xss_clean($this->input->post());
        
        $params = $data_update;
        $result = $this->settings_model->check_dep_cur($params);
        
        print(json_encode(($result)));
      }
    }
    
    public function check_wcur_method() {
      if ($this->input->is_ajax_request()) {
        $data_update = $this->security->xss_clean($this->input->post());
        
        $params = $data_update;
        $result = $this->settings_model->check_w_cur($params);
        
        print(json_encode(($result)));
      }
    }
    
    public function get_credit_defaults() {
      $settings = $this->settings_model->get_credit_settings();
      print(json_encode(($settings)));
    }
    
    public function delete_voucher() {
      
      if ($this->input->is_ajax_request()) {
        
        $return['status'] = 0;
        
        //dump_exit($this->input->post());
        $vid = $this->input->post("vid", TRUE);
        
        $this->load->model('vouchers_model');
        $this->load->model('transactions_model');
        $this->load->model('users_model');
        $this->load->library('currencys');
        
        //$user_id = $this->session->userdata['logged_in']['id'];
        //$this->cryptapi_model->delete_address($address, $user_id);
        
        $v_data = $this->vouchers_model->get_vouchers($vid);
        //dump_exit($this->session->userdata['logged_in']['username']);
        
        if ($v_data['status'] == 2) { //delete if ACTIVATED
          $this->vouchers_model->delete_voucher($vid, $this->session->userdata['logged_in']['username']);
        } elseif ($v_data['status'] == 1) { //NOT ACTIVATED => return funds
          
          //RETURN FUNDS
          
          $user = $this->users_model->get_user($this->user['id']);
          
          $user_current_balance = $user[$v_data['currency']];
          
          $user_new_balance = $user_current_balance + $v_data['amount'];
          
          //dump_exit($user_current_balance);
          
          // update sender wallet
          
			$this->users_model->update_wallet_transfer($user['username'],
								array(
									$v_data['currency'] => $user_new_balance,
								)
							);				
            
							$label = uniqid("cvc_");

							// add transaction for user
							$transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "1",
								"sum"  				=> $v_data['amount'],
								"fee"    			=> 0,
								"amount" 			=> $v_data['amount'],
								"currency"			=> $v_data['currency'],
								"status" 			=> "2",
								"sender" 			=> "system",
								"receiver" 			=> $user['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Deleted Voucher '.$v_data['code'].'',
								"label" 	    	=> $label,
								"ip_address" 	    => $_SERVER["REMOTE_ADDR"],
								"protect" 	    	=> "none",
								)
							);
                            
          //DELETE VOUCHER                  
          $this->vouchers_model->delete_voucher($vid, $this->session->userdata['logged_in']['username']);
                            
          if ($v_data['currency'] != 'debit_base5') {
            $user_new_balance = number_format($user_new_balance, 2, '.', '');
          } else {
            $user_new_balance = number_format($user_new_balance, 8, '.', '');
          }
          
          $cur_symb = "";
          if     ($v_data['currency'] == 'debit_base')   { $cur_symb = $this->currencys->display->base_code; }
          elseif ($v_data['currency'] == 'debit_extra1') { $cur_symb = $this->currencys->display->extra1_code;  }
          elseif ($v_data['currency'] == 'debit_extra2') { $cur_symb = $this->currencys->display->extra2_code;  }
          elseif ($v_data['currency'] == 'debit_extra3') { $cur_symb = $this->currencys->display->extra3_code;  }
          elseif ($v_data['currency'] == 'debit_extra4') { $cur_symb = $this->currencys->display->extra4_code;  }
          elseif ($v_data['currency'] == 'debit_extra5') { $cur_symb = $this->currencys->display->extra5_code;  }
          
          //dump($v_data['currency']);
          //dump_exit($cur_symb);
          
          $return['status']     = 1;
          $return['currency']   = $v_data['currency'];
          $return['newbalance'] = $user_new_balance." ".$cur_symb;
        }
        
        print(json_encode(($return)));
        
      }
      
    }

}

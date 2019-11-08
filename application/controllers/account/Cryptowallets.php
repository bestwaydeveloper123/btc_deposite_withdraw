<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cryptowallets extends Private_Controller {

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
        $this->load->model('cryptapi_model');
        $this->load->model('transactions_model');
        $this->load->library('notice');
    }
  
        /**
     * Default
       */
    function index()
    {
      
      if ($this->input->post()) {
        
        $data_update = $this->security->xss_clean($this->input->post());
        
        if (isset($data_update['address_id'])) {
          $change = $this->cryptapi_model->update_wallet_label($data_update, $this->user['id']);
          $flag_msg_new = true;
        }
      }
      
      $user = $this->users_model->get_user($this->user['id']);
      
      //$percent = $this->settings->com_transfer;
      //$fee = $this->settings->com_transfer/"100";

          // setup page header data
          $this->set_title(sprintf(lang('users menu cryptowallets'), $this->settings->site_name));

          $data = $this->includes;
          
          $wallets = $this->cryptapi_model->get_user_wallets($this->user['id']);

          // set content data
          $content_data = array(
            'user'          => $user,
            'user_wallets'  => $wallets,
          );
          
          if ($flag_msg_new) {
            $content_data['show_msg_new'] = 1;
          }

          // load views
          $data['content'] = $this->load->view('account/cryptowallets/index', $content_data, TRUE);
          
          
      //print("<pre>"); print_r($content_data);die;
          
      $this->load->view($this->template, $data);
    }
    
    function delete() {
      
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->cryptapi_model->delete_address($address, $user_id);
        //redirect('/account/cryptowallets', 'refresh');
      
    }
    
    function view($id) {
      $this->load->library('notice');
      
      $wallet = $this->cryptapi_model->get_user_wallet($id);
      
      $this->set_title(sprintf(lang('users menu cryptowallets'), $this->settings->site_name));

      $data = $this->includes;
      
      $user = $this->users_model->get_user($this->user['id']);
      $content_data = array(
            'user'          => $user,
            'wallet'          => $wallet,
          );
      
      $data['content'] = $this->load->view('account/cryptowallets/view', $content_data, TRUE);
          
      $this->load->view($this->template, $data);
    }
    
    function buyextra() {
      $data = $this->includes;
      $user = $this->users_model->get_user($this->user['id']);
      $content_data = array(
            'user'             => $user,
          );
      
      //print("<pre>");
      //print_r($user);
      
      $btc_addon = $this->settings_model->get_add_btc_address_price();
      //print("<pre>");
      //print_r($btc_addon);
      
      //check if we have enough money to buy address
      
      if ($user[$btc_addon['address_price_currency']] >= $btc_addon['address_price']) {
        //print("we have money");
        
        
        $new_extra_addresses = $user['debit_extra5_addresses'] + $btc_addon['address_addon'];
        $this->users_model->add_crypto_address($user['id'], 'debit_extra5', $new_extra_addresses);
        
        
        $label = uniqid("add_");
        $transactions = $this->transactions_model->add_transaction(array(
												"type" 				=> "5",
												"sum"  				=> $btc_addon['address_price'],
												"fee"    			=> 0,
												"amount" 			=> $btc_addon['address_price'],
												"currency"		    => $btc_addon['address_price_currency'],
												"status" 			=> "2",
												"sender" 			=> $user['username'],
												"receiver" 		    => 'system',
												"time"          	=> date('Y-m-d H:i:s'),
												"label" 	        => $label,
												"admin_comment" 	=> 'Additional addresses',
												"user_comment" 	    => 'Additional addresses',
												"ip_address" 	    => "none",
												"protect" 	        => "none",
												)
											);
          //update user wallet
          $new_sum = $user[$btc_addon['address_price_currency']] - $btc_addon['address_price'];
          $this->users_model->update_wallet_transfer($user['username'],
						array(
							$btc_addon['address_price_currency'] => $new_sum,
						)
					);
         
        redirect('/account/cryptowallets', 'refresh');
      } else {
        redirect('/account/deposit', 'refresh');
      }
      
      $data['content'] = $this->load->view('account/cryptowallets/buyextra', $content_data, TRUE);
          
      $this->load->view($this->template, $data);
    }
    
    function generate() {
      
      // setup page header data
      $this->set_title(sprintf(lang('users menu cryptowallets'), $this->settings->site_name));
      
      
      $wallets = $this->cryptapi_model->get_user_wallets($this->user['id']);
      
      $user_btc_address_limit = $this->users_model->get_user_btc_address_limit($this->user['id']);
      
      //print("LIMIT = ".$user_btc_address_limit);
      //print_r($wallets);
      
      $new_wallet_data = array();
       $data = $this->includes;
       $user = $this->users_model->get_user($this->user['id']);
      
      if ($wallets['count'] < $user_btc_address_limit) {
        $new_wallet_data = $this->cryptapi_model->get_new_wallet($this->user['id']);
        $content_data = array(
            'user'             => $user,
            'new_wallet_data'  => $new_wallet_data,
            'new_address'      => 1,
          );
      } else {
        
        $btc_addon = $this->settings_model->get_add_btc_address_price();
        
        $content_data = array(
            'user'             => $user,
            'new_address'      => 0,
            'btc_addon'    => $btc_addon,
          );
      }
      
      
      
      $data['content'] = $this->load->view('account/cryptowallets/generate', $content_data, TRUE);
          
      $this->load->view($this->template, $data);
    }
  
  
  
}
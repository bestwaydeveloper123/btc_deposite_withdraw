<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cryptapi_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'crypt_rates';
        $this->_db2 = 'btc_address_list';
    }

    public function delete_address($address, $user_id) {
      
      $this->load->helper('security');
      $address = $this->security->xss_clean($this->input->post('address'));
      $sql = "UPDATE `btc_address_list` SET `user_id` = NULL WHERE address = '".$address."' AND user_id = ".$user_id." ;";
      //print($sql);
      $this->db->query($sql);
      
    }
    /**
     * Save rate
     *
     * @param  array $data
     * @return boolean
     */
    public function save_rate($data=array())
    {
      if ($data)
        {
            // build query
            $sql = "
                INSERT INTO {$this->_db} (
                    source, pair, value, active
                ) VALUES (
                    " . $this->db->escape($data['source']) . ",
                    " . $this->db->escape($data['pair']) . ",
                    " . $this->db->escape($data['value']) . ",
                    " . $this->db->escape($data['active']) . "
                )
            ";

            // execute query
            //if ($data['value'] != NULL) {
              $this->db->query($sql);
            //}

            if ($id = $this->db->insert_id())
            {
              return TRUE;

            }
        }

        return FALSE;

    }
    
    public function get_current_rate() {
      $sql = "SELECT * FROM `crypt_rates` WHERE `pair` LIKE 'XXBTZEUR' AND active = 1 ORDER BY `crypt_rates`.`datetime` DESC LIMIT 0,1";
      $result = $this->db->query($sql);
      $result = $result->row_array();
      
      $return['btc_rate'] = round((1/$result['value']),10);
      $return['status'] = 1;
      
      
      return $return;
    }
    
    public function update_wallet_label($data, $user) {
      $sql = "UPDATE `btc_address_list` SET `label` = '".$data['address_label']."' WHERE `btc_address_list`.`id` = ".$data['address_id']." AND user_id = ".$user." ;";
      //print($sql);
      $this->db->query($sql);
      
      return $sql;
    }
    
    public function get_user_wallet($id) {
      $sql = "SELECT * FROM `btc_address_list` WHERE `id` = ".$id." AND user_id = ".$this->user['id'];
      $query = $this->db->query($sql);

      $results['count']   = $query->num_rows();
      $results['results'] = $query->row_array();

      return $results;
      
    }
    
    public function get_user_wallets($user_id) {
      $sql = "SELECT * FROM `btc_address_list` WHERE `user_id` = ".$user_id;
      $query = $this->db->query($sql);
      
      if ($query->num_rows() > 0)
        {
         $results['count']   = $query->num_rows();
         $results['results'] = $query->result_array();
        } else {
          $results['count']   = 0;
          $results['results'] = array();
        }
        
        //print_r($results);
        return $results;
      
    }
    
    public function insert_btc_tr ($tr) {
      
      if ($tr['data']['confirmations'] > 5) {
        $tr_status = 1;
      } else {
        $tr_status = 0;
      }
      
      $sql = "INSERT INTO `btc_transactions` (`tr_id`, `tx_id`, `address`, `confirmations`, `amount`, `timereceived`, `status`) VALUES (NULL, '".$tr['data']['txid']."', '".$tr['address']."', '".$tr['data']['confirmations']."', '".$tr['data']['amount']."', '".$tr['data']['timereceived']."', '".$tr_status."');";
      $query = $this->db->query($sql);
      
    }
    
    public function update_btc_tr ($tr) {
      
      $exist = 0;
      $sql = "SELECT * FROM btc_transactions WHERE tx_id = '".$tr['data']['txid']."' ";
      $query = $this->db->query($sql);
      $tr_data = $query;
      if ($query->num_rows()) {
        $exist = 1 ;
      }
      
      if ($exist == 0) {
        $this->cryptapi_model->insert_btc_tr($tr);
      } else {
        $transaction_data = $tr_data->row_array();
        //print_r($transaction_data);die;
        if ($transaction_data['status'] == 1) { //CONFIRMED
          //get user address
          $sql = "SELECT * FROM `btc_address_list` WHERE `address` = '".$transaction_data['address']."' ";
          $query = $this->db->query($sql);
          $query = $query->row_array();  
          
          $user_id_deposit = $query['user_id'];   
          $user_dep = $this->users_model->get_user($user_id_deposit);
          $user_name_deposit = $user_dep['username'];
          //$user_name_deposit = $query['user_id'];
          
          $sql = "UPDATE `btc_transactions` SET `status` = '2' WHERE `btc_transactions`.`tr_id` = ".$transaction_data['tr_id']." ;"; 
          $this->db->query($sql); 
           
          $label = uniqid("btc_"); 
          
          
          $transactions = $this->transactions_model->add_transaction(array(
						"type" 				=> "1",
						"sum"  				=> $transaction_data['amount'],
						"fee"    			=> 0,
						"amount" 			=> $transaction_data['amount'],
						"currency"		    => 'debit_extra5',
						"status" 			=> '2',
						"sender" 			=> 'system',
						"receiver" 		    => $user_name_deposit,
						"time"              => date('Y-m-d H:i:s'),
						"label" 	        => $label,
						"admin_comment" 	=> $transaction_data['tx_id'],
						"user_comment" 	    => '',
						"ip_address" 	    =>  $_SERVER["REMOTE_ADDR"],
						"protect" 	        => "none",
						)
					);
          
          
            $user_r = $this->users_model->get_username($user_name_deposit);
              
            $new_balance = $user_r['debit_extra5'] + $transaction_data['amount'];
          
            
            $this->users_model->update_user($user_name_deposit,
                  array(
                      'debit_extra5'  => $new_balance,
                  )
              );
          
        }
      }
      
    }
    
    public function get_btc_b() {
      
    }
    
    public function process_batch_to_bankaero() {
      
      
      //get tx fee setting
      $sql = "SELECT * FROM `settings` WHERE `name` LIKE 'btc_transaction_speed'";
      $result = $this->db->query($sql);
      $result = $result->row_array();
      
      $btc_speed = $result['value'];
      
      $fee_spd = $this->cryptapi_model->get_fee_speed();
      
      if ($btc_speed == 1) { $selected_tx_fee = $fee_spd['fastestFee']; }
      elseif ($btc_speed == 2) {$selected_tx_fee = $fee_spd['halfHourFee']; }
      elseif ($btc_speed == 3) {$selected_tx_fee = $fee_spd['hourFee']; }
      
      //dump_exit($selected_tx_fee);
      print("selected fee = ".$selected_tx_fee." sat / byte <br>");
      
      require_once(APPPATH.'libraries/easyb.php');
      $bitcoin = new Bitcoin('bankaero','bankaero','176.223.141.87','8332');
      
      
      //set tx speed
      
      $tx_fee_calculated = $selected_tx_fee * 0.00000001 * 1000; //0.00
      
      $spd_res = $bitcoin->settxfee($tx_fee_calculated);
      if ($spd_res == true) {
        print("BTC set TX = ".$tx_fee_calculated." sat / Kbyte <br>");
      }
      
      $current_balances = $this->get_btc_balances();
      
      //dump_exit($current_balances);
      
      $wallets = [];
      $total_sum = 0;
      foreach ($current_balances['address_list'] as $k=>$v) {
        /*
        if ($v['balance'] > 0.0001) {
          $wallets[] = $v;
        }
         * 
         */
          $total_sum += $v['balance'];
      }
      
      
      
      $unspentTransactions = $bitcoin->listunspent(); 
      //dump_exit($unspentTransactions);
      
      $from_array = [];
      
      $tr_size = 0;
      
      foreach ($unspentTransactions as $tr) {
        $tmp['txid'] = $tr['txid'];
        $tmp['vout'] = $tr['vout'];
                
        $from_array[] = $tmp;
        $tr_size += 146;
      }
      
      $tr_size += 33 + 10;
      print('Total sum = '.$total_sum.'<br>');
      print('TR size   = '.$tr_size.' bytes<br>');
      
      $tx_fee = $tr_size * ($tx_fee_calculated / 1000); //sat/byte 
      print('TX FEE   = '.number_format($tx_fee, 8, '.', '').'<br>');
      $total_sum = $total_sum - $tx_fee;
      print('SUM - TX FEE = '.$total_sum.'<br>');
      
      $to_array['3ATxe4fyiExTJrySyNjCvjL9N8A3Abf5Ge'] = $total_sum;
      
      $rawTransaction = $bitcoin->createrawtransaction($from_array, $to_array);
      $newTransaction = $bitcoin->fundrawtransaction($rawTransaction);

      dump($newTransaction);
      
      $decoded = $bitcoin->decoderawtransaction($newTransaction['hex']);
      dump($decoded);
      
      
      $signed = $bitcoin->signrawtransaction($newTransaction['hex']);
      dump($signed); //should say complete:1 if everything was correctly put

      //$published = $bitcoin->sendrawtransaction($signed['hex']);
      //dump($published); //will output you tx id
    }
    
    public function check_batch_run() {
      $sql = "SELECT * FROM `btc_transactions_batch` ORDER BY `btc_transactions_batch`.`btb_id` DESC  ";
      $query = $this->db->query($sql);
      $query = $query->result_array();  
      $query = $query[0];
      
      $last_run = $query['last_run'];
      
      $start_date = new DateTime($last_run);
      $since_start = $start_date->diff(new DateTime(date('Y-m-d H:i:s')));
      
      $minutes = $since_start->days * 24 * 60;
      $minutes += $since_start->h * 60;
      $minutes += $since_start->i;

      $sql = "SELECT * FROM `settings` WHERE `name` LIKE 'btc_wallet_check'";
      $query = $this->db->query($sql);
      $query = $query->row_array();
      
      $check_mins = $query['value'];
      
      print('interval = '.$check_mins.'<br>');
      print('current = '.$minutes.'<br>');
      
      $return = 0;
      
      if ($minutes >= $check_mins) {
        $return = 1;
      }
      
      //dump_exit($return);
      
      return $return;
    }
    
    public function update_batch_run($data) {
      $sql = "INSERT INTO `btc_transactions_batch` (`btb_id`, `last_run`, `data`) VALUES (NULL, CURRENT_TIMESTAMP, '".$data."');";
      $this->db->query($sql);
    }
    
    public function get_btc_balances() {
      require_once(APPPATH.'libraries/easyb.php');
      $bitcoin = new Bitcoin('bankaero','bankaero','176.223.141.87','8332');
      $getinfo = $bitcoin->listreceivedbyaddress(1, true);
      //$getinfo2 = $bitcoin->listtransactions();
      
      $a_total = 0; $tr_total = 0;
      $tr_data = []; 
      foreach ($getinfo as $id=>$data) {
        $result['address_list'][$id]['address'] = $data['address'];
        $result['address_list'][$id]['balance'] = $data['amount'];
        
        foreach ($data['txids'] as $k=>$v) {
          //$result['transaction_list']
          
          $item['data'] = $bitcoin->gettransaction($v);
          $item['address'] = $data['address'];

          array_push($tr_data, $item);
          
          $this->cryptapi_model->update_btc_tr($item);

          $tr_total++;
        }
        
        $result['transaction_list'] = array();
        $a_total++;
      }
      
      //print("<pre>");
      //print_r($tr_data);die;
      
      $result['address_total'] = $a_total;
      
      $result['transaction_list'] = $tr_data;
      $result['transaction_total'] = $tr_total;
      
      //print("<pre>"); print_r($getinfo);
      
      return $result;
    }
    
    public function get_new_wallet($user_id) {
      
      require_once(APPPATH.'libraries/easyb.php');
      $bitcoin = new Bitcoin('bankaero','bankaero','176.223.141.87','8332');
      $address = $bitcoin->getnewaddress();
      
      //$address = 'test_addr_text2';
      //print($address);
      
      $sql = "INSERT INTO `btc_address_list` (`id`, `address`, `user_id`, `date_added`, `label`) VALUES (NULL, '".$address."', '".$user_id."', CURRENT_TIMESTAMP, NULL);";
      $this->db->query($sql);
      $id = $this->db->insert_id();
      
      $sql = "SELECT * FROM `btc_address_list` WHERE `id` = ".$id." ";
      $query = $this->db->query($sql);
      $query = $query->row_array();
        
      $result['address'] = $query['address'];
      $result['id'] = $query['id'];
      
      return $result;
    }
    
    public function get_new_credit_wallet($user_id, $loan_id) {
      
      require_once(APPPATH.'libraries/easyb.php');
      $bitcoin = new Bitcoin('bankaero','bankaero','176.223.141.87','8332');
      $address = $bitcoin->getnewaddress();
      
      //$address = 'test_addr_text2';
      //print($address);
      
      $sql = "UPDATE `loans` SET `btc_address` = '".$address."' WHERE `loans`.`id` = ".$loan_id.";";
      $this->db->query($sql);
      $id = $this->db->insert_id();
        
      $result['address'] = $query['address'];
      //$result['id'] = $query['id'];
      
      return $result;
    }
    
    public function calc_rate_r($params) {
      
      //dump($params);
      
      $params['give_cur'] = 5;
      
      $this->load->library('currencys');
	  $this->load->library('notice');
	  $this->load->library('fixer');
      
      $rates = $this->currencys_model->get_currencys();
      $rates = $rates -> row_array();
      
      if ($rates['api_extra5'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
        $rates['extra5_rate'] = $this->cryptapi_model->get_current_rate()['btc_rate'];
      }

      if ($rates['api_extra1'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra1_rate'] = $this->fixer->get_rates('EUR', 'USD');
      }
      
      if ($rates['api_extra2'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra2_rate'] = $this->fixer->get_rates('EUR', 'GBP');
      }
      
      if ($rates['api_extra3'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra3_rate'] = $this->fixer->get_rates('EUR', 'JPY');
      }
      
      if ($rates['api_extra4'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra4_rate'] = $this->fixer->get_rates('EUR', 'RUB');
      }
      
      //dump($rates);
      
      if ($params['get_cur'] == 0) { $result = $params['get'] * $rates['extra5_rate'];  }
      elseif ($params['get_cur'] == 1) { 
        $result = $params['get'] * $rates['extra5_rate'];
        $result = $result / $rates['extra1_rate'];
        }
      elseif ($params['get_cur'] == 2) { 
        $result = $params['get'] * $rates['extra5_rate'];
        $result = $result / $rates['extra2_rate'];
        }
      elseif ($params['get_cur'] == 3) { 
        $result = $params['get'] * $rates['extra5_rate'];
        $result = $result / $rates['extra3_rate'];
        }
      elseif ($params['get_cur'] == 4) { 
        $result = $params['get'] * $rates['extra5_rate'];
        $result = $result / $rates['extra4_rate'];
        }
      
      
      //dump($result);
      return $result;
      
    }
  
    public function calc_rate($params) {
      
      
      
      $this->load->library('currencys');
	  $this->load->library('notice');
	  $this->load->library('fixer');
      
      $rates = $this->currencys_model->get_currencys();
      $rates = $rates -> row_array();
      
      //dump_exit($rates);

      if ($rates['api_extra5'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
        $rates['extra5_rate'] = $this->cryptapi_model->get_current_rate()['btc_rate'];
      }

      if ($rates['api_extra1'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra1_rate'] = $this->fixer->get_rates('EUR', 'USD');
      }
      
      if ($rates['api_extra2'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra2_rate'] = $this->fixer->get_rates('EUR', 'GBP');
      }
      
      if ($rates['api_extra3'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra3_rate'] = $this->fixer->get_rates('EUR', 'JPY');
      }
      
      if ($rates['api_extra4'] == 1 && $this->cryptapi_model->get_current_rate()['status'] == 1) {
       $rates['extra4_rate'] = $this->fixer->get_rates('EUR', 'RUB');
      }
      
      //dump_exit($rates);
 
      
      if ($params['give_cur'] == 0) { 
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = $result['give']; }
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = round ( ($result['give'] * $rates['extra1_rate']), 2); }
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = round ( ($result['give'] * $rates['extra2_rate']), 2); }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = round ( ($result['give'] * $rates['extra3_rate']), 2); }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = round ( ($result['give'] * $rates['extra4_rate']), 2); }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = $result['give'] * $rates['extra5_rate']; }
      }
      elseif ($params['give_cur'] == 1) { 
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = round((1 * $params['give']/$rates['extra1_rate']),2); }
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = $result['give']; }
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra1_rate']) * $rates['extra2_rate']),2); }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra1_rate']) * $rates['extra3_rate']),2); }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra1_rate']) * $rates['extra4_rate']),2); }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra1_rate']) * $rates['extra5_rate']),10); }
      }
      elseif ($params['give_cur'] == 2) { 
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = round((1 * $params['give'] /$rates['extra2_rate']),2); }
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra2_rate']) * $rates['extra1_rate']),2); }
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = $result['give']; }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra2_rate']) * $rates['extra3_rate']),2); }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra2_rate']) * $rates['extra4_rate']),2); }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra2_rate']) * $rates['extra5_rate']),10); }
      }
      elseif ($params['give_cur'] == 3) { 
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = round((1 * $params['give'] /$rates['extra3_rate']),2); }
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra3_rate']) * $rates['extra1_rate']),2); }
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra3_rate']) * $rates['extra2_rate']),2); }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = $params['give']; $result['get'] = $result['give']; }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra3_rate']) * $rates['extra4_rate']),2); }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra3_rate']) * $rates['extra5_rate']),10); }
      }
      elseif ($params['give_cur'] == 4) { 
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = round((1 * $params['give'] /$rates['extra4_rate']),2); }
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra4_rate']) * $rates['extra1_rate']),2); }
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra4_rate']) * $rates['extra2_rate']),2); }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra4_rate']) * $rates['extra3_rate']),2); }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = $result['give']; }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = round(((1 * $params['give']/$rates['extra4_rate']) * $rates['extra5_rate']),10); }
      }
      elseif ($params['give_cur'] == 5) { 
        //print("zz");
        if ($params['get_cur'] == 0) { $result['give'] = $params['give']; $result['get'] = round(($params['give'] * 1/$rates['extra5_rate']),2);}
        if ($params['get_cur'] == 1) { $result['give'] = $params['give']; $result['get'] = round(($params['give'] * $rates['extra1_rate'] * 1/$rates['extra5_rate']),2);}
        if ($params['get_cur'] == 2) { $result['give'] = $params['give']; $result['get'] = round(($params['give'] * $rates['extra2_rate'] * 1/$rates['extra5_rate']),2); }
        if ($params['get_cur'] == 3) { $result['give'] = $params['give']; $result['get'] = round(($params['give'] * $rates['extra3_rate'] * 1/$rates['extra5_rate']),2); }
        if ($params['get_cur'] == 4) { $result['give'] = $params['give']; $result['get'] = round(($params['give'] * $rates['extra4_rate'] * 1/$rates['extra5_rate']),2); }
        if ($params['get_cur'] == 5) { $result['give'] = $params['give']; $result['get'] = $result['give']; }
      }
      //print_r($result);
      return $result;
      
    }

    
    public function epay_request() {
      
    }
    
    
    public function get_fee_speed() {
      $json = file_get_contents('https://bitcoinfees.earn.com/api/v1/fees/recommended');
      $data = json_decode($json, true);
      
      if (isset($data['fastestFee']) && isset($data['halfHourFee']) && isset($data['hourFee'])) {
        $return['status'] = 1;
        
        $return['fastestFee']  = $data['fastestFee'];
        $return['halfHourFee'] = $data['halfHourFee'];
        $return['hourFee']     = $data['hourFee'];
        
      } else {
        $return['status'] = 0;
      }
      
      //dump_exit($data);
      return $return;
      
    }

}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vaults_model extends CI_Model {

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
        $this->_db = 'vaults';
    }
    
    public function deletetemp() {
      //Email Completed Vault
              $this->load->library('email');
              $this->load->library('currencys');
                                    //email send
              
                                    $v['user_id'] = 24;
                                    $v['vault_currency'] = 'debit_base';
              
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Vault goal completed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(37);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = 'https://bankaero.com/account/vaults';
                                    $mail_amount = number_format(100, 2, '.', '');
                                    if ($v['vault_currency'] == "debit_base") {
													$symbol = $this->currencys->display->base_code;
												} elseif ($v['vault_currency'] == "debit_extra1") {
													$symbol = $this->currencys->display->extra1_code;
												} elseif ($v['vault_currency'] == "debit_extra2") {
													$symbol = $this->currencys->display->extra2_code;
												} elseif ($v['vault_currency'] == "debit_extra3") {
													$symbol = $this->currencys->display->extra3_code;
												} elseif ($v['vault_currency'] =="debit_extra4") {
													$symbol = $this->currencys->display->extra4_code;
												} elseif ($v['vault_currency'] =="debit_extra5") {
													$symbol = $this->currencys->display->extra5_code;
												}
                                    $name_user2 = $receiver_data['first_name'] . ' ' . $receiver_data['last_name'];
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');
									$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $name_user2);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    
                                    $this -> email -> message($str_1);
                                    $this->email->send();
              
              
              //.email
    }
	
	function get_user_vaults($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user_id = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE (user_id = '{$user_id}')
        ";
        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
            {
                $value = $this->db->escape('%' . $value . '%');
                $sql .= " AND {$key} LIKE {$value}";
            }
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
        {
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $results['results'] = $query->result_array();
        }
        else
        {
            $results['results'] = NULL;
        }

        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;

        return $results;
    }
    
    public function add_vault($user_id, $data) {
      
      $sql = "INSERT INTO `vaults` (`id`, `user_id`, `vault_name`, `vault_currency`, `vault_total`, `vault_current`, `vault_paysum`, `vault_period`, `vault_daystart`, `vault_last_payment`, `date_added`, `date_updated`, `status`) VALUES (NULL, '".$user_id."', '".$data['vault_name']."', '".$data['vault_currency']."', '".$data['vault_total']."', '0', '".$data['vault_paysum']."', '".$data['vault_period']."', '".$data['vault_daystart']."', NULL, CURRENT_TIMESTAMP, NULL, '0');";
      $this->db->query($sql);
    }
    
    public function delete_vault($user_id, $id) {
      
      $sql = "SELECT * FROM `vaults` WHERE `id` = ".$id." AND `user_id` = ".$user_id." ";
      
      $query = $this->db->query($sql);
      $result['data'] = $query->row_array();
      //dump($result);
      if (isset($result['data']['id'])) {
        
        //delete vault
        $sql = "DELETE FROM `vaults` WHERE `id` = ".$id." AND user_id = ".$user_id." ";
        $this->db->query($sql);
        
        $v = $result['data'];
        //return to balance
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
              $new_balance = $receiver_data[$v['vault_currency']] + $v['vault_current'];
              
              if ($v['vault_current'] > 0) {
                $transactions = $this->transactions_model->add_transaction(array(
                                  "type" 				=> "1",
                                  "sum"  				=> $v['vault_current'],
                                  "fee"    			=> 0,
                                  "amount" 			=> $v['vault_current'],
                                  "currency"			=> $v['vault_currency'],
                                  "status" 			=> 2,
                                  "sender" 			=> $receiver_data['username'],
                                  "receiver" 			=> $receiver_data['username'],
                                  "time"          	=> date('Y-m-d H:i:s'),
                                  "user_comment"  	=> 'Vault #'.$v['id'].' closed',
                                  "label" 	        => uniqid("vlt_"),
                                  "ip_address" 	    => $_SERVER["REMOTE_ADDR"],
                                  "protect" 	        => "none",
                                  )
                              );

                $this->users_model->update_wallet_transfer($receiver_data['username'],
                                    array(
                                        $v['vault_currency'] => $new_balance,
                                        )
                                    );
              }
              
              //.return 
      }
    }    
    
    public function get_detail_vault($user_id, $id) {
      
      $result['status'] = 0;
      
      $sql = "SELECT * FROM `vaults` WHERE `id` = ".$id." AND `user_id` = ".$user_id." ";
      
      $query = $this->db->query($sql);
      $result['data'] = $query->row_array();
      if (isset($result['data']['id'])) {
        
        //dump($result);
        
        if (($result['data']['vault_last_payment']) != '' ) {
          //2018-11-23 04:12:53
          //dump($result);
          
          if ($result['data']['vault_period'] == 1) {
            $extra_days = 1;
          } elseif ($result['data']['vault_period'] == 2) {
            $extra_days = 7;
          } elseif ($result['data']['vault_period'] == 3) {
            $extra_days = 30;
          }
          
          $result['data']['vault_next_payment'] = date('Y-m-d', strtotime($result['data']['vault_last_payment']. ' + '.$extra_days.' day'));
          
          
          
          
        } else {
          $result['data']['vault_next_payment'] = $result['data']['vault_daystart'];
        }
        
        $result['status'] = 1;
      }
      
      
      
      return $result;
    }
    
    public function process_vaults() {
      $sql = "SELECT * FROM `vaults` " ;
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      //dump_exit($result);
      
      $today = date('Y-m-d');
      print($today."<br>");
      
      
      foreach ($result as $k=>$v) {
        print("***********************************************<br>");
        
        $v['pay'] = 0;
        $v['close'] = 0;
        
        
        if ($v['vault_period'] == 1) {$v['p_text'] = 'daily';}
        elseif ($v['vault_period'] == 2) {$v['p_text'] = 'weekly';}
        elseif ($v['vault_period'] == 3) {$v['p_text'] = 'monthly';}
        
        print("vault id=".$v['id']." ".$v['vault_name']." ".$v['vault_current']." / ".$v['vault_total']." +".$v['vault_paysum']." ".$v['p_text']." start ".$v['vault_daystart']." Last Payment = ".$v['vault_last_payment']."<br>");
         
        
        if ($v['vault_last_payment'] == "") {
          $diff = round((strtotime($today)-strtotime($v['vault_daystart']))/86400);
          
          print("new vault<br>");
          print("date start = ".$v['vault_daystart']." today=".$today." diff = ".$diff."<br>");
          if ($diff >=0) {
            $v['pay'] = 1;
            print("make payment!<br>"); 
          }
        } else {
          $diff = round((strtotime($today)-strtotime($v['vault_last_payment']))/86400);
          //calc next payment day limit
          
          if ($v['vault_period'] == 1)  {$limit = 1;}
          elseif ($v['vault_period'] == 2) {$limit = 7;}
          elseif ($v['vault_period'] == 3) {$limit = 30;}
          
          print("DF=".$diff."<br>");
          if ($diff >= $limit) {
            $v['pay'] = 1;
            print("make payment!<br>"); 
            
          
          }
          
        }
        
        
        if ($v['pay'] == 1) {
          $sql = "SELECT ".$v['vault_currency']." FROM users WHERE id = ".$v['user_id']." ";
          $q = $this->db->query($sql);
          $q = $q->row_array();

          $balance = $q[$v['vault_currency']];
          
          if ($v['vault_paysum'] < $balance) {
            print("We have enough money to process<br>");
            
             //check vault after payment
            $vault_after_payment = $v['vault_current'] + $v['vault_paysum'];
            print("new vault total = ".$vault_after_payment."<br>");

            //PROCESS PAYMENT
            $new_balance = $balance - $v['vault_paysum'];
              
              //update vault
              $new_vault_current = $v['vault_current'] + $v['vault_paysum'];
              
              $sql = "UPDATE `vaults` SET `vault_current` = '".$new_vault_current."', status = 1, vault_last_payment = CURRENT_TIMESTAMP WHERE `id` = ".$v['id']." ;";
              $this->db->query($sql);
              
              $sql = "INSERT INTO `vaults_log` (`vl_id`, `vault_id`, `action`, `log_date`) VALUES (NULL, '".$v['id']."', 'New value = ".$new_vault_current."', CURRENT_TIMESTAMP);";
              $this->db->query($sql);
              
              
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
							if ($vault_after_payment >= $v['vault_total']) {
								$tr_vault_paysum = $v['vault_paysum'] - ($vault_after_payment - $v['vault_total']);
							} else {
								$tr_vault_paysum = $v['vault_paysum'];
							}
							
              $transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "3",
								"sum"  				=> $tr_vault_paysum,
								"fee"    			=> 0,
								"amount" 			=> $tr_vault_paysum,
								"currency"			=> $v['vault_currency'],
								"status" 			=> 2,
								"sender" 			=> $receiver_data['username'],
								"receiver" 			=> $receiver_data['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Vault #'.$v['id'].' transaction',
								"label" 	        => uniqid("vlt_"),
								"ip_address" 	    => '127.0.0.1',
								"protect" 	        => "none",
								)
							);
              $total_receiver = $receiver_data[$v['vault_currency']] - $v['vault_paysum'];
              
               //$receiver = $this->users_model->get_usernamemail($receiver_data)['username'];
               $this->users_model->update_wallet_transfer($receiver_data['username'],
                                  array(
                                      $v['vault_currency'] => $total_receiver,
                                      )
                                  );
               //.PAYMENT
            
               if ($vault_after_payment >= $v['vault_total']) {
                $v['close'] = 1;
              }
            
            
            if ($v['close'] == 1) {
              //CLOSE VAULT
              print('CLOSING VAULT!<br>');
              //delete VAULT
              $sql = "DELETE FROM `vaults` WHERE `id` = ".$v['id']." ";
              $this->db->query($sql);
              //.delete
              
              
              //return to balance
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
              $new_balance = $receiver_data[$v['vault_currency']] + $new_vault_current;
              
              $transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "1",
								"sum"  				=> $v['vault_total'],
								"fee"    			=> 0,
								"amount" 			=> $v['vault_total'],
								"currency"			=> $v['vault_currency'],
								"status" 			=> 2,
								"sender" 			=> $receiver_data['username'],
								"receiver" 			=> $receiver_data['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Vault #'.$v['id'].' closed',
								"label" 	        => uniqid("vlt_"),
								"ip_address" 	    => '127.0.0.1',
								"protect" 	        => "none",
								)
							);
              
              $this->users_model->update_wallet_transfer($receiver_data['username'],
                                  array(
                                      $v['vault_currency'] => $new_balance,
                                      )
                                  );
              
              //.return 
              
              //Email Completed Vault
              $this->load->library('email');
              $this->load->library('currencys');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Vault goal completed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(37);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = 'https://bankaero.com/account/vaults';
                                    $mail_amount = number_format($vault_after_payment, 2, '.', '');
                                    if ($v['vault_currency'] == "debit_base") {
													$symbol = $this->currencys->display->base_code;
												} elseif ($v['vault_currency'] == "debit_extra1") {
													$symbol = $this->currencys->display->extra1_code;
												} elseif ($v['vault_currency'] == "debit_extra2") {
													$symbol = $this->currencys->display->extra2_code;
												} elseif ($v['vault_currency'] == "debit_extra3") {
													$symbol = $this->currencys->display->extra3_code;
												} elseif ($v['vault_currency'] =="debit_extra4") {
													$symbol = $this->currencys->display->extra4_code;
												} elseif ($v['vault_currency'] =="debit_extra5") {
													$symbol = $this->currencys->display->extra5_code;
												}
                                    $name_user2 = $receiver_data['first_name'] . ' ' . $receiver_data['last_name'];
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');
									$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $name_user2);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
              
              
              //.email
              //.CLOSE
            }
            
          } else {
            
            print('SEND NOTIFICATION FAILED');
            $this->load->library('email');
            //email send
            $receiver_data = $this->users_model->get_user($v['user_id']);
            $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
            $title = "Vault recurring payment failed!";
            $this -> email -> from($this->settings->site_email, $this->settings->site_name);
            $this->email->to($receiver_data['email']);
            $this -> email -> subject($title);
            $email_template = $this->template_model->get_email_template(36);
            $rawstring = $email_template['message'];
            //dump($rawstring);
            // what will we replace
            $placeholders = array('[NAME]');
            $vals_1 = array($user_name);
            $str_1 = str_replace($placeholders, $vals_1, $rawstring);
            $this -> email -> message($str_1);
            $this->email->send();
            //.email                                                  
            
          }
          
        }
        
       
        
         
      }
      
    }
     
    public function process_vaults2() {
      
      //get all vaults
      $sql = "SELECT * FROM `vaults`  ";
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      //dump($result);
      
      foreach ($result as $k=>$v) {
        
        if ($v['status'] == 0)  { //process newly added vaults

          
          //check payment
          $today = date('Y-m-d');
          $diff0 = strtotime($v['vault_daystart'])-strtotime($today);
          $diff = round((strtotime($v['vault_daystart'])-strtotime($today))/86400);
          
          $sql = "INSERT INTO `vaults_log` (`vl_id`, `vault_id`, `action`, `log_date`) VALUES (NULL, '".$v['id']."', 'Diff = ".$diff." (".$diff0.")', CURRENT_TIMESTAMP);";
          $this->db->query($sql);

          if ($diff <= 0) { //process vault transaction

            //START VAULT PAY
            $sql = "SELECT ".$v['vault_currency']." FROM users WHERE id = ".$v['user_id']." ";
            $q = $this->db->query($sql);
            $q = $q->row_array();

            $balance = $q[$v['vault_currency']];
            $delta = $balance - $v['vault_paysum'];

            if ($delta >= 0) { //if we have enough money
              
              $new_balance = $balance - $v['vault_paysum'];
              
              //update vault
              $new_vault_current = $v['vault_current'] + $v['vault_paysum'];
              
              $sql = "UPDATE `vaults` SET `vault_current` = '".$new_vault_current."', status = 1, vault_last_payment = CURRENT_TIMESTAMP WHERE `id` = ".$v['id']." ;";
              $this->db->query($sql);
              
              $sql = "INSERT INTO `vaults_log` (`vl_id`, `vault_id`, `action`, `log_date`) VALUES (NULL, '".$v['id']."', 'New value = ".$new_vault_current."', CURRENT_TIMESTAMP);";
              $this->db->query($sql);
              
              
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
              $transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "1",
								"sum"  				=> $v['vault_paysum'],
								"fee"    			=> 0,
								"amount" 			=> $v['vault_paysum'],
								"currency"			=> $v['vault_currency'],
								"status" 			=> 2,
								"sender" 			=> $receiver_data['username'],
								"receiver" 			=> $receiver_data['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Vault #'.$v['id'].' transaction',
								"label" 	        => uniqid("vlt_"),
								"ip_address" 	    => '127.0.0.1',
								"protect" 	        => "none",
								)
							);
              $total_receiver = $receiver_data[$v['vault_currency']] - $v['vault_paysum'];
              
               //$receiver = $this->users_model->get_usernamemail($receiver_data)['username'];
               $this->users_model->update_wallet_transfer($receiver_data['username'],
                                  array(
                                      $v['vault_currency'] => $total_receiver,
                                      )
                                  );
    
              
            } else { //send email
              
              $this->load->library('email');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Vault recurring payment failed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(36);
                                    $rawstring = $email_template['message'];
                                    dump($rawstring);
                                    // what will we replace
                                    $placeholders = array('[NAME]');
                                    $vals_1 = array($user_name);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
                                    //.email                      
              
              
              //
              
            }

            //.VAULT PAY
          }
        
        }
        
        elseif ($v['status'] == 1) { //already opened vaults
          //dump($v);
          
          $today = date('Y-m-d');
          $diff = round(abs(strtotime($v['vault_last_payment'])-strtotime($today))/86400);
          
          $diff0 = strtotime($v['vault_daystart'])-strtotime($today);
          
          $sql = "INSERT INTO `vaults_log` (`vl_id`, `vault_id`, `action`, `log_date`) VALUES (NULL, '".$v['id']."', 'Diff = ".$diff." (".$diff0.")', CURRENT_TIMESTAMP);";
          $this->db->query($sql);
          
          if ($v['vault_period'] == 1) 
          {
            
            $limit = 1;
            
          }
          elseif ($v['vault_period'] == 2) 
          {
            $limit = 7;
          }
          elseif ($v['vault_period'] == 3) 
          {
            $limit = 30;
          }

          //dump($diff);
          //dump_exit($limit);
          
          if ($diff >= $limit) {
            //START VAULT PAY
            $sql = "SELECT ".$v['vault_currency']." FROM users WHERE id = ".$v['user_id']." ";
            $q = $this->db->query($sql);
            $q = $q->row_array();

            $balance = $q[$v['vault_currency']];
            $delta = $balance - $v['vault_paysum'];

            if ($delta >= 0) { //if we have enough money
              
              $new_balance = $balance - $v['vault_paysum'];
              
              //update vault
              $new_vault_current = $v['vault_current'] + $v['vault_paysum'];
              
              $sql = "UPDATE `vaults` SET `vault_current` = '".$new_vault_current."', status = 1, vault_last_payment = CURRENT_TIMESTAMP WHERE `id` = ".$v['id']." ;";
              $this->db->query($sql);
              
              $sql = "INSERT INTO `vaults_log` (`vl_id`, `vault_id`, `action`, `log_date`) VALUES (NULL, '".$v['id']."', 'New value = ".$new_vault_current."', CURRENT_TIMESTAMP);";
              $this->db->query($sql);
              
              
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
              $transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "1",
								"sum"  				=> $v['vault_paysum'],
								"fee"    			=> 0,
								"amount" 			=> $v['vault_paysum'],
								"currency"			=> $v['vault_currency'],
								"status" 			=> 2,
								"sender" 			=> $receiver_data['username'],
								"receiver" 			=> $receiver_data['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Vault #'.$v['id'].' transaction',
								"label" 	        => uniqid("vlt_"),
								"ip_address" 	    => $_SERVER["REMOTE_ADDR"],
								"protect" 	        => "none",
								)
							);
              $total_receiver = $receiver_data[$v['vault_currency']] - $v['vault_paysum'];
              
               //$receiver = $this->users_model->get_usernamemail($receiver_data)['username'];
               $this->users_model->update_wallet_transfer($receiver_data['username'],
                                  array(
                                      $v['vault_currency'] => $total_receiver,
                                      )
                                  );
               
            if ($v['vault_total'] <= $new_vault_current) { //Close vault & return funds to balance
              
              
              //delete VAULT
              $sql = "DELETE FROM `vaults` WHERE `id` = ".$v['id']." ";
              $this->db->query($sql);
              //.delete
              
              
              //return to balance
              $receiver_data = $this->users_model->get_user($v['user_id']);
              
              $new_balance = $receiver_data[$v['vault_currency']] + $new_vault_current;
              
              $transactions = $this->transactions_model->add_transaction(array(
								"type" 				=> "1",
								"sum"  				=> $new_balance,
								"fee"    			=> 0,
								"amount" 			=> $new_balance,
								"currency"			=> $v['vault_currency'],
								"status" 			=> 2,
								"sender" 			=> $receiver_data['username'],
								"receiver" 			=> $receiver_data['username'],
								"time"          	=> date('Y-m-d H:i:s'),
								"user_comment"  	=> 'Vault #'.$v['id'].' closed',
								"label" 	        => uniqid("vlt_"),
								"ip_address" 	    => $_SERVER["REMOTE_ADDR"],
								"protect" 	        => "none",
								)
							);
              
              $this->users_model->update_wallet_transfer($receiver_data['username'],
                                  array(
                                      $v['vault_currency'] => $new_balance,
                                      )
                                  );
              
              //.return 
              
              //Email Completed Vault
              $this->load->library('email');
              $this->load->library('currencys');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Vault goal completed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(37);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = base_url('account/vaults');
                                    $mail_amount = number_format($new_balance, 2, '.', '');
                                    if ($v['vault_currency'] == "debit_base") {
													$symbol = $this->currencys->display->base_code;
												} elseif ($v['vault_currency'] == "debit_extra1") {
													$symbol = $this->currencys->display->extra1_code;
												} elseif ($v['vault_currency'] == "debit_extra2") {
													$symbol = $this->currencys->display->extra2_code;
												} elseif ($v['vault_currency'] == "debit_extra3") {
													$symbol = $this->currencys->display->extra3_code;
												} elseif ($v['vault_currency'] =="debit_extra4") {
													$symbol = $this->currencys->display->extra4_code;
												} elseif ($v['vault_currency'] =="debit_extra5") {
													$symbol = $this->currencys->display->extra5_code;
												}
                                    $name_user2 = $user_receiver['first_name'] . ' ' . $user_receiver['last_name'];
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');
									$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $name_user2);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
              
              
              //.email
              
            }
    
              
            } else { //send email
              
              $this->load->library('email');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Vault recurring payment failed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(36);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $placeholders = array('[NAME]');
                                    $vals_1 = array($user_name);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
                                    //.email                      
              
              
              //
              
            }

            //.VAULT PAY
          }
          

          
        }
        
        
      }
      
      
    }

}
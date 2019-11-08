<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions_model extends CI_Model {

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
        $this->_db = 'transactions';
				$this->_db2 = 'vouchers';
    }
    
    function process_limited_transactions() {
      $sql = "SELECT * FROM `transactions` WHERE `status` = '6' ORDER BY `id` DESC";
      $query = $this->db->query($sql);
      $query = $query->result_array();
      
      //print("<pre>");
      //print_r($query);
      //print("</pre>");
      
      //check limit for each user/tr
      foreach ($query as $id=>$tr_data) {
        
        $receiver_user_id = $this->users_model->get_user_id($tr_data['receiver']);
        $currency = $tr_data['currency'];
        $amount = $tr_data['amount'];
        
        $result = $this->users_model->check_user_limit($receiver_user_id, $currency, $amount);
        
        if ($result['status'] == 1) { //we have free limit in $currency for $sum - proceess transaction
          
          //get receiver balance
          $sql2 = "SELECT ".$currency." FROM `users` WHERE `username` LIKE 'test555'";
          $q2 = $this->db->query($sql2);
          $q2 = $q2->row_array();
          $user_balance = $q2[$currency];
          
          //update wallet receiver
          $new_balance = $amount + $user_balance;
          $this->users_model->update_wallet_transfer($tr_data['receiver'],
			array(
              $currency => $new_balance,
			)
          );
          //update tr status
          $sql3 = "UPDATE `transactions` SET `status` = '2' WHERE `transactions`.`id` = ".$tr_data['id'].";";
          $this->db->query($sql3);
          
          //EMAIL notification for receiver
          $user_receiver = $this->users_model->get_usernamemail($tr_data['receiver']);
          $email_template2 = $this->template_model->get_email_template(9);
          
          $mail_amount = number_format($amount, 2, '.', '');
											
											if($email_template2['status'] == "1") {
												
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

												// variables to replace
												$site_name = $this->settings->site_name;
												$site_link  = base_url('account/dashboard');
												$name_user2 = $user_receiver['first_name'] . ' ' . $user_receiver['last_name'];

												$rawstring = $email_template2['message'];

												// what will we replace
												$placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');

												$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $name_user2);

												//replace
												$str_1 = str_replace($placeholders, $vals_1, $rawstring);

												$this -> email -> from($this->settings->site_email, $this->settings->site_name);
												$this->email->to($user_receiver['email']);
												$this -> email -> subject($email_template2['title']);

												$this -> email -> message($str_1);

												$this->email->send();

											}
											
											$sms_template2 = $this->template_model->get_sms_template(20);
							
											if($sms_template2['status'] == "1") {
												
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

												$rawstring = $sms_template2['message'];

												// what will we replace
												$placeholders = array('[SUM]', '[CUR]');

												$vals_1 = array($mail_amount, $symbol);

												//replace
												$str_1 = str_replace($placeholders, $vals_1, $rawstring);

												$result = $this->sms->send_sms($user_receiver['phone'], $str_1);

											}
          
          //END EMAIL NOTIFY
          
        }
        //print("<pre>");
        //print_r($result);
        //print("</pre>");
      }
      
      //die;
    }
	
    function get_user_ref_payouts($username) 
    {
      $sql = "SELECT sum(sum) FROM `transactions` WHERE `type` = '6' AND `status` = '2' AND `receiver` = '".$username."' ORDER BY `id` DESC";
      $query = $this->db->query($sql);
      $query = $query->row_array();
      if ($query['sum(sum)'] > 0) {
        $result = number_format($query['sum(sum)'], 2, '.', '');
      } else {
        $result = 0;
      }
      return $result;      
    }
    
	function get_transaction_user_admin($user) 
	{
		$where = "(sender = '$user' OR receiver = '$user')";
        $return = $this->db->where($where)->order_by('id', 'DESC')->limit(20)->get("transactions");
        
		return $return;
	}
	
	function get_all_vouchers($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db2}
						WHERE id > '0'
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
	
	function get_vouchers($id = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db2}
                WHERE id = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
	
	function update_voucher($transaction, $data) {
		$this->db->where("ID", $transaction)->update("vouchers", $data);
	}
	
	function get_all($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE id > '0'
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
        
        //print("<pre>");print_r($results);die;
        foreach ($results['results'] as $id=>$transaction) {
          if ($transaction['currency'] == 'debit_extra5') {
            $results['results'][$id]['sum'] = number_format($transaction['sum'], 8, '.', '');
            $results['results'][$id]['fee'] = number_format($transaction['fee'], 8, '.', '');
            $results['results'][$id]['amount'] = number_format($transaction['amount'], 8, '.', '');
          } else {
            $results['results'][$id]['sum'] = number_format($transaction['sum'], 2, '.', '');
            $results['results'][$id]['fee'] = number_format($transaction['fee'], 2, '.', '');
            $results['results'][$id]['amount'] = number_format($transaction['amount'], 2, '.', '');
          }
          
        }

        //print("<pre>");print_r($results);die;
        
        return $results;
    }
  
    function get_user_transactions($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE (sender = '{$user}' OR receiver = '{$user}')
        ";

        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
            {
                if ($key != 'timefrom' && $key != 'timeto') {
                  $value = $this->db->escape('%' . $value . '%');
                  $sql .= " AND {$key} LIKE {$value}";
                }
            }
            if (isset($filters['timefrom']) && isset($filters['timeto'])) {
              $sql .=" AND time BETWEEN '" . $filters['timefrom'] . "' AND '" . $filters['timeto'] . "'  ";
            } 
            elseif (isset($filters['timefrom']) && !isset($filters['timeto']))
              {
              $sql .=" AND time >= '" . $filters['timefrom'] . "'" ;
              }
             elseif (!isset($filters['timefrom']) && isset($filters['timeto']))
              {
              $sql .=" AND time <= '" . $filters['timeto'] . "'" ;
              }
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
        {
            $sql .= " LIMIT {$offset}, {$limit}";
        }
        
        //print($sql);

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

        
        
        foreach ($results['results'] as $id=>$result) {

          if ($result['currency'] != 'debit_extra5') {
            $results['results'][$id]['sum'] = number_format($result['sum'], 2, '.', '');
            $results['results'][$id]['amount'] = number_format($result['amount'], 2, '.', '');
            $results['results'][$id]['fee'] = number_format($result['fee'], 2, '.', '');
                    
          }
        }
        
        //print("<pre>");print_r($results);die;
        
        return $results;
    }	
	
	function get_history($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE sender = '{$user}' OR receiver = '{$user}'
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
	
	function get_pending($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE status = '1'
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
	
	function get_confirmed($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE status = '2'
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
	
	function get_disputed($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE status = '4'
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
	
	function get_blocked($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE status = '5'
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
	
	function get_refunded($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE status = '3'
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
	
	function get_total_transactions() 
	{
		$s= $this->db->select("COUNT(*) as num")->get("transactions");
		$r = $s->row();
		if(isset($r->num)) return $r->num;
		return 0;
	}
    
    function check_referal_payout($referal_name) {
      
      $sql = "SELECT * FROM `users` WHERE `username` LIKE '".$referal_name."' ";
      $query = $this->db->query($sql);
      $result = $query->row_array();
      $ref_email = $result['email'];
      //print('1rmail='.$ref_email.'<br>');

      $sql = "SELECT * FROM `referrals` WHERE email = '".$ref_email."' ";
      $query = $this->db->query($sql);
      $result = $query->row_array();
      
      //print('rmail='.$result['status'].'<br>');
      
      if (isset($result['status'])) {
        $status['ref_status'] = $result['status'];
        
        $receiver_username = $result['username'];
        
        if ($status['ref_status'] == 1){ //if no ref payment
        
          $sql = "SELECT * FROM `transactions` WHERE `type` = '1' AND `receiver` LIKE '".$referal_name."' AND status = 2 ORDER BY `id` DESC";
          $query = $this->db->query($sql);
          $result = $query->result_array();
          $sum = 0;
          foreach ($result as $k=>$v) {
            $sum += $v['sum'];
          }
          
          if ($sum >= 100) {
            $status['payout_status'] = 1;
            $status['ref_email'] = $ref_email;
            $status['receiver_username'] = $receiver_username;
          } 
          
        } else {
          $status['payout_status'] = 0;
        }
        
      } else {
        $status['ref_status'] = 0;
      }
      
      return $status;
      
    }
	
	function get_transactions($id = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE id = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
              
              $return = $query->row_array();
              
              
              
              if ($return['currency'] == 'debit_extra5') {
              
              $return['amount'] = number_format($return['amount'], 8, '.', '');
              $return['sum']    = number_format($return['sum'], 8, '.', '');
              $return['fee']    = number_format($return['fee'], 8, '.', '');
              
              } else {
                $return['amount'] = number_format($return['amount'], 2, '.', '');
                $return['sum']    = number_format($return['sum'], 2, '.', '');
                $return['fee']    = number_format($return['fee'], 2, '.', '');
              }
              
              return $return;
            }
        }

        return FALSE;
    }	
	
	function get_label($label = NULL)
    {
        if ($label)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE label = " . $this->db->escape($label) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_duplicate($txn_id = NULL)
    {
        if ($txn_id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE user_comment = " . $this->db->escape($txn_id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_chain($user_comment = NULL)
    {
        if ($user_comment)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE user_comment = " . $this->db->escape($user_comment) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_merchant($id = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM merchants
                WHERE id = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
	
	function get_chain_sci($adress = NULL)
    {
        if ($adress)
        {
            $sql = "
                SELECT *
                FROM btc_order
                WHERE adress = " . $this->db->escape($adress) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_adress($adress = NULL)
    {
        if ($adress)
        {
            $sql = "
                SELECT *
                FROM {$this->_db_sci}
                WHERE adress = " . $this->db->escape($adress) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function delete_order($adress) {
		$this->db->where("adress", $adress)->delete("btc_order");
	}
	
	function get_detail_transactions($id = NULL, $username = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
								WHERE (id = " . $this->db->escape($id) . " AND sender = " . $this->db->escape($username) . ") OR (id = " . $this->db->escape($id) . " AND receiver = " . $this->db->escape($username) . ")
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
              
              $return = $query->row_array();
              
              if ($return['currency'] == 'debit_extra5') {
              
              $return['amount'] = number_format($return['amount'], 8, '.', '');
              $return['sum']    = number_format($return['sum'], 8, '.', '');
              $return['fee']    = number_format($return['fee'], 8, '.', '');
              
              } else {
                $return['amount'] = number_format($return['amount'], 2, '.', '');
                $return['sum']    = number_format($return['sum'], 2, '.', '');
                $return['fee']    = number_format($return['fee'], 2, '.', '');
              }
              
              return $return;
            }
        }

        return FALSE;
    }	
	
	function get_detail_btc_order($adress = NULL)
    {
        if ($adress)
        {
            $sql = "
                SELECT *
                FROM {$this->_db_sci}
                WHERE adress = " . $this->db->escape($adress) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_detail_btc_transactions($user_comment = NULL, $username = NULL)
    {
        if ($user_comment)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
								WHERE (user_comment = " . $this->db->escape($user_comment) . " AND sender = " . $this->db->escape($username) . ") OR (user_comment = " . $this->db->escape($user_comment) . " AND receiver = " . $this->db->escape($username) . ")
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function get_start_dispute($id = NULL, $username = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
								WHERE id = " . $this->db->escape($id) . " AND sender = " . $this->db->escape($username) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	public function update_transactions($data) 
	{
		$this->db->where("id", $this->db->escape($data['id']))->update("transactions", $data);
	}
	
	function update_btc_transactions($transaction, $data) {
		$this->db->where("ID", $transaction)->update("transactions", $data);
	}
	
	function update_dispute_transactions($transaction, $data) {
		$this->db->where("ID", $transaction)->update("transactions", $data);
	}
	
	function add_transaction($data) 
	{
      $this->db->insert("transactions", $data); 
      return $this->db->insert_id();
	}
	
	function get_detail_sci_transactions($badge_sci = NULL)
    {
        if ($badge_sci)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE admin_comment = " . $this->db->escape($badge_sci) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }	
	
	function add_order($data) 
	{
		$this->db->insert("btc_order", $data);
		return $this->db->insert_id();
	}
	
	function get_log_transactions() 
	{
		return $this->db->where("status", "1")->order_by('id', 'DESC')->limit(5)->get("transactions");
	}
	
	/**
     * Edit transaction
     *
     * @param  array $data
     * @return boolean
     */
    function edit_transaction($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
										time = " . $this->db->escape($data['time']) . ",
										ip_address = " . $this->db->escape($data['ip_address']) . ",
										label = " . $this->db->escape($data['label']) . ",
										protect = " . $this->db->escape($data['protect']) . ",
                    sender = " . $this->db->escape($data['sender']) . ",
                    receiver = " . $this->db->escape($data['receiver']) . ",
                    user_comment = " . $this->db->escape($data['user_comment']) . ",
                    admin_comment = " . $this->db->escape($data['admin_comment']) . "
                WHERE id = " . $this->db->escape($data['id']) . "
            ";

            $this->db->query($sql);

            if ($this->db->affected_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }
	
	/**
     * Save generated CAPTCHA to database
     *
     * @param  array $data
     * @return boolean
     */
    public function save_captcha($data = array())
    {
        // CAPTCHA data required
        if ($data)
        {
            // insert CAPTCHA
            $query = $this->db->insert_string('captcha', $data);
            $this->db->query($query);

            // return
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Verify CAPTCHA
     *
     * @param  string $captcha
     * @return boolean
     */
    public function verify_captcha($captcha = NULL)
    {
        // CAPTCHA string required
        if ($captcha)
        {
            // remove old CAPTCHA
            $expiration = time() - 7200; // 2-hour limit
            $this->db->query("DELETE FROM captcha WHERE captcha_time < {$expiration}");

            // build query
            $sql = "
                SELECT
                    COUNT(*) AS count
                FROM captcha
                WHERE word = " . $this->db->escape($captcha) . "
                    AND ip_address = '" . $this->input->ip_address() . "'
            ";

            // execute query
            $query = $this->db->query($sql);

            // return results
            if ($query->row()->count > 0)
            {
                return TRUE;
            }
        }

        return FALSE;
    }
	
	function delete_captcha($captcha) {
		$this->db->where("word", $captcha)->delete("captcha");
	}
	
	// total deposits ////////////////////////////////////////////
  function total_deposits($data = array())
  {
		if ( ! empty($data)) {
    	
				$where = "(status = '2' AND type = '1' ";
                
                $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1')";
		}
		//$where = "(status = '2' AND type = '1' AND time >= '2018-02-05' AND time <= '2018-02-16')";
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_base($data = array())
  {
		if ( ! empty($data)) {
    	
				$where = "(status = '2' AND type = '1' AND currency = 'debit_base' ";
                
                $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_base')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_extra1($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_extra2($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_extra3($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_extra4($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// total deposits
  function total_deposits_debit_extra5($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	function select_sum_total_deposits_debit_base($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_deposits_debit_extra1($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_deposits_debit_extra2($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_deposits_debit_extra3($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_deposits_debit_extra4($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_deposits_debit_extra5($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	// FEE //
	
	function select_sum_total_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_base'";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
    } else {
			$where = "(status = '2' AND type = '1' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	// deposit method stat
	
	function select_sum_total_method($method = NULL, $data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND sender = '$method' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '1' AND sender = '$method')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	function select_sum_win_total_method($method = NULL, $data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '1' AND user_comment = '$method' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND user_comment = '$method')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	// summar
	
	function select_sum_withd_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            //print($start_date);        
            //print($end_date);        
            
            if ( $start_date > 4) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
             
        } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
            
        } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_base' ";

            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	
	
	function select_sum_withd_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra1' ";
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	
	function select_sum_withd_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
            
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_withd_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_withd_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_withd_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_transfer_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '3' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_exchange_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '4' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_sci_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '5' AND currency = 'debit_extra5')";
		}
        
        //print($where);
        
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	// withdrawal
	
  function total_withdrawal($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
  function total_withdrawal_debit_base($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_base')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
  function total_withdrawal_debit_extra1($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra1')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }

  function total_withdrawal_debit_extra2($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra2')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }

  function total_withdrawal_debit_extra3($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra3')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
  function total_withdrawal_debit_extra4($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra4')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
  function total_withdrawal_debit_extra5($data = array())
  {
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra5')";
		}
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	function select_sum_total_withdrawal_debit_base($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_debit_extra1($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_debit_extra2($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_debit_extra3($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_debit_extra4($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_debit_extra5($data = array())  // sum profit 
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('sum', 'Sum');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Sum == NULL) {
			return 0;
		} else {
			return $result[0]->Sum;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_base($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_base' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_base')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_extra1($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra1' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra1')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_extra2($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra2' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra2')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_extra3($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra3' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra3')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_extra4($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra4' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra4')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function select_sum_total_withdrawal_fee_debit_extra5($data = array())
	{
		if ( ! empty($data)) {
    	
			$where = "(status = '2' AND type = '2'  AND currency = 'debit_extra5' ";
            
            $start_date = strlen($this->db->escape($data['start_date']));
            $end_date = strlen($this->db->escape($data['end_date']));
            
            if ( $start_date > 4 ) {
              $where .= " AND time >= " . $this->db->escape($data['start_date']) . " ";
            }
            if ( $end_date > 4  ) {

              $where .= " AND time <= " . $this->db->escape($data['end_date']) . "";
            }

            $where .= ")";
    } else {
			$where = "(status = '2' AND type = '2' AND currency = 'debit_extra5')";
		}
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
	}
	
	function hold_balance($user = NULL, $currency = NULL)
	{

		$where = "(status = '4' AND receiver = '$user' AND currency = '$currency') OR (status = '5' AND receiver = '$user' AND currency = '$currency') OR (status = '6' AND receiver = '$user' AND currency = '$currency')";
		$query = $this->db->select_sum('amount', 'Amount');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Amount == NULL) {
			return 0;
		} else {
            if ($currency == 'debit_extra5') {
              
              $result[0]->Amount = number_format($result[0]->Amount, 8, '.', '');
            } else {
              $result[0]->Amount = number_format($result[0]->Amount, 2, '.', '');
            }

			return $result[0]->Amount;
		}
		
	}
	
	function profit_user_debit_base($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_base') OR (status = '2' AND receiver = '$user' AND currency = 'debit_base')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	function profit_user_debit_extra1($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_extra1') OR (status = '2' AND receiver = '$user' AND currency = 'debit_extra1')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	function profit_user_debit_extra2($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_extra2') OR (status = '2' AND receiver = '$user' AND currency = 'debit_extra2')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	function profit_user_debit_extra3($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_extra3') OR (status = '2' AND receiver = '$user' AND currency = 'debit_extra3')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	function profit_user_debit_extra4($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_extra4') OR (status = '2' AND receiver = '$user' AND currency = 'debit_extra4')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	function profit_user_debit_extra5($user = NULL)
	{

		$where = "(status = '2' AND sender = '$user' AND currency = 'debit_extra5') OR (status = '2' AND receiver = '$user' AND currency = 'debit_extra5')";
		$query = $this->db->select_sum('fee', 'Fee');
		$query = $this->db->where($where);
		$query = $this->db->get('transactions');
		$result = $query->result();
		if ($result[0]->Fee == NULL) {
			return 0;
		} else {
			return $result[0]->Fee;
		}
		
	}
	
	// total transactions ////////////////////////////////////////////
  function total_dash_transactions()
  {
  	$s= $this->db->select("COUNT(*) as num")->get("transactions");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Transactions;
  }
	
	function get_pending_dash($user) 
	{
		$where = "status = '1' AND type = '2'";
		return $this->db->where($where)->order_by('id', 'DESC')->limit(20)->get("transactions");
	}
    
  function temp_epay($user_id, $username) {
    
    $sql = "SELECT * FROM `transactions` WHERE `receiver` LIKE '".$username."' AND `label` LIKE '%epay_%' AND status = 1 ORDER BY `transactions`.`id` DESC";
    $query = $this->db->query($sql);
    $transaction = $query->result_array();
    
    //print_r($tra)
    
    if (isset($transaction[0])) {
      $transaction = $transaction[0];

      $user = $this->users_model->get_user($user_id);

      $sql = "UPDATE `transactions` SET `status` = '2' WHERE `transactions`.`id` = ".$transaction['id'].";";
      $this->db->query($sql);

      $new_amount = $user[$transaction['currency']] + $transaction['amount'];

      $this->users_model->update_wallet_transfer($username,
          array(
             $transaction['currency'] => $new_amount,
          )
        );
      
      //EMAIL notification for receiver
          $user_receiver = $user;
          //$user_receiver = $this->users_model->get_usernamemail($tr_data['receiver']);
          $email_template2 = $this->template_model->get_email_template(9);
          
          $mail_amount = number_format($transaction['amount'], 2, '.', '');
											
											if($email_template2['status'] == "1") {
												
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

												// variables to replace
												$site_name = $this->settings->site_name;
												$site_link  = base_url('account/dashboard');
												$name_user2 = $user_receiver['first_name'] . ' ' . $user_receiver['last_name'];

												$rawstring = $email_template2['message'];

												// what will we replace
												$placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');

												$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $name_user2);

												//replace
												$str_1 = str_replace($placeholders, $vals_1, $rawstring);

												$this -> email -> from($this->settings->site_email, $this->settings->site_name);
												$this->email->to($user_receiver['email']);
												$this -> email -> subject($email_template2['title']);

												$this -> email -> message($str_1);

												$this->email->send();

											}
											
											$sms_template2 = $this->template_model->get_sms_template(20);
							
											if($sms_template2['status'] == "1") {
												
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

												$rawstring = $sms_template2['message'];

												// what will we replace
												$placeholders = array('[SUM]', '[CUR]');

												$vals_1 = array($mail_amount, $symbol);

												//replace
												$str_1 = str_replace($placeholders, $vals_1, $rawstring);

												$result = $this->sms->send_sms($user_receiver['phone'], $str_1);

											}
                                             
                                             
          
          //END EMAIL NOTIFY
      
    }
    
    
        
  }
    
  function check_limited_transactions() {
    $sql = "SELECT * FROM `transactions` WHERE `status` = '6' ORDER BY `id` DESC";
    $query = $this->db->query($sql);
    $query = $query->result_array();
    
    foreach ($query as $id=>$transaction) {
      
      if ($transaction['currency'] == 'debit_base') { //EUR
        $index = 'eur';
      } elseif ($transaction['currency'] == 'debit_extra1') { //USD
        $index = 'usd';
      } elseif ($transaction['currency'] == 'debit_extra2') { //GBP
        $index = 'gbp';
      }
      
      $user_id = $this->users_model->get_user_id($transaction['receiver']);  
      //check current user limit
      $limits = $this->users_model->get_user_limits($user_id);
      //print("<pre>");
      //print_r($limits);
      //die;
      
      if ($limits['limit_status'][$index] == 1) {
        //print($index.' '.$limits['limit_status'][$index]);
        $check_amount = $this->users_model->check_user_limit($user_id, $transaction['currency'], $transaction['amount']);
        
        if ($check_amount['status'] == 1) {

          //modify transaction status 6 => 1/2
          if ($transaction['protect'] != 'none') {
            $sql = "UPDATE `transactions` SET `status` = '1' WHERE `transactions`.`id` = ".$transaction['id'].";"; //set to pending
          } else {
            $sql = "UPDATE `transactions` SET `status` = '2' WHERE `transactions`.`id` = ".$transaction['id'].";";
            
            //LIMIT update wallet 
            $this->users_model->update_wallet_transfer($transaction['receiver'],
                                  array(
                                      $transaction['currency'] => $transaction['amount'],
                                      )
                                  );
            
          }
          $this->db->query($sql);
          
        }
        
      }
      
      
    }
  }
    

}
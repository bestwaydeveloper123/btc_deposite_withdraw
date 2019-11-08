<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {

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
        $this->_db = 'settings';
        $this->_db2 = 'settings_withdrawal';
        $this->_db3 = 'settings_deposit';
        $this->_db4 = 'crypt_address_limits';
    }
    
    function get_add_btc_address_price() {
      $sql = "SELECT * FROM crypt_address_limits WHERE currency = 'debit_extra5'";
      $query = $this->db->query($sql);
      $result = $query->row_array();
      
      $result['description'] = "BTC address limit";
      
      return $result;
    }
    
    function get_address_limits() {
      $sql = "SELECT * FROM crypt_address_limits";
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      foreach ($result as $k=>$v) {
        if ($v['currency'] == 'debit_extra5') {
          $result[$k]['description'] = "BTC address limit";
        }
      }
      
      return $result;
    }
    
    function update_btc_address_limits($data) {
      $sql = "UPDATE `crypt_address_limits` SET `address_max` = '".$data['btc_address_max']."', `address_addon` = '".$data['btc_address_addon']."', `address_price` = '".$data['btc_address_price']."', `address_price_currency` = '".$data['btc_address_price_currency']."' WHERE `ca_limit` = 1;";
      $this->db->query($sql);
    }

    /**
     * Retrieve all settings
     *
     * @return array|null
     */
    function get_settings()
    {
        $results = NULL;

        $sql = "
            SELECT *
            FROM {$this->_db}
            ORDER BY sort_order ASC
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $results = $query->result_array();
        }

        return $results;
    }
    
    function update_user_notification($user_id, $params) {

      if ($params['status'] == 'false') {
        $params['status'] = 0;
      } else {
        $params['status'] = 1;
      }

      //check if setting exist for user
      $sql = "SELECT * FROM `users_notification_settings` WHERE `user_id` = ".$user_id." AND type = '".$params['type']."' AND id = ".$params['id']." ";
      $query = $this->db->query($sql);
      if ($query->num_rows()) { $exist = 1; } else {$exist = 0;}
        
      if ($exist == 1) { //change value
        $sql = "UPDATE `users_notification_settings` SET `status` = '".$params['status']."' WHERE `user_id` = ".$user_id." AND type = '".$params['type']."' AND id = ".$params['id']." ";
        $this->db->query($sql);
      } else { //insert value
        $sql = "INSERT INTO `users_notification_settings` (`ns_id`, `user_id`, `type`, `id`, `status`) VALUES (NULL, '".$user_id."', '".$params['type']."', '".$params['id']."', '".$params['status']."');";
        $this->db->query($sql);
      }
       
      
    }
    
    function get_user_notifications($user) {
      $return = [];
      
      $sql = "SELECT * FROM `email_templates` WHERE `user_enabled` = 1";
      $query = $this->db->query($sql);
      
      if ($query->num_rows()) {
        $n_array = $query->result_array();
        foreach ($n_array as $k=>$v) {
          
          $params['type'] = 'email';
          $params['id'] = $v['id'];
          $params['user_id'] = $user['id'];
          
          $item['id'] = $v['id'];
          $item['name'] = $v['title'];
          $item['type'] = $params['type'];
          $item['status'] = $this->users_model->get_user_notification_settings($params);
          $item['global_status'] = $v['status'];
          
          array_push($return, $item);
          unset($item);
        }
      }
      
      $sql = "SELECT * FROM `sms_template` WHERE `user_enabled` = 1";
      $query = $this->db->query($sql);
      
      if ($query->num_rows()) {
        $n_array = $query->result_array();
        foreach ($n_array as $k=>$v) {
          
          $params['type'] = 'sms';
          $params['id'] = $v['id'];
          $params['user_id'] = $user['id'];
          
          $item['id'] = $v['id'];
          $item['name'] = $v['title'];
          $item['type'] = $params['type'];
          $item['status'] = $this->users_model->get_user_notification_settings($params);
          $item['global_status'] = $v['status'];
          
          array_push($return, $item);
          unset($item);
        }
      }
      
      return $return;
      
    }
  
    function get_twilio_lib($id = NULL)
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
                return $query->row_array();
            }
        }

        return FALSE;
    }
    
    function save_limits($data=array(), $user_id=NULL) {
      //print_r($data);
      //die;
      
      $saved = 0;
      
      foreach ($data as $param => $value) {
        $sql = "UPDATE `settings` SET `value` = '".$value."' WHERE `settings`.`name` = '".$param."'";
        $this->db->query($sql);
        if ($this->db->affected_rows() > 0)
          {
           $saved = 1;
          }
      }
      
      return $saved;
      
    }


    /**
     * Save changes to the settings
     *
     * @param  array $data
     * @param  int $user_id
     * @return boolean
     */
    function save_settings($data=array(), $user_id=NULL)
    {
        if ($data && $user_id)
        {
            $saved = FALSE;

            foreach ($data as $key => $value)
            {
                $sql = "
                    UPDATE {$this->_db}
                    SET value = " . ((is_array($value)) ? $this->db->escape(serialize($value)) : $this->db->escape($value)) . ",
                        last_update = '" . date('Y-m-d H:i:s') . "',
                        updated_by = " . $this->db->escape($user_id) . "
                    WHERE name = " . $this->db->escape($key) . "
                ";

                $this->db->query($sql);

                if ($this->db->affected_rows() > 0)
                {
                    $saved = TRUE;
                }
            }

            if ($saved)
            {
                return TRUE;
            }
        }

        return FALSE;
    }
  
    function get_all_withdrawal($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db2}
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
  
    function get_all_deposit($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db3}
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
  
    function get_win_method($id = NULL)
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
  
    function get_dep_method($id = NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db3}
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
  
    function get_dep_method_name($name = NULL)
    {
        if ($name)
        {
            $sql = "
                SELECT *
                FROM {$this->_db3}
                WHERE name = " . $this->db->escape($name) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
  
    function get_win_method_name($name = NULL)
    {
        if ($name)
        {
            $sql = "
                SELECT *
                FROM {$this->_db2}
                WHERE name = " . $this->db->escape($name) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
  
    function edit_win_methode($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db2}
                SET
                    name = " . $this->db->escape($data['name']) . ",
                    fee_fix = " . $this->db->escape($data['fee_fix']) . ",
                    fee = " . $this->db->escape($data['fee']) . ",
                    terms = " . $this->db->escape($data['terms']) . ",
                    start_verify = " . $this->db->escape($data['start_verify']) . ",
                    standart_verify = " . $this->db->escape($data['standart_verify']) . ",
                    expanded_verify = " . $this->db->escape($data['expanded_verify']) . ",
                    debit_base = " . $this->db->escape($data['debit_base']) . ",
                    debit_extra1 = " . $this->db->escape($data['debit_extra1']) . ",
                    debit_extra2 = " . $this->db->escape($data['debit_extra2']) . ",
                    debit_extra3 = " . $this->db->escape($data['debit_extra3']) . ",
                    debit_extra4 = " . $this->db->escape($data['debit_extra4']) . ",
                    debit_extra5 = " . $this->db->escape($data['debit_extra5']) . ",
                    minimum_debit_base = " . $this->db->escape($data['minimum_debit_base']) . ",
                    maximum_debit_base = " . $this->db->escape($data['maximum_debit_base']) . ",
                    minimum_debit_extra1 = " . $this->db->escape($data['minimum_debit_extra1']) . ",
                    maximum_debit_extra1 = " . $this->db->escape($data['maximum_debit_extra1']) . ",
                    minimum_debit_extra2 = " . $this->db->escape($data['minimum_debit_extra2']) . ",
                    maximum_debit_extra2 = " . $this->db->escape($data['maximum_debit_extra2']) . ",
                    minimum_debit_extra3 = " . $this->db->escape($data['minimum_debit_extra3']) . ",
                    maximum_debit_extra3 = " . $this->db->escape($data['maximum_debit_extra3']) . ",
                    minimum_debit_extra4 = " . $this->db->escape($data['minimum_debit_extra4']) . ",
                    maximum_debit_extra4 = " . $this->db->escape($data['maximum_debit_extra4']) . ",
                    minimum_debit_extra5 = " . $this->db->escape($data['minimum_debit_extra5']) . ",
                    maximum_debit_extra5 = " . $this->db->escape($data['maximum_debit_extra5']) . ",
                    status = " . $this->db->escape($data['status']) . "
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
  
    function edit_dep_methode($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db3}
                SET
                    name = " . $this->db->escape($data['name']) . ",
                    fee_fix = " . $this->db->escape($data['fee_fix']) . ",
                    fee = " . $this->db->escape($data['fee']) . ",
                    start_verify = " . $this->db->escape($data['start_verify']) . ",
                    standart_verify = " . $this->db->escape($data['standart_verify']) . ",
                    expanded_verify = " . $this->db->escape($data['expanded_verify']) . ",
                    debit_base = " . $this->db->escape($data['debit_base']) . ",
                    debit_extra1 = " . $this->db->escape($data['debit_extra1']) . ",
                    debit_extra2 = " . $this->db->escape($data['debit_extra2']) . ",
                    debit_extra3 = " . $this->db->escape($data['debit_extra3']) . ",
                    debit_extra4 = " . $this->db->escape($data['debit_extra4']) . ",
                    debit_extra5 = " . $this->db->escape($data['debit_extra5']) . ",
                    minimum_debit_base = " . $this->db->escape($data['minimum_debit_base']) . ",
                    maximum_debit_base = " . $this->db->escape($data['maximum_debit_base']) . ",
                    minimum_debit_extra1 = " . $this->db->escape($data['minimum_debit_extra1']) . ",
                    maximum_debit_extra1 = " . $this->db->escape($data['maximum_debit_extra1']) . ",
                    minimum_debit_extra2 = " . $this->db->escape($data['minimum_debit_extra2']) . ",
                    maximum_debit_extra2 = " . $this->db->escape($data['maximum_debit_extra2']) . ",
                    minimum_debit_extra3 = " . $this->db->escape($data['minimum_debit_extra3']) . ",
                    maximum_debit_extra3 = " . $this->db->escape($data['maximum_debit_extra3']) . ",
                    minimum_debit_extra4 = " . $this->db->escape($data['minimum_debit_extra4']) . ",
                    maximum_debit_extra4 = " . $this->db->escape($data['maximum_debit_extra4']) . ",
                    minimum_debit_extra5 = " . $this->db->escape($data['minimum_debit_extra5']) . ",
                    maximum_debit_extra5 = " . $this->db->escape($data['maximum_debit_extra5']) . ",
                    ac_debit_base = " . $this->db->escape($data['ac_debit_base']) . ",
                    ac_debit_extra1 = " . $this->db->escape($data['ac_debit_extra1']) . ",
                    ac_debit_extra2 = " . $this->db->escape($data['ac_debit_extra2']) . ",
                    ac_debit_extra3 = " . $this->db->escape($data['ac_debit_extra3']) . ",
                    ac_debit_extra4 = " . $this->db->escape($data['ac_debit_extra4']) . ",
                    ac_debit_extra5 = " . $this->db->escape($data['ac_debit_extra5']) . ",
                    api_value1 = " . $this->db->escape($data['api_value1']) . ",
                    api_value2 = " . $this->db->escape($data['api_value2']) . ",
                    status = " . $this->db->escape($data['status']) . "
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
    
    
   function check_dep_cur($data) {
   
    $sql = "SELECT debit_base, debit_extra1, debit_extra2, debit_extra3, debit_extra4, debit_extra5 FROM `settings_deposit` WHERE `id` = ".$data['method']." ";
    $result = $this->db->query($sql);
    $result = $result->row_array();
    $return['status'] = $result[$data['input_cur']];
    
    foreach ($result as $k=>$v) {
      if ($v == 1) {$return['cur'][] = $k;}
    }
         
    return $return;
   }
   
   function check_w_cur($data) {
   
    $sql = "SELECT debit_base, debit_extra1, debit_extra2, debit_extra3, debit_extra4, debit_extra5 FROM `settings_withdrawal` WHERE `id` = ".$data['method']." ";
    $result = $this->db->query($sql);
    $result = $result->row_array();
    $return['status'] = $result[$data['input_cur']];
    
    foreach ($result as $k=>$v) {
      if ($v == 1) {$return['cur'][] = $k;}
    }
         
    return $return;
   }
   
   public function get_cards_section_vis() {
     $sql = "SELECT value FROM `settings` WHERE `id` = 33";
     $result = $this->db->query($sql);
      $result = $result->row_array();
      
      return $result['value'];
   }
   
   public function get_credit_settings() {
     $sql = "SELECT * FROM `settings_credit`";
     $result = $this->db->query($sql);
     $result = $result->result_array();
     
     
     return $result;
     
   }
   
   public function update_credit_settings($data) {
     
     foreach ($data as $k => $v) {
       $sql = "UPDATE `settings_credit` SET `value` = '".$v."' WHERE `param` = '".$k."';";
       $this->db->query($sql);
     }
     //dump_exit($data);
   }

}

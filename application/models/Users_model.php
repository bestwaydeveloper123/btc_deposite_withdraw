<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

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
        $this->_db = 'users';
        $this->_db2 = 'referrals';
        $this->_db3 = 'users_notification_settings';
    }
    
    function update_max_btc_addresses($max_addresses) {
      $sql = "UPDATE `users` SET `debit_extra5_max_addresses` = '".$max_addresses."' ";
      $this->db->query($sql);
    }
    
    function get_user_btc_address_limit($user_id) {
      $sql = "SELECT debit_extra5_max_addresses, debit_extra5_addresses FROM `users` WHERE `id` = ".$user_id." ";
      $result = $this->db->query($sql);
      $result = $result->row_array();
      
      $return = $result['debit_extra5_max_addresses'] + $result['debit_extra5_addresses'];
      
      return $return;
    }
    
    function add_crypto_address($user_id, $currency, $value) {
      $sql = "UPDATE `users` SET `debit_extra5_addresses` = '".$value."' WHERE `id` = ".$user_id.";";
      $this->db->query($sql);
    }
    
    function get_user_id($user_data) {
      
      //print($user_data); die;
      if (strpos($user_data, '@') !== false) {
        $where = 'email';
      } else {
        $where = 'username';
      }
      
      $sql = "SELECT id FROM `users` WHERE `".$where."` LIKE '".$user_data."'";
      $query = $this->db->query($sql);
      $query = $query->row_array();
      $query = $query['id'];
      
      return $query;
    }
   

    function get_user_notification_settings ($params) {
      $sql = "SELECT * FROM `users_notification_settings` WHERE `user_id` = ".$params['user_id']." AND type = '".$params['type']."' AND id = ".$params['id']." ";
      $query = $this->db->query($sql);
      if ($query->num_rows()) {
        $query = $query->row_array();
        $status = $query['status'];
      } else {
        $status = 1;
      }
      return $status;
    }
     
    
    /**
     * Get list of non-deleted users
     *
     * @param  int $limit
     * @param  int $offset
     * @param  array $filters
     * @param  string $sort
     * @param  string $dir
     * @return array|boolean
     */
    function get_all($limit=0, $offset=0, $filters=array(), $sort='last_name', $dir='ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
            WHERE deleted = '0'
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


    /**
     * Get specific user
     *
     * @param  int $id
     * @return array|boolean
     */
    function get_user($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE id = " . $this->db->escape($id) . "
                    AND deleted = '0'
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
  
    
    function get_usernamemail($user = NULL)
    {
        if ($user)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE username = " . $this->db->escape($user) . " OR email = " . $this->db->escape($user) . "
                    AND deleted = '0'
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
    
	function get_username($user = NULL)
    {
        if ($user)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE username = " . $this->db->escape($user) . "
                    AND deleted = '0'
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
              $return = $query->row_array();
              $return['debit_extra5'] = number_format($return['debit_extra5'], 8, '.', '');
              //print("<pre>");
              //print_r($return);die;
              
              return $return;
            }
        }
        
        

        return FALSE;
    }


    /**
     * Add a new user
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function add_user($data=array())
    {
        if ($data)
        {
            // secure password
            $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
            $password = hash('sha512', $data['password'] . $salt);

            $sql = "
                INSERT INTO {$this->_db} (
                    username,
                    password,
                    salt,
                    first_name,
                    last_name,
                    email,
                    language,
                    is_admin,
                    status,
                    deleted,
                    created,
                    updated
                ) VALUES (
                    " . $this->db->escape($data['username']) . ",
                    " . $this->db->escape($password) . ",
                    " . $this->db->escape($salt) . ",
                    " . $this->db->escape($data['first_name']) . ",
                    " . $this->db->escape($data['last_name']) . ",
                    " . $this->db->escape($data['email']) . ",
                    " . $this->db->escape($this->config->item('language')) . ",
                    " . $this->db->escape($data['is_admin']) . ",
                    " . $this->db->escape($data['status']) . ",
                    '0',
                    '" . date('Y-m-d H:i:s') . "',
                    '" . date('Y-m-d H:i:s') . "'
                )
            ";

            $this->db->query($sql);

            if ($id = $this->db->insert_id())
            {
                return $id;
            }
        }

        return FALSE;
    }
	
		function update_wallet_transfer($username, $data) {
          
			$this->db->where("username", $username)->update("users", $data);
		}


    /**
     * User creates their own profile
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function create_profile($data=array(), $ip = NULL)
    {
        if ($data)
        {
            // secure password and create validation code
            $salt            = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
            $password        = hash('sha512', $data['password'] . $salt);
            $validation_code = sha1(microtime(TRUE) . mt_rand(10000, 90000));

            $sql = "
                INSERT INTO {$this->_db} (
                    username,
                    password,
                    salt,
                    first_name,
                    last_name,
                    email,
                    language,
                    is_admin,
                    status,
                    deleted,
                    validation_code,
                    created,
                    updated,
                    ip_address, 
                    bday
                ) VALUES (
                    " . $this->db->escape($data['username']) . ",
                    " . $this->db->escape($password) . ",
                    " . $this->db->escape($salt) . ",
                    " . $this->db->escape($data['first_name']) . ",
                    " . $this->db->escape($data['last_name']) . ",
                    " . $this->db->escape($data['email']) . ",
                    " . $this->db->escape($data['language']) . ",
                    '0',
                    '0',
                    '0',
                    " . $this->db->escape($validation_code) . ",
                    '" . date('Y-m-d H:i:s') . "',
                    '" . date('Y-m-d H:i:s') . "',
                    " . $this->db->escape($ip) . ",
                    " . $this->db->escape($data['bday']) . "

                )
            ";

            $this->db->query($sql);

            if ($this->db->insert_id())
            {
              
              if (isset($data['reflink'])) {
                $sql = "SELECT * FROM `users` WHERE `reflink` LIKE '".$data['reflink']."'";
                $query = $this->db->query($sql);
                $result = $query->row_array();
                $ref_username = $result['username'];

                $sql = "UPDATE `referrals` SET `status` = '1' WHERE `referrals`.`username` = '".$ref_username."' AND `referrals`.`email` = '".$data['email']."' ";
                $this->db->query($sql);
                
                if (!$this->db->affected_rows()) {
                  $date = date('Y-m-d');
                  $sql = "INSERT INTO `referrals` (`id`, `username`, `email`, `date_added`, `status`) VALUES (NULL, '".$ref_username."', '".$data['email']."', '".$date."', '1');";
                  $this->db->query($sql);
                }
              }
              
                return $validation_code;
            }
            
            
        }

        return FALSE;
    }


    /**
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    function edit_user($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    username = " . $this->db->escape($data['username']) . ",
            ";

            if ($data['password'] != '')
            {
                // secure password
                $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $data['password'] . $salt);

                $sql .= "
                    password = " . $this->db->escape($password) . ",
                    salt = " . $this->db->escape($salt) . ",
                ";
            }

            $sql .= "
                    bday = " . $this->db->escape($data['bday']) . ",
                    first_name = " . $this->db->escape($data['first_name']) . ",
                    last_name = " . $this->db->escape($data['last_name']) . ",
                    email = " . $this->db->escape($data['email']) . ",
										verify_status = " . $this->db->escape($data['verify_status']) . ",
                    language = " . $this->db->escape($data['language']) . ",
                    is_admin = " . $this->db->escape($data['is_admin']) . ",
                    status = " . $this->db->escape($data['status']) . ",
                    updated = '" . date('Y-m-d H:i:s') . "'
                WHERE id = " . $this->db->escape($data['id']) . "
                    AND deleted = '0'
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
     * User edits their own profile
     *
     * @param  array $data
     * @param  int $user_id
     * @return boolean
     */
    function edit_profile($data = array(), $user_id = NULL)
    {
        if ($data && $user_id)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
            ";

            if ($data['password'] != '')
            {
                // secure password
                $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $data['password'] . $salt);

                $sql .= "
                    password = " . $this->db->escape($password) . ",
                    salt = " . $this->db->escape($salt) . ",
                ";
            }

            $sql .= "
                    first_name = " . $this->db->escape($data['first_name']) . ",
                    last_name = " . $this->db->escape($data['last_name']) . ",
                    email = " . $this->db->escape($data['email']) . ",
                    phone = " . $this->db->escape($data['phone']) . ",
                    language = " . $this->db->escape($data['language']) . ",
                    updated = '" . date('Y-m-d H:i:s') . "'
                WHERE id = " . $this->db->escape($user_id) . "
                    AND deleted = '0'
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
     * User edits their own profile
     *
     * @param  array $data
     * @param  int $user_id
     * @return boolean
     */
    function standart_verification($data = array(), $user_id = NULL)
    {
        if ($data && $user_id)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
            ";
					
						if ($data['company'] != '')
            {

                $sql .= "
                    company = " . $this->db->escape($data['company']) . ",
                ";
            }
					
						if ($data['address_2'] != '')
            {

                $sql .= "
                    address_2 = " . $this->db->escape($data['address_2']) . ",
                ";
            }

            $sql .= "
                    bday = " . $this->db->escape($data['bday']) . ",
                    country = " . $this->db->escape($data['country']) . ",
                    zip = " . $this->db->escape($data['zip']) . ",
                    city = " . $this->db->escape($data['city']) . ",
										phone = " . $this->db->escape($data['phone']) . ",
                    address_1 = " . $this->db->escape($data['address_1']) . "
                WHERE id = " . $this->db->escape($user_id) . "
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
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    function edit_billing($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    username = " . $this->db->escape($data['username']) . ",
            ";

            $sql .= "
                    paypal = " . $this->db->escape($data['paypal']) . ",
                    card = " . $this->db->escape($data['card']) . ",
                    bitcoin = " . $this->db->escape($data['bitcoin']) . ",
                    skrill = " . $this->db->escape($data['skrill']) . ",
                    payza = " . $this->db->escape($data['payza']) . ",
                    advcash = " . $this->db->escape($data['advcash']) . ",
										perfect_m = " . $this->db->escape($data['perfect_m']) . ",
                    swift = '" . $this->db->escape($data['swift']) . "'
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
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    function edit_verify($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    username = " . $this->db->escape($data['username']) . ",
            ";

            $sql .= "
                    company = " . $this->db->escape($data['company']) . ",
                    country = " . $this->db->escape($data['country']) . ",
                    zip = " . $this->db->escape($data['zip']) . ",
                    city = " . $this->db->escape($data['city']) . ",
                    address_1 = " . $this->db->escape($data['address_1']) . ",
                    address_2 = " . $this->db->escape($data['address_2']) . "
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
     * Soft delete an existing user
     *
     * @param  int $id
     * @return boolean
     */
    function delete_user($id=NULL)
    {
        if ($id)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    is_admin = '0',
                    status = '0',
                    deleted = '1',
                    updated = '" . date('Y-m-d H:i:s') . "'
                WHERE id = " . $this->db->escape($id) . "
                    AND id > 1
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
     * Check for valid login credentials
     *
     * @param  string $username
     * @param  string $password
     * @return array|boolean
     */
    function login($username=NULL, $password=NULL)
    {
        if ($username && $password)
        {
            $sql = "
                SELECT
                    id,
                    username,
                    password,
                    salt,
                    first_name,
                    last_name,
                    email,
                    language,
                    is_admin,
                    status,
                    created,
                    updated
                FROM {$this->_db}
                WHERE (username = " . $this->db->escape($username) . "
                        OR email = " . $this->db->escape($username) . ")
                    AND status = '1'
                    AND deleted = '0'
                LIMIT 1
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                $results = $query->row_array();
                $salted_password = hash('sha512', $password . $results['salt']);

                if ($results['password'] == $salted_password)
                {
                    unset($results['password']);
                    unset($results['salt']);

                    return $results;
                }
            }
        }

        return FALSE;
    }


    /**
     * Handle user login attempts
     *
     * @return boolean
     */
    function login_attempts()
    {
        // delete older attempts
        $older_time = date('Y-m-d H:i:s', strtotime('-' . $this->config->item('login_max_time') . ' seconds'));

        $sql = "
            DELETE FROM login_attempts
            WHERE attempt < '{$older_time}'
        ";

        $query = $this->db->query($sql);

        // insert the new attempt
        $sql = "
            INSERT INTO login_attempts (
                ip,
                attempt
            ) VALUES (
                " . $this->db->escape($_SERVER['REMOTE_ADDR']) . ",
                '" . date("Y-m-d H:i:s") . "'
            )
        ";

        $query = $this->db->query($sql);

        // get count of attempts from this IP
        $sql = "
            SELECT
                COUNT(*) AS attempts
            FROM login_attempts
            WHERE ip = " . $this->db->escape($_SERVER['REMOTE_ADDR'])
        ;

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            $results = $query->row_array();
            $login_attempts = $results['attempts'];
            if ($login_attempts > $this->config->item('login_max_attempts'))
            {
                // too many attempts
                return FALSE;
            }
        }

        return TRUE;
    }


    /**
     * Validate a user-created account
     *
     * @param  string $encrypted_email
     * @param  string $validation_code
     * @return boolean
     */
    function validate_account($encrypted_email=NULL, $validation_code=NULL)
    {
        if ($encrypted_email && $validation_code)
        {
            $sql = "
                SELECT id
                FROM {$this->_db}
                WHERE SHA1(email) = " . $this->db->escape($encrypted_email) . "
                    AND validation_code = " . $this->db->escape($validation_code) . "
                    AND status = '0'
                    AND deleted = '0'
                LIMIT 1
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                $results = $query->row_array();

                $sql = "
                    UPDATE {$this->_db}
                    SET status = '1',
                        validation_code = NULL
                    WHERE id = '" . $results['id'] . "'
                ";

                $this->db->query($sql);

                if ($this->db->affected_rows())
                {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }


    /**
     * Reset password
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function reset_password($data=array())
    {
        if ($data)
        {
            $sql = "
                SELECT
                    id,
                    first_name
                FROM {$this->_db}
                WHERE email = " . $this->db->escape($data['email']) . "
                    AND status = '1'
                    AND deleted = '0'
                LIMIT 1
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                // get user info
                $user = $query->row_array();

                // create new random password
                $user_data['new_password'] = generate_random_password();
                $user_data['first_name']   = $user['first_name'];

                // create new salt and stored password
                $salt     = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $user_data['new_password'] . $salt);

                $sql = "
                    UPDATE {$this->_db} SET
                        password = " . $this->db->escape($password) . ",
                        salt = " . $this->db->escape($salt) . "
                    WHERE id = " . $this->db->escape($user['id']) . "
                ";

                $this->db->query($sql);

                if ($this->db->affected_rows())
                {
                    return $user_data;
                }
            }
        }

        return FALSE;
    }


    /**
     * Check to see if a username OR email already exists
     *
     * @param  string $username
     * @return boolean
     */
    function user_exists($user)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE BINARY username = " . $this->db->escape($user) . " OR email = " . $this->db->escape($user) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * Check to see if a username already exists
     *
     * @param  string $username
     * @return boolean
     */
    function username_exists($username)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE username = " . $this->db->escape($username) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Check to see if an email already exists
     *
     * @param  string $email
     * @return boolean
     */
    function email_exists($email)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE email = " . $this->db->escape($email) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    function phone_exists($phone)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE phone = " . $this->db->escape($phone) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }
  
    function get_user_check($check_user = NULL)
    {
        if ($check_user)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE username = " . $this->db->escape($check_user) . "
                    OR email = " . $this->db->escape($check_user) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
	
		function update_setting_user($id, $data) {
			$this->db->where("ID", $id)->update("users", $data);
		}
	
	function update_user($defendant, $data) {
			$this->db->where("username", $defendant)->update("users", $data);
            
            //print($this->db->last_query());die;
		}
	
	function get_rel_user_admin($ip_address) 
	{
		return $this->db->where("ip_address", $ip_address)->order_by('id', 'DESC')->get("users");
	}
	
	// total users ////////////////////////////////////////////
  function total_users_deposit()
  {
		$where = "deleted = '0'";
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("users");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->Users;
  }
  
  //09.06.18 by DM get user country by IP
  function get_user_country($ip) {
   	$ip_link = "http://api.wipmania.com/";
    $ip_link .= $ip;
	  $ip_link .="?bankaero.com";
    //print($ip);
    
    //;
    
    if ($country = (file_get_contents($ip_link))) {
      if ($country == 'XX') {$country = 'us';}
      $output = $country;
      //print($country);
    }

    else {$output = "err";}
    
    return $output;
  }
  
  function get_phone_prefix($country) {
    $s = $this->db->select('phone_code')->where('country_code', $country)->get('misc_phonecodes');
    
    $r = $s->row()->phone_code;
    if (!$r) {
      $r = "+1";
    }
    
    return $r;

  }
  
  function check_ref($ref, $ref_mail) {
    $sql = "SELECT * FROM `users` WHERE `reflink` LIKE '".$ref."'";
    $query = $this->db->query($sql);
    $result = $query->row_array();
    if (isset($result['id'])) {
      $status = 1;
      
      $sql = "DELETE FROM `referrals` WHERE `referrals`.`email` = '".$ref_mail."' AND `referrals`.`username` != '".$result['username']."'   ";
      $this->db->query($sql);
      //print($sql);
      
    } else {$status = 0;}
      
    return $status;
  }
  
  function add_ref($username, $email) {
    
    $date = date('Y-m-d');
    
    $sql = "INSERT INTO `referrals` (`id`, `username`, `email`, `date_added`, `status`) VALUES (NULL, '".$username."', '".$email."', '".$date."', '0');";
    $this->db->query($sql);
  }
  
  function get_user_ref_link($user_id) {
    
    function RandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
    RandomString();
    
    $sql = "SELECT * FROM `users` WHERE `id` = ".$user_id." ";
    $query = $this->db->query($sql);
    $result = $query->row_array();
    $result = $result['reflink'];
    
    if ($result == '') {
      //print('z');
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randstring = '';
      
      for ($i = 0; $i < 16; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
      
      $result = $randstring;
      
      $sql = "UPDATE `users` SET `reflink` = '".$result."' WHERE `users`.`id` = ".$user_id." ;";
      $this->db->query($sql);
    }
  
    //print_r($result);
    
    return $result;
  }
  
    function update_user_showcur($userid, $cur) {
      $sql = "UPDATE `users` SET `view_balance` = '".$cur."' WHERE `users`.`id` = ".$userid.";";
      $this->db->query($sql);
    }
  
    function update_ref($refmail, $status) {
      $sql = "UPDATE `referrals` SET `status` = '".$status."' WHERE `referrals`.`email` = '".$refmail."' ;";
      //print($sql);
      $this->db->query($sql);
    }
  
  	function get_user_referrals($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db2}
						WHERE (username = '{$user}' )
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

        //print_r($results);
        
        return $results;
    }
    
    
    
    public function check_user_limit($user_id, $currency, $sum) {
      
      $return['status'] = 1;
      $index = 'none';
      
      $current_limits = $this->get_user_limits($user_id);
      
      //print("<pre>");print_r($current_limits);
      
      if ($currency == 'debit_base') { //EUR
        $index = 'eur';
      } elseif ($currency == 'debit_extra1') { //USD
        $index = 'usd';
      } elseif ($currency == 'debit_extra2') { //GBP
        $index = 'gbp';
      }
      
      //print($index);
      
      if ($index != 'none') { 
        $limit_r = $current_limits['limits'][$index] - $current_limits['current'][$index];

        //print('sum='.$sum.'<br>');
        //print('ostatok='.$limit_r."<br>");die

        if ($sum > $limit_r) {
          $return['status'] = 0;
        }
      
      } else {
        $return['status'] = 1;
      }
      
      $sql = "SELECT verify_status FROM users WHERE id = ".$user_id." ";
      $query = $this->db->query($sql);
      $return['vrf_status'] = $query->row_array()['verify_status'];
      
      
      $return['limit_sum_left'] = $limit_r;
      
      
      return $return;
      
    }
    
    public function get_user_limits($user_id) {
      $sql = "SELECT verify_status FROM users WHERE id = ".$user_id." ";
      $query = $this->db->query($sql);
      $vrf_status = $query->row_array()['verify_status'];
      
      if ($vrf_status == 0) {
        $sql = "SELECT name, value FROM settings WHERE id BETWEEN 20 AND 22";
        $vrf_status_text = 'Initial';
      } elseif ($vrf_status == 1) {
        $sql = "SELECT name, value FROM settings WHERE id BETWEEN 23 AND 25";
        $vrf_status_text = 'Standard';
      } elseif ($vrf_status == 2) {
        $sql = "SELECT name, value FROM settings WHERE id BETWEEN 26 AND 28";
        $vrf_status_text = 'Extended';
      }
      
      $query = $this->db->query($sql);
      $limits = $query->result_array();
     
      $limit['eur'] = $limits[1]['value'];
      $limit['usd'] = $limits[0]['value'];
      $limit['gbp'] = $limits[2]['value'];
      
      //current
      $sql = "SELECT debit_base as eur, debit_extra1 as usd, debit_extra2 as gbp FROM users WHERE id = ".$user_id." ";
      $query = $this->db->query($sql);
      
      $current = $query->row_array();
      
      $check_usd = $current['usd'] - $limit['usd'];
      
      if ($current['eur'] >= $limit['eur']) { $limit_status['eur'] = 0; } else { $limit_status['eur'] = 1; }
      if ($current['usd'] >= $limit['usd']) { $limit_status['usd'] = 0; } else { $limit_status['usd'] = 1; }
      if ($current['gbp'] >= $limit['gbp']) { $limit_status['gbp'] = 0; } else { $limit_status['gbp'] = 1; }
      
      $limit_percentage['eur'] = round(($current['eur'] / $limit['eur']),2) * 100;
      $limit_percentage['usd'] = round(($current['usd'] / $limit['usd']),2) * 100;
      $limit_percentage['gbp'] = round(($current['gbp'] / $limit['gbp']),2) * 100;        
      
      $return['limits'] = $limit;
      $return['current'] = $current;
      $return['limit_percentage'] = $limit_percentage;
      $return['limit_status'] = $limit_status;
      
      $return['vrf']['status'] = $vrf_status;
      $return['vrf']['text']   = $vrf_status_text;
      
      //print("<pre>");
      //print_r($return);die;
      
      return $return;
    }
    
    public function get_user_favlist($user_id) {
      $sql = "SELECT users_favourites.*, users.username FROM `users_favourites`"
              . " LEFT OUTER JOIN users ON users.id = users_favourites.fav_id"
              . " WHERE `user_id` = ".$user_id." ";
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      return $result;
      
    }
    
    public function add_fav($user_id, $fav_id) {
      
      $sql = "SELECT * FROM users_favourites WHERE user_id = ".$user_id." AND fav_id = ".$fav_id." ";
      
      //dump_exit($sql);
      
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      if (isset($result[0])) {
        
      } else {
        $sql = "INSERT INTO `users_favourites` (`uf_id`, `user_id`, `fav_id`, `date_added`) VALUES (NULL, '".$user_id."', '".$fav_id."', CURRENT_TIMESTAMP);";
        $this->db->query($sql);
      }
      
      
    }

}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices_model extends CI_Model {

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
        $this->_db = 'invoices';
    }
	
	function get_invoices_user_admin($user) 
	{
		$where = "(sender = '$user' OR receiver = '$user')";
		return $this->db->where($where)->order_by('id', 'DESC')->limit(20)->get("invoices");
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
	
	function get_declined($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
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
  
  function get_user_invoices($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
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
              if ($key != 'datefrom' && $key != 'dateto') {  
                $value = $this->db->escape('%' . $value . '%');
                $sql .= " AND {$key} LIKE {$value}";
              }
            }
            if (isset($filters['datefrom']) && isset($filters['dateto'])) {
              $sql .=" AND date BETWEEN '" . $filters['datefrom'] . "' AND '" . $filters['dateto'] . "'  ";
            } 
            elseif (isset($filters['datefrom']) && !isset($filters['dateto']))
              {
              $sql .=" AND date >= '" . $filters['datefrom'] . "'" ;
              }
             elseif (!isset($filters['datefrom']) && isset($filters['dateto']))
              {
              $sql .=" AND date <= '" . $filters['dateto'] . "'" ;
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

        return $results;
    }	
	
	function get_user_inbox_invoices($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE receiver = '{$user}'
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
	
	function get_user_sent_invoices($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC', $user = NULL)
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
						WHERE sender = '{$user}'
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
  
  function get_detail_invoice($id = NULL, $username = NULL)
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
                return $query->row_array();
            }
        }

        return FALSE;
    }
  
  function update_invoice($invoice, $data) {
		$this->db->where("ID", $invoice)->update("invoices", $data);
	}
  
  function add_invoice($data)
	{
		$this->db->insert("invoices", $data);
		return $this->db->insert_id();
	}
	
	// sum requests (for menu)
  function sum_user_invoices($user)
  {
		$where = "(status = '1' AND receiver = '{$user}')";
  	$s= $this->db->where($where)->select("COUNT(*) as num")->get("invoices");
    $r = $s->row();
    if(isset($r->num)) return $r->num;
    return 0;

    return $result[0]->User;
  }
	
	function get_invoice($id = NULL)
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
	
	function edit_invoice($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
										date = " . $this->db->escape($data['date']) . ",
										name = " . $this->db->escape($data['name']) . ",
										code = " . $this->db->escape($data['code']) . ",
										info = " . $this->db->escape($data['info']) . ",
                    sender = " . $this->db->escape($data['sender']) . ",
                    receiver = " . $this->db->escape($data['receiver']) . "
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
	
	function delete($id) {
		$this->db->where("id", $id)->delete("invoices");
	}
    
    public function process_invoices() {
      
      $check_date = date('Y-m-d', strtotime("+1 day"));
      
      //dump_exit($check_date);
      
      $sql = "SELECT * FROM `invoices` WHERE due_date = '".$check_date."' ";
      //print($sql);
      
      $query = $this->db->query($sql);
      $query = $query->result_array();
      
      //dump_exit($query);
      
      foreach ($query as $k=>$invoice) {
        //
        $receiver_data = $this->users_model->get_username($invoice['receiver']);
        
        //dump_exit($receiver_data);
        
        

                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Invoice reminder!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(40);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = 'https://bankaero.com/account/invoices';
                                    
                                    if ($invoice['currency'] != 'debit_base5') {
                                      $mail_amount = number_format($invoice['amount'], 2, '.', '');
                                    } else {
                                      $mail_amount = number_format($invoice['amount'], 8, '.', '');
                                    }
                                    
                                    if ($invoice['currency'] == "debit_base") {
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
                                                
                                    //dump_exit($symbol);
                                                
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]', '[INVNUM]', '[INVNAME]');
									$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $user_name, $invoice['id'], $invoice['name']);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
        
        
      }
      
    }
  
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template_model extends CI_Model {

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
        $this->_db = 'email_templates';
				$this->_db2 = 'sms_template';
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
	
	function get_all_sms($limit = 0, $offset = 0, $filters = array(), $sort = 'dir', $dir = 'ASC')
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
	
	function get_templates($id = NULL)
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
	
	/**
     * Edit email template
     *
     * @param  array $data
     * @return boolean
     */
    function edit_template($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    title = " . $this->db->escape($data['title']) . ",
                    message = " . $this->db->escape($data['message']) . ",
                    status = " . $this->db->escape($data['status']) . ",
                    user_enabled = " . $this->db->escape($data['user_enabled']) . "
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
     * Edit email template
     *
     * @param  array $data
     * @return boolean
     */
    function edit_sms_template($data = array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db2}
                SET
                    title = " . $this->db->escape($data['title']) . ",
                    message = " . $this->db->escape($data['message']) . ",
                    status = " . $this->db->escape($data['status']) . ",
                    user_enabled = " . $this->db->escape($data['user_enabled']) . "
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
	
	function get_email_template($id = NULL, $user_id = 0)
    {
        if (isset($this->session->userdata['logged_in'])) {
          if ($this->session->userdata['logged_in']['is_admin'] == 0) {
            $user_id = $this->session->userdata['logged_in']['id'];
          }
        }
        
        //print($user_id);die;
      
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
              $template = $query->row_array();
              
              
              if ($template['status'] == 1) //template is enabled by admin
              {
              
                //check template for current user
                if ($user_id > 0) {
                  $sql = "SELECT * FROM `users_notification_settings` WHERE `user_id` = ".$user_id." AND `type` LIKE 'email' AND `id` = ".$id." " ;
                  //print($sql);die;
                  $query = $this->db->query($sql);
                  if ($query->num_rows()) {
                    $query = $query->row_array();
                    $user_template_status = $query['status'];
                    if ($user_template_status == 1) {
                      $template['status'] = 1;
                    } else {
                      $template['status'] = 0;
                    }
                  } 
                }
              }
              
              return $template;
            }
        }

        return FALSE;
    }
	
	function get_sms_template($id = NULL, $user_id = 0)
    {
        if (isset($this->session->userdata['logged_in'])) {
          if ($this->session->userdata['logged_in']['is_admin'] == 0) {
            $user_id = $this->session->userdata['logged_in']['id'];
          }
        }
      
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
              $template = $query->row_array();
              
              if ($template['status'] == 1) //template is enabled by admin
              {
              
                //check template for current user
                if ($user_id > 0) {
                  $sql = "SELECT * FROM `users_notification_settings` WHERE `user_id` = ".$user_id." AND `type` LIKE 'sms' AND `id` = ".$id." " ;
                  //print($sql);die;
                  $query = $this->db->query($sql);
                  if ($query->num_rows()) {
                    $query = $query->row_array();
                    $user_template_status = $query['status'];
                    if ($user_template_status == 1) {
                      $template['status'] = 1;
                    } else {
                      $template['status'] = 0;
                    }
                  } 
                }
              }
              
              return $template;
            }
        }

        return FALSE;
    }
	
}
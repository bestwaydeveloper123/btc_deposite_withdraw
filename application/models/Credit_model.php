<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_model extends CI_Model {

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
        //$this->_db = 'vaults';
    }
    
    public function update_loan_status($user_id, $loan_id, $status) {
      $sql = "UPDATE `loans` SET `status` = '".$status."' WHERE `id` = ".$loan_id." AND user_id = ".$user_id." ;";
       $this->db->query($sql);
       
       if ($status == 3) {
         
         $date_r = date('Y-m-d');
         
         $sql = "UPDATE `loans` SET `date_repaid` = '".$date_r."' WHERE `id` = ".$loan_id." AND user_id = ".$user_id." ;";
         $this->db->query($sql);
       }
       
    }
    
    public function get_loan_detailed($user_id, $loan_id) {
      $sql = "SELECT * FROM `loans` WHERE `id` = ".$loan_id." AND `user_id` = ".$user_id." " ;
      $query = $this->db->query($sql);
      $result = $query->row_array();
      
      $return['status'] = 0;
      if (isset($result['id'])) {
        $return['status'] = 1;
        
        if     ($result['get_currency'] == 0) { $result['get_currency_text'] = $this->currencys->display->base_code; }
        elseif ($result['get_currency'] == 1) { $result['get_currency_text'] = $this->currencys->display->extra1_code; }
        elseif ($result['get_currency'] == 2) { $result['get_currency_text'] = $this->currencys->display->extra2_code; }
        elseif ($result['get_currency'] == 3) { $result['get_currency_text'] = $this->currencys->display->extra3_code; }
        elseif ($result['get_currency'] == 4) { $result['get_currency_text'] = $this->currencys->display->extra4_code; }
        elseif ($result['get_currency'] == 5) { $result['get_currency_text'] = $this->currencys->display->extra5_code; }
        
        if ($result['get_currency'] == 0) { $result['cur'] = 'debit_base'; } 
                  if ($result['get_currency'] == 1) { $result['cur'] = 'debit_extra1'; } 
                  if ($result['get_currency'] == 2) { $result['cur'] = 'debit_extra2'; } 
                  if ($result['get_currency'] == 3) { $result['cur'] = 'debit_extra3'; } 
                  if ($result['get_currency'] == 4) { $result['cur'] = 'debit_extra4'; } 

        $return['data'] = $result;
        //dump_exit($result);
      }
      
      return $return;
    }
    
    public function get_loans() {
      $sql = "SELECT loans.*, users.username FROM `loans`
        LEFT OUTER JOIN users ON loans.user_id = users.id
        ORDER BY `loans`.`id` ASC " ;
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      foreach ($result as $k=>$v) {
        if ($v['status'] == 0) { $result[$k]['status_text'] = 'New'; }
        elseif ($v['status'] == 1) { $result[$k]['status_text'] = 'Paid'; }
        elseif ($v['status'] == 2) { $result[$k]['status_text'] = 'Active'; }
        elseif ($v['status'] == 3) { $result[$k]['status_text'] = 'Closed'; }
        
        if ($v['get_currency'] == 0) { $result[$k]['cur_text'] = 'EUR'; } 
                  if ($v['get_currency'] == 1) { $result[$k]['cur_text'] = 'USD'; } 
                  if ($v['get_currency'] == 2) { $result[$k]['cur_text'] = 'GBP'; } 
                  if ($v['get_currency'] == 3) { $result[$k]['cur_text'] = 'JPY'; } 
                  if ($v['get_currency'] == 4) { $result[$k]['cur_text'] = 'RUB'; } 
      }
      
      $return['results'] = $result;
      $return['total'] = count($result);
      
      return $return;
    }
    
    public function get_user_loans($user_id, $limit = 0, $offset = 0, $filters = 0, $sort = 0, $dir = 0) {
      $sql = "SELECT * FROM `loans` WHERE `user_id` = ".$user_id." ORDER BY `loans`.`id` ASC " ;
      $query = $this->db->query($sql);
      $result = $query->result_array();
      
      $return['results'] = $result;
      $return['total'] = count($result);
      
      return $return;
    }
    
    public function add_loan($user_id, $data) {
      
      $data['user_id']        = $user_id;


      //print("<pre>");print_r( $loan_data );print("</pre>");
      
      $this->db->insert('loans', $data);

      $loan_id = 0;
      if ($this->db->affected_rows() > 0) {
          $loan_id = $this->db->insert_id();
      }
      
      return $loan_id;
    }
    
    public function process_loans() {
      $loans = $this->get_loans();
      
      foreach ($loans['results'] as $k=>$v) {
       
        
        $today = date('Y-m-d');
        
        $v['check_payment'] =  round((strtotime($today)-strtotime($v['date_end']))/86400);
        
        if ($v['status'] == 2 && $v['check_payemnt'] == 0) {
          
          $this->update_loan_status($v['user_id'], $v['id'], 3);
          // EMAIL termination
              $this->load->library('email');
              $this->load->library('currencys');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($v['user_id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Loan repayment failed!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(39);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = 'https://bankaero.com/account/credits';
                                    
                                    $crypt_sum = number_format($v['give_sum'], 8, '.', '');
                                    $fiat_sum  = number_format($v['get_sum'], 2, '.', '');
                                    
                                    if ($v['get_currency'] == 0) {
													$symbol = $this->currencys->display->base_code;
												} elseif ($v['vault_currency'] == 1) {
													$symbol = $this->currencys->display->extra1_code;
												} elseif ($v['vault_currency'] == 2) {
													$symbol = $this->currencys->display->extra2_code;
												} elseif ($v['vault_currency'] == 3) {
													$symbol = $this->currencys->display->extra3_code;
												} elseif ($v['vault_currency'] == 4) {
													$symbol = $this->currencys->display->extra4_code;
												} elseif ($v['vault_currency'] == 5) {
													$symbol = $this->currencys->display->extra5_code;
												}
                                    $user_name = $receiver_data['first_name'] . ' ' . $receiver_data['last_name'];
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[CRYPT]', '[SUM]', '[CUR]', '[NAME]');
									$vals_1 = array($site_name, $site_link, $crypt_sum, $fiat_sum, $symbol, $user_name);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
              
              
              //.email
        }
        
         
      }
    }

    public function delete_loan($loan_id, $user_id) {
      $this->load->helper('security');
      $loan_id = $this->security->xss_clean($this->input->post('loan_id'));
      $sql = "UPDATE `loans` SET `user_id` = NULL WHERE id = '".$loan_id."' AND user_id = ".$user_id." ;";
      $this->db->query($sql);
    }

}
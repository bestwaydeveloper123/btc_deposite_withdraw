<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Credits extends Private_Controller {

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
        $this->load->model('transactions_model');
        $this->load->model('vouchers_model');
        $this->load->model('vaults_model');
        $this->load->model('credit_model');
      
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('account/credits'));
        define('DEFAULT_LIMIT', $this->settings->per_page_limit);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "desc");
			
		// use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER))
        {
            $this->_redirect_url = $this->session->userdata(REFERRER);
        }
        else
        {
            $this->_redirect_url = THIS_URL;
        }
    }
  
    /**
	* Loans
    */
	function index()
	{

		$user = $this->users_model->get_user($this->user['id']);
		$username = $user['username'];
        
        //$user_limits = $this->users_model->get_user_limits($this->user['id']);
	
		// get parameters
        $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
        $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;
        
        
		
		// get filters
        $filters = array();
		// build filter string
        $filter = "";
        foreach ($filters as $key => $value)
        {
            $filter .= "&{$key}={$value}";
        }
			
		// are filters being submitted?
        if ($this->input->post())
        {
                // apply the filter(s)
                $filter = "";

                if ($this->input->post('id'))
                {
                    $filter .= "&id=" . $this->input->post('id', TRUE);
                }

                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");

			//$referrals = $this->users_model->get_user_referals($limit, $offset, $filters, $sort, $dir, $username);	
		}
		
        // setup page header data
        $this->set_title(sprintf(lang('users loans loans'), $this->settings->site_name));
		// reload the new user data and store in session

        $data = $this->includes;
					
		$loans = $this->credit_model->get_user_loans($this->user['id'], $limit, $offset, $filters, $sort, $dir);

		// build pagination
		$this->pagination->initialize(array(
			'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
			'total_rows' => 0,
			'per_page'   => $limit
		));
            
        $credit_settings = $this->settings_model->get_credit_settings();
			
		// set content data
        $content_data = array(
			'user'       => $user,
			'username'   => $username,
            'this_url'   => THIS_URL,
            'loans'      => $loans['results'],
            'total'      => $loans['total'],
            'credit_settings' => $credit_settings,
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir
        );
        
        //dump($content_data);

        //print("<pre>"); print_r($content_data); print("</pre>"); die;
        

        // load views
        $data['content'] = $this->load->view('account/credits/index', $content_data, TRUE);
		$this->load->view($this->template, $data);

	}
	
	function delete() {
      
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->credit_model->delete_loan($loan_id, $user_id);
        //redirect('/account/credits', 'refresh');
      
    }
    
    public function pay($loan_id) {
      
      $this->load->model('users_model');
      $this->load->model('transactions_model');
      $this->load->model('vouchers_model');
      $this->load->model('vaults_model');
      $this->load->model('credit_model');
      
      $user = $this->users_model->get_user($this->user['id']);
      $loan_details = $this->credit_model->get_loan_detailed($this->user['id'], $loan_id);
      
      //dump($loan_details);
      
      if ($loan_details['status'] == 0) {
        redirect('/account/credits');
      }
      
      //If we have enought BTC
      if ($user['debit_extra5'] > $loan_details['data']['give_sum']) {
        
        //btc transaction
        
        $new_balance = $user['debit_extra5'] - $loan_details['data']['give_sum'];
        
        $transactions = $this->transactions_model->add_transaction(array(
                                  "type" 			=> "3",
                                  "sum"  			=> $loan_details['data']['give_sum'],
                                  "fee"    			=> 0,
                                  "amount" 			=> $loan_details['data']['give_sum'],
                                  "currency"        => 'debit_extra5',
                                  "status" 			=> 2,
                                  "sender" 			=> $user['username'],
                                  "receiver" 		=> 'system',
                                  "time"          	=> date('Y-m-d H:i:s'),
                                  "user_comment"  	=> 'Payment for Loan #'.$loan_details['data']['id'].' ',
                                  "label" 	        => uniqid("pln_"),
                                  "ip_address" 	    => $_SERVER["REMOTE_ADDR"],
                                  "protect" 	    => "none",
                                  )
                              );

                $this->users_model->update_wallet_transfer($user['username'],
                                    array(
                                        'debit_extra5' => $new_balance,
                                        )
                                    );
        
        
        $this->credit_model->update_loan_status($this->user['id'], $loan_id, 1);
        
        //EMAIL 
              $this->load->library('email');
              $this->load->library('currencys');
                                    //email send
                                    $receiver_data = $this->users_model->get_user($this->user['id']);
                                    $user_name = $receiver_data['first_name'] . ' '. $receiver_data['last_name'];
                                    $title = "Loan available for withdraw!";
                                    $this -> email -> from($this->settings->site_email, $this->settings->site_name);
                                    $this->email->to($receiver_data['email']);
                                    $this -> email -> subject($title);
                                    $email_template = $this->template_model->get_email_template(38);
                                    $rawstring = $email_template['message'];
                                    //dump($rawstring);
                                    // what will we replace
                                    $site_name = $this->settings->site_name;
									$site_link  = 'https://bankaero.com/account/credits';
                                    $mail_amount = number_format($loan_details['data']['get_sum'], 2, '.', '');
                                    if ($loan_details['data']['get_currency'] == 0) {
													$symbol = $this->currencys->display->base_code;
												} elseif ($loan_details['data']['get_currency'] == 1) {
													$symbol = $this->currencys->display->extra1_code;
												} elseif ($loan_details['data']['get_currency'] == 2) {
													$symbol = $this->currencys->display->extra2_code;
												} elseif ($loan_details['data']['get_currency'] == 3) {
													$symbol = $this->currencys->display->extra3_code;
												} elseif ($loan_details['data']['get_currency'] == 4) {
													$symbol = $this->currencys->display->extra4_code;
												} elseif ($loan_details['data']['get_currency'] == 5) {
													$symbol = $this->currencys->display->extra5_code;
												}
                                    $user_name = $receiver_data['first_name'] . ' ' . $receiver_data['last_name'];
                                    $placeholders = array('[SITE_NAME]', '[SITE_LINK]', '[SUM]', '[CUR]', '[NAME]');
									$vals_1 = array($site_name, $site_link, $mail_amount, $symbol, $user_name);
                                    $str_1 = str_replace($placeholders, $vals_1, $rawstring);
                                    $this -> email -> message($str_1);
                                    $this->email->send();
              
              
              //.email
      }

      redirect('/account/credits');
      
    }
    
    public function withdraw($loan_id) {
      
      $this->load->model('users_model');
      $this->load->model('transactions_model');
      $this->load->model('vouchers_model');
      $this->load->model('vaults_model');
      $this->load->model('credit_model');
      
      $user = $this->users_model->get_user($this->user['id']);
      $loan_details = $this->credit_model->get_loan_detailed($this->user['id'], $loan_id);
      
      
      
      if ($loan_details['status'] == 0) {
        redirect('/account/credits');
      }
      
      if ($loan_details['data']['status'] != 1) {
        redirect('/account/credits');
      }
      
      if ($loan_details['data']['get_currency'] == 0) { $loan_details['data']['cur'] = 'debit_base'; } 
      if ($loan_details['data']['get_currency'] == 1) { $loan_details['data']['cur'] = 'debit_extra1'; } 
      if ($loan_details['data']['get_currency'] == 2) { $loan_details['data']['cur'] = 'debit_extra2'; } 
      if ($loan_details['data']['get_currency'] == 3) { $loan_details['data']['cur'] = 'debit_extra3'; } 
      if ($loan_details['data']['get_currency'] == 4) { $loan_details['data']['cur'] = 'debit_extra4'; } 
      
      //dump_exit($loan_details);
      
      //If we have enought BTC

        
        //Loan transaction
        
        $new_balance = $user[$loan_details['data']['cur']] + $loan_details['data']['get_sum'];
        
        $transactions = $this->transactions_model->add_transaction(array(
                                  "type" 			=> "1",
                                  "sum"  			=> $loan_details['data']['get_sum'],
                                  "fee"    			=> 0,
                                  "amount" 			=> $loan_details['data']['get_sum'],
                                  "currency"        => $loan_details['data']['cur'],
                                  "status" 			=> 2,
                                  "sender" 			=> 'system',
                                  "receiver" 		=> $user['username'],
                                  "time"          	=> date('Y-m-d H:i:s'),
                                  "user_comment"  	=> 'Loan #'.$loan_details['data']['id'].' ',
                                  "label" 	        => uniqid("pln_"),
                                  "ip_address" 	    => $_SERVER["REMOTE_ADDR"],
                                  "protect" 	    => "none",
                                  )
                              );

                $this->users_model->update_wallet_transfer($user['username'],
                                    array(
                                        $loan_details['data']['cur'] => $new_balance,
                                        )
                                    );
        
        
        $this->credit_model->update_loan_status($this->user['id'], $loan_id, 2);

      redirect('/account/credits');
      
    }
    
    public function repay($loan_id) {
      
      $this->load->model('users_model');
      $this->load->model('transactions_model');
      $this->load->model('vouchers_model');
      $this->load->model('vaults_model');
      $this->load->model('credit_model');
      
      $user = $this->users_model->get_user($this->user['id']);
      $loan_details = $this->credit_model->get_loan_detailed($this->user['id'], $loan_id);
      
      
      
      if ($loan_details['status'] == 0) {
        redirect('/account/credits');
      }
      
      if ($loan_details['data']['status'] != 2) {
        redirect('/account/credits');
      }
      
      if ($loan_details['data']['get_currency'] == 0) { $loan_details['data']['cur'] = 'debit_base'; } 
      if ($loan_details['data']['get_currency'] == 1) { $loan_details['data']['cur'] = 'debit_extra1'; } 
      if ($loan_details['data']['get_currency'] == 2) { $loan_details['data']['cur'] = 'debit_extra2'; } 
      if ($loan_details['data']['get_currency'] == 3) { $loan_details['data']['cur'] = 'debit_extra3'; } 
      if ($loan_details['data']['get_currency'] == 4) { $loan_details['data']['cur'] = 'debit_extra4'; } 
      
      //dump($loan_details);
      
      //If we have enought BTC

        
        //Loan transaction
        
        $new_balance_fiat = $user[$loan_details['data']['cur']] - $loan_details['data']['total_amount'];
        $new_balance_btc = $user['debit_extra5'] + $loan_details['data']['give_sum'];
        
        //dump($new_balance_fiat);
        //dump($new_balance_btc);
        
        //dump_exit('1');
        
        $transactions = $this->transactions_model->add_transaction(array(
                                  "type" 			=> "3",
                                  "sum"  			=> $loan_details['data']['total_amount'],
                                  "fee"    			=> 0,
                                  "amount" 			=> $loan_details['data']['total_amount'],
                                  "currency"        => $loan_details['data']['cur'],
                                  "status" 			=> 2,
                                  "sender" 			=> $user['username'],
                                  "receiver" 		=> 'system',
                                  "time"          	=> date('Y-m-d H:i:s'),
                                  "user_comment"  	=> 'Loan #'.$loan_details['data']['id'].' repayment',
                                  "label" 	        => uniqid("pln_"),
                                  "ip_address" 	    => $_SERVER["REMOTE_ADDR"],
                                  "protect" 	    => "none",
                                  )
                              );

        $this->users_model->update_wallet_transfer($user['username'],
                                    array(
                                        $loan_details['data']['cur'] => $new_balance_fiat,
                                        )
                                    );
        // BTC Transaction
        //unset($new_balance);
        
        
        $transactions = $this->transactions_model->add_transaction(array(
                                  "type" 			=> "1",
                                  "sum"  			=> $loan_details['data']['give_sum'],
                                  "fee"    			=> 0,
                                  "amount" 			=> $loan_details['data']['give_sum'],
                                  "currency"        => 'debit_extra5',
                                  "status" 			=> 2,
                                  "sender" 			=> 'system',
                                  "receiver" 		=> $user['username'],
                                  "time"          	=> date('Y-m-d H:i:s'),
                                  "user_comment"  	=> 'Loan #'.$loan_details['data']['id'].' return',
                                  "label" 	        => uniqid("pln_"),
                                  "ip_address" 	    => $_SERVER["REMOTE_ADDR"],
                                  "protect" 	    => "none",
                                  )
                              );

        $this->users_model->update_wallet_transfer($user['username'],
                                    array(
                                        'debit_extra5' => $new_balance_btc,
                                        )
                                    );
        
        
        $this->credit_model->update_loan_status($this->user['id'], $loan_id, 3);
        //$this->credit_model->repay_loan($this->user['id'], $loan_id, 3);

      redirect('/account/credits');
      
    }
    
    
    
    public function detail($loan_id) {
      
      $user = $this->users_model->get_user($this->user['id']);
		$username = $user['username'];
		
		
        // setup page header data
        $this->set_title(sprintf(lang('users loans loans'), $this->settings->site_name));
		// reload the new user data and store in session

        $data = $this->includes;
					
		$loan = $this->credit_model->get_loan_detailed($this->user['id'], $loan_id);
        $credit_settings = $this->settings_model->get_credit_settings();
		// set content data
        $content_data = array(
			'user'       => $user,
			'username'   => $username,
            'this_url'   => THIS_URL,
            'status'    => $loan['status'],
            'loan'      => $loan,
            'credit_settings' =>$credit_settings, 
        );
        
        if ($loan['status'] == 0) {
          redirect('/account/credits');
        }
        
        //dump($content_data);

        //print("<pre>"); print_r($content_data); print("</pre>"); die;
        

        // load views
        $data['content'] = $this->load->view('account/credits/details', $content_data, TRUE);
		$this->load->view($this->template, $data);
      
    }
	
	
    public function get_loan() {
      
      $this->load->model('cryptapi_model');
      $this->load->model('settings_model');
      $this->load->model('currencys_model');
      
      $data_update = $this->input->post();
      
      if ($data_update['amount'] == 0) {
        redirect('/account/credits');
      } else {
        
        //dump($data_update);
        
        $data['give_sum'] = $data_update['give']; //amount in btc
        
        $data['get_currency'] = $data_update['get_cur']; //amount in selected fiat
        $data['get_sum'] = $data_update['amount'];
        
        $data['days'] = $data_update['loan_days'];
        
       
        $params['term'] = $data_update['days'];
        $params['give'] = (float)$data_update['give'];
        $params['give_cur'] = 5; 
        $params['get_cur'] = (int)$data_update['get_cur'];
        $loan_data = $this->cryptapi_model->calc_rate($params);
        
        $credit_settings = $this->settings_model->get_credit_settings();
        $ltv = $credit_settings[0]['value'];
        $annual_rate = $credit_settings[3]['value'];
        $loan_data['daily_rate'] = round( $annual_rate /365, 3);
        
        $daily_rate = round( $annual_rate /365, 3);
        
        
        $loan_data['term'] = $data['days'];
        $loan_data['credut_sum'] = round ( ($loan_data['get'] * $ltv / 100), 2);
        $loan_data['daily_rate'] = $daily_rate;
        $loan_data['days'] = $data['days'];
        $loan_data['loan_total'] = round ( ($loan_data['credut_sum'] + $loan_data['credut_sum'] * $daily_rate / 100 * $loan_data['term']), 2);
        
        //dump($loan_data);
       
        
        $insert_data['give_currency']= 5; //BTC
        $insert_data['give_sum']     = $data_update['give'];
        $insert_data['get_currency'] = (int)$data_update['get_cur'];
        $insert_data['get_sum']      = $loan_data['credut_sum'];
        $insert_data['days']         = $data_update['loan_days'];
        $insert_data['total_amount'] = $loan_data['loan_total'];
        $insert_data['overpay']      = $loan_data['loan_total'] - $loan_data['credut_sum'];

        $insert_data['date_start']   = date('Y-m-d');
        $insert_data['date_end']     = date('Y-m-d', strtotime("+".$insert_data['days']." days"));
        
        //dump_exit($insert_data);
        
        $loan_id = $this->credit_model->add_loan($this->user['id'], $insert_data);
        
        if ($loan_id > 0) {
          $this->cryptapi_model->get_new_credit_wallet($this->user['id'], $loan_id);
        }
        
        redirect('/account/credits/detail/'.$loan_id);
        //dump_exit($loan_id);
        //calc loal
        //dump_exit($insert_data);
        
        
        
      
      //total_amount
        
        
        
      }
      
      
    }
	
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Address_limits extends Admin_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
			
			$this->load->helper('security');
            $this->load->model('pages_model');
			$this->load->model('support_model');
			$this->load->model('verification_model');
            $this->load->model('users_model'); 
            $this->load->model('transactions_model'); 
			$this->load->library('notice'); 

				
		// set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/btcdashboard'));
        define('DEFAULT_LIMIT', $this->settings->per_page_limit);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

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
     * All pages
     */
    function index()
    {
		// get parameters
        $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
        $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;
			
		// get filters
        $filters = array();
			
		// build filter string
        $filter = "";
			
		// are filters being submitted?
        if ($this->input->post())
        {
          
          $data_update = $this->input->post();
          $data['btc_address_max'] = $data_update['btc_address_max'];
          $data['btc_address_addon'] = $data_update['btc_address_addon'];
          $data['btc_address_price'] = $data_update['btc_address_price'];
          $data['btc_address_price_currency'] = $data_update['btc_address_price_currency'];
          
          $this->settings_model->update_btc_address_limits($data);
          $this->users_model->update_max_btc_addresses($data['btc_address_max']);
          /*
          [btc_address_max] => 10
          [btc_address_addon] => 10
          [btc_address_price] => 5
          [btc_address_price_currency] => debit_base
           */
          //print("<pre>");
          //print_r($this->input->post());
          //print("</pre>");          
          //die;
        }
			
		// save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
			
        // setup page header data
		$this->set_title( lang('admin btcdash alimits') );
			
		$data = $this->includes;
        
        $address_limits = $this->settings_model->get_address_limits();
        /*
        print("<pre>");
        print_r($address_limits);
        print("</pre>");
        die;
        
         * 
         */
        //$usd = $settings[17]['value'];
        //$eur = $settings[18]['value'];
        //$gbp = $settings[19]['value'];
        
        
        //print("<pre>");
        //print_r($settings);
        //die;
        
		// set content data
        $content_data = array(
            'this_url'            => THIS_URL,
            'address_limits'      => $address_limits,

        );
        
        //print("<pre>"); print_r($content_data);

        // load views
		$data['content'] = $this->load->view('admin/limits/address_limits_index', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
  
 
	
	
	
  }
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Credit extends Admin_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
			
			$this->load->helper('security');
            $this->load->model('pages_model');
            $this->load->model('credit_model');
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
          //dump_exit($data_update);
          
          $data['ltv_ratio'] = $data_update['ltv_ratio'];
          $data['days_min'] = $data_update['days_min'];
          $data['days_max'] = $data_update['days_max'];
          $data['rate_annual'] = $data_update['rate_annual'];
          
          $this->settings_model->update_credit_settings($data);

        }
			
		// save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
			
        // setup page header data
		$this->set_title( 'Loans list & settings' );
			
		$data = $this->includes;
        
        $credit_settings = $this->settings_model->get_credit_settings();

        $this->load->model('credit_model'); 
        $loans = $this->credit_model->get_loans();
        
		// set content data
        $content_data = array(
            'this_url'             => THIS_URL,
            'credit_settings'      => $credit_settings,
            'loans' => $loans,

        );
        
        //print("<pre>"); print_r($content_data);

        // load views
		$data['content'] = $this->load->view('admin/settings/credit_settings', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
  
 
	
	
	
  }
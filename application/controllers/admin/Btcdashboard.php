<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Btcdashboard extends Admin_Controller {

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
			
		if ($this->input->get('id'))
        {
            $id_xss = $this->security->xss_clean($this->input->get('id'));
						$id_replace = htmlentities($id_xss, ENT_QUOTES, "UTF-8");
            $filters['id'] = $id_replace;
        }
			
		// build filter string
        $filter = "";
        foreach ($filters as $key => $value)
        {
            $filter .= "&{$key}={$value}";
        }
			
		// are filters being submitted?
        if ($this->input->post())
        {
            if ($this->input->post('clear'))
            {
                // reset button clicked
                redirect(THIS_URL);
            }
            else
            {
                // apply the filter(s)
                $filter = "";

                if ($this->input->post('id'))
                {
                    $filter .= "&id=" . $this->input->post('id', TRUE);
                }

                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
					
			// get list
			$pages = $this->pages_model->get_all($limit, $offset, $filters, $sort, $dir);

        }
			
		// save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
			
        // setup page header data
		$this
			->set_title( lang('admin btcdash dashboard') );
			
		$data = $this->includes;

		// get list
		//$pages = $this->pages_model->get_all($limit, $offset, $filters, $sort, $dir);
			
		// build pagination
		/*
        $this->pagination->initialize(array(
			'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
			'total_rows' => $pages['total'],
			'per_page'   => $limit
		));
         * *
         */
        $this->load->model('cryptapi_model');
        $btc_data = $this->cryptapi_model->get_btc_balances();
        
        //dump_exit($btc_data);
        
        //transaction fees
        $fee_spd = $this->cryptapi_model->get_fee_speed();
        $settings = $this->settings_model->get_settings();
        
		// set content data
        $content_data = array(
            'this_url'              => THIS_URL,
            'address_list'          => $btc_data['address_list'],
            'address_total'         => $btc_data['address_total'],
            'transaction_list'      => $btc_data['transaction_list'],
            'transaction_total'     => $btc_data['transaction_total'],
            'fee_spd'               => $fee_spd,
            'settings'              => $settings,
            //'filters'               => $filters,
            //'filter'                => $filter,
            //'pagination'            => $this->pagination->create_links(),
            //'limit'                 => $limit,
            //'offset'                => $offset,
            //'sort'                  => $sort,
            //'dir'                   => $dir
        );
        
        //print("<pre>"); print_r($content_data);

        // load views
		$data['content'] = $this->load->view('admin/btcdashboard/index', $content_data, TRUE);
        //dump_exit($this->template);
        $this->load->view($this->template, $data);
    }
  
    function batch()
    {
      //$this->load->model('cryptapi_model');
      //$this->cryptapi_model->process_batch_to_bankaero();
    }
	
	
	
  }
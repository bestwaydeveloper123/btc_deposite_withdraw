<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Limits extends Admin_Controller {

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
          //print_r($this->input->post());
          // save the settings
            $saved = $this->settings_model->save_limits($this->input->post(), $user['id']);

            if ($saved == 1)
            {
                $this->session->set_flashdata('message', lang('admin settings msg save_success'));
                // reload the new settings
                
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin settings error save_failed'));
            }
          
        }
			
		// save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
			
        // setup page header data
		$this
			->set_title( lang('admin btcdash limits') );
			
		$data = $this->includes;
        
        $settings = $this->settings_model->get_settings();
        
        //$usd = $settings[17]['value'];
        //$eur = $settings[18]['value'];
        //$gbp = $settings[19]['value'];
        
        foreach ($settings as $id=>$setting) {
          
          if ($setting['name'] == 'limit_usd') { $usd = $setting['value']; }
          if ($setting['name'] == 'limit_eur') { $eur = $setting['value']; }
          if ($setting['name'] == 'limit_gbp') { $gbp = $setting['value']; }
          
          if ($setting['name'] == 'limit_usd_std') { $usd_std = $setting['value']; }
          if ($setting['name'] == 'limit_eur_std') { $eur_std = $setting['value']; }
          if ($setting['name'] == 'limit_gbp_std') { $gbp_std = $setting['value']; }
          
          if ($setting['name'] == 'limit_usd_ext') { $usd_ext = $setting['value']; }
          if ($setting['name'] == 'limit_eur_ext') { $eur_ext = $setting['value']; }
          if ($setting['name'] == 'limit_gbp_ext') { $gbp_ext = $setting['value']; }
          
        }
        
        //print("<pre>");
        //print_r($settings);
        //die;
        
		// set content data
        $content_data = array(
            'this_url'              => THIS_URL,
            'usd'                   => $usd,
            'eur'                   => $eur,
            'gbp'                   => $gbp,
            'usd_std'                   => $usd_std,
            'eur_std'                   => $eur_std,
            'gbp_std'                   => $gbp_std,
            'usd_ext'                   => $usd_ext,
            'eur_ext'                   => $eur_ext,
            'gbp_ext'                   => $gbp_ext,
        );
        
        //print("<pre>"); print_r($content_data);

        // load views
		$data['content'] = $this->load->view('admin/limits/limits_index', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
  
 
	
	
	
  }
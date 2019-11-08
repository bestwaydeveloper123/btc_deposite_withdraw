<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vaults extends Private_Controller {

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
      
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('account/referral'));
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
	* Vouchers
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
        $this->set_title(sprintf(lang('users settings vaults'), $this->settings->site_name));
		// reload the new user data and store in session

        $data = $this->includes;
					
		$vaults = $this->vaults_model->get_user_vaults($limit, $offset, $filters, $sort, $dir, $this->user['id']);
					
		// build pagination
		$this->pagination->initialize(array(
			'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
			'total_rows' => $referrals['total'],
			'per_page'   => $limit
		));
			
		// set content data
        $content_data = array(
			'user'       => $user,
			'username'   => $username,
            'this_url'   => THIS_URL,
            'vaults'     => $vaults['results'],
            'total'      => $vaults['total'],
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir
        );

        //print("<pre>"); print_r($content_data); print("</pre>"); die;
        

        // load views
        $data['content'] = $this->load->view('account/vaults/index', $content_data, TRUE);
		$this->load->view($this->template, $data);

	}
	
	/**
	*  New voucher page
    */
	function new_vault()
	{
      $error_data = [];
      $form_data = [];
      
      if ($this->input->post()) {
        
        if(isset($_SESSION['error'])){
            unset($_SESSION['error']);
        }

        $form_data = $this->input->post();
        //dump($form_data);
        
        $this->form_validation->set_rules('vault_name', lang('users vouchers errorname'), 'required|max_length[254]|min_length[1]');
        $this->form_validation->set_rules('vault_total', lang('users vouchers code'), 'required|trim|greater_than[0]');
        $this->form_validation->set_rules('vault_paysum', lang('users vouchers code'), 'required|trim|greater_than[0]');
        
        if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error', lang('users vault adderror'));
        } else {
          //add vault
          $this->vaults_model->add_vault($this->user['id'], $form_data);
          $this->session->set_flashdata('message', lang('users vault added'));
          redirect(site_url("account/vaults"));
        }
      }
      
		// setup page header data
	    $this->set_title(sprintf(lang('users vaults new'), $this->settings->site_name));
			// reload the new user data and store in session
	    $user = $this->users_model->get_user($this->user['id']);
					
	    $data = $this->includes;

	    // set content data
	    $content_data = array(
			'user'    => $user,
            'error_data' => $error_data,
            'form_data' => $form_data,
	    );

    	// load views
    	$data['content'] = $this->load->view('account/vaults/new_vault', $content_data, TRUE);
		$this->load->view($this->template, $data);

	}
    
    function delete($id = NULL) {
      if (is_null($id) OR ! is_numeric($id))
      {
        redirect(site_url("account/vaults"));
      }
      $this->vaults_model->delete_vault($this->user['id'], $id);
      
      redirect(site_url("account/vaults"));
    }
	
    function detail($id = NULL)
    {
		$user = $this->users_model->get_user($this->user['id']);
			
        // make sure we have a numeric id
        if (is_null($id) OR ! is_numeric($id))
        {
            redirect($this->_redirect_url);
        }

        // get the data
        $vault = $this->vaults_model->get_detail_vault($this->user['id'], $id);
        //dump_exit($vault);
        // if empty results, return to list
        if ( $vault['status'] == 0)
        {
            redirect(site_url("account/vaults"));
        }
    
        $currency = $vault['data']['vault_currency'];

        // check currency
        if ($currency == "debit_base") {
          $symbol = $this->currencys->display->base_code;
        } elseif ($currency == "debit_extra1") {
          $symbol = $this->currencys->display->extra1_code;
        } elseif ($currency == "debit_extra2") {
          $symbol = $this->currencys->display->extra2_code;
        } elseif ($currency == "debit_extra3") {
          $symbol = $this->currencys->display->extra3_code;
        } elseif ($currency =="debit_extra4") {
          $symbol = $this->currencys->display->extra4_code;
        } elseif ($currency =="debit_extra5") {
          $symbol = $this->currencys->display->extra5_code;
        }
			
        // setup page header data
        $this->set_title(sprintf(lang('users settings vaults'), $this->settings->site_name));

        $data = $this->includes;

        // set content data
        $content_data = array(
          'this_url'   		=> THIS_URL,
          'user'              => $user,
          'cancel_url'        => $this->_redirect_url,
          'vault'      => $vault,
          'vault_id'   => $id,
          'symbol'   => $symbol,
          'sender'   => $sender
        );

        // load views
        $data['content'] = $this->load->view('account/vaults/detail', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
	
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Referrals extends Private_Controller {

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
        
        //check user ref link
        $ref_link = $this->users_model->get_user_ref_link($this->user['id']);
		
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

			$referrals = $this->users_model->get_user_referals($limit, $offset, $filters, $sort, $dir, $username);	
		}
		
        // setup page header data
        $this->set_title(sprintf(lang('users referrals referrals'), $this->settings->site_name));
		// reload the new user data and store in session

        $data = $this->includes;
					
		$referrals = $this->users_model->get_user_referrals($limit, $offset, $filters, $sort, $dir, $username);
        $payouts = $this->transactions_model->get_user_ref_payouts($username);
		
		$user = $this->users_model->get_user($this->user['id']);
					
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
            'reflink'    => $ref_link,
            'referrals'  => $referrals['results'],
            'total_ref'  => $referrals['total'],
            'payouts'    => $payouts,
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
        $data['content'] = $this->load->view('account/referrals/index', $content_data, TRUE);
		$this->load->view($this->template, $data);

	}
	
	
	
	
}
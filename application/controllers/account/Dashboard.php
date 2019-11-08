<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Private_Controller {

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
       redirect('/account/transactions', 'refresh');
	}
	
	
	
	
}
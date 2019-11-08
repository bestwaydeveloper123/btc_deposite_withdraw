<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends API_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Cryptapi_model');
        $this->load->model('Currencys_model');
    }

    
    function process_btc_transactions() {
      
      $this->load->model('cryptapi_model');
      $this->load->model('settings_model');
      
      $settings = $this->settings_model->get_settings();
      
      foreach ($settings as $setting) {
        if ($setting['name'] == 'btc_address') {
          $btc['address'] = $setting['value'];
        } elseif ($setting['name'] == 'btc_limit') {
          $btc['limit'] = $setting['value'];
        }
      }

      
      $tmp = $this->cryptapi_model->get_btc_balances();
      //dump_exit($tmp);
      foreach ($tmp['address_list'] as $address) {
        if ($address['balance'] > $btc['limit']) {
          dump($address);
        
        }
      }
    }

    /**
     * Default
     */
    function index()
    {
        $this->lang->load('core');
        $results['error'] = lang('core error no_results');
        display_json($results);
        exit;
    }
    
    function get_currencies() {
      $data = $this->Currencys_model->get_currencys();
      $data = $data->row_array();
      
      //print("<pre>"); print_r($data);
      
      for ($i = 1; $i <= 5; $i++) {
        $result[$i]['name'] = $data['extra'.$i.'_name'];
        $result[$i]['code'] = $data['extra'.$i.'_code'];
        $result[$i]['rate'] = $data['extra'.$i.'_rate'];
      }
      
      print("<pre>"); print_r($result);
    }
    
    function process_vaults() {
      
      $this->load->library('email');
      $this->load->model('vaults_model');
      $this->load->model('users_model');
      $this->load->model('template_model');
      $this->load->model('transactions_model');
      
      $this->vaults_model->process_vaults();
    }
    
    function process_loans() {
      
      $this->load->library('email');
      $this->load->model('vaults_model');
      $this->load->model('users_model');
      $this->load->model('template_model');
      $this->load->model('transactions_model');
      $this->load->model('credit_model');
      
      $this->credit_model->process_loans();
    }
    
    function temp_check() {
      die;
      $this->load->library('email');
      $this->load->model('vaults_model');
      $this->load->model('users_model');
      $this->load->model('template_model');
      $this->load->model('transactions_model');
      
      $this->vaults_model->deletetemp();
    }

    
    function getKrakenEurRate() {

      $url='https://api.kraken.com';
      $sslverify=true;
      $request = array('pair' => 'XBTEUR');
      $version='0';
      
      $this->curl = curl_init();
       
      curl_setopt_array($this->curl, array(
            CURLOPT_SSL_VERIFYPEER => $sslverify,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'Kraken PHP API Agent',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true)
        );
      
      //error_reporting(E_ALL);
      $method = 'Ticker';
        
      // build the POST data string
      $postdata = http_build_query($request, '', '&');
      // make request
      curl_setopt($this->curl, CURLOPT_URL, $url . '/' . $version . '/public/' . $method);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());
      $result = curl_exec($this->curl);
      //print($result);
      if($result===false)
        throw new KrakenAPIException('CURL error: ' . curl_error($this->curl));
      // decode results
      $result = json_decode($result, true);
      if(!is_array($result))
        //throw new KrakenAPIException('JSON decode error');

      if (!isset($result['error'][0])) {
        //print("<pre>");print_r($result['result']['XXBTZEUR']['a'][0]);
        //die;
        
        $params['source'] = 'kraken';
        $params['pair'] = 'XXBTZEUR';
        $params['value'] = $result['result']['XXBTZEUR']['a'][0];
        $params['active'] = 1;
        
        //  
        //if (!$result['result']['XXBTZEUR']['a'][0]) {
          $this->Cryptapi_model->save_rate($params);
        //}
      } 
      
      if (isset($result['result']['XXBTZEUR']['a'][0])) {
        $params['source'] = 'kraken';
        $params['pair'] = 'XXBTZEUR';
        $params['value'] = $result['result']['XXBTZEUR']['a'][0];
        $params['active'] = 1;
        
          $this->Cryptapi_model->save_rate($params);
      }
      
      //print($result['result']['XXBTZEUR']['a'][0]);
      
      
    }
    
    function process_batch() {
      $this->load->model('cryptapi_model');
      
      $check_run = $this->cryptapi_model->check_batch_run();
      
      if ($check_run == 1) {
        
        $data = 'data';
        //$this->cryptapi_model->update_batch_run($data);
        
        $this->cryptapi_model->process_batch_to_bankaero();
      }
      
      //dump_exit($check_run);
    }
    
    function process_invoices() {
      
      $this->load->library('email');
      $this->load->library('currencys');
      $this->load->model('invoices_model');
      $this->load->model('users_model');
      $this->load->model('template_model');
      $this->load->model('transactions_model');
      
      $this->invoices_model->process_invoices();
    }

} 
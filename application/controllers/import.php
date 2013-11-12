<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends CI_Controller {

    function __construct() {
        parent::__construct();

        // To load the CI benchmark and memory usage profiler - set 1==1.
        if (1 == 2) {
            $sections = array(
                'benchmarks' => TRUE, 'memory_usage' => TRUE,
                'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => FALSE, 'queries' => FALSE,
                'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => FALSE
            );
            $this->output->set_profiler_sections($sections);
            $this->output->enable_profiler(TRUE);
        }

        // Load required CI libraries and helpers.
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');

        // IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded! 
        // It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
        $this->auth = new stdClass;

        // Load 'standard' flexi auth library by default.
        $this->load->library('flexi_auth');

        // Check user is logged in via either password or 'Remember me'.
        // Note: Allow access to logged out users that are attempting to validate a change of their email address via the 'update_email' page/method.
        if (!$this->flexi_auth->is_logged_in()) {
            // Set a custom error message.
            $this->flexi_auth->set_error_message('You must login to access this area.', TRUE);
            $this->session->set_flashdata('message', $this->flexi_auth->get_messages());
            redirect('user');
        }

        // Note: This is only included to create base urls for purposes of this demo only and are not necessarily considered as 'Best practice'.
        $this->load->vars('base_url', 'http://localhost/hris_att/');
        $this->load->vars('includes_dir', 'http://localhost/hris_att/includes/');
        $this->load->vars('current_url', $this->uri->uri_to_assoc(1));

        // Define a global variable to store data that is then used by the end view page.
        $this->data = null;
    }
    
    public function index() {
        
    }
    
    public function trim_filename($filename = NULL) {
        $filename = str_replace('&', '', $filename);
        $filename = str_replace(',', '', $filename);
        
        $filename = str_replace(' ', '', $filename);
        return $filename;
    }
    
    public function mdb_transfer() {
        //TO SERVER VIA FTP
        $this->load->library('ftp');
        
        $config['hostname'] = $this->Parameter->get_value('FTP_HOSTNAME_FOR_MDB');
        $config['username'] = $this->Parameter->get_value('FTP_USERNAME_FOR_MDB');
        $config['password'] = $this->Parameter->get_value('FTP_PASSWORD_FOR_MDB');
        $config['debug'] = TRUE;

        $this->ftp->connect($config);
        
        $this->ftp->delete_file($this->Parameter->get_value('REMOTE_FILE_ON_SERVER_FOR_MDB'));
        $this->ftp->upload($this->Parameter->get_value('FILE_ON_LOCAL_FOR_MDB'), $this->Parameter->get_value('REMOTE_FILE_ON_SERVER_FOR_MDB'));

        $this->ftp->close();
    }
    
    public function mdb_get_data() {
        //TO DB TEMPORARY
        
    }
    
    public function mdb_process_data() {
        //TO DB PRIMARY
        
    }
}
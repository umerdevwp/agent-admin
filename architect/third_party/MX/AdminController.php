<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
 
class MX_AdminController extends MX_Controller 
{
	public $autoload = array();
	public function __construct() 
	{
		parent::__construct();
		//admin checking....
        $ci = &get_instance();
        
        //  setting temporary session need to remove as soon as login functionality is completed
        //  my_set_session(ADMIN_SESSION, 1);
        
        $userDetails= my_session(ADMIN_SESSION);
        
        if(empty($userDetails) || !isset($userDetails->id)) {
            
            $cur_controller = $ci->router->fetch_class() . "/";
            $get = $ci->input->get();
            if ($get) {
                $get = '?' . http_build_query($get);
            }
            
            my_set_session("return_url", site_url() . uri_string() . $get);
            
            redirect(base_url('finbee-backoffice/login'));
            
        }
        
        $this->load->model('permission/permission_model');
		
	}
	
	public function __get($class) 
	{
		return CI::$APP->$class;
	}
}
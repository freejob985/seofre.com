<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
require dirname(__FILE__).'/Base.php';
class MX_Controller 
{
	public $autoload = array();
	public $short_key;
	public function __construct() 
	{
		$class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		date_default_timezone_set(TIMEZONE);
		/* copy a loader instance and initialize */
		$this->load = clone load_class('Loader');
		$this->load->initialize($this);	
		$this->short_key = base64_decode("aHR0cHM6Ly9zbWFydHBhbmVsc21tLmNvbS9wY192ZXJpZnkvaW5zdGFsbD90eXBlPXVwZ3JhZGUmcHVyY2hhc2VfY29kZT0=");
		$CI = &get_instance();
		$CI->load->database();
		/*----------  Check maintenace mode  ----------*/
		$cookie_verify_maintenance_mode = "non-verified";
		if (isset($_COOKIE["verify_maintenance_mode"]) && $_COOKIE["verify_maintenance_mode"] != "") {
          $cookie_verify_maintenance_mode = encrypt_decode($_COOKIE["verify_maintenance_mode"]);
        }
        $is_maintenance_mode =  $this->__check_maintenance_mode();
		if ($cookie_verify_maintenance_mode != 'verified' && $is_maintenance_mode && segment(1) != "maintenance") {
			redirect(cn("maintenance"));
		}
		$allowed_controllers = ['auth', 'client', 'blog', 'contact', 'checkout', 'paypal', 'package', 'custom_page'];
		$allowed_page        = ['logout', 'ipn'];
		$limited_page        = ['update'];
		if (!session('uid') && !$is_maintenance_mode) {
			if (!in_array($this->router->fetch_class(), $allowed_controllers) && !in_array($this->router->fetch_method(), $allowed_page)) {
				if(segment(1) != "" && segment(1) != "cron" && segment(1) != "checkout" &&  !in_array(segment(2), ['set_language'])){
					redirect(PATH);
				}
			}
			if (in_array($this->router->fetch_method(), $limited_page)) {
				redirect(PATH);
			}
		}
		if (session("uid")) {
			$user_allowed_controllers = [];
			$user = $this->__get_current_user_data( session('uid') );
			switch ($user->role) {
				case 'supporter':
					$user_allowed_controllers = array('setting', 'module', 'provider');
					break;	
				case 'user':
					$user_allowed_controllers = array('users', 'setting', 'module', 'provider', 'category');
					break;
			}
			if ($user->role != 'admin' && in_array($this->router->fetch_class(), $user_allowed_controllers)) {
				redirect(PATH."auth/logout");
			}
			$cookie_lc_verified = '';
			if (isset($_COOKIE["lc_verified"]) && $_COOKIE["lc_verified"] != "") {
	          $cookie_lc_verified = base64_decode($_COOKIE["lc_verified"]);
	        }
			if($cookie_lc_verified != "verified" && segment(2) != "logout"){
				if(segment(1) != "module" && $user->role == 'admin'){
					$code = $CI->db->select("purchase_code")->where("pid", 24815787)->get('general_purchase')->row()->purchase_code;
					$code = trim($code);
					if(!empty($code)){
					    $domain = base_url();
					    $result = $this->__curl( $this->short_key . urlencode($code) . "&domain=" . urlencode($domain) );
					    $error_ms = "There is some issue with your purchase code!";
					    $error_ms = base64_encode($error_ms);
					    if ($result != "") {
							$result_object = json_decode($result);
							if (is_object($result_object)) {
								switch ($result_object->status) {
									case 'error':
										$message = base64_encode($result_object->message);
										redirect(PATH."module?error=".$message);
										exit(0);
										break;	
									case 'success':
										set_cookie("lc_verified", base64_encode("verified"), 1209600);
										break;
								}
							}else{
								$message = $error_ms;
								redirect(PATH."module?error=".$message);
								exit(0);
							}
						}else{
							$message = $error_ms;
							redirect(PATH."module?error=".$message);
							exit(0);
						}
					}else{
						$message = $error_ms;
						redirect(PATH."module?error=".$message);
						exit(0);
					}
			    }	
			}

			if ($user->role == 'admin' && segment(1) == "statistics") {
				$CI->db->query("DELETE FROM general_sessions WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 60 MINUTE))");
			}
		}
		
		// $this->output->enable_profiler(ENVIRONMENT == 'development');
		/* autoload module items */
		$this->load->_autoloader($this->autoload);
	}
	
	public function __get($class) 
	{
		return CI::$APP->$class;
	}

	private  function __check_maintenance_mode(){
		$CI = &get_instance();
		$CI->load->database();
		$user = $CI->db->select("value");
				$CI->db->from(OPTIONS);
				$CI->db->where("name", "is_maintenance_mode");
		        $query = $this->db->get();
		$result = $query->row();
		if(!empty($result)){
			if ($result->value) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function __get_current_user_data(){
		$CI = &get_instance();
		$CI->load->database();
		$user = $CI->db->select("role");
				$CI->db->from(USERS);
				$CI->db->where("id", session("uid"));
		        $query = $this->db->get();
		$result = $query->row();
		if(!empty($result)){
			return $result;
		}else{
			return false;
		}
	}

	private function __curl($url){
	    $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); 
	    curl_setopt($ch, CURLOPT_VERBOSE, 1); 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_AUTOREFERER, false); 
	    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
	    curl_setopt($ch, CURLOPT_HEADER, 0); 
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	    curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch); 
	    curl_close($ch); 
	    return $result; 
	}
}

?>
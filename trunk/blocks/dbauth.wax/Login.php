<?php	
	/**
	* The LoginController class provides an interface to performing
	* database-based Logins
	*
	* @author Joe Chrzanowski
	*/
	class LoginController extends Controller implements View {
		/**
		* Constructor -- run the parent constructor and 
		* create the model.  In this case, we want to use 
		* a users model
		*/
		function __construct() {
			parent::__construct("dbauth",false);
			$this->model = new UsersModel();
		}
		/**
		* Nothing
		*/
		function index() {
			// do nothing
		}
		
		/**
		* Perform the login 
		* In this case we're checking against the UsersModel, which implements the DBAuth roles
		* interface.  
		*
		* @returns mixed The userinfo if the user is sucessfully logged in, false otherwise
		*/
		function login() {
			// show login form
			if (isset($this->session['logged_in'])) {
				return true;
			}
			else {
				if ($info = $this->model->Authenticate($this->request['username'],$this->request['password'])) {
					$this->session['userinfo'] = array_shift($info);
					return $info;
				}
				else return false;
			}
		}
		
		/**
		* logged_in() function returns whether the user is logged in
		*/
		function logged_in() {
			return isset($this->session['logged_in']);
		}
	
		/**
		* Logout - ends the session
		*/
		function logout() {
			unset($this->session['logged_in']);
			return;
		}
		/**
		* Creates a new user via DBAuth
		*/
		function create() {
			// create a new user
			$block = Wax::GetBlock($this->block);
			$this->Render($block['views']['create'], array());
		}
		/**
		* Renders the innerHTML for a form which modifies the user's info
		*/
		function edit() {
			// get the current working block (location of this file)
			$block = Wax::GetBlock($this->block);
			
			$userinfo = $this->model->Read(array(
				"id" => $this->request['id']
			));
			
			// render the view named 'edit' within the current block
			$this->Render($block['views']['edit'], $userinfo);
		}
	}
?>
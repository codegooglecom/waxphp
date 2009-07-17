<?php
	require_once "View.php";
	
	// open a session
	session_start();

	// base class for arrays that need special func
	// ie: files, session, cookie
	class FilesArr extends ArraySurrogate {
		function Set($index, $val) {
			return;
		}
	}
	class CookiesArr extends ArraySurrogate {
		function Set($index, $val) {
			setcookie($index,$val,0);		// create the cookie
		}
		function offsetUnset($offset) {
			setcookie($index,"",time()-12000); // expire the cookie
		}
	}
	class SessionArr extends ArraySurrogate {
		function __construct($parent) {
			parent::__construct($parent);
			@session_start();
		}
		function Set($index,$val) {
			session_register($index,$val);
			parent::Set($index,$val);
		}
	}

	// controllers define a base model
	// they then execute various contexts to perform actions
	// there is no real application logic here, so controllers are usually very 
	// transparent.	
	abstract class Controller extends DCIObject implements View {
		// variables for routing - defaults
		// using this we can call the proper context
		protected $modelvar = "model";
		protected $idvar = "id";
		protected $actionvar = "action";
		
		// references to the superglobals
		var $get = NULL;
		var $post = NULL;
		var $request = NULL;
		
		var $files = NULL;
		var $cookie = NULL;	
		var $session = NULL;
		
		protected $model = NULL;
		protected $action = NULL;
		protected $block = NULL;
		
		function __construct($parent_block = NULL) {
			parent::__construct();
			// try to route the context based on the page requests
			// first determine the working model, then determine the action
			// from there we can route to the proper context
			$this->get =& $_GET;
			$this->post =& $_POST;
			$this->request =& $_REQUEST;
			$this->files = new FilesArr($_FILES);
			$this->cookie = new CookiesArr($_COOKIE);
			$this->session = new SessionArr($_SESSION);
			
			if (!is_null($parent_block)) {
				$this->block = $parent_block;
			}
			
			// try to bind to a model
			// acts as the $src when performing role methods
			// TODO: move this
			$modelid = $this->modelvar . $this->idvar;
			$modelid = (isset($this->request[$modelid]) ? $this->request[$modelid] : NULL);
			
			$modelclass = NULL;
			if (isset($this->request[$this->modelvar]))
				$modelclass = $this->request[$this->modelvar] . "Model";
	
			if (isset($modelclass) && class_exists($modelclass)) {
				$this->model = new $modelclass($modelid);
			}
			else {
				$this->model = NULL;
			}
			//////////////////////////////
			
			// call the actions to initiate contexts
			if (isset($this->request[$this->actionvar]))
				$this->action = $this->request[$this->actionvar];
			else $this->action = "";
			
			$viewvars = array();
			if (empty($this->action))
				$this->action = "index";
			
			if (method_exists($this,$this->action)) {
				$action = $this->action;
				$viewvars = $this->$action();
				if (is_null($viewvars)) {
					// then it's probably just an update/insert/delete query -- assume index action 
					$viewvars = $this->redirect("index");
				}
			}
		
			if (file_exists($this->getView())) {
				echo $this->Render($this->getView(),$viewvars);
			}
		}
		
		protected function redirect($action) {
			$this->action = $action;
			return $this->$action();
		}
		
		protected function getView() {
			$prefix = "";
			if (!is_null($this->block)) {
				// directory of the current class's file
				$ref = Reflection::export(new ReflectionClass(get_class($this)),true);
				$path = array();
				
				preg_match_all("/\@\@\s*([^\s]+)\s/",$ref,$path);
				
				$path = $path[1][0];
				$info = pathinfo($path);
				
				$prefix = $info['dirname'] . "/";
			}
			
			if (file_exists("${prefix}views/" . $this->action . ".view.php")) {
				return "${prefix}views/" . $this->action . ".view.php";
			}
		}
		
		// require an index action (which may just redirect to another context)
		abstract function index();
	}
?>
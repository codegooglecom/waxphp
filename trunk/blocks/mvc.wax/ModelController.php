<?php
	abstract class ModelController extends Controller implements View {
		// constructor -- establish a relationship with the model
		function __construct() {		
			$class = get_class($this);
			$class = str_replace("Controller","",$class);
			$model_class = $class . "Model";
			if (class_exists($model_class))
				$this->model = new $model_class();
				
			parent::__construct("mvc");
		}
		
		// always need a few view handlers to get data before a view
		function index() {
			return $this->model->Read();
		}
		function edit() {
			return $this->model->Read($this->request['id']);
		}
		
		// the action handlers
		function create() {
			return $this->model->Create($this->post);
		}
		function read() {
			return $this->model->Read(array("id" => $this->request['id']));
		}
		function update() {
			return $this->model->Update($this->post);
		}
		function delete() {
			return $this->model->Delete($this->request['id']);
		}
	}
?>
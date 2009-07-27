<?php
	class Posts extends Controller implements View {
		// __construct is optional
		// you only need it when specifying
		// that the controller is part of a block
		function __construct() {
			// specify that this is part of a block (mostly for locating views)
			$this->model = new PostsModel();
			parent::__construct("posts");
		}
		function index() {
			// check sort queries
			$args = array();
			if (isset($this->request['sort'])) {
				$args['sort']['column'] = $this->request['sort'];
				if (isset($this->request['sort_direction']))
					$args['sort']['direction'] = $this->request['sort_direction'];
			}
				
			$arr = $this->model->Read($args);
			return $arr;
		}
		
		// inserts the information - don't need a utility function to get the default
		// data (like edit/update) because there isn't any
		function insert() {
			$id = $this->model->Create($this->post);			
		}
		
		// fetches information for the edit view
		function edit() {
			return array_shift($this->model->Read(array("id" => $this->get['id'])));
		}
		// saves the update and redirects to index()
		function update() {
			$this->model->Update($this->post);
		}
		
		// delete $this->request['id'] and redirect to index()
		function delete() {
			$this->model->Delete($this->request['id']);
		}
	}
?>
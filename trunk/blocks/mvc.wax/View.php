<?php
	// the roles for rendering views
	interface View extends Role {}
	interface ModelViewer extends View {}
	
	class ViewActions {
		static function Render(View $self, $basefile, $arguments) {
			// called in a static context, so we need to use arguments to pass in arguments to the renderer,
			// in most cases, in the form of variable => value
			if (file_exists($basefile)) {	
				if (is_array($arguments)) {
					foreach ($arguments as $arg => $val) {
						if (!is_numeric($arg))
							$$arg = $val;
					}
				}
				
				ob_start();
				require($basefile);
				$resulting_file = ob_get_contents();
				ob_end_clean();
				
				return $resulting_file;
			}
		}
	}
?>
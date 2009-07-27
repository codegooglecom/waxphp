<?php require_once "init.php"; ?>
<?php Wax::LoadBlock("messageboxes"); ?>
<?php new HeaderController(); ?>
<h2>Messages</h2>
<?php
	class MessageRenderer extends Plugin implements MessageBoxRenderer {
		function __construct() {
			parent::__construct();
			
			$this->Success("Success Message","Hello... this is a test success message...");
			$this->Error("Error Message","Hello... this is a test error  message...");
			$this->Warning("Warning Message","Hello... this is a test warning message...");
			$this->Info("Info Message","Hello... this is a test info message...");
		}
	}
	new MessageRenderer();
?>
<?php new FooterController(); ?>
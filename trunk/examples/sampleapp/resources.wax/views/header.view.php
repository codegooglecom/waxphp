<html>
	<head>
		<title>Wax Sample Website: Message Board</title>
		<?php foreach ($css as $css_address): ?>
		<link rel='stylesheet' type='text/css' href='<?=$css_address?>' />
		<?php endforeach; ?>
		<?php foreach ($js as $script_address): ?>
		<script type='text/javascript' src='<?=$script_address?>'></script>
		<?php endforeach; ?>
	</head>
	<body >
	
	<div class='pagewrapper'>
		<div class='header'>
			<div class='sidebar'>	
				<div class='logo'>
					<a href='index.php'>
						<img src='/wax/doc/logo/logo_standard.png' />
					</a>
				</div>
				<b>Wax PHP Framework</b><br />
				<span style='font-size:8pt;'>
					v<?=Wax::Version()?><br />
					&copy; 2009
				</span>
				<br />
			</div>
			<div class='navigation'>
				<ul class='wnavlist'>
					<li><a href='posts.php'>Home</a></li>
					<li><a href='posts.php?action=create'>New Post</a></li>
					<li><a href='ref.php'>Color Reference</a></li>
					<li><a href='messages.php'>Message Boxes</a></li>
				</ul>
			</div>
		</div>
		<div class='content'>
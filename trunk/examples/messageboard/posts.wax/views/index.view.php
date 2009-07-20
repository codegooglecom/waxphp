<?php
	if (count($arguments) == 0) {
		?><tr><td colspan='2'>No Posts</td></tr><?php
	}
	else {
		foreach ($arguments as $post) {
			?>
			<table align='center' class='post_table'>
				<tr>
					<th style='text-align:left;'><?=date("F d, Y g:i A",$post['date'])?></th>
					<th style='text-align:right;'>
						By: <?=$post['author']?>
					</th>
				</tr>
				<tr>
					<td colspan='2'><?=$post['message']?></td>
				</tr>
				<tr>
					<td class='post_table_footer' colspan='2'>
						<a href='posts.php?action=edit&id=<?=$post['id']?>'>edit post</a> | 
						<a href='posts.php?action=delete&id=<?=$post['id']?>'>delete post</a>
					</td>
				</tr>
			</table>
			<br /><br />
			<?php
		}
	}
?>
<?php
	if (count($arguments) == 0) {
		?><tr><td colspan='2'>No Posts</td></tr><?php
	}
	else {
?>
<table align='center' style='width:600px;' class='waxtable'>
	<thead>
		<tr>
			<th>Post ID</th>
			<th>Date</th>
			<th>Author</th>
			<th>Message</th>
			<th>Actions</th>
		</tr>
	</thead>
	
	
	<?php foreach ($arguments as $post): ?>	
	<tr>
		<td><?=$post['id']?></td>
		<td><?=$post['date']?></td>
		<td><?=$post['author']?></td>
		<td><?=$post['message']?></td>
		<td>
			<a href='posts.php?action=edit&id=<?=$post['id']?>'>edit post</a> | 
			<a href='posts.php?action=delete&id=<?=$post['id']?>'>delete post</a>
		</td>
	</tr>
	<?php endforeach; ?>
	<tfoot>
		<td colspan='5'><input type='reset' /><input type='submit' value='Sample Submit Button' /></td>
	</tfoot>
</table>
<br /><br />

<?php } ?>
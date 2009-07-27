<form method="POST" action="posts.php?action=update">
	<input type='hidden' name='date' value='<?=time()?>' />
	<input type='hidden' name='id' value='<?=$id?>' />
	<table class='waxtable' align='center'>
		<thead>
			<tr>
				<th colspan='2'>Edit Message</th>
			</tr>
		</thead>
		<tr>
			<td >Your Name</td>
			<td><input type='text' name='author' value='<?=$author?>' /></td>
		</tr>
		<tr>
			<td>Message</td>
			<td><textarea name='message'><?=$message?></textarea></td>
		</tr>
		<tfoot>
			<tr>
				<td colspan='2'><input type='submit' value='Post Message' /></td>
			</tr>
		</tfoot>
	</table>
</form>
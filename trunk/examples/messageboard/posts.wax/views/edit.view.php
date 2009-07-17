<form method="POST" action="?posts/update">
	<input type='hidden' name='date' value='<?=time()?>' />
	<input type='hidden' name='id' value='<?=$id?>' />
	<table align='center'>
		<tr>
			<th colspan='2'>Edit Post</th>
		</tr>
		<tr>
			<td>Your Name</td>
			<td><input type='text' name='author' value='<?=$author?>' /></td>
		</tr>
		<tr>
			<td>Message</td>
			<td><textarea name='message'><?=$message?></textarea></td>
		</tr>
		<tr>
			<td colspan='2'><input type='submit' value='Post Message' /></td>
		</tr>
	</table>
</form>
<form method="POST" action="?posts/insert">
	<input type='hidden' name='date' value='<?=time()?>' />
	<table align='center'>
		<tr>
			<th colspan='2'>Leave a Message</th>
		</tr>
		<tr>
			<td>Your Name</td>
			<td><input type='text' name='author' /></td>
		</tr>
		<tr>
			<td>Message</td>
			<td><textarea name='message'></textarea></td>
		</tr>
		<tr>
			<td colspan='2'><input type='submit' value='Post Message' /></td>
		</tr>
	</table>
</form>
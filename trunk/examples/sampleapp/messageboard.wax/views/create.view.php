<form method="POST" action="posts.php?action=insert">
	<input type='hidden' name='date' value='<?=time()?>' />
	<table align='center' class='waxtable'>
		<thead>
			<tr>
				<th colspan='2'>Leave a Message</th>
			</tr>
		</thead>
		<tr>
			<td class='label'>Your Name</td>
			<td><input type='text' name='author' /></td>
		</tr>
		<tr>
			<td class='label'>Message</td>
			<td><textarea name='message'></textarea></td>
		</tr>
		<tfoot>
			<tr>
				<td colspan='2'><input type='submit' value='Post Message' /></td>
			</tr>
		</tfoot>
	</table>
</form>
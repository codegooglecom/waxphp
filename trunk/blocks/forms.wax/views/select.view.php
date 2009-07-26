<select <?=$attributes?>>
	<?php foreach ($options as $name => $value): ?>
	<option value='<?=$name?>'><?=$value?></option>
	<?php endforeach; ?>
</select>
<?php
	$errors = isset($errors) ? $errors : array();
	foreach ($inputs as $field => $input) {
		$attr = '';
		foreach ($input['attributes'] as $attribute => $value)
			$attr .= " $attribute='$value'";
		$has_error = isset($errors[$field]) ? (count($errors[$field]) > 0 ? true : false) : false;

		echo
			"<div class='form-group".($has_error ? " has-error" : "")."'>
				<label for='".$field."' class='".$input['label-class']."'>".$input['name']."</label>";
		if(in_array($input['input-type'], array('text', 'password', 'textarea')))
			echo "<input type='".$input['input-type']."' class='form-control input-sm' id='".$field."' name='".$field."' placeholder='".$input['name']."'$attr value='".(isset($params[$field]) ? $params[$field] : "")."'>";
		else if($input['input-type'] == 'select') {
			echo "<select class='form-control' id='".$field."' name='".$field."'$attr>";
			foreach ($input['options'] as $value => $text)
				echo "<option value='$value'".(isset($params[$field]) ? ($params[$field] == $value ? " selected" : "") : "").">$text</option>";
			echo "</select>";
		}
		echo ($has_error ? "<p>".implode("</p><p>", $errors[$field])."</p>" : "")."</div>\n";
	}
?>
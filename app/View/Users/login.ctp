<form class="form-horizontal" role="form" action="<?php echo $this->webroot; ?>login" method="POST">
	<div class="form-group">
		<label for="input-username" class="col-sm-2 col-sm-offset-3 control-label">Username</label>
		<div class="col-sm-4">
			<input type="text" name="username" class="form-control" id="input-username" placeholder="Username">
		</div>
	</div>
	<div class="form-group">
		<label for="input-password" class="col-sm-2 col-sm-offset-3 control-label">Password</label>
		<div class="col-sm-4">
			<input type="password" name="password" class="form-control" id="input-password" placeholder="Password">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-5 col-sm-4">
			<button type="submit" class="btn btn-default">Sign in</button>
		</div>
	</div>
</form>
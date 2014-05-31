<!-- admin html -->
<body class="razor-access">

	<?php
		// generate signature 
		if ($this->site["allow_registration"])
		{
			// check server details
			$signature = null;
			if (isset($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"]) && !empty($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["HTTP_USER_AGENT"]))
			{
				// signature generation
				$signature = sha1($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"].rand(0, 100000));
				$_SESSION["signature"] = $signature;
			}
		}
	?>
 
	<?php if ($this->site["allow_registration"]): ?>
		<script type="text/javascript">
			var RAZOR_FORM_SIGNATURE = '<?php echo $signature ?>';
		</script>
	<?php endif ?>

	<!--[if lt IE 9]>
		<div class="ie8 ie8-admin">
			<p class="message">
				<i class="fa fa-exclamation-triangle"></i> You are using an outdated version of Internet Explorer that is not supported, 
				please update your browser or consider using an alternative, modern browser, such as 
				<a href="http://www.google.com/chrome">Google Chome</a>.
			</p>
		</div>
	<![endif]-->
	<div id="razor-access" class="ng-cloak" ng-controller="access" ng-init="init()">

		<global-notification></global-notification>

		<div class="razor-admin-panel">
			<div class="container">
				<div ng-if="showLogin" class="row">
					<form ng-if="activePage != 'password-reset'" name="form" class="form-inline login-form" role="form" ng-submit="login()" novalidate>
						<div class="form-group">
							<!--[if IE 9]>
								<label for="email">Email</label>
							<![endif]-->
							<input name="email" type="email" class="form-control" placeholder="Email address" ng-model="loginDetails.u" ng-class="{'input-error' :form.email.$dirty && form.email.$invalid && form.email.$error.required}" required>
						</div>
						<div class="form-group">
							<!--[if IE 9]>
								<label for="password">Password</label>
							<![endif]-->
							<input name="password" type="password" class="form-control" placeholder="Password" ng-model="loginDetails.p" ng-class="{'input-error' :form.password.$dirty && form.password.$invalid && form.password.$error.required}">
						</div>
						<button type="submit" class="btn btn-default" ng-disabled="form.$invalid || processing || !loginDetails.p || loginDetails.p.length < 1">
							<i class="fa fa-sign-in" ng-hide="processing"></i>
							<i class="fa fa-spinner fa-spin" ng-show="processing"></i>
							<span class="mobile-hide-inline"> Sign In</span>
						</button>
						<div class="btn-group pull-right">
							<button type="submit" class="btn btn-default" ng-click="forgotLogin()" ng-disabled="form.$invalid || processing">
								<i class="fa fa-question-circle"></i>
								<span class="mobile-hide-inline"> Forgot Login</span>
							</button>
							<?php if ($this->site["allow_registration"]): ?>
								<a href="#" class="btn btn-default" ng-click="register()">
									<i class="fa fa-users"></i>
									<span class="mobile-hide-inline"> register</span>
								</a>
							<?php endif ?>
						</div>
					</form>
					<form ng-if="activePage == 'password-reset'" name="form" role="form" class="form-inline login-form" ng-submit="passwordReset()" novalidate>
						<div class="form-group">
							<!--[if IE 9]>
								<label for="password">Password</label>
							<![endif]-->
							<input name="password" type="password" class="form-control" placeholder="Password" ng-model="passwordDetails.password" ng-class="{'input-error' :form.password.$dirty && form.password.$invalid && form.password.$error.required}" required>
						</div>
							<div class="form-group">
							<!--[if IE 9]>
								<label for="password">New Password</label>
							<![endif]-->
							<input name="repeat_password" type="password" class="form-control" placeholder="New Password" ng-model="passwordDetails.repeat_password" ng-class="{'input-error' :form.repeat_password.$dirty && form.repeat_password.$invalid && (form.repeat_password.$error.required || form.repeatPassword.$error.confirm)}"  confirm="{{passwordDetails.password}}" required>
						</div>
						<button type="submit" class="btn btn-default" ng-disabled="form.$invalid || processing">
							<i class="fa fa-key" ng-hide="processing"></i>
							<i class="fa fa-spinner fa-spin" ng-show="processing"></i>
							<span class="mobile-hide-inline"> Reset Password</span>
						</button>
					</form>
				</div>
				<div class="row" ng-if="user.id">
					<div class="col-xs-6">
						<?php if ($this->logged_in > 5): ?>
							<i class="razor-logo razor-logo-50 razor-logo-black-circle dashboard-icon mobile-hide-block" ng-hide="changed" ng-click="openDash()"></i>
							<div class="editor-controls">
								<button class="btn btn-sm btn-default mobile-show-inline-block" ng-hide="changed" ng-click="openDash()">
									<i class="fa fa-dot-circle-o"></i>
								</button>
								<a href="?edit" class="btn btn-sm btn-primary" ng-if="user.access_level > 6 || !page.active">
									<i class="fa fa-pencil"></i><span class="mobile-hide-inline"> Edit Page</span>
								</a>
								<button class="btn btn-sm btn-default" ng-click="addNewPage()" ng-hide="toggle || changed">
									<i class="fa fa-file-text-o"></i><span class="mobile-hide-inline"> Add New Page</span>
								</button>
							</div>
						<?php endif ?>
					</div>
					<div class="col-xs-6">
						<div class="account-details text-right" ng-show="user.id">
							<span class="name">
								{{user.name}} 
								<a href="#" ng-click="editProfile()"><i class="fa fa-user" data-toggle="tooltip" data-placement="bottom" title="User Profile"></i></a>
								<a href="#" ng-click="logout()"><i class="fa fa-sign-out" data-toggle="tooltip" data-placement="bottom" title="Sign Out"></i></a>
							</span>
							<span class="last-login-date">Last login: {{user.last_logged_in * 1000 | date:'EEE, MMM d, y'}}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if ($this->logged_in > 5): ?>
			<div class="razor-admin-dashboard" ng-if="dash && user.id">
				<div class="dash-nav">
					<div class="container">
						<div class="row">
							<div class="col-sm-12">	
								<i class="razor-logo razor-logo-50 razor-logo-white-circle dashboard-icon mobile-hide-block" ng-click="closeDash();"></i>
								<div class="dash-controls">
									<ul class="dashbar-nav pull-left mobile-show-inline-block">
										<li>
											<a href="#" ng-click="closeDash();">
												<i class="fa fa-dot-circle-o"></i>
											</a>
										</li>
									</ul>
									<ul class="dashbar-nav pull-left">
										<li ng-class="{'active':activePage == 'page'}">
											<a href="#page">
												<i class="fa fa-file-text-o"></i><span class="mobile-hide-inline"> Details</span>
											</a>
										</li>
									</ul>
									<ul class="dashbar-nav pull-right">
										<li ng-class="{'active':activePage == 'pages'}">
											<a href="#pages">
												<i class="fa fa-files-o"></i><span class="mobile-hide-inline"> Pages</span>
											</a>
										</li>
										<li ng-class="{'active':activePage == 'content'}">
											<a href="#content">
												<i class="fa fa-th-large"></i><span class="mobile-hide-inline"> Content</span>
											</a>
										</li>
										<li ng-class="{'active':activePage == 'extensions'}" ng-if="user.access_level > 8">
											<a href="#extensions">
												<i class="fa fa-puzzle-piece"></i><span class="mobile-hide-inline"> Extensions</span>
											</a>
										</li>
										<li ng-class="{'active':activePage == 'profile'}" ng-if="user.access_level == 10">
											<a href="#profile">
												<i class="fa fa-user"></i><span class="mobile-hide-inline"> Users</span>
											</a>
										</li>
										<li ng-class="{'active':activePage == 'settings'}" ng-if="user.access_level > 8">
											<a href="#settings">
												<i class="fa fa-cog"></i><span class="mobile-hide-inline"> Settings</span>
												<i class="fa fa-exclamation-triangle" ng-show="upgrade" style="color: #f00;"></i>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="dash-workspace">
					<div class="container">
						<div class="row">
							<div class="col-lg-12">
								<div class="workspace" ng-show="user.id" ng-view></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif ?>
<!-- /body in template - do not close -->
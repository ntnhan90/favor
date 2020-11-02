@extends('home.layout')

@section('content')
	<div class="container"></div>
		<nav aria-label="Breadcrumb">
			<ol vocab="http://schema.org/" typeof="BreadcrumbList" class="crumbs">
				<li property="itemListElement" typeof="ListItem" class="crumbs__item">
					<a property="item"  href="/" class="crumbs__link">
						<span property="name" class="crumbs__text">Home</span>
					</a>
				</li>
				<li property="itemListElement" typeof="ListItem" class="crumbs__item">
					<a property="item" typeof="WebPage" href="" class="crumbs__link">
						<span property="name" class="crumbs__text">My Revolve</span>
					</a>
				</li>
				<li property="itemListElement" typeof="ListItem" class="crumbs__item">
					<a property="item" aria-current="page" typeof="WebPage" href="" class="crumbs__link crumbs__link--active">
						<span property="name" class="crumbs__text">Settings</span>
					</a>
				</li>
			</ol>
		</nav>
		<div class="u-margin-tb--xl">
			<h1 class="page-title__hed u-margin-t--sm u-margin-b--none">Account Settings</h1>
		</div>
		<div class="g">
	   		<div class="gc n-3-of-12 sidebar u-margin-t--none">
				<aside role="navigation" aria-labelledby="settings">
	               	<h2 id="settings" class="h5 heading heading--sub u-padding-b--lg u-margin-b--lg u-margin-t--lg u-border-b u-border--xs u-border--color-lightest">
					  	<span>Settings</span>
					</h2>
					<ul class="ui-list u-padding-t--md js-my-settings-current-page">
	  					<li id="myrev_acct_settings_nav" class="ui-list__item">
	  						<a href="{{URL::to('/profile')}}" class="active">Account Settings</a>
	  					</li>
	  					
	  					<li id="myrev_ship_nav" class="ui-list__item">
	  						<a href="{{URL::to('/profile/shipping-address')}}">Shipping Address</a>
	  					</li>
	  					<li id="myrev_bill_nav" class="ui-list__item">
	  						<a href="{{URL::to('/profile/orders')}}">Billing Information</a>
	  					</li>
					</ul>
				</aside>
			</div>
			<div class="gc n-9-of-12">
				<div class="account-settings">
					<main role="main" id="main-content" tabindex="-1">
						<div class="u-margin-t--xxl u-border--color-lightest u-border--xs u-border-b u-margin-b--xl"></div>

						<p class="account-settings__dek">You are signed in as <strong>{{ $user->email }}</strong><br>Not you?<span class="u-margin-l--md"><a href="#" class="link h4" href="#">Sign out</a></span></p>
						
						<section class="account-settings__content">
							<h2 class="account-settings__sub-hed h4">Your user name</h2>
							<form method="post">
								<fieldset>
									<div class="field n-9-of-12">
										<label for="user_name">
											<p class="field__label">user name</p>
											<input class="field__input" id="user_name" name="name" type="text" aria-invalid="false" value="{{ $user->user_name }}" required="">
											<span class="field__feedback">display name: invalid entry</span>
										</label>
									</div>
									<button id="update_name_btn" class="btn btn--lg js-modal">update display name</button>
								</fieldset>
							</form>
						</section>
						<section class="account-settings__content">
							<h2 class="account-settings__sub-hed h4">Update my password</h2>
							<form id="changePassword">
								<fieldset>
									<div class="field n-9-of-12">
										<label for="current_password">
											<p class="field__label">current password</p>
											<input class="no-mouseflow field__input" type="password" name="current_password" id="current_password" aria-invalid="false">
											<span class="field__feedback"><span aria-hidden="true">password:</span> invalid entry</span>
										</label>
									</div>
									<div class="field n-9-of-12">
										<label for="new_password">
											<p class="field__label">new password</p>
											<input class="no-mouseflow field__input" type="password" name="new_password" id="new_password" aria-invalid="false" required="">
											<span class="field__feedback"><span aria-hidden="true">new password:</span> missing entry</span>
										</label>
									</div>
									<div class="field n-9-of-12">
										<label for="verify_password">
											<p class="field__label">verify new password</p>
											<input class="field__input" name="verify_password" id="verify_password" type="password" aria-invalid="false" required="">
											<span class="field__feedback">password and verify password do not match</span>
										</label>
									</div>
									<button id="save_change_password" onclick="javascript:verifyChangePassword();return false;" type="submit" class="btn btn--lg js-modal">update password</button>
								</fieldset>
							</form>
						</section>
					</main>
				</div>
			</div>
	   	</div>

	   		
	</div>
@endsection
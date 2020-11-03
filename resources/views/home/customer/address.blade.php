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
  						<a href="#" >Account Settings</a>
  					</li>
  					<li id="myrev_ship_nav" class="ui-list__item">
  						<a href="#" class="active">Shipping Address</a>
  					</li>
  					<li id="myrev_bill_nav" class="ui-list__item">
  						<a href="#">Billing Information</a>
  					</li>
				</ul>
			</aside>
		</div>
		<div class="gc n-9-of-12">
			<div class="gc n-9-of-12">
				<main role="main" id="main-content" tabindex="-1">
					<div class="u-margin-t--xxl u-border--color-lightest u-border--xs u-border-b u-margin-b--lg"></div>
					<div class="clear"></div>
					<div class="u-block mr_settings_shipping u-padding-t--none">
						<table class="mr_ship_edit shopping-bag u-margin-t--none">
							<tbody>
								@foreach ($result['address'] as $address)
								<tr class="shopping-bag__row">
									<td class="shopping-bag__row-item">
										<div class="g g--collapse">
											<div class="gc n-6-of-12 u-padding-b--none">
												<div class="g">
													
													<div class="gc n-6-of-12">
														<p class="u-margin-b--none">{{$address->lastname}} {{$address->firstname}}</p>
														<p class="u-margin-b--none">{{$address->street}}</p>
														<p class="u-margin-b--none">{{$address->city}}</p>
														
														<p class="u-margin-b--none"></p>
														<p class="u-margin-b--none">{{$address->country_name}}</p>
													</div>	
													
												</div>
											</div>
											<div class="gc n-6-of-12 u-right">
												<div><a class="js-modal link edit_a" href="{{ URL::to('/profile/shipping-address?address_id='.$address->address_id)}}" >Edit </a>
												</div>
												<div><a class="js-modal link remove_a" href="#remove_shipping" data-type="small" data-address-id="58432916">Remove </a></div>
											</div>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>

						<input type="submit" value="Add New Address" class="co_cta btn btn--md" id="add_new_address">

						<div class="main-form" style="padding:15px 0">
              				<form name="addMyAddress" class="form-validate" enctype="multipart/form-data" action="@if(!empty($result['editAddress'])){{ URL::to('/profile/update-address')}}@else{{ URL::to('/profile/addAddress')}}@endif" method="post">
                				<input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}" class="field__input"/>
                				@if(!empty($result['editAddress']))
				                <input type="hidden" class="field__input" name="address_book_id" value="{{$result['editAddress'][0]->address_id}}">
				                @endif
				                @if( count($errors) > 0)
			                    @foreach($errors->all() as $error)
			                       	<div class="alert alert-danger" role="alert">
			                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			                            <span class="sr-only">@lang('website.Error'):</span>
			                               {{ $error }}
			                        </div>
			                    @endforeach
			                   	@endif
                                <div class="form-row">
						            <div class="form-group col-md-6">
						                <label for="inputfirstname"><span class="star">*</span>First Name</label>
						                <input type="text" name="entry_firstname" class="form-control field-validate field__input" id="entry1_firstname" @if(!empty($result['editAddress'])) value="{{$result['editAddress'][0]->firstname}}" @endif>
						                <span class="help-block error-content7" hidden="">Please enter your first name</span>
						            </div>
						            <div class="form-group col-md-6">
						            	<label for="inputlastname">Last Name</label>
						            	<input type="text" name="entry_lastname" class="form-control field-validate field__input" rid="entry1_lastname" @if(!empty($result['editAddress'])) value="{{$result['editAddress'][0]->lastname}}" @endif>
						            	<span class="help-block error-content7" hidden="">Please enter your address.</span>       
						            </div>
						        </div>
						        
						        <div class="form-row">
						            <div class="form-group col-md-6">
						                <label for="inputcomapnyname"><span class="star">*</span>Address</label>
						                <input type="text" name="entry_street_address"  class="form-control field-validate field__input" id="entry1_street_address" @if(!empty($result['editAddress'])) value="{{$result['editAddress'][0]->street}}" @endif>
						                      	<span class="help-block error-content7" hidden="">Please enter your address.</span>
						        	</div>
						        </div>
						        <div class="form-row">
						            <div class="form-group select-control col-md-6">
						             	<label for="inputState"><span class="star">*</span> City</label>
						                <input type="text" name="entry_city"  class="form-control field-validate field__input" id="entry_city1" @if(!empty($result['editAddress'])) value="{{$result['editAddress'][0]->city}}" @endif>
						                <span class="help-block error-content7" hidden="">Please enter your city.</span>
						            </div>
						        </div>

						       	<select name="entry_country_id"  onChange="getZones();" id="entry_country_id" class="form-control field-validate">
			                          	<option value="">select Country</option>
			                          	@foreach($result['countries'] as $countries)
			                          	<option value="{{$countries->countries_id}}" @if(!empty($result['editAddress'])) @if($countries->countries_id==$result['editAddress'][0]->countries_id) selected @endif @endif>{{$countries->countries_name}}</option>
			                          	@endforeach
			                    </select>
	                 
							    <div class="button">
				                  	@if(!empty($result['editAddress']))
				                      <a href="{{ URL::to('/shipping-address')}}" class="btn btn-default">cancel</a>
				                  	@endif
				                      <button type="submit" class="co_cta btn btn--md">@if(!empty($result['editAddress']))  Update  @else Add Address @endif </button>
				                </div>
					        </form>
					    </div>
					</div>
			  		<div class="clear"></div>
				</main>
			</div>
		</div>
   	</div>	
	</div>
@endsection
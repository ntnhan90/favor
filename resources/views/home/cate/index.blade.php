@extends('home.layout')

@section('content')
    <nav class="crumbs" aria-label="Breadcrumb">
        <nav aria-label="Breadcrumb">
    		<ol vocab="http://schema.org/" typeof="BreadcrumbList" class="crumbs">
                <li property="itemListElement" typeof="ListItem" class="crumbs__item">
                    <a property="item" typeof="WebPage" href="#" class="crumbs__link" title="Women">
                		<span property="name" class="crumbs__text">Women</span>
                	</a>
                	<meta property="position" content="0">
            	</li>
                <li property="itemListElement" typeof="ListItem" class="crumbs__item">
                    <a property="item" aria-current="page" typeof="WebPage" href="#" class="crumbs__link crumbs__link--active" title="Clothing">
                		<span property="name" class="crumbs__text">Clothing</span>
                	</a>
                	<meta property="position" content="1">
            	</li>
            </ol>
		</nav>
	</nav>    
	<div class="u-margin-tb--xl">
	  	<div class="u-flex">
	        <h1 class="page-title__hed u-margin-t--sm u-margin-b--none">CLOTHING</h1>
	  	</div>
	</div>
	<div class="g">
        <div class="gc n-3-of-12 sidebar sidebar--categories">
			<aside class="block block--md js-left-nav-container" role="complementary" aria-label="left navigation">
			    <h2 id="side-nav-2" class="h5 heading heading--sub u-padding-b--lg u-margin-b--md u-margin-t--none u-border-b u-border--xs u-border--color-lightest">
    				<span class="">Category</span>
				</h2>

                <ul class="ui-list u-padding-t--md u-margin-b--xxl" role="navigation" aria-labelledby="side-nav-2">
                	@foreach($result['categories'] as $category)
                	<li class="ui-list__item">
                        <a class="false " href="cate/{{$category->slug}}" title="Activewear">{{ $category->name}}</a>
                    </li>
                	@endforeach
            	</ul>
			</aside>
        </div>

        <div class="gc n-9-of-12">
            <div class="filters">
                <div class="filters__bar u-clearfix">
                	<!-- @dump($result['attributes'][0]) -->
                   	@foreach($result['attributes'] as $attribute)
		            <div class="u-margin-r--xl js-dropdown dropdown dropdown--full u-inline-block">
    					<a href="#" class="js-dropdown__trigger link link--noline filters__type u-font-primary u-text--lg" aria-haspopup="true" role="button" aria-expanded="false" aria-controls="dd-menu-922197166">
    						<span class="u-screen-reader">filter </span>
    						{{  $attribute->products_options_name }}
        					<span class="h5 filters__count-size"></span>
					        <span aria-hidden="true" class="icon icon--sm icon--arrow-down">
					            <svg viewBox="0 0 16 16" focusable="false" aria-hidden="true">
					                <use xlink:href="#icon-arrow-down"></use>
					            </svg>
					        </span>
    					</a>
   					 	<div class="u-margin-r--xl js-dropdown__menu dropdown__menu island" role="menu" id="dd-menu-922197166" style="opacity: 0; display: none;">
                    		<div class="island">
                				<div class="h5 u-margin-t--none"> {{  $attribute->products_options_name }} </div>
								<ul class="ui-list g g--collapse-all n-block-grid--6 filters__size-list">
									@foreach($attribute->data as $value1)
									 	@if(count($value1->values)>0)
                                        	@foreach($value1->values as $value)
                                        	<li class="ui-list__item gc">
										        <input id="Size__xxs" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="size" value="XXS">
							            		<label for="Size__xxs" class="filters__size-copy link link--noline push-button push-button--md">{{$value->options_values_name}}</label>
										    </li>
                                            @endforeach
                                        @else
                                            --- <br>
                                        @endif
                                    @endforeach
    							</ul>
            				</div>
			                <div class="island">
					            <button class="js-dropdown-close btn filters__apply-btn" onclick="applyFilters({filter: 'size'})">
									Done
					            </button>
					            <button class="" onclick="clearFilter('size')">
					                <span class="u-margin-l--xl u-text--lg link link--bold">
										Clear
					                </span>
					            </button>
					        </div>
					    </div>
					</div>
		            @endforeach
					<!--
                    <div class="u-margin-r--xl js-dropdown dropdown dropdown--full u-inline-block">
    					<a href="#" class="js-dropdown__trigger link link--noline filters__type u-font-primary u-text--lg" aria-haspopup="true" role="button" aria-expanded="false" aria-controls="dd-menu-8538309235">
						    <span class="u-screen-reader">filter </span>
						    Price
						    <span class="h5 filters__count-price">   </span>
						    <span aria-hidden="true" class="icon icon--sm icon--arrow-down">
						       	<svg viewBox="0 0 16 16" focusable="false" aria-hidden="true">
						            <use xlink:href="#icon-arrow-down"></use>
						        </svg>
						    </span>
    					</a>
    					<div class="u-margin-r--xl js-dropdown__menu dropdown__menu island" role="menu" id="dd-menu-8538309235" style="opacity: 0; display: none;">
                    		<div class="island">
				                <div class="h5 u-margin-t--none"> Price</div>
								<ul class="ui-list filters__size-list u-inline-block u-full-width">
									<li class="ui-list__item u-float--left">
									    <input id="Price__under_ten" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="UNDER_TEN">
									    <label for="Price__under_ten" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">Under ₫ 231,800</label>
									</li>
									                                    
									<li class="ui-list__item u-float--left">
									    <input id="Price__ten_to_twenty" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="TEN_TO_TWENTY">
									    <label for="Price__ten_to_twenty" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 231,800 - ₫ 463,600</label>
									</li>
                                    
									<li class="ui-list__item u-float--left">
									    <input id="Price__twenty_to_forty" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="TWENTY_TO_FORTY">
									    <label for="Price__twenty_to_forty" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 463,600 - ₫ 927,200</label>
									</li>
									                                    
									<li class="ui-list__item u-float--left">
									    <input id="Price__forty_to_sixty" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="FORTY_TO_SIXTY">
									    <label for="Price__forty_to_sixty" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 927,200 - ₫ 1,390,800</label>
									</li>
									                                    
									 <li class="ui-list__item u-float--left">
									    <input id="Price__sixty_to_eighty" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="SIXTY_TO_EIGHTY">
									    <label for="Price__sixty_to_eighty" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 1,390,800 - ₫ 1,854,400</label>
									</li>
                                    
									<li class="ui-list__item u-float--left">
									   	<input id="Price__eighty_to_one" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="EIGHTY_TO_ONE">
									    <label for="Price__eighty_to_one" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 1,854,400 - ₫ 2,318,000</label>
									</li>
									                                    
									<li class="ui-list__item u-float--left">
									    <input id="Price__one_to_two" class="js-enterable filters__size-checkbox u-screen-reader" type="checkbox" name="price" value="ONE_TO_TWO">
									    <label for="Price__one_to_two" class="filters__size-copy link link--noline u-padding-rl--xl push-button push-button--md">₫ 2,318,000 - ₫ 4,636,000</label>
									</li>
    							</ul>
						   	</div>
						    <div class="island">
							    <button class="js-dropdown-close btn filters__apply-btn" onclick="applyFilters({filter: 'price'})">Done </button>
							    <button class="" onclick="clearFilter('price')">
							        <span class="u-margin-l--xl u-text--lg link link--bold">Clear</span>
							    </button>
							</div>
						</div>
					</div>
					-->
					<button class="link link--bold u-text--lg js-filter-clear-button" style="display: none" onclick="clearFilters()">
					    Reset
					</button>
               	</div>

                <div class="filters__sub">
                    <div class="g g--collapse-all">
                        <div class="gc n-6-of-12 u-static">
                            <span class="u-margin-r--xl h5">
                            	<span class="js-item-count">{{$result['count'] }} </span> 
                            Items</span>
                            <div class="js-dropdown dropdown dropdown--center u-inline-block">
    							<div class="filters__type u-text--lg">
        							Sort By
							        <a href="#" class="js-dropdown__trigger h5 link link--noline js-sort-by" aria-haspopup="true" role="button" aria-expanded="false" aria-controls="dd-menu-1456615742">Newest </a>
							        <span aria-hidden="true" class="icon icon--sm icon--arrow-down">
							            <svg viewBox="0 0 16 16" focusable="false" aria-hidden="true">
							                <use xlink:href="#icon-arrow-down"></use>
							            </svg>
							        </span>
    							</div>
    							<div class="js-dropdown__menu dropdown__menu island" role="menu" id="dd-menu-1456615742" style="opacity: 0; display: none;">
        							<div class="island">
                       					<ul class="ui-list">
	                                    	<li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap" id="js-sort-by-newest" onclick="sortBy('newest', 'Newest');">Newest</button>
						                    </li>
                                    		<li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap" id="js-sort-by-priced" onclick="sortBy('priced', 'Price: High to Low');">Price: High to Low</button>
						                    </li>
                                    		<li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap" id="js-sort-by-price" onclick="sortBy('price', 'Price: Low to High');">Price: Low to High</button>
						                    </li>
                            			</ul>
							        </div>
							    </div>
							</div>
                        </div>
                        <div class="gc n-6-of-12 u-right u-static">
                            <div class="js-dropdown dropdown dropdown--center u-inline-block">
							    <div class="filters__type u-text--lg">
							        View :
							        <a href="#" class="js-dropdown__trigger link link--noline h5" aria-haspopup="true" role="button" aria-expanded="false" aria-controls="dd-menu-9953062540">
							          	10
							          	<span aria-hidden="true" class="icon icon--sm icon--arrow-down">
								            <svg viewBox="0 0 16 16" focusable="false" aria-hidden="true">
								                <use xlink:href="#icon-arrow-down"></use>
								            </svg>
							          	</span>
							        </a>
							    </div>
    							<div class="js-dropdown__menu dropdown__menu island" role="menu" id="dd-menu-9953062540" style="opacity: 1; display: block;">
        							<div class="island">
                       			 		<ul class="ui-list u-left">
                       			 			<li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap">1</button>
						                    </li>
		                                    <li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap">10</button>
						                    </li>
			                                <li class="ui-list__item">
						                        <button class="link link--light link--noline u-no-wrap">30</button>
						                    </li>
                            			</ul>
							        </div>
							    </div>
							</div>
                        </div>
	                </div>
	            </div>

	            <main role="main" id="main-content" data-filter-delimeter="-">
					<input type="hidden" id="isFilterResultProducts" name="isFilterResultProducts" value="true">
					<ul id="plp-prod-list" class="g products-grid u-margin-b--xxl block-grid--inline n-block-grid--3 lg-block-grid--4 js-plp-lazy-load">
						@foreach ($result['products'] as $product)
						<li class="gc u-center item js-plp-container plp-container-VCHP-WD28" id="VCHP-WD28">
				        	<div class="u-text-decoration--none image-hover products-grid__image-link u-margin-b--lg">
	            				<a class="js-plp-pdp-link plp__image-link plp__image-link--lg" href="">
	                    			<img alt="" src="{{ asset($product->path) }}" >
	                            </a>
	            
								<button class="js-favorite-button favorite-button" >
								    <span class="icon  icon--lg icon--heart-fill favorite-button__icon">
								        <svg focusable="false" aria-hidden="true">
								          <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-heart-fill"></use>
								        </svg>
								    </span>
								</button>
								<span class="js-favorite-notify u-screen-reader" role="alert"></span>

	                            <a href="#" id="qv-open-VCHP-WD28" class="image-hover__btn image-hover__btn--focusable js-plp-quickview" data-code="VCHP-WD28" onclick="showQuickViewV2(this, 'VCHP-WD28', 'Womens', '', event)" aria-label="QUICK VIEW Lily Dress">
					                QUICK VIEW
					            </a>
	      					</div>
		        			<a href="/product/{{$product->products_slug}}" class="u-text-decoration--none js-plp-pdp-link2 product-link" tabindex="-1">
						        <div class="product-name u-margin-t--lg js-plp-name">{{ $product->products_name}}</div>
						            <div class="product-brand u-margin-b--sm js-plp-brand">V. Chapman</div>
		            				<div>
		                                <div class="price js-plp-prices-div">
		                                     <span class="plp_price price__retail">{{ $product->products_price}}</span>
		                                </div>
		                            </div>
		                        </a>
		                        <!--
		                        <div class="plp-swatches product-swatches product-swatches--sm u-margin-t--none js-plp-swatches" style="overflow-y: hidden;">
		                    		<div class="js-swatch-toggle-container" style="float: right; display: none">
		                        		<span class="icon icon--sm icon--arrow-down js-swatch-toggle-down"><svg viewBox="0 0 16 16"> <use xlink:href="#icon-arrow-down"></use> </svg></span>

		                        		<span class="icon icon--sm icon--arrow-up js-swatch-toggle-up" style="display: none"><svg viewBox="0 0 16 16"> <use xlink:href="#icon-arrow-up"></use> </svg></span>
		                    		</div>
		                    		<ul class="ui-list">
		                                <li class="product-swatches__swatch js-plp-swatch" data-code="VCHP-WD28" data-department="F" data-badge="NEW" data-badge-class="">
		                                	<input id="color-swatch-VCHP-WD28-1" type="radio" name="product-color-VCHP-WD28" class="product-swatches__radio" checked="" value="Black">
		                                	<label for="color-swatch-VCHP-WD28-1" class="product-swatches__label">
		                                        <img class="product-swatches__image" src="https://is4.revolveassets.com/images/p/s2/VCHP-WD28_V2.jpg" alt="color: Black">
		                                    </label>
		                            	</li>
		                                <li class="product-swatches__swatch js-plp-swatch" data-code="VCHP-WD27" data-department="F" data-badge="NEW" data-badge-class="">
			                                <input id="color-swatch-VCHP-WD28-2" type="radio" name="product-color-VCHP-WD28" class="product-swatches__radio" value="Red Clay">
			                                <label for="color-swatch-VCHP-WD28-2" class="product-swatches__label">
		                                        <img class="product-swatches__image" src="https://is4.revolveassets.com/images/p/s2/VCHP-WD27_V2.jpg" alt="color: Red Clay">
		                                    </label>
		                            	</li>
		                            </ul>
		                		</div>
		                	-->
							</li>
						@endforeach

					</ul>
					
					{{ $result['products']->links('home.paginate', ['products' => $result['products']]) }}
					
				</main>
			</div>
		</div>
	</div>
@stop


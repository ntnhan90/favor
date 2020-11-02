@if ($paginator->hasPages())
	<div class="pagination-alternative u-margin-t--xl u-margin-b--xxl u-center">
		@if ($paginator->onFirstPage())
            <a tabindex="-1" class="pagination__controls link link--noline u-inline-block" href="javascript:void(0);" rel="prev" disabled="">
				<div class="btn btn--circle btn--circle--lg" aria-label="disabled" disabled="">
					<span class="icon icon--sm icon--arrow-left--lg">
						<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"> <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-left--lg"></use> </svg>
					</span>
				</div>
				<span class="u-hide--visible">previous page</span>
			</a>
            @else
            <a tabindex="-1" class="pagination__controls link link--noline u-inline-block" href="{{ $paginator->previousPageUrl() }}" rel="prev">
				<div class="btn btn--circle btn--circle--lg" aria-label="disabled" >
					<span class="icon icon--sm icon--arrow-left--lg">
						<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"> <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-left--lg"></use> </svg>
					</span>
				</div>
				<span class="u-hide--visible">previous page</span>
			</a>
        @endif
		
		<div class="pagination u-inline-block">
			<ul class="pagination__list u-margin-l--none">
				@foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                        <li class="filters__pagination-item pagination__item ">
	                        <a class="pagination__link u-padding-l--none active" href="{{ $url }}">{{ $page }}</a>
	                    </li>
                        @elseif (($page == $paginator->currentPage() + 1 || $page == $paginator->currentPage() + 2) || $page == $paginator->lastPage())
                        <li class="filters__pagination-item pagination__item">
	                        <a class="pagination__link u-padding-l--none" href="{{ $url }}">{{ $page }}</a>
	                    </li>
                        @endif
                    @endforeach
                @endif
            	@endforeach
			</ul>
		</div>
						
		@if ($paginator->hasMorePages())
            <a class="pagination__controls link link--noline u-inline-block--top" href="{{ $paginator->nextPageUrl() }}" rel="next" id="tr-pagination__controls--next">
				<div class="btn btn--circle btn--circle--lg">
					<span class="icon icon--sm icon--arrow-right--lg u-padding-r--xs">
						<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"> <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right--lg"></use> </svg>
					</span>
				</div>
				<span class="u-hide--visible">next page</span>
			</a>
        @else
            <a class="pagination__controls link link--noline u-inline-block--top" href="javascript:void(0);" rel="next" id="tr-pagination__controls--next" disabled="">
				<div class="btn btn--circle btn--circle--lg">
					<span class="icon icon--sm icon--arrow-right--lg u-padding-r--xs">
						<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"> <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow-right--lg"></use> </svg>
					</span>
				</div>
				<span class="u-hide--visible">next page</span>
			</a>
        @endif
		
	</div>
@endif
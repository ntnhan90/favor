<!DOCTYPE html>
<html>

@include('admin.common.meta')

<body class=" hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <div class="se-pre-con" id="loader" style="/* display: none; */">
            <div class="pre-loader">
                <div class="la-line-scale">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <p>@lang('labels.Loading')..</p>
            </div>
        </div>
        @include('admin.common.header')
        @include('admin.common.sidebar')

        @yield('content')

        @include('admin.common.controlsidebar')
        @include('admin.common.footer')
    </div>

    @include('admin.common.scripts')

</body>
</html>

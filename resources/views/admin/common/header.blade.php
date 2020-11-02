<header class="main-header">
    <a href="{{ URL::to('admin/dashboard')}}" class="logo">
        <span class="logo-mini" style="font-size:12px"><b>{{ trans('labels.admin') }}</b></span>
        <span class="logo-lg"><b>{{ trans('labels.admin') }}</b></span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" id="linkid" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('labels.toggle_navigation') }}</span>
        </a>
    <div id="countdown" style="width: 350px; margin-top: 13px !important;position: absolute;font-size: 16px;color: #ffffff; display: inline-block;margin-left: -175px;left: 50%;"></div>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-list-ul"></i>
                        <span class="label label-success"></span>
                    </a>
                </li>
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-users"></i>
                        <span class="label label-warning">1</span>
                    </a>
                </li>
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-th"></i>
                        <span class="label label-warning">2</span>
                    </a>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs">{{ auth()->user()->user_name }}  </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <p>
                                {{ auth()->user()->user_name }}
                                <small>{{ trans('labels.administrator')}}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                              <a href="{{ URL::to('admin/admin/profile')}}" class="btn btn-default btn-flat">{{ trans('labels.profile_link')}}</a>
                            </div>
                            <div class="pull-right">
                              <a href="{{ URL::to('admin/logout')}}" class="btn btn-default btn-flat">{{ trans('labels.sign_out') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

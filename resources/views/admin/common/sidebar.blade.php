<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">{{ trans('labels.navigation') }}</li>
            <li class="treeview {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>{{ trans('labels.header_dashboard') }}</span>
                </a>
            </li>
      
            <li class="treeview {{ Request::is('admin/media') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-picture-o"></i> <span>{{ trans('labels.media') }}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="treeview {{ Request::is('admin/media') ? 'active' : ''}}">
                        <a href="{{ URL::to('admin/media/setting')}}">
                            <i class="fa fa-circle-o" aria-hidden="true"></i> <span> {{ trans('labels.media') }} </span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-circle-o" aria-hidden="true"></i> <span> {{ trans('labels.Media Setings') }} </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ URL::to('admin/menus')}}">
                    <i class="fa fa-dashboard"></i> <span>{{ trans('labels.menus') }}</span>
                </a>
            </li>

            <li class="treeview">
                <a href="{{ URL::to('admin/currencies')}}">
                    <i class="fa fa-circle-o"></i> {{ trans('labels.currency') }}
                </a>
            </li>
     
            <li class="treeview">
                <a href="{{ URL::to('admin/customers')}}">
                    <i class="fa fa-users" aria-hidden="true"></i> <span>{{ trans('labels.link_customers') }}</span>
                </a>
            </li>

           

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-database"></i> <span>{{ trans('labels.Catalog') }}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{ URL::to('admin/manufacturers')}}"><i class="fa fa-circle-o"></i> {{ trans('labels.link_manufacturer') }}</a>
                    </li>
           
                    <li class="">
                        <a href="{{ URL::to('admin/categories')}}"><i class="fa fa-circle-o"></i> {{ trans('labels.link_main_categories') }}</a>
                    </li>

                    <li class="" >
                        <a href="{{ URL::to('admin/products/attributes/' )}}"><i class="fa fa-circle-o"></i> {{ trans('labels.products_attributes') }}</a>
                    </li>
                    <li class="">
                        <a href="{{ URL::to('admin/products')}}"><i class="fa fa-circle-o"></i> {{ trans('labels.link_all_products') }}</a>
                    </li>
                    <li class="">
                        <a href="{{ URL::to('admin/products/reviews')}}"><i class="fa fa-circle-o" aria-hidden="true"></i> <span>{{ trans('labels.reviews') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#" ><i class="fa fa-list-ul" aria-hidden="true"></i> <span> {{ trans('labels.link_orders') }}</span> <i class="fa fa-angle-left pull-right"></i> </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{ URL::to('admin/orderstatus')}}">
                            <i class="fa fa-circle-o"></i> {{ trans('labels.link_order_status') }}
                        </a>
                    </li>
                    <li class="">
                        <a href="{{ URL::to('admin/orders')}}" >
                            <i class="fa fa-circle-o" aria-hidden="true"></i> <span> {{ trans('labels.link_orders') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    <span>{{ trans('labels.link_reports') }}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{ URL::to('admin/outofstock')}}">
                            <i class="fa fa-circle-o"></i> {{ trans('labels.link_out_of_stock_products') }}
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="{{ URL::to('admin/coupons/')}}" ><i class="fa fa-tablet" aria-hidden="true"></i> 
                    <span>{{ trans('labels.link_coupons') }}</span>
                </a>
            </li>

            <li class="treeview">
                <a href="{{ URL::to('admin/shipping')}}">
                    <i class="fa fa-truck" aria-hidden="true"></i> 
                    <span> {{ trans('labels.link_shipping_methods') }}</span> *
                </a>
            </li>

            <li class="treeview">
                <a  href="{{ URL::to('admin/payment/index')}}">
                    <i class="fa fa-credit-card" aria-hidden="true"></i> 
                    <span>  {{ trans('labels.link_payment_methods') }}</span>*
                </a>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-database" aria-hidden="true"></i>
                    <span> {{ trans('labels.Blog') }}</span> *
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{ URL::to('admin/newscategories')}}">
                            <i class="fa fa-circle-o"></i>
                            {{ trans('labels.link_news_categories') }}
                        </a>
                    </li>
                    <li class="">
                        <a href="{{ URL::to('admin/news')}}">
                            <i class="fa fa-circle-o"></i> 
                            {{ trans('labels.link_sub_news') }}
                        </a>
                    </li>
                </ul>
            </li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                    <span>{{ trans('labels.link_notifications') }}</span> *
                </a>
            </li>

            <li class="treeview {{ Request::is('admin/admins') ? 'active' : '' }} {{ Request::is('admin/addadmins') ? 'active' : '' }} {{ Request::is('admin/editadmin/*') ? 'active' : '' }} {{ Request::is('admin/manageroles') ? 'active' : '' }} {{ Request::is('admin/addadminType') ? 'active' : '' }} {{ Request::is('admin/editadminType/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    <span> {{ trans('labels.Manage Admins') }}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul class="treeview-menu">
                    <li class="">
                        <a href="{{ URL::to('admin/admins')}}">
                            <i class="fa fa-circle-o"></i> {{ trans('labels.link_admins') }}</a>
                        </li>
                    <li class="">
                        <a href="{{ URL::to('admin/manageroles')}}">
                            <i class="fa fa-circle-o"></i> {{ trans('labels.link_manage_roles') }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
  </aside>

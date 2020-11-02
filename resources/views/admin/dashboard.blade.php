@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
            <small>{{ trans('labels.title_dashboard') }} </small>
            </h1>
            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</li>
            </ol>
        </section>
       
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>order</h3>
                            <p>{{ trans('labels.NewOrders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer" data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.NewOrders') }}">{{ trans('labels.NewOrders') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
               
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-light-blue">
                        <div class="inner">
                            <h3>123</h3>
            			     <p>{{ trans('labels.totalProducts') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer" data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.viewAllProducts') }}">{{ trans('labels.viewAllProducts') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="nav-tabs-custom">
                        <div class="box-header with-border">
                            <h3 class="box-title"> {{ trans('labels.addedSaleReport') }}</h3>
                            <div class="box-tools pull-right">
                                <p class="notify-colors">
                                    <span class="sold-content" data-toggle="tooltip" data-placement="bottom" title="Sold Products"></span> {{ trans('labels.soldProducts') }} 
                                    <span class="purchased-content" data-toggle="tooltip" data-placement="bottom" title="Added Products"></span>{{ trans('labels.addedProducts') }} 
                                </p>
                            </div>
                        </div>
                        {!! Form::hidden('reportBase' ) !!}
                        <ul class="nav nav-tabs">
                            <li style="width: 33%"><a href="#" data-toggle="tab">
                                    <div class="input-group ">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default" aria-label="Help">{{ trans('labels.custom') }}</button>
                                        </div>
                                        <input class="form-control reservation dateRange" readonly value="" name="dateRange" aria-label="Text input with multiple buttons ">
                                        <div class="input-group-btn"><button type="button" class="btn btn-primary getRange" >{{ trans('labels.go') }}</button> </div>
                                    </div>
                                </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="chart">
                                    <canvas id="salesChart" style="height: 400px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ trans('labels.latest_customers') }}</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    
                                    <p style="padding: 8px 0 0 10px;">{{ trans('labels.no_customer_exist') }}</p>

                                </div>
                                <div class="box-footer text-center">
                                    <a href="{{ url('admin/customers')}}" class="uppercase" data-toggle="tooltip" data-placement="bottom" title="View All Customers">{{ trans('labels.viewAllCustomers') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE: LATEST ORDERS -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('labels.NewOrders') }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('labels.OrderID') }}</th>
                                        <th>{{ trans('labels.CustomerName') }}</th>
                                        <th>{{ trans('labels.TotalPrice') }}</th>
                                        <th>{{ trans('labels.Status') }} </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <a href="#" class="btn btn-sm btn-default btn-flat pull-right" data-toggle="tooltip" data-placement="bottom" title="View All Orders">{{ trans('labels.viewAllOrders') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('labels.GoalCompletion') }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="progress-group">
                                <span class="progress-text">{{ trans('labels.AddProductstoCart') }}</span>
                                <span class="progress-number"><b></b>/500</span>

                            </div>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('labels.RecentlyAddedProducts') }}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="products-list product-list-in-box">

                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <a href="#" class="uppercase" data-toggle="tooltip" data-placement="bottom" title="View All Products">{{ trans('labels.viewAllProducts') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="{!! asset('admin/plugins/jQuery/jQuery-2.2.0.min.js') !!}"></script>

    <script src="{!! asset('admin/dist/js/pages/dashboard2.js') !!}"></script>
@endsection

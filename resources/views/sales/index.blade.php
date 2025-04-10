@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('app.sales_dashboard') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __("app.todays_sales") }}</h5>
                                    <h2>${{ number_format($todaySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('app.weekly_sales') }}</h5>
                                    <h2>${{ number_format($weeklySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('app.monthly_sales') }}</h5>
                                    <h2>${{ number_format($monthlySales, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Range Filter -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.filter_sales_trend') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.index') }}" method="GET" class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_start_date">{{ __('app.start_date') }}</label>
                                                <input type="date" class="form-control" id="chart_start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="chart_end_date">{{ __('app.end_date') }}</label>
                                                <input type="date" class="form-control" id="chart_end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">{{ __('app.apply_filter') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.sales_trend') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <!-- Payment Methods -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.payment_methods') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Trending Items -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.trending_items') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($trendingItems) && count($trendingItems) > 0)
                                        <ul class="list-group">
                                            @foreach($trendingItems as $item)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $item->product_name }}
                                                    <span class="badge bg-primary rounded-pill text-white">{{ $item->quantity_sold }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">{{ __('app.no_trending_items_data') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.order_breakdown') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($orderBreakdownCount > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('app.date') }}</th>
                                                        <th>{{ __('app.orders') }}</th>
                                                        <th>{{ __('app.items_sold') }}</th>
                                                        <th>{{ __('app.revenue') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderBreakdown as $breakdown)
                                                    <tr>
                                                        <td>{{ $breakdown->date }}</td>
                                                        <td>{{ $breakdown->order_count }}</td>
                                                        <td>{{ $breakdown->items_sold ?? 0 }}</td>
                                                        <td>${{ number_format($breakdown->revenue, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            {{ __('app.no_order_data') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Reports -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('app.export_reports') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales.export') }}" method="GET">
                                        <div class="form-group mb-3">
                                            <label for="timeframe">{{ __('app.select_timeframe') }}</label>
                                            <select name="timeframe" id="timeframe" class="form-control">
                                                <option value="today">{{ __('app.today') }}</option>
                                                <option value="yesterday">{{ __('app.yesterday') }}</option>
                                                <option value="this_week">{{ __('app.this_week') }}</option>
                                                <option value="last_week">{{ __('app.last_week') }}</option>
                                                <option value="this_month">{{ __('app.this_month') }}</option>
                                                <option value="last_month">{{ __('app.last_month') }}</option>
                                                <option value="custom">{{ __('app.custom') }}</option>
                                            </select>
                                        </div>
                                        <div id="customDateRange" style="display: none;" class="mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="start_date">{{ __('app.start_date') }}</label>
                                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="end_date">{{ __('app.end_date') }}</label>
                                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ __('app.download_csv') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->
@endsection

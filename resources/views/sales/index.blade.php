@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sales Dashboard</h1>

    <div class="row">
        <div class="col-md-6">
            <h3>Total Sales Today</h3>
            <p>${{ number_format($totalSalesToday, 2) }}</p>
        </div>
        <div class="col-md-6">
            <h3>Total Sales This Month</h3>
            <p>${{ number_format($totalSalesThisMonth, 2) }}</p>
        </div>
    </div>
</div>
@endsection

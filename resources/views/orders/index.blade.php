@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('app.order_history') }}</h1>

    @if ($orders->isEmpty())
        <p>{{ __('app.no_orders_found') }}</p>
    @else
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>{{ __('app.order_id') }}</th>
                    <th>{{ __('app.customer_name') }}</th>
                    <th>{{ __('app.total_amount') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th>{{ __('app.payment_method') }}</th>
                    <th>{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ __('app.' . $order->status) }}</td>
                        <td>{{ __('app.' . strtolower($order->payment_method)) }}</td>
                        <td>
                            <a href="#" class="btn btn-info">{{ __('app.view') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

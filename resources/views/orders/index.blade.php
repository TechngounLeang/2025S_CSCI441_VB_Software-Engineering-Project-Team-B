<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>{{ __('app.order_history') }}</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('app.create_new_order') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($orders->isEmpty())
        <div class="card">
            <div class="card-body text-center p-5">
                <h3>{{ __('app.no_orders_found') }}</h3>
                <p class="text-muted">{{ __('app.create_your_first_order') }}</p>
                <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> {{ __('app.create_new_order') }}
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('app.order_id') }}</th>
                                <th>{{ __('app.customer_name') }}</th>
                                <th>{{ __('app.total_amount') }}</th>
                                <th>{{ __('app.status') }}</th>
                                <th>{{ __('app.payment_method') }}</th>
                                <th class="text-center">{{ __('app.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->formatted_total }}</td>
                                    <td>
                                        <span class="badge badge-{{ $order->status_badge }} px-3 py-2">
                                            {{ __('app.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ __('app.' . strtolower($order->payment_method)) }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> {{ __('app.view') }}
                                            </a>
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> {{ __('app.edit') }}
                                            </a>
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('{{ __('app.delete_confirmation') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> {{ __('app.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('app.product_list') }}</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">{{ __('app.create_new_product') }}</a>

    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>{{ __('app.id') }}</th>
                    <th>{{ __('app.image') }}</th>
                    <th>{{ __('app.name') }}</th>
                    <th>{{ __('app.description') }}</th>
                    <th>{{ __('app.category') }}</th>
                    <th>{{ __('app.price') }}</th>
                    <th>{{ __('app.stock_quantity') }}</th>
                    <th>{{ __('app.reorder_level') }}</th>
                    <th>{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100px;">
    @else
        {{ __('app.no_image') }}
    @endif
</td>

                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->category ? $product->category->name : __('app.na') }}</td>
                        <td>
                            @if(app()->getLocale() == 'km')
                                {{ number_format($product->price, 2) }}$
                            @else
                                ${{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->reorder_level }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">{{ __('app.edit') }}</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('app.delete_confirmation') }}');">{{ __('app.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

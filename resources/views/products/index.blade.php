@extends('layouts.app')

@section('content')
<div class="container">
<<<<<<< HEAD
    <h1>Product List</h1>
=======
    <h1>{{ __('app.product_list') }}</h1>
>>>>>>> 0da82be (Modify pages to support khmer language partially)

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

<<<<<<< HEAD
    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">Create New Product</a>
=======
    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">{{ __('app.create_new_product') }}</a>
>>>>>>> 0da82be (Modify pages to support khmer language partially)

    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
<<<<<<< HEAD
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th> <!-- Add category column -->
                    <th>Price</th>
                    <th>Stock Quantity</th> <!-- Add stock quantity column -->
                    <th>Reorder Level</th> <!-- Add stock quantity column -->
                    <th>Actions</th>
=======
                    <th>{{ __('app.id') }}</th>
                    <th>{{ __('app.image') }}</th>
                    <th>{{ __('app.name') }}</th>
                    <th>{{ __('app.description') }}</th>
                    <th>{{ __('app.category') }}</th>
                    <th>{{ __('app.price') }}</th>
                    <th>{{ __('app.stock_quantity') }}</th>
                    <th>{{ __('app.reorder_level') }}</th>
                    <th>{{ __('app.actions') }}</th>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
<<<<<<< HEAD
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->category ? $product->category->name : 'N/A' }}</td> <!-- Show category name -->
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock_quantity }}</td> <!-- Show stock quantity -->
                        <td>{{ $product->reorder_level }}</td> <!-- Show reorder_level -->
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
=======
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
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> 0da82be (Modify pages to support khmer language partially)

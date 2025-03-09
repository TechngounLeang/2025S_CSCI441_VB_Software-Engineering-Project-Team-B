@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product List</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">Create New Product</a>

    <div class="table-responsive">
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th> <!-- Add category column -->
                    <th>Price</th>
                    <th>Stock Quantity</th> <!-- Add stock quantity column -->
                    <th>Reorder Level</th> <!-- Add stock quantity column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
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
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

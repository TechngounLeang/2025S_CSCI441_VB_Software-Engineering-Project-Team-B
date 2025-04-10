@extends('layouts.app')

@section('content')
    <div class="container">
<<<<<<< HEAD
        <h1>Edit Product</h1>
=======
        <h1>{{ __('app.edit_product') }}</h1>
>>>>>>> 0da82be (Modify pages to support khmer language partially)

        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
<<<<<<< HEAD
                <label for="name" class="form-label">Product Name</label>
=======
                <label for="name" class="form-label">{{ __('app.product_name') }}</label>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="description" class="form-label">Description</label>
=======
                <label for="description" class="form-label">{{ __('app.description') }}</label>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                <textarea class="form-control" id="description" name="description" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="price" class="form-label">Price</label>
=======
                <label for="price" class="form-label">{{ __('app.price') }}</label>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" required>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="category_id" class="form-label">Category</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">Select a Category</option>
=======
                <label for="category_id" class="form-label">{{ __('app.category') }}</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">{{ __('app.select_category') }}</option>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
=======
                <label for="stock_quantity" class="form-label">{{ __('app.stock_quantity') }}</label>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="reorder_level" class="form-label">Reorder Level</label>
                <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}">
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
=======
                <label for="reorder_level" class="form-label">{{ __('app.reorder_level') }}</label>
                <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}">
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.update_product') }}</button>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
        </form>
    </div>
@endsection

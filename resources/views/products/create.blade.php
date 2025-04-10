@extends('layouts.app')

@section('content')
    <div class="container">
<<<<<<< HEAD
        <h1>Create a New Product</h1>
=======
        <h1>{{ __('app.create_new_product') }}</h1>
>>>>>>> 0da82be (Modify pages to support khmer language partially)

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="mb-3">
<<<<<<< HEAD
                <label for="name" class="form-label">Product Name</label>
=======
                <label for="name" class="form-label">{{ __('app.product_name') }}</label>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">Select a Category</option>
=======
                <label for="description" class="form-label">{{ __('app.description') }}</label>
                <textarea class="form-control" id="description" name="description"
                    required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('app.price') }}</label>
                <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" step="0.01"
                    required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">{{ __('app.category') }}</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">{{ __('app.select_category') }}</option>
>>>>>>> 0da82be (Modify pages to support khmer language partially)
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
            </div>

            <div class="mb-3">
<<<<<<< HEAD
                <label for="reorder_level" class="form-label">Reorder Level</label>
                <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 5) }}">
            </div>

            <button type="submit" class="btn btn-primary">Create Product</button>
        </form>
    </div>
@endsection
=======
                <label for="reorder_level" class="form-label">{{ __('app.reorder_level') }}</label>
                <input type="number" class="form-control" id="reorder_level" name="reorder_level"
                    value="{{ old('reorder_level', 5) }}">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">{{ __('app.product_image') }}</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>


            <button type="submit" class="btn btn-primary">{{ __('app.create_product') }}</button>
        </form>
    </div>
@endsection
>>>>>>> 0da82be (Modify pages to support khmer language partially)

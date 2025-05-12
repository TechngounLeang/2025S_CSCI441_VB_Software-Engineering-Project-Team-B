<!-- Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
Tested by: Tech Ngoun Leang-->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('app.edit_product') }}</h1>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('app.product_name') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('app.description') }}</label>
                <textarea class="form-control" id="description" name="description"
                    required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('app.price') }}</label>
                <input type="number" class="form-control" id="price" name="price"
                    value="{{ old('price', $product->price) }}" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">{{ __('app.category') }}</label>
                <select class="form-control" id="category_id" name="category_id">
                    <option value="">{{ __('app.select_category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="stock_quantity" class="form-label">{{ __('app.stock_quantity') }}</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                    value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            </div>

            <div class="mb-3">
                <label for="reorder_level" class="form-label">{{ __('app.reorder_level') }}</label>
                <input type="number" class="form-control" id="reorder_level" name="reorder_level"
                    value="{{ old('reorder_level', $product->reorder_level) }}">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">{{ __('app.product_image') }}</label>
                @if($product->photo_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->photo_path) }}" alt="{{ $product->name }}"
                            style="max-width: 100px;">
                    </div>
                @endif
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.update_product') }}</button>
        </form>
    </div>
@endsection
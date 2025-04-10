@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('app.edit_user') }}</h2>
    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>{{ __('app.name') }}</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('app.email') }}</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('app.update') }}</button>
    </form>
</div>
@endsection

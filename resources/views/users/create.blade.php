@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('app.create_user') }}</h2>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="mb-3">
            <label>{{ __('app.name') }}</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('app.email') }}</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('app.password') }}</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('app.confirm_password') }}</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>{{ __('app.role') }}</label>
            <select name="role" class="form-control" required>
                <option value="cashier">{{ __('app.cashier') }}</option>
                <option value="manager">{{ __('app.manager') }}</option>
                <option value="admin">{{ __('app.admin') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">{{ __('app.create') }}</button>
    </form>
</div>
@endsection
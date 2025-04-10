@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ __('app.user_management') }}</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">{{ __('app.add_user') }}</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('app.id') }}</th>
                <th>{{ __('app.name') }}</th>
                <th>{{ __('app.email') }}</th>
                <th>{{ __('app.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">{{ __('app.edit') }}</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('app.confirm_delete') }}')">
                                {{ __('app.delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

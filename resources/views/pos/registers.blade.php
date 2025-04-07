@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Register Management</h1>
    
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Create New Register</div>
                <div class="card-body">
                    <form action="{{ route('pos.create-register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="register_name" class="form-label">Register Name</label>
                            <input type="text" class="form-control" id="register_name" name="register_name" required>
                        </div>
                        <div class="mb-3">
    <label for="location" class="form-label">Location</label>
    <input type="text" class="form-control" id="location" name="location">
</div>
                        <button type="submit" class="btn btn-primary">Create Register</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Open Register</div>
                <div class="card-body">
                    <form action="{{ route('pos.open-register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="register_id" class="form-label">Select Register</label>
                            <select class="form-control" id="register_id" name="register_id" required>
    <option value="">Select Register</option>
    @foreach($registers->where('status', 'closed') as $register)
        <option value="{{ $register->id }}">
            Register #{{ $register->id }} ({{ $register->name }})
        </option>
    @endforeach
</select>
                        </div>
                        <div class="mb-3">
                            <label for="opening_balance" class="form-label">Opening Cash Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="opening_balance" name="opening_balance" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Open Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Close Register</div>
                <div class="card-body">
                    <form action="{{ route('pos.close-register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="close_register_id" class="form-label">Select Register</label>
                            <select class="form-control" id="close_register_id" name="register_id" required>
                                <option value="">Select Register</option>
                                @foreach($registers->where('status', 'open') as $register)
                                <option value="{{ $register->id }}" data-balance="{{ $register->cash_balance }}">
                                    Register #{{ $register->id }} ({{ $register->name }}) - Balance: ${{ number_format($register->cash_balance, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="counted_balance" class="form-label">Counted Cash Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="counted_balance" name="counted_balance" required>
                            </div>
                            <div class="form-text" id="balance_difference"></div>
                        </div>
                        <button type="submit" class="btn btn-danger">Close Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">All Registers</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Cash Balance</th>
                        <th>Transactions</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registers as $register)
                    <tr>
                        <td>{{ $register->id }}</td>
                        <td>{{ $register->name }}</td>
                        <td>{{ $register->location ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $register->status === 'open' ? 'success' : 'secondary' }}">
                                {{ ucfirst($register->status) }}
                            </span>
                        </td>
                        <td>${{ number_format($register->cash_balance, 2) }}</td>
                        <td>{{ $register->orders_count }}</td>
                        <td>
                            @if($register->status === 'open')
                                Opened: {{ $register->opened_at->diffForHumans() }}
                            @else
                                Closed: {{ $register->closed_at ? $register->closed_at->diffForHumans() : 'N/A' }}
                            @endif
                        </td>
                        <td>
                            @if($register->status === 'closed' && $register->orders_count === 0)
                            <form action="{{ route('pos.delete-register', $register->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this register?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('pos.index') }}" class="btn btn-primary">Back to POS</a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const closeRegisterSelect = document.getElementById('close_register_id');
    const countedBalanceInput = document.getElementById('counted_balance');
    const balanceDifferenceDiv = document.getElementById('balance_difference');
    
    function updateBalanceDifference() {
        const selectedOption = closeRegisterSelect.options[closeRegisterSelect.selectedIndex];
        if (selectedOption.value) {
            const systemBalance = parseFloat(selectedOption.dataset.balance);
            const countedBalance = parseFloat(countedBalanceInput.value) || 0;
            const difference = countedBalance - systemBalance;
            
            let statusClass = 'text-success';
            let statusText = 'Balanced';
            
            if (difference > 0) {
                statusClass = 'text-success';
                statusText = 'Over by';
            } else if (difference < 0) {
                statusClass = 'text-danger';
                statusText = 'Short by';
            }
            
            balanceDifferenceDiv.className = statusClass;
            balanceDifferenceDiv.textContent = difference === 0 ? 
                'Drawer is balanced' : 
                `Drawer is ${statusText} $${Math.abs(difference).toFixed(2)}`;
        } else {
            balanceDifferenceDiv.textContent = '';
        }
    }
    
    closeRegisterSelect.addEventListener('change', updateBalanceDifference);
    countedBalanceInput.addEventListener('input', updateBalanceDifference);
});
</script>
@endpush
@endsection
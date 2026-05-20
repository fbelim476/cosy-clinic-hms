@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="page-header mb-4"><h2 class="page-title">User Management</h2></div>
<div class="glass-card card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-primary">{{ $u->roles->first()?->name }}</span></td>
                        <td>{{ $u->department?->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $u->is_active ? 'success' : 'danger' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
</div>
@endsection

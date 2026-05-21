@extends('layouts.app')
@section('title', 'User Management')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li>Users</li>
@endsection
@section('content')
<x-ui.page-header title="Users & Roles" subtitle="Manage staff access across the hospital" icon="ti-users" />

<x-ui.card title="Staff Directory" :padding="false">
    <x-ui.table>
        <x-slot:head>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th></tr>
        </x-slot:head>
        @foreach($users as $u)
            <tr>
                <td class="fw-semibold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge bg-primary-lt text-capitalize">{{ str_replace('-', ' ', $u->roles->first()?->name) }}</span></td>
                <td>{{ $u->department?->name ?? '—' }}</td>
                <td><span class="badge bg-{{ $u->is_active ? 'success' : 'danger' }}-lt">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
            </tr>
        @endforeach
    </x-ui.table>
    <x-slot:footer>{{ $users->links() }}</x-slot:footer>
</x-ui.card>
@endsection

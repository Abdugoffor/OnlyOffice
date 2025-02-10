@extends('layouts.admin')
@section('title', 'Users')
@section('content')
    <section class="content">
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Eamil</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
            @foreach ($models as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->created_at->diffForHumans() }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection

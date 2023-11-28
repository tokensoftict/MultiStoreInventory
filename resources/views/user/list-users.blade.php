@extends('layouts.app')


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        <span class="pull-right">
                            <a class="btn btn-info btn-sm" href="{{ route('user.create') }}"><i class="fa fa-user-md"></i> Add User</a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="user-list">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Store(s)</th>
                                    <th>Username</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $num = 1;
                                @endphp
                                @foreach($users as $user)
                                        <tr>
                                            <td>{{ $num }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ implode(",", $user->getStores()) }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->group->name }}</td>
                                            <td>{!! $user->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                            <td>
                                                @if (userCanView('user.edit'))
                                                    <a href="{{ route('user.edit',$user->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                                @endif
                                                    @if (userCanView('user.toggle') && auth()->id() !=$user->id)
                                                        <a href="{{ route('user.toggle',$user->id) }}" class="btn btn-success btn-sm">Toggle</a>
                                                    @endif
                                            </td>
                                        </tr>
                                    @php
                                        $num++;
                                    @endphp
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection


@extends('layouts.app')

@section('content')
        <div class="ui-container">
                <div class="row">
                        <div class="col-sm-12">
                                <section class="panel">
                                        <header class="panel-heading panel-border">
                                                List Groups
                                                @if(userCanView('user.group.create'))
                                                        <span class="tools pull-right">
                                                <a  href="{{ route('user.group.create') }}" class="btn btn-primary"> Add User Group</a>
                                        </span>
                                                @endif
                                        </header>
                                        <div class="panel-body">
                                                <div class="table-responsive">
                                                        <table class="table convert-data-table table-striped">
                                                                <thead>
                                                                <tr>
                                                                        <th>Name</th>
                                                                        <th>Status</th>
                                                                        <th>Details</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($groups as $group)
                                                                        <tr>
                                                                                <td>{{ $group->name }}</td>
                                                                                <td>{!! $group->status == '1' ? label("Active", "success") : label("Inactive", "danger") !!}</td>
                                                                                <td>
                                                                                        @if(userCanView('user.group.show'))
                                                                                                <a href="{{ route('user.group.show', [$group->id]) }}"><button class='btn btn-info btn-xs'><i class='fa fa-eye'></i> Details</button></a>
                                                                                        @endif
                                                                                        @if(userCanView('user.group.permission'))
                                                                                                <a href="{{ route('user.group.permission', [$group->id]) }}"><button class='btn btn-warning btn-xs'><i class='fa fa-list'></i> Permissions</button></a>
                                                                                        @endif
                                                                                </td>
                                                                        </tr>
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



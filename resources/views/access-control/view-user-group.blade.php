@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-actions">
                            <tbody>
                            <tr>
                                <td width="30%"><strong>User Group Name</strong></td>
                                <td>{{ $group->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    @if($group->status == 0)
                                        {!! label("Inactive", "danger") !!}
                                    @else
                                        {!! label("Active", "success") !!}
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <footer class="panel-footer">

                        @if($group->status == 1)
                            <a id="disable" class="btn btn-danger" href="{{ route('user.group.toggle',[$group->id]) }}">
                                <i class="fa fa-times"></i> Deactivate User Group</a>
                        @else
                            <a id="enable" class="btn btn-success" href="{{ route('user.group.toggle',[$group->id]) }}">
                                <i class="fa fa-check-square-o"></i> Activate User Group</a>
                        @endif


                        <a href='{{ route('user.group.index') }}'>
                            <button class="btn btn-primary"><i class="fa fa-list"></i>List User Groups</button>
                        </a>
                        &nbsp;
                        <a href='{{ route('user.group.edit',[$group->id]) }}'>
                            <button class="btn btn-info" ><i class="fa fa-pencil-square-o"></i>Edit User Group</button>
                        </a>

                    </footer>
                </section>
            </div>
        </div>
    </div>
@endsection



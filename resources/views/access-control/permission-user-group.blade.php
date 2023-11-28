@extends('layouts.app')

@section('css')

@endsection

@section('js')

@endsection

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
                <form id="validate" action="{{ route('user.group.permission',[$group->id]) }}" method="post"
                      class='form-horizontal'>
                    <section class="panel">
                        <header class="panel-heading panel-border">
                            {{ $title }}
                        </header>
                        <div class="panel-body">
                            @if(session('success'))
                                {!! alert_success(session('success')) !!}
                            @elseif(session('error'))
                                {!! alert_error(session('error')) !!}
                            @endif

                            {{ csrf_field() }}
                            <input type="hidden" name="group_id" value="{{ $group->id }}">

                            @foreach($modules->chunk(2) as $chunkModule)
                                <div class="row">
                                    @foreach($chunkModule as $module)
                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><span
                                                                class="{{ $module->icon }}"></span> {{ $module->label }}</h3>
                                                </div>
                                                <div class="panel-body" style="height: 150px; overflow: auto">
                                                    <div id="mCSB_4" class=""
                                                         tabindex="0">
                                                        <div id="" class=""
                                                             style="position: relative; top: 0px; left: 0px;" dir="ltr">
                                                            <div class="row">
                                                                @foreach($module->tasks->chunk(2) as $chunkTask)

                                                                    @foreach($chunkTask as $task)
                                                                        <div class="col-md-6">
                                                                            <div class="checkbox">
                                                                                <label class="i-checks">
                                                                                    <input  {{ count($task->permissions)  ? "checked" : ''  }} name="privileges[{{ $task->id }}]" value="" type="checkbox">
                                                                                    <i></i>
                                                                                    {{ $task->name }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                        </div>
                        <footer class="panel-footer">
                            <a href="{{ route('user.group.index') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> View all User Groups
                            </a>
                            <button type="submit" class="btn btn-info btn-sm pull-right">
                                <span class="fa fa-save"></span> Assign User Group Privileges
                            </button>
                        </footer>
                    </section>
                </form>
            </div>
        </div>
    </div>
@endsection

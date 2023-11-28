@extends('layouts.app')


@section('content')
    <div class="ui-content-body">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                       System Reports
                    </header>
                    <div class="panel-body">
                        <br/>
                        <div class="row">
                        @foreach($modules as $module)
                            <div class="col-sm-4">
                                <div class="panel">
                                    <div class="panel-header" style="padding-left: 20px; font-weight: bolder">
                                        {{ $module->label }}
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-group">
                                            @foreach($module->tasks as $task)
                                                @if(userCanView($task->route))
                                                    <li class="list-group-item">
                                                        <a href="{{ route($task->route) }}" target="_blank" class="text-body pb-3 pt-2 d-block">{{ $task->description }}
                                                            <i class="fa fa-arrow-right pull-right"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

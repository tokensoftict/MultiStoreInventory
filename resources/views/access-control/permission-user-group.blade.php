@extends('layouts.app')

@section('css')

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // When 'Select All' is toggled, toggle all tasks within its panel
            $('.check-all-module').on('change', function() {
                var isChecked = $(this).is(':checked');
                var $panel = $(this).closest('.panel-default');
                $panel.find('.task-checkbox').prop('checked', isChecked).trigger('change');
            });

            // Update the 'Select All' state based on the individual task checkboxes
            $('.panel-default').each(function() {
                var $panel = $(this);
                var $checkAll = $panel.find('.check-all-module');
                var $tasks = $panel.find('.task-checkbox');

                if ($tasks.length > 0) {
                    var updateCheckAll = function() {
                        var allChecked = $tasks.length === $tasks.filter(':checked').length;
                        $checkAll.prop('checked', allChecked);
                    };
                    
                    $tasks.on('change', updateCheckAll);
                    updateCheckAll(); // Set initial state
                }
            });
        });
    </script>
@endpush

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
                                                    <h3 class="panel-title" style="display: inline-block;">
                                                        <span class="{{ $module->icon }}"></span> {{ $module->label }}
                                                    </h3>
                                                    <label class="i-checks pull-right" style="margin: 0;">
                                                        <input type="checkbox" class="check-all-module">
                                                        <i></i> Select All
                                                    </label>
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
                                                                                    <input  {{ count($task->permissions)  ? "checked" : ''  }} name="privileges[{{ $task->id }}]" value="" type="checkbox" class="task-checkbox">
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

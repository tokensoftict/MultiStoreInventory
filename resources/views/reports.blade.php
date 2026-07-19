@extends('layouts.app')

@push('css')
    <style>
        .report-grid {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .report-card-wrapper {
            display: flex;
            flex-direction: column;
            margin-bottom: 25px;
        }
        .report-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f5;
            border-top: 3px solid #3498db;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.2s ease-in-out;
        }
        .report-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }
        .report-card .card-heading {
            background: #fafbfc;
            font-weight: 600;
            font-size: 14px;
            color: #2b303a;
            border-bottom: 1px solid #f2f5f8;
            padding: 12px 15px;
            border-top-left-radius: 7px;
            border-top-right-radius: 7px;
            display: flex;
            align-items: center;
        }
        .report-card .card-heading i {
            margin-right: 8px;
            font-size: 14px;
        }
        .report-card .card-body {
            padding: 10px 15px;
            flex: 1;
        }
        .report-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .report-item {
            border-bottom: 1px solid #f6f8fa;
            padding: 9px 0;
        }
        .report-item:last-child {
            border-bottom: none;
        }
        .report-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #5b6777;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .report-link:hover {
            color: #3498db;
            text-decoration: none;
        }
        .report-link i {
            font-size: 11px;
            opacity: 0.4;
            transition: all 0.15s ease;
        }
        .report-link:hover i {
            transform: translateX(4px);
            opacity: 1;
            color: #3498db;
        }
        /* Style variation for headers */
        .color-0 { border-top-color: #3498db; }
        .color-1 { border-top-color: #2ecc71; }
        .color-2 { border-top-color: #9b59b6; }
        .color-3 { border-top-color: #e67e22; }
        .color-4 { border-top-color: #1abc9c; }
        .color-5 { border-top-color: #e74c3c; }
        .color-6 { border-top-color: #34495e; }
    </style>
@endpush

@section('content')
    <div class="ui-content-body">
        <div class="row">
            <div class="col-md-12">
                <section class="panel" style="border: none; box-shadow: none; background: transparent;">
                    <div class="panel-body" style="padding: 0;">
                        <h3 style="margin-top: 0; margin-bottom: 20px; font-weight: 600; color: #2b303a;">
                            <i class="fa fa-bar-chart text-info" style="margin-right: 5px;"></i> System Reports Directory
                        </h3>
                        <div class="row report-grid">
                            @php $colorIndex = 0; @endphp
                            @foreach($modules as $module)
                                @php
                                    $hasVisibleTasks = false;
                                    foreach($module->tasks as $task) {
                                        if(userCanView($task->route)) {
                                            $hasVisibleTasks = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if($hasVisibleTasks)
                                    <div class="col-md-4 col-sm-6 report-card-wrapper">
                                        <div class="report-card color-{{ $colorIndex % 7 }}">
                                            <div class="card-heading">
                                                <i class="{{ $module->icon ?: 'fa fa-folder-open-o' }} text-muted"></i>
                                                {{ $module->label }}
                                            </div>
                                            <div class="card-body" style="display: flex; flex-direction: column;">
                                                <div class="report-search-container" style="margin-bottom: 12px; position: relative;">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-addon" style="background: #fafbfc; border-color: #eef2f5; padding: 4px 8px; font-size: 11px;"><i class="fa fa-search text-muted"></i></span>
                                                        <input type="text" class="form-control report-search-input" placeholder="Search reports in this module..." style="font-size: 11px; height: 26px; border-color: #eef2f5; box-shadow: none;">
                                                    </div>
                                                </div>
                                                <ul class="report-list">
                                                    @foreach($module->tasks as $task)
                                                        @if(userCanView($task->route))
                                                            <li class="report-item">
                                                                <a href="{{ route($task->route) }}" target="_blank" class="report-link">
                                                                    <span>{{ $task->description }}</span>
                                                                    <i class="fa fa-chevron-right"></i>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @php $colorIndex++; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.report-search-input').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                var $card = $(this).closest('.report-card');
                var visibleCount = 0;

                $card.find('.report-item').each(function() {
                    var matches = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(matches);
                    if (matches) visibleCount++;
                });

                var $noResults = $card.find('.no-reports-found');
                if (visibleCount === 0 && value !== '') {
                    if ($noResults.length === 0) {
                        $card.find('.report-list').after('<div class="no-reports-found text-muted text-center" style="font-size: 11px; padding: 15px 0;"><i class="fa fa-info-circle"></i> No matching reports</div>');
                    }
                } else {
                    $noResults.remove();
                }
            });
        });
    </script>
@endpush

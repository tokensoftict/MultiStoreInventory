@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-8">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Unit Name</th>
                                    <th>Description</th>
                                    <th>Unit Type</th>
                                    <th>Status</th>
                                    <th style="width: 170px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $unit)
                                    <tr>
                                        <td><strong>Level {{ $unit->level }}</strong></td>
                                        <td><strong style="font-size: 14px;">{{ $unit->name }}</strong></td>
                                        <td>{{ $unit->description ?: '-' }}</td>
                                        <td>
                                            @if($unit->is_base || $unit->level == 3)
                                                <span class="label label-primary"><i class="fa fa-star"></i> Base Unit</span>
                                            @else
                                                <span class="label label-default">Packaging Unit</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! $unit->status == 1 ? label("Active", "success") : label("Disabled", "danger") !!}
                                        </td>
                                        <td>
                                            @if (userCanView('quantity_unit.edit'))
                                                <a href="{{ route('quantity_unit.edit', $unit->id) }}" class="btn btn-primary btn-xs" title="Change name">
                                                    <i class="fa fa-pencil"></i> Edit Name
                                                </a>
                                            @endif

                                            @if($unit->is_base || $unit->level == 3)
                                                <button class="btn btn-default btn-xs disabled" title="Base unit cannot be disabled" style="opacity: 0.6; cursor: not-allowed;">
                                                    <i class="fa fa-lock"></i> Locked
                                                </button>
                                            @else
                                                @if (userCanView('quantity_unit.toggle'))
                                                    @if($unit->status == 1)
                                                        <a href="{{ route('quantity_unit.toggle', $unit->id) }}" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to disable this packaging level?');">
                                                            <i class="fa fa-ban"></i> Disable
                                                        </a>
                                                    @else
                                                        <a href="{{ route('quantity_unit.toggle', $unit->id) }}" class="btn btn-success btn-xs">
                                                            <i class="fa fa-check"></i> Enable
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-md-4">
                <section class="panel">
                    <header class="panel-heading">
                        Multi-Level Quantity Guide
                    </header>
                    <div class="panel-body">
                        <p><strong>How Dynamic Quantity Levels Work:</strong></p>
                        <ul style="padding-left: 20px;">
                            <li style="margin-bottom: 8px;">
                                <strong>Level 1 (Top Packaging):</strong> Largest bulk package (e.g. <em>Cartons</em>, <em>Boxes</em>, <em>Crates</em>).
                            </li>
                            <li style="margin-bottom: 8px;">
                                <strong>Level 2 (Middle Packaging):</strong> Optional intermediate package (e.g. <em>Packets</em>, <em>Rolls</em>, <em>Bundles</em>). 
                                Can be disabled if your products only have 2 levels (e.g. Cartons &rarr; Pieces).
                            </li>
                            <li style="margin-bottom: 8px;">
                                <strong>Level 3 (Base Unit):</strong> Foundational counting unit (e.g. <em>Pieces</em>, <em>Units</em>, <em>Bottles</em>). 
                                <br><span class="text-danger"><strong>Note:</strong> The base unit cannot be disabled.</span>
                            </li>
                        </ul>
                        <hr/>
                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fa fa-info-circle"></i> <strong>Dynamic Labels:</strong> Changing a unit name here updates product forms, stock lists, invoices, and purchase order labels across the system immediately.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }} (Level {{ $unit->level }})
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <form id="validate" action="{{ route('quantity_unit.update', $unit->id) }}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group">
                                <label>Level</label>
                                <input type="text" readonly class="form-control" value="Level {{ $unit->level }} {{ $unit->is_base ? '(Base Unit - Cannot be disabled)' : '(Packaging Unit)' }}"/>
                            </div>

                            <div class="form-group">
                                <label>Unit Name <span style="color:red;">*</span></label>
                                <input type="text" value="{{ old('name', $unit->name) }}" required class="form-control" name="name" placeholder="e.g. Cartons, Boxes, Packets, Pieces"/>
                                <small class="text-muted">This label is shown in product setup, stock tables, invoices, and purchase orders.</small>
                                @if ($errors->has('name'))
                                    <label for="name-error" class="error" style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" value="{{ old('description', $unit->description) }}" class="form-control" name="description" placeholder="Optional description or examples"/>
                                @if ($errors->has('description'))
                                    <label for="description-error" class="error" style="display: inline-block;">{{ $errors->first('description') }}</label>
                                @endif
                            </div>

                            <div class="pull-right">
                                <a href="{{ route('quantity_unit.index') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

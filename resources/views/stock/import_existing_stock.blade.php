@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-5">
                @if(session('success'))
                    {!! alert_success(session('success')) !!}
                @elseif(session('error'))
                    {!! alert_error(session('error')) !!}
                @endif
                <section class="panel">
                    <header class="panel-heading">
                        Import And Update Existing Stock
                    </header>
                    <div class="panel-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Select Excel File</label>
                                <input type="file" class="form-control" name="excel_file"/>
                            </div>
                            <button type="submit" class="btn btn-default">Upload</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

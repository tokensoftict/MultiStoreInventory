@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
                <form id="validate" action="{{ route('user.group.store') }}" method="post" class='form-horizontal'>
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $title }}
                        </header>
                        <div class="panel-body">
                            <div class="col-md-12">

                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">User Group Name<span class="star">*</span>:</label>

                                    <div class="col-md-4 col-xs-12">
                                        <div class="input-group">
                                            <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                            <input type="text" name="name" value=""
                                                   class="validate[required] form-control" id="name">
                                        </div>
                                        @if ($errors->has('name'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <footer class="panel-footer">
                            <a href='{{ route('user.group.index') }}'>
                                <button class="btn btn-primary" data-toggle="tooltip" data-placement="top"><i
                                            class="fa fa-list"></i>View all User Groups
                                </button>
                            </a>
                            <button type="submit" class="btn btn-info pull-right"><span class="fa fa-edit"></span> Add
                                User Group
                            </button>
                        </footer>
                    </section>
                </form>
            </div>
        </div>
    </div>
@endsection

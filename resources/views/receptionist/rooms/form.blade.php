@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-7 col-lg-offset-3">
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
                        @if(!isset($room->id))
                        <form  method="POST" action="{{route('room.store')}}">
                         @else
                             <form  method="POST" action="{{route('room.update',$room->id)}}">
                                {{ method_field('PUT') }}
                        @endif
                            {{ csrf_field() }}
                            <div class="form-group">
                            <label for="type_id" class="form-label">Room Type</label>
                            <select id="type_id" name="room_type_id" class="form-control select2">
                                @foreach ($types as $type)
                                    <option {{ $room->type_id == $type->id ? "selected" : "" }} value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                            @error('type_id')
                            <div class="text-danger mt-1">
                                {{ $message  }}
                            </div>
                            @enderror
                            </div>
                            <div class="form-group">
                                <label for="number" class="form-label">Room Name</label>
                                <input type="text" class="form-control" id="number" name="name" value="{{old('name',$room->name)}}" placeholder="Room Name ex : Room 101">
                            </div>
                            <div class="form-group" >
                                <label for="capacity" class="form-label">Room Capacity</label>
                                <input text="text" class="form-control" id="capacity" name="capacity" value="{{old('capacity', $room->capacity)}}" placeholder="Room Capacity">
                            </div>
                            <div class="form-group" >
                                <label for="price" class="form-label">Room Rate (Price)</label>
                                <input type="text" class="form-control" id="price" name="price" value="{{old('price', $room->price)}}" placeholder="Room Rate">
                            </div>
                            <div class="form-group" >
                                <label for="view" class="form-label">Description</label>
                                <textarea class="form-control" id="view" name="view" rows="3" placeholder="Description">{{old('view', $room->view)}}</textarea>
                            </div>
                                <button room="submit" class="btn btn-lg btn-primary">Save</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

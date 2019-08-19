@extends('layouts.guest')

@section('content')
<div class="content_block">
    <h1>
        <b>Web Research: Image to Google Calender.</b>
    </h1>
</div>
<div class="content_block scanned_image-wrapper">
    <h2>
        Scanned Image
    </h2>
    <div class="scanned_image">
        <img src="{{asset($Path)}}" alt="Image that is being scanned">
    </div>
    <div class="image_upload">
        <h3>Upload a new calendar page</h3>
        <form action="{{ action('PagesController@storeImage') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="content_block">

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    <div class="u-row">
        <div class="u-col-xs-12 u-col-md-4">
            <h2>Scanned image output</h2>
            <h3>{{$Date}}</h3>
            @foreach($activities as $activity)
                <p>{{$activity}}</p>
            @endforeach
            <a class="btn btn-primary" href="{{action('PagesController@createEvent')}}" role="button">Yes, create these events!</a>
        </div>
        <div class="u-col-xs-12 u-col-md-8 calender">
            <iframe src="https://calendar.google.com/calendar/embed?src=0qai6lrgia2vb21cp4uf4uasik%40group.calendar.google.com&ctz=Europe%2FBrussels" style="border: 0" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
</div>
@endsection

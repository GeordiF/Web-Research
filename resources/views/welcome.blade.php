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
</div>
<div class="content_block">
    @if (Session::has('success'))
        <div class="alert alert-success">
            <ul class="list-inline">
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @else
        <a class="btn btn-primary" href="{{action('PagesController@createEvent')}}" role="button">Yes, create these events!</a>
    @endif
    <h2>Scanned image output</h2>
    <h3>{{$Date}}</h3>
    @foreach($activities as $activity)
        <p>{{$activity}}</p>
    @endforeach

    <iframe src="https://calendar.google.com/calendar/embed?src=0qai6lrgia2vb21cp4uf4uasik%40group.calendar.google.com&ctz=Europe%2FBrussels" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
</div>
@endsection

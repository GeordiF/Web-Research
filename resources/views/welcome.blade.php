@extends('layouts.guest')

@section('content')
<div class="content_block">
    <div class="u-row header_wrapper">
        <div class="u-col-xs-12 u-col-md-8">
            <h1>
                <b>Web Research: Image to Google Calender.</b>
            </h1>
        </div>
        <div class="u-col-xs-12 u-col-md-4">
            <a class="btn btn-primary" href="/connect" role="button">Link je Google Account</a>
        </div>
    </div>
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
    <h2>Scanned image output</h2>
    <h3>{{$Date}}</h3>
    @foreach($activities as $activity)
        <p>{{$activity}}</p>
    @endforeach
</div>
@endsection

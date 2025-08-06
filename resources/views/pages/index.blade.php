@extends('layouts.master')

@section('title', 'Home')

@section('page-title', 'Home')

@section('content')

    @include('partials.banner')
    @include('partials.recentsection')
    {{-- @include('partials.commingsoon') --}}
    @include('partials.pasteventssection')
    @include('partials.posters.bannersection')
    @include('partials.posterssection')
    {{-- @include('partials.movie-section') --}}
    {{-- @include('partials.event-section') --}}
    {{-- @include('partials.sports-section') --}}
    @include('partials.sponsors-section')



@endsection

@extends('layouts.master')

@section('title', 'Home')

@section('page-title', 'Home')

@section('content')

    @include('partials.banner')
    @include('partials.categorysection')
    @include('partials.commingsoon')
    @include('partials.pasteventssection')
    {{-- @include('partials.ticket-search') --}}
    {{-- @include('partials.movie-section') --}}
    {{-- @include('partials.event-section') --}}
    {{-- @include('partials.sports-section') --}}
    @include('partials.sponsors-section')



@endsection

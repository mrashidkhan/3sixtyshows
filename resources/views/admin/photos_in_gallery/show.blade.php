@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Photo Details</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h4>Photo in Gallery: {{ $gallery->title }}</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $photo->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $photo->description ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gallery</th>
                                        <td>
                                            <a href="{{ route('photogallery.show', $gallery->id) }}">
                                                {{ $gallery->title }}
                                            </a>
                                        </td>
                                    </tr

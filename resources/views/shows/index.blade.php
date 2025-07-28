{{-- resources/views/shows/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Shows & Events')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Shows & Events</h1>

        <!-- Filters -->
        <div class="flex space-x-4">
            <select name="category" class="border rounded-lg px-4 py-2">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="status" class="border rounded-lg px-4 py-2">
                <option value="">All Shows</option>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
            </select>
        </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($shows as $show)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                @if($show->featured_image)
                    <img src="{{ asset('storage/' . $show->featured_image) }}"
                         alt="{{ $show->title }}"
                         class="w-full h-48 object-cover">
                @endif

                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="text-xl font-semibold">{{ $show->title }}</h2>
                        @if($show->is_featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Featured</span>
                        @endif
                    </div>

                    <p class="text-gray-600 mb-4">{{ Str::limit($show->short_description, 100) }}</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $show->start_date->format('M d, Y \a\t g:i A') }}
                        </div>

                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $show->venue->name }}
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-lg font-bold text-green-600">{{ $show->formatted_price }}</span>
                            @if($show->sold_out)
                                <span class="text-red-500 text-sm ml-2">Sold Out</span>
                            @endif
                        </div>

                        <a href="{{ route('shows.show', $show->slug) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No shows available at the moment.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $shows->links() }}
    </div>
</div>
@endsection

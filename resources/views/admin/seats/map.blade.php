<!-- admin/seats/map.blade.php -->
<!-- Admin - Seat Map Management -->

@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Seat Map for {{ $venue->name }}</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <!-- Seat Map Editor -->
                <div class="seat-map-container" id="seat-map-editor" data-venue-id="{{ $venue->id }}">
                    <!-- Interactive seat map with drag-and-drop functionality -->
                    @foreach($seats as $seat)
                    <div class="seat-item"
                         id="seat-{{ $seat->id }}"
                         data-id="{{ $seat->id }}"
                         data-x="{{ $seat->coordinates_x }}"
                         data-y="{{ $seat->coordinates_y }}"
                         data-category="{{ $seat->category->id ?? 0 }}"
                         style="background-color: {{ $seat->category->color_code ?? '#CCCCCC' }};
                                left: {{ $seat->coordinates_x ?? 0 }}px;
                                top: {{ $seat->coordinates_y ?? 0 }}px;">
                        {{ $seat->fullSeatIdentifier }}
                    </div>
                    @endforeach
                </div>

                <!-- Controls -->
                <div class="seat-map-controls mt-3">
                    <button id="save-positions" class="btn btn-primary">Save Positions</button>
                    <div class="category-legend mt-2">
                        @foreach($categories as $category)
                        <div class="category-item">
                            <span class="color-box" style="background-color: {{ $category->color_code }}"></span>
                            <span>{{ $category->name }} - ${{ number_format($category->price, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Implement draggable functionality using a library like jQuery UI
    // Save positions using AJAX to the updatePositions endpoint
</script>
@endsection

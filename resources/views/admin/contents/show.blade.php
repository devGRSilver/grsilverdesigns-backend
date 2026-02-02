<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Category Details' }}</h5>
    </div>

    <div class="card-body pt-15">
        {!! $content->description ?? '' !!}
        <div class="text-center">
            {{-- <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary">Edit Category</a> --}}
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
    </div>
</div>

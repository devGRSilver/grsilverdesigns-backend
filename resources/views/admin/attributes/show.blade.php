<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Category Details' }}</h5>
    </div>

    <div class="card-body pt-15">
        <div class="table-responsive mb-15">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{ $category->id }}</td>
                        <td>Parent ID</td>
                        <td>{{ $category->parent->name ?? 'This Is Main Category' }}</td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>{{ $category->name }}</td>
                        <td>Slug</td>
                        <td>{{ $category->slug }}</td>
                    </tr>
                    <tr>
                        <td>Is Primary</td>
                        <td>{{ $category->is_primary ? 'Yes' : 'No' }}</td>
                        <td>Metal</td>
                        <td>{{ $category->metal->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Meta Title</td>
                        <td>{{ $category->meta_title }}</td>
                        <td>Meta Description</td>
                        <td>{{ $category->meta_description }}</td>
                    </tr>
                    <tr>
                        <td>Meta Keywords</td>
                        <td>{{ $category->meta_keywords }}</td>
                        <td>Status</td>
                        <td>{{ $category->status ? 'Active' : 'Inactive' }}</td>
                    </tr>
                    <tr>
                        <td>Image</td>
                        <td>
                            @if ($category->image)
                                {!! image_show($category->image, 50, 50) !!}
                            @endif
                        </td>
                        <td>Banner Image</td>
                        <td>
                            @if ($category->banner_image)
                                {!! image_show($category->banner_image, 50, 50) !!}
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td>{{ $category->created_at }}</td>
                        <td>Updated At</td>
                        <td>{{ $category->updated_at }}</td>
                    </tr>
                    <tr>

                        <td>Total Sub Category</td>
                        <td>
                            {{ $category->sub_categories_count }}
                        </td>
                        <td>Status</td>
                        <td>
                            {!! status_dropdown($category->status, [
                                'id' => $category->id,
                                'url' => route('categories.status', encrypt($category->id)),
                                'method' => 'PUT',
                            ]) !!}

                        </td>



                    </tr>

                </tbody>
            </table>
        </div>
        <div class="text-center">
            {{-- <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary">Edit Category</a> --}}
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
    </div>
</div>

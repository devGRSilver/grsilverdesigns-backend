<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Blog Details' }}</h5>
    </div>

    <div class="card-body pt-15">
        <div class="table-responsive mb-15">
            <table class="table table-bordered">
                <tbody>

                    <tr>
                        <td>Title</td>
                        <td>{{ $blog->title }}</td>
                        <td>Slug</td>
                        <td>{{ $blog->slug }}</td>
                    </tr>

                    <tr>
                        <td>Short Description</td>
                        <td colspan="3">{{ $blog->short_description }}</td>
                    </tr>



                    <tr>
                        <td>Meta Title</td>
                        <td>{{ $blog->meta_title }}</td>
                        <td>Meta Description</td>
                        <td>{{ $blog->meta_description }}</td>
                    </tr>

                    <tr>
                        <td>Meta Keywords</td>
                        <td colspan="3">
                            @if (is_array($blog->meta_keywords))
                                {{ implode(', ', $blog->meta_keywords) }}
                            @else
                                {{ $blog->meta_keywords }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>Featured Image</td>
                        <td>
                            @if ($blog->featured_image)
                                {!! image_show($blog->featured_image, 50, 50) !!}
                            @endif
                        </td>
                        <td>Status</td>
                        <td>
                            {!! status_dropdown($blog->status, [
                                'id' => $blog->id,
                                'url' => route('blogs.status', encrypt($blog->id)),
                                'method' => 'PUT',
                            ]) !!}
                        </td>
                    </tr>

                    <tr>
                        <td>Published At</td>
                        <td>{{ optional($blog->published_at)->format('d M Y H:i') }}</td>
                        <td>Created At</td>
                        <td>{{ $blog->created_at->format('d M Y H:i') }}</td>
                    </tr>

                    <tr>
                        <td>Updated At</td>
                        <td colspan="3">{{ $blog->updated_at->format('d M Y H:i') }}</td>
                    </tr>



                    <tr>
                        <td>Content</td>
                        <td colspan="3">{!! $blog->content !!}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="text-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>

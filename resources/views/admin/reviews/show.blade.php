<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Review Details' }}</h5>
    </div>

    <div class="card-body pt-15">
        <div class="table-responsive mb-15">
            <table class="table table-bordered">
                <tbody>

                    <tr>
                        <td><strong>User</strong></td>
                        <td colspan="3">
                            {{ $review->user?->name ?? 'Guest' }}
                            <br>
                            <small class="text-muted">
                                {{ $review->user?->email ?? '' }}
                            </small>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Rating</strong></td>
                        <td>
                            {{ $review->rating }} ⭐
                        </td>
                        <td><strong>Status</strong></td>
                        <td>
                            {!! status_dropdown($review->status, [
                                'id' => $review->id,
                                'url' => route('reviews.status', encrypt($review->id)),
                                'method' => 'PUT',
                            ]) !!}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Comment</strong></td>
                        <td colspan="3">
                            {{ $review->comment ?? '—' }}
                        </td>
                    </tr>

                    <tr>
                        <td><strong>IP Address</strong></td>
                        <td>{{ $review->ip_address ?? '—' }}</td>
                        <td><strong>User Agent</strong></td>
                        <td>
                            <small>{{ $review->user_agent ?? '—' }}</small>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Created At</strong></td>
                        <td>{{ $review->created_at->format('d M Y H:i') }}</td>
                        <td><strong>Updated At</strong></td>
                        <td>{{ $review->updated_at->format('d M Y H:i') }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="text-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
            </button>
        </div>
    </div>
</div>

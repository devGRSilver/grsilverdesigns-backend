<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Transaction Details' }}</h5>
    </div>

    <div class="card-body pt-15">
        <div class="table-responsive mb-15">
            <table class="table table-bordered">
                <tbody>

                    {{-- User --}}
                    <tr>
                        <td><strong>User</strong></td>
                        <td colspan="3">
                            {{ $transaction->user?->name ?? 'Guest' }}
                            <br>
                            <small class="text-muted">
                                {{ $transaction->user?->email ?? '' }}
                            </small>
                        </td>
                    </tr>

                    {{-- Order / References --}}
                    <tr>
                        <td><strong>Order ID</strong></td>
                        <td>{{ $transaction->order->order_number ?? '—' }}</td>

                        <td><strong>Transaction ID</strong></td>
                        <td>{{ $transaction->transaction_id }}</td>
                    </tr>

                    {{-- Amount --}}
                    <tr>
                        <td><strong>Amount</strong></td>
                        <td>
                            {{ number_format($transaction->amount, 2) }}
                            {{ strtoupper($transaction->currency_code) }}
                        </td>

                        <td><strong>Net Amount</strong></td>
                        <td>
                            {{ number_format($transaction->net_amount ?? 0, 2) }}
                            {{ strtoupper($transaction->currency_code) }}
                        </td>
                    </tr>

                    {{-- Payment --}}
                    <tr>
                        <td><strong>Payment Method</strong></td>
                        <td>{{ $transaction->payment_method ?? '—' }}</td>

                        <td><strong>Payment Gateway</strong></td>
                        <td>{{ $transaction->payment_gateway ?? '—' }}</td>
                    </tr>



                    {{-- Gateway Transaction ID --}}
                    <tr>
                        <td><strong>Gateway Transaction ID</strong></td>
                        <td colspan="3">
                            {{ $transaction->gateway_transaction_id ?? '—' }}
                        </td>
                    </tr>

                    {{-- Status --}}
                    <tr>
                        <td><strong>Status</strong></td>
                        <td colspan="3">
                            {!! view_payment_status($transaction->status) !!}
                        </td>
                    </tr>


                    {{-- Customer --}}
                    <tr>
                        <td><strong>Customer Email</strong></td>
                        <td>{{ $transaction->customer_email ?? '—' }}</td>

                        <td><strong>Customer Phone</strong></td>
                        <td>{{ $transaction->customer_phone ?? '—' }}</td>
                    </tr>

                    {{-- Meta --}}
                    <tr>
                        <td><strong>IP Address</strong></td>
                        <td>{{ $transaction->customer_ip ?? '—' }}</td>

                        <td><strong>User Agent</strong></td>
                        <td>
                            <small>{{ $transaction->user_agent ?? '—' }}</small>
                        </td>
                    </tr>

                    {{-- Timestamps --}}
                    <tr>
                        <td><strong>Created At</strong></td>
                        <td>{{ $transaction->created_at->format('d M Y H:i a') }}</td>

                        <td><strong>Updated At</strong></td>
                        <td>{{ $transaction->updated_at->format('d M Y H:i a') }}</td>
                    </tr>

                    {{-- Settlement --}}
                    <tr>
                        <td><strong>Settled At</strong></td>
                        <td>{{ optional($transaction->settled_at)->format('d M Y H:i a') ?? '—' }}</td>

                        <td><strong>Refunded At</strong></td>
                        <td>{{ optional($transaction->refunded_at)->format('d M Y H:i a') ?? '—' }}</td>
                    </tr>

                    {{-- Notes --}}
                    <tr>
                        <td><strong>Notes</strong></td>
                        <td colspan="3">
                            {{ $transaction->notes ?? '—' }}
                        </td>
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

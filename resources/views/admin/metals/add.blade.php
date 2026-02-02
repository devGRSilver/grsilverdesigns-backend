<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? '' }}</h5>
    </div>

    <div class="card-body pt-3">
        <form class="validate_form" action="{{ route('metals.assign.category') }}" method="POST">
            @csrf
            <div class="row g-3">
                <!-- Parent Category -->

                <div class="col-md-12">
                    <label for="filterMetal" class="form-label">Metal</label>

                    <!-- Visible, readonly select -->
                    <select class="form-control filter-input" disabled>
                        @foreach ($metals ?? [] as $metal)
                            <option value="{{ encrypt($metal->id) }}"
                                {{ strtolower($metal->name) === strtolower($type) ? 'selected' : '' }}>
                                {{ $metal->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Hidden input to submit the selected value -->

                    @php
                        $met = DB::table('metals')->where('name', strtolower($type))->first();
                    @endphp
                    <input type="hidden" name="metal_id" value="{{ $met->id }}">
                </div>


                <div class="col-md-6">
                    <label for="filterParent" class="form-label">Main Category</label>
                    <select name="parent_id" id="filterParent" class="form-control select2 filter-input">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories ?? [] as $cat)
                            <option value="{{ encrypt($cat->id) }}">
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sub Category -->
                <div class="col-md-6">
                    <label for="subCategory" class="form-label">Sub Category</label>
                    <select name="sub_category_id[]" id="subCategory"
                        class="form-control select2 sub_category filter-input" multiple required>
                        <option value="">-- Select Sub Category --</option>
                    </select>
                </div>

                <div class="alert alert-warning d-flex align-items-start gap-2 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <div>
                        <strong>Important Notice:</strong><br>
                        Assigning a category to this metal directly affects product pricing.
                        Any price changes made to this metal by <strong>Admin or Management</strong> users will be
                        automatically reflected across <strong>all products</strong> associated with the selected
                        category and its subcategories.
                        <br><br>
                        Please review and select the category carefully before proceeding, as this action may impact
                        multiple products.
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Submit
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    $('.select2:not(.sub_category)').select2({
        width: '100%',
        placeholder: 'Select Option',
        allowClear: true
    });

    $('.sub_category').select2({
        width: '100%',
        placeholder: 'Select Sub Categories',
        allowClear: true,
        closeOnSelect: false
    });



    $('#filterParent').on('change', function() {
        let categoryId = $(this).val();
        let $sub = $('.sub_category');
        $sub.val(null).trigger('change');



        if (!categoryId) {
            $sub.html('<option value="">— Select Sub Category —</option>');
            return;
        }

        $sub.html('<option>Loading...</option>');
        let url = "{{ route('subcategories.ajax', ':id') }}".replace(':id', categoryId);

        $.get(url, function(data) {
            $sub.html('<option value="">— Select Sub Category —</option>');
            $.each(data, function(_, item) {
                $sub.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }).fail(function() {
            $sub.html('<option>Error loading data</option>');
        });
    });
</script>

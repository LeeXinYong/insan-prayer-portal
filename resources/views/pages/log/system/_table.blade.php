<!--begin::Table-->
{{ $table->table() }}
<!--end::Table-->

{{-- Inject Scripts --}}
@push("scripts")
    {{ $table->scripts() }}
@endpush

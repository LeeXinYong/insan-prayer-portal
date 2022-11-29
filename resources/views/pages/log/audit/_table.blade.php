<!--begin::Table-->
{{ $dataTable->table() }}
<!--end::Table-->

{{-- Inject Scripts --}}
@push("scripts")
    {{ $dataTable->scripts() }}
@endpush

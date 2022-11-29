<!--begin::Table-->
{{ ${$dataTableName ?? "dataTable"}->table() }}
<!--end::Table-->

{{-- Inject Scripts --}}
@push("scripts")
    {{ ${$dataTableName ?? "dataTable"}->scripts() }}
@endpush

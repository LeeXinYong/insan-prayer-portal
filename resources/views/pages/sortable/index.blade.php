<x-base-layout>
    <x-slot name="page_title_slot">{{ __("$model.page_title.arrange") }}</x-slot>
    <!--begin::Form-->
    <form method="post" id="arrange_form" action="{{ route("$model.arrange") }}" enctype="multipart/form-data">
        @csrf
        <!--begin::Card-->
        <div class="card">
            <div class="card-body">
                <ol class="sortable">
                    @foreach($items as $item)
                        <li data-id="{{ $item->id }}" class="mb-3">
                            <div class="bg-light rounded py-3 px-5 border border-hover border-hover-primary border-2 d-flex d-inline align-items-center justify-content-between">
                                <div>
                                    {!! $sortableItemTemplate($item) !!}
                                </div>
                                <i class="fa fa-grip-lines fs-2 text-primary"></i>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <textarea name="new_order" id="new_order" class="d-none"></textarea>
            </div>
            <div class="card-footer">
                <div class="d-flex align-items-center justify-content-end">
                    @include("pages.common-components.buttons.save-button", [
                        "indicator" => true
                    ])
                </div>
            </div>
        </div>
        <!--end::Card-->
    </form>
    <!--end::Form-->

    {{-- Inject Scripts --}}
    @push("scripts")
        <script type="text/javascript">
            $(document).ready(function() {
                const sortable = $("ol.sortable").sortable({
                    onDrop: function(item, container, _super) {
                        item.removeClass(container.group.options.draggedClass).removeAttr("style");
                        $("body").removeClass(container.group.options.bodyClass);

                        $("#new_order").val(JSON.stringify(sortable.sortable("serialize").get()));
                    }
                })

                $("#new_order").val(JSON.stringify(sortable.sortable("serialize").get()));

                initFormSubmission(
                    $("#arrange_form"),
                    "{{ __("layout.spinner.saving") }}",
                    "{{ __("general.message.fail_arrange") }}"
                )
            });
        </script>
    @endpush
</x-base-layout>

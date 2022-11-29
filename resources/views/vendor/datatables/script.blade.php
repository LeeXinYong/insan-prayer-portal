$(function(){window.{{ config("datatables-html.namespace", "LaravelDataTables") }}=window.{{ config("datatables-html.namespace", "LaravelDataTables") }}||{};window.{{ config("datatables-html.namespace", "LaravelDataTables") }}["%1$s"]=$("#%1$s").DataTable(%2$s);

$.ajaxSetup({headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")}});
    {{ config("datatables-html.namespace", "LaravelDataTables") }}["%1$s"].on("click", "[data-destroy]", function (e) {
        e.preventDefault();
        const deleteElement = $(this);
        Swal.fire({
            title: "{{ __("general.message.confirmation") }}",
            text: "{{ __("general.message.delete_msg") }}",
            showCancelButton: true,
            reverseButtons: true,
            buttonsStyling: false,
            confirmButtonText: "{{ __("general.message.yes") }}",
            cancelButtonText: "{{ __("general.button.cancel") }}",
            customClass: {
                popup: "swal2-danger",
                confirmButton: "btn btn-custom-gradient btn-active-custom-gradient min-w-60px min-w-lg-150px min-h-40px rounded-1",
                cancelButton: "btn btn-custom-light btn-active-custom-light min-w-60px min-w-lg-150px min-h-40px rounded-1 btn-outline"
            }
        }).then(function (result) {
            if (result.value) {
                axios.delete(deleteElement.data("destroy"), {
                    "_method": "DELETE",
                })
                .then(function (response) {
                    toastr.success(response.data?.message || "{{ __("general.message.deleted") }}");
                    {{ config("datatables-html.namespace", "LaravelDataTables") }}["%1$s"].ajax.reload();
                })
                .catch(function (error) {
                    toastr.error(error.response?.data?.error || "{{ __("general.message.please_try_again") }}");
                    console.log(error);
                });
            }
        });
    });
});

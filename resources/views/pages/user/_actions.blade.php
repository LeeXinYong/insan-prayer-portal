@php
    $actions = [
        "edit" => [
            "url" => '#',
            "id" => "editAdminBtn",
            "data" => [
                "bs-toggle" => "modal",
                "bs-target" => "#edit-user-modal",
            ],
            "label" => __("user.action_menu.edit_profile"),
            "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::update')
        ],
        "resetPassword" => [
            "url" => '#',
            "id" => "reset_password_btn",
            "label" => __('user.action_menu.reset_password'),
            "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::updatePassword')
        ],
        "sendTestNotification" => [
            "url" => '#',
            "id" => "send_test_notification_btn",
            "label" => __('user.button.send_test_notification'),
            "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::sendTestNotification')
        ],
        "suspend" => [
            "url" => '#',
            "id" => "suspend_user_btn",
            "label" => __('user.action_menu.suspend'),
            "classes" => "text-danger ". (!$user->status ? "d-none" : ""),
            "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::updateStatus')
        ],
        "reactivate" => [
            "url" => '#',
            "id" => "reactivate_user_btn",
            "label" => __('user.action_menu.reactivate'),
            "classes" => "text-success ". ($user->status ? "d-none" : ""),
            "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::updateStatus')
        ]
    ];

    if(Auth::user()->id == $user->id) {
        unset($actions['resetPassword']);
        unset($actions['suspend']);
        unset($actions['reactivate']);
    }
@endphp
@include("pages.common-components.buttons.action-menu-button", ["actions" => $actions, "forceDropdown" => true])


@push('scripts')
    <script>
        $(document).ready(function() {
            @if(Auth::user()->hasPermissionTo('App\Models\User::sendTestNotification'))
                $('#send_test_notification_btn').on('click', function() {
                    fireSwal("info",
                        "{!! __("user.message.send_test_notification_msg", ["user_name" => $user->name]) !!}",
                        null,
                        false,
                        "{{ __("general.button.send") }}",
                        "{{ __("general.button.cancel") }}",
                        onConfirm = function() {
                            callAPI("{{ route("user.sendTestNotification", ["user" => $user->id]) }}");
                        }
                    );
                });
            @endif


            @if(Auth::user()->id != $user->id)
                @if(Auth::user()->hasPermissionTo('App\Models\User::updateStatus'))
                    @if(Auth::user()->hasPermissionTo('App\Models\User::updatePassword'))
                        $('#reset_password_btn').on('click', function() {
                            fireSwal("warning",
                                "{{ __("user.reset_prompt") }}",
                                null,
                                false,
                                "{{ __("general.button.yes") }}",
                                "{{ __("general.button.cancel") }}",
                                onConfirm = function() {
                                    callAPI("{{ route("user.updatePassword", ["user" => $user->id]) }}");
                                }
                            );
                        });
                    @endif

                    $('#suspend_user_btn').on('click', function() {
                        fireSwal("danger",
                            "{!! __("user.message.suspend_user_msg", ["user_name" => $user->name]) !!}",
                            null,
                            false,
                            "{{ __("general.button.suspend") }}",
                            "{{ __("general.button.cancel") }}",
                            onConfirm = function() {
                                callAPI("{{ route("user.updateStatus", ["user" => $user->id, "action" => "suspend"]) }}", function(response) {
                                    toggleStatusElements(response?.data?.user_status)
                                });
                            }
                        );
                    });

                    $('#reactivate_user_btn').on('click', function() {
                        fireSwal("success",
                            "{!! __("user.message.reactivate_user_msg", ["user_name" => $user->name]) !!}",
                            null,
                            false,
                            "{{ __("general.button.reactivate") }}",
                            "{{ __("general.button.cancel") }}",
                            onConfirm = function() {
                                callAPI("{{ route("user.updateStatus", ["user" => $user->id, "action" => "reactivate"]) }}", function(response) {
                                    toggleStatusElements(response?.data?.user_status)
                                });
                            }
                        );
                    });
                @endif
            @endif
        });

        function toggleStatusElements(status) {
            if(status != null) {
                $("#status_badge")
                    .removeClass(!status ? "badge-light-success" : "badge-light-danger")
                    .addClass(!status ? "badge-light-danger" : "badge-light-success")
                    .html(!status ? "{{ __("general.message.inactive") }}" : "{{ __("general.message.active") }}");

                $("#suspend_user_btn").toggleClass("d-none", !status);
                $("#reactivate_user_btn").toggleClass("d-none", status);
            }
        }

        function callAPI(url, callback = null) {
            toggleLoadingActionMenu();

            // Send ajax request
            axios.post(url)
                .then(function (response) {
                    const data = response.data;

                    toastr.success(data.success);

                    if (callback) {
                        callback(response);
                    }
                })
                .catch(function (error) {
                    toastr.error(error.response?.data?.message || (error.response?.data?.errors !== undefined ? Object.values(error.response?.data?.errors)?.[0]?.[0] : (error.response?.data?.error || "{{ __("general.message.please_try_again") }}")));
                })
                .then(function () {
                    toggleLoadingActionMenu();
                });
        }

        function toggleLoadingActionMenu() {
            if( $('#actionMenu').attr("data-kt-indicator") == "on" ) {
                $('#actionMenu').attr("data-kt-indicator", "off");
                $('#actionMenu').prop("disabled", false);
            } else {
                $('#actionMenu').attr("data-kt-indicator", "on");
                $('#actionMenu').prop("disabled", true);
            }
        }
    </script>
@endpush

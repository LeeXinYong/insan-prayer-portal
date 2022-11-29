@include("pages.common-components.buttons.action-menu-button", ["actions" => [
                "edit" => [
                    "url" => '#',
                    "id" => "editAdminBtn",
                    "data" => [
                        "bs-toggle" => "modal",
                        "bs-target" => "#edit-user-modal",
                    ],
                    "label" => __("profile.action_menu.edit_profile"),
                    "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::update')
                ],
                "changePassword" => [
                    "url" => route('profile.changePassword'),
                    "label" => __('profile.action_menu.change_password'),
                    "disabled" => !Auth::user()->hasPermissionTo('App\Models\User::updatePassword')
                ],
    ], "forceDropdown" => true])


@push('scripts')
    <script>
    </script>
@endpush

@pushOnce('scripts')
    <script>
        window.getPermissionTemplate = (permission) =>
            '<div class="card shadow-sm" style="height: fit-content">' +
                '<div class="card-header collapsible collapsed cursor-pointer rotate" data-bs-toggle="collapse"' +
                    ' data-bs-target="#collapsible_permission_' + permission.id + '">' +
                    '<h3 class="card-title" style="max-width: calc(100% - 2rem)">' +
                        '<div class="form-check form-check-custom form-check-solid form-check-sm">' +
                            '<input class="form-check-input master-checkbox" type="checkbox"' +
                                ' id="' + permission.module + '" data-bs-toggle="collapse" data-bs-target' +
                                (permission.updatable && true ? '' : ' disabled readonly') +
                            '/>' +
                            '<label class="form-check-label fs-4">' +
                                permission.name +
                                '<span class="count-span fs-7 fw-normal" data-target="' + permission.module + '"> (' + (Object.values(permission.permission_list) || []).filter(s => s.granted === 1).length + '/' + (Object.values(permission.permission_list) || []).length + ')</span>' +
                            '</label>' +
                        '</div>' +
                    '</h3>' +
                    '<div class="card-toolbar rotate-180">' +
                        '@svg("icons/duotune/arrows/arr072.svg, svg-icon-2")' +
                    '</div>' +
                '</div>' +
                '<div id="collapsible_permission_' + permission.id + '" class="collapse">' +
                    '<div class="card-body">' +
                        '<div class="d-grid gap-6" style="grid-template-columns: repeat(2, 1fr)">' +
                            (Object.values(permission.permission_list) || []).map((permissible) =>
                                '<div class="form-check form-check-custom form-check-solid form-check-sm align-items-start">' +
                                    '<input class="form-check-input permission-checkbox slave-checkbox"' +
                                        ' data-target="' + permission.module + '" type="checkbox"' +
                                        (permissible.granted === 1 ? ' checked' : '') +
                                        (permissible.updatable && true ? '' : ' disabled readonly') +
                                        ' id="' + permission.class + '::' + permissible.permission + '" name="permissions[]"' +
                                        ' value="' + permission.class + '::' + permissible.permission + '"' +
                                    '/>' +
                                    '<label class="form-check-label">' + permissible.name + '</label>' +
                                '</div>'
                            ).join('') +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    </script>
@endPushOnce

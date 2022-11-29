<?php

namespace App\Http\Controllers;

use App\DataTables\Permissions\PermissionRoleDataTable;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }

    public function index(PermissionRoleDataTable $dataTable): mixed
    {
        return $dataTable->render('pages.permissions.index');
    }
}

<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

interface RoleAndPermissionRepository
{
    public function getRoles(): Collection;
    public function createRole(array $roleData): Role;
    public function updateRole(int $roleId, array $roleData): Role;
    public function deleteRole(int $roleId): void;
    public function getRolePermission(int $roleId): array;
    public function getRoleUser(int $roleId): array;
    public function switchPermission(int $roleId, int $permissionId, bool $value): void;
}

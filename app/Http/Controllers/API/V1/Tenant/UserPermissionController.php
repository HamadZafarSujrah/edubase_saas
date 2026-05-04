<?php

namespace App\Http\Controllers\API\V1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tenant\UserPermission;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
    /**
     * Retrieve the current permissions grid for a user.
     * This matches the UI grid (Modules x Actions).
     */
    public function getPermissions(Request $request, $userId)
    {
        // Only tenant_super_admin can fetch/assign these
        $user = User::findOrFail($userId);
        
        $permissions = UserPermission::where('user_id', $userId)
            ->where('is_granted', true)
            ->get();

        // Let's format this nicely for your React Frontend to interpret the Checkboxes
        $formattedPermissions = [];
        foreach ($permissions as $perm) {
            // E.g., 'student' -> ['list' => true, 'add' => true]
            $stringParts = explode('.', $perm->permission);
            if (count($stringParts) === 2) {
                $moduleKey = $stringParts[0]; // e.g., 'student'
                $action = $stringParts[1];    // e.g., 'add'
                
                $formattedPermissions[$moduleKey][$action] = true;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $formattedPermissions
        ]);
    }

    /**
     * Completely overwrite a user's permissions based on the UI grid selection.
     */
    public function assignPermissions(Request $request, $userId)
    {
        $targetUser = User::findOrFail($userId);

        // Expects payload: [ "permissions" => [ 'student.add', 'student.list', 'fee.delete', 'exam.pdf' ] ]
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        DB::beginTransaction();
        try {
            // Wipe out existing custom permissions and refresh with the new strict UI state
            UserPermission::where('user_id', $targetUser->id)->delete();

            $inserts = [];
            foreach ($validated['permissions'] as $permissionStr) {
                // We extract the UI module name (e.g. 'student' from 'student.add') for easy querying later
                $moduleKey = explode('.', $permissionStr)[0];

                $inserts[] = [
                    'user_id' => $targetUser->id,
                    'module' => $moduleKey, 
                    'permission' => $permissionStr,
                    'is_granted' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($inserts)) {
                UserPermission::insert($inserts);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User permissions strictly updated.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize permissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

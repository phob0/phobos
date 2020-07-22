<?php

namespace Phobos\Framework\App\Http\Controllers;

use Phobos\Framework\Editables\EditableController;
use Phobos\Framework\App\Resources\UserResource;
use Phobos\Framework\App\Repositories\UserRepository;
use Phobos\Framework\App\Models\PhobosUser;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class UserController extends EditableController
{
    protected $defaultSortBy = 'email';
    protected $defaultSortOrder = 'asc';
    protected $sortByOptions = [
        'id' => [ 'expression' => 'users.id' ],
        'email' => [ 'expression' => 'users.email' ],
        'name' => [ 'expression' => 'users.last_name' ],
    ];

    public function list(UserRepository $userRepository, Request $request)
    {
        $params = $this->parseParams($request, [
            'role' => $request->get('role') ?: null
        ]);

        $this->authorize('viewAny', [PhobosUser::class, $params['target_type'] ?? '', $params['target_id'] ?? 0]);

        return UserResource::collection($userRepository->list($params))
            ->additional($this->listAdditionalData($request, $params));
    }

    public function item(PhobosUser $user)
    {
        $this->authorize('view', $user);

        return UserResource::make($user);
    }

    public function create(Request $request, UserRepository $userRepository)
    {
        $targetType = 'application';
        $targetId = 0;

        $this->authorize('create', [PhobosUser::class, $targetType, $targetId]);

        $data = $this->validate($request, $this->getRules($request));

        $newId = $userRepository->create($data);

        if ($newId) {
            return [
                'newRecordId' => $newId,
            ];
        }

        return $this->apiError('Could not create record');
    }

    public function update(Request $request, UserRepository $userRepository, PhobosUser $user)
    {
        $this->authorize('update', $user);

        $data = $this->validate($request, $this->getRules($request));

        $updated = $userRepository->update($user, $data);

        if ($updated) {
            return $this->apiSuccess();
        }

        return $this->apiError('Could not update record');
    }

    public function destroy(UserRepository $userRepository, PhobosUser $user)
    {
        $this->authorize('delete', $user);

        $destroyed = $userRepository->destroy($user);

        if ($destroyed) {
            return $this->apiSuccess();
        }

        return $this->apiError('Could not remove record');
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $data = parent::validate($request, $rules, $messages, $customAttributes);

        if ($request->isMethod('put') && !$request->has('role')) {
            return Arr::except($data, ['target_type', 'target_id']);
        }

        $data['target_type'] = $this->getTargetType($request);
        $data['target_id'] = $this->getTargetId($request);

        if (empty($data['target_type'])) {
            throw ValidationException::withMessages([
                'role' => 'You are not allowed to assign this role',
            ]);
        }

        return $data;
    }

    private function getRules(Request $request)
    {
        $rules = [
            'role' => [],
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|max:50|unique:users',
            'password' => 'required|string|max:100',
            'phone' => 'string|max:50'
        ];

        if ($request->has('role') && $request->get('role') == 'responsible') {
            $rules['location'] = 'required|integer';
        }

        if ($request->isMethod('put')) {
            $rules['email'] .= ',email,' . $request->route('user')->id;
            $rules['password'] = 'string|max:100';
        }

        return $rules;
    }

    private function getTargetType(Request $request)
    {
        $targetType = 'application';
        if ($request->has('role')) {
            $role = $request->get('role');

            if ($role == 'responsible') {
                $targetType = 'location';
            }

            if ( in_array($role, ['nurse', 'collector', 'patient']) ) {
                $currentUserRoles = Auth::user()->roles;
                $targetType = $currentUserRoles[0]['target_type'];
            }

            if ($role == 'caregiver') {
                $targetType = 'user';
            }
        }

        return $targetType;
    }

    private function getTargetId(Request $request)
    {
        $targetId = 0;
        if ($request->has('role')) {
            $role = $request->get('role');

            if ($role == 'responsible') {
                $targetId = $request['location'];
            }

            if ( in_array($role, ['nurse', 'collector', 'patient']) ) {
                $currentUserRoles = Auth::user()->roles;
                $targetId = $currentUserRoles[0]['target_id'];
            }

            if ($role == 'caregiver') {
                $targetId = $request['user_id'];
            }
        }

        return $targetId;
    }

    protected function parseParams(Request $request, $extra = [])
    {
        $params = parent::parseParams($request, $extra);

        $user = \Auth::user();

        $targetType = '';
        $targetId = 0;

        if (($extra['role'] ?? '') === 'superadmin') {
            $targetType = 'application';
        } else if (($user->role['target_type'] ?? '') === 'location') {
            $targetType = $user->role['target_type'];
            $targetId = $user->role['target_id'];
        }

        return [
            'target_type' => $targetType,
            'target_id' => $targetId,
        ] + $params;
    }

    public function nursesLookup(UserRepository $userRepository)
    {
        $user = \Auth::user();

        if (($user->role['target_type'] ?? '') !== 'location') {
            return [];
        }

        $targetType = $user->role['target_type'];
        $targetId = $user->role['target_id'];

        return $userRepository->nursesLookup($targetType, $targetId);
    }
}

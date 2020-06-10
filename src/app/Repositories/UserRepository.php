<?php


namespace Phobos\Framework\App\Repositories;


use App\PhobosUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Phobos\Framework\Editables\EditableRepository;
use Phobos\Framework\Editables\EditableModel;

class UserRepository extends EditableRepository
{
    public function find($userId, $columns = ['*'])
    {
        return PhobosUser::find($userId, $columns);
    }

    public function list($params)
    {
        $query = PhobosUser::query()->select('users.*');

        if (!empty($params['role'])) {
            $query
                ->join('user_roles as ur', 'ur.user_id', '=', 'users.id')
                ->where('ur.role', '=', $params['role']);
        }

        if (!empty($params['target_type'])) {
            if (empty($params['role'])) {
                $query
                    ->join('user_roles as ur', 'ur.user_id', '=', 'users.id');
            }

            $query
                ->where('ur.target_type', '=', $params['target_type'])
                ->where('ur.target_id', '=', $params['target_id']);
        }

        if (!empty($params['sortBy'])) {
            $query->orderBy($params['sortBy'], $params['sortOrder'] ?? 'asc');
        }

        if (!empty($params['filters']['name'])) {
            $query->where(function($q) use ($params) {
                $q
                    ->where('users.first_name', 'LIKE', $params['filters']['name'] . '%')
                    ->orWhere('users.last_name', 'LIKE', $params['filters']['name'] . '%')
                ;
            });
        }

        if (!empty($params['filters']['status']) && in_array(EditableModel::STATUS_INACTIVE, $params['filters']['status'])) {
            if (!in_array(EditableModel::STATUS_ACTIVE, $params['filters']['status'])) {
                $query->onlyTrashed();
            } else {
                $query->withTrashed();
            }
        }

        return $query->paginate($params['perPage']);
    }

    public function lookup($targetType, $targetId, $role = '')
    {
        $query = PhobosUser::orderBy('last_name', 'asc')
            ->join('user_roles as ur', 'ur.user_id', '=', 'users.id')
            ->where('ur.target_type', '=', $targetType)
            ->where('ur.target_id', '=', $targetId);

        if ($role) {
            $query->where('ur.role', '=', $role);
        }

        return $query
            ->get(['users.id', 'users.first_name', 'users.last_name', 'ur.role as role_name'])
            ->map(function($record) {
                return [
                    'label' => $record->first_name . ' ' . $record->last_name,
                    'value' => $record->id,
                    'role' => $record->role_name,
                ];
            });
    }

    public function nursesLookup($targetType, $targetId)
    {
        return $this->lookup($targetType, $targetId, 'nurse');
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return DB::transaction(function () use ($data) {
            $record = PhobosUser::create(Arr::except($data, ['role', 'target_type', 'target_id']));
            $record->roles()->create(Arr::only($data, ['role', 'target_type', 'target_id']));

            return $record->id;
        });
    }

    public function update(PhobosUser $user, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return \DB::transaction(function () use ($user, $data) {
            $user->fill(Arr::except($data, ['role']));

            if ($user->role['name'] !== $data['role'] || $data['role'] === 'responsible') {
                $user->roles()->delete();
                $user->roles()->create([
                    'role' => $data['role'],
                    'target_type' => $data['target_type']?:'application',
                    'target_id' => $data['target_id']?:0,
                ]);
            }

            return $user->push();
        });
    }

    public function destroy(PhobosUser $user)
    {
        return ['success' => boolval($user->delete())];
    }
}

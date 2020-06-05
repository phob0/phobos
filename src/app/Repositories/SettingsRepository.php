<?php


namespace Phobos\Framework\App\Repositories;

use Phobos\Framework\App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Phobos\Framework\Editables\EditableRepository;

class SettingsRepository extends EditableRepository
{
    private static $config = [];

    public function create(array $data)
    {
        $record = new Setting($data);
        $record->forceFill([
            'history' => [],
            'user_id' => \Auth::id(),
            'setting_options' => '',
        ]);
        $record->save();
        return $record->id;
    }

    public function update(Setting $record, array $data)
    {
        $record->fill(Arr::except($data, ['history']));
        $record->forceFill(Arr::only($data, ['history']));
        return $record->save();
    }

    public static function all(?int $userId = null)
    {
        if (is_null($userId)) {
            $user = Auth::user();

            if (!$user) {
                return [];
            }
        } else if (!$userId) {
            $user = (object)[
                'id' => 0,
                'role' => [],
            ];
        } else {
            $user = app(UserRepository::class)->find($userId);

            if (!$user) {
                return [];
            }
        }

        $configKey = $user->id;
        if (empty(self::$config[$configKey])) {
            self::$config[$configKey] = collect(self::readSettings($user->id))
                ->map(function($item) {
                    return self::convertSettingValue($item->value ?? null, $item->setting_type ?? null);
                });
        }

        return self::$config[$configKey];
    }

    public static function general()
    {
        return self::all(0);
    }

    public static function get($key, $default = null, ?int $userId = null)
    {
        $all = self::all($userId);

        if (empty($all)) {
            return null;
        }

        return $all[$key] ?? $default;
    }

    public static function getGeneral($key, $default = null)
    {
        return self::get($key, $default, 0);
    }

    /**
     * Read all settings or specified setting from the DB. Do not call this directly unless you know what you are doing.
     * It doesn't cache results, as the get method does
     *
     * @param int $userId
     * @param string|null $setting
     * @return array
     */
    public static function readSettings(int $userId, ?string $setting = null)
    {
        $query = DB::table('settings')
            ->whereIn('user_id', [0, $userId])
            ->where('value', '<>', '')
            ->orderByRaw("CASE WHEN user_id = ? THEN 75 END", [
                $userId,
            ]);

        if ($setting) {
            $ret = $query
                ->where('setting', '=', $setting)
                ->first(['id', 'setting', 'value', 'setting_type']);
            return $ret ? (array)$ret : null;
        }

        return $query
            ->get(['setting', 'value', 'setting_type'])
            ->keyBy('setting')
            ->toArray();
    }

    private static function convertSettingValue($value, $type)
    {
        if (!$type) {
            return $value;
        }

        switch ($type) {
            case 'int':
                return intval($value);
            case 'float':
                return floatval($value);
            case 'bool':
                return boolval($value);
            case 'json':
                return json_decode($value, true, 512, JSON_NUMERIC_CHECK);
            default:
                $complexType = explode(':', $type);
                switch ($complexType[0]) {
                    case 'lookup':
                        return intval($value);
                    case 'lookup-list':
                        return json_decode($value, true, 512, JSON_NUMERIC_CHECK);
                }
                return $value;
        }
    }

    public function list($params)
    {
        $query = Setting::query();

        if (in_array($params['user']->role['name'], ['user'])) {
            $query
                ->where('user_id', '=', $params['user']->id);
        } else {
            $query
                ->whereIn('user_id', [0, $params['user']->id]);
        }

        return $query
            ->paginate();
    }
}

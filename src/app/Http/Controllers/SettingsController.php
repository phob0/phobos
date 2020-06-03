<?php

namespace Phobos\Framework\app\Http\Controllers;

use Phobos\Framework\Editables\EditableController;
use Phobos\Framework\app\Resources\SettingResource;
use Phobos\Framework\app\Repositories\SettingsRepository;
use Phobos\Framework\app\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SettingsController extends EditableController
{
    public function list(SettingsRepository $settingRepository, Request $request)
    {
        $this->authorize('viewAny', [Setting::class]);

        $params = $this->parseParams($request, [
            'user' => \Auth::user(),
        ]);

        return SettingResource::collection($settingRepository->list($params))
                ->additional($this->listAdditionalData($request, $params));
    }

    public function item(SettingsRepository $settingRepository, Setting $setting)
    {
        $this->authorize('view', $setting);

        return SettingResource::make($setting);
    }

    public function create(Request $request, SettingsRepository $settingsRepository)
    {
        $this->authorize('create', [Setting::class]);

        $data = $this->validate($request, $this->getRules($request));

        $newId = $settingsRepository->create($data);

        if ($newId) {
            return [
                'newRecordId' => $newId,
            ];
        }

        return $this->apiError('Could not create record');
    }

    public function update(Request $request, SettingsRepository $settingsRepository, Setting $setting)
    {
        $this->authorize('update', $setting);

        $request->merge(['history' => $this->history($request)]);
        $data = $this->validate($request, $this->getRules($request));

        $updated = $settingsRepository->update($setting, $data);

        if ($updated) {
            return $this->apiSuccess();
        }

        return $this->apiError('Could not update record');
    }

    private function getRules(Request $request)
    {
        $rules = [
            'setting' => 'required|string|max:100',
            'value' => 'required|string',
            'setting_type' => 'required|string|max:20',
            'secured' => 'boolean',
        ];

        if ($request->isMethod('put')) {
            return Arr::except($rules, ['setting', 'setting_type']);
        }

        return $rules;
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $data = parent::validate($request, $rules, $messages, $customAttributes);

        $setting = $request->route('setting');

        // secured settings cannot be unsecured
        if ($setting) {
            if ($setting->secured) {
                unset($data['secured']);
            }

            $history = $setting->history;

            $found = false;
            foreach (array_reverse($history, true) as $ix => $item) {
                if ($data['value'] == $item['value']) {
                    $found = true;
                    $history = array_reverse(array_slice($history, 0, $ix));
                    $data['history'] = $history;
                    break;
                }
            }

            if (!$found && $setting->value != $data['value']) {
                // new value
                array_push($history, [
                    'value' => $setting->value,
                    'date' => now()->toDateTimeString(),
                ]);

                $data['history'] = $history;
            }
        }

        return $data;
    }

    private function history($request)
    {
        $history = $request->history;

        if (count($history) !== 0) {
            $history = array_reverse($history);

            if ($history[(count($history) - 1)] !== $request->value) {
                if (count($history) == 5) {
                    unset($history[0]);
                }
                array_push($history, $request->value);
            }

            return array_reverse($history);
        } else {
            array_push($history, $request->initial_value);

            return $history;
        }
    }
}

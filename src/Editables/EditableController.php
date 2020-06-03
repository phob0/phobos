<?php

namespace Phobos\Framework\Editables;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditableController extends Controller
{
    protected $defaultSortBy = 'id';
    protected $defaultSortOrder = 'asc';
    protected $sortByOptions = [
        'id' => [],
        'updated_at' => [ 'expression' => 'updated_at' ],
    ];

    protected function parseParams(Request $request, $extra = [])
    {
        $params = [
            'page' => min(max(intval($request->get('page')), 1), 100),
            'perPage' => min(max(intval($request->get('per_page')), 10), 100),
            'filters' => $request->get('filters', []),
        ];

        $sortBy = $request->get('sort_by');
        if (in_array($sortBy, array_keys($this->sortByOptions))) {
            $params += [
                'sortBy' => $this->sortByOptions[$sortBy]['expression'] ?? $sortBy,
                'sortOrder' => $request->get('sort_order') == 'desc' ? 'desc' : 'asc',
            ];
        } else {
            $sortBy = $this->defaultSortBy;
            $request->offsetSet('sort_by', $sortBy); // send back default sort criterion
            $params += [
                'sortBy' => $this->sortByOptions[$sortBy]['expression'] ?? $sortBy,
                'sortOrder' => $this->defaultSortOrder,
            ];
        }

        if(!empty($extra) && isset($extra['role'])) {
            $extra['role'] = $extra['role']  === 'administrators' ? 'superadmin' : ($extra['role'] === 'organizers' ? 'organizer' : 'guide');
        }

        return $extra + $params;
    }

    protected function listAdditionalData(Request $request, $params)
    {
        return [
            'meta' => [
                'sort_by' => $request->get('sort_by'),
                'sort_order' => $params['sortOrder'],
                'filters' => (object)$params['filters'],
            ],
        ];
    }

    protected function parseWeekdays($weekdays)
    {
        $ret = '';
        foreach($weekdays as $key => $value) {
            $ret .= (string)((int) $value);
        }
        return $ret;
    }
}

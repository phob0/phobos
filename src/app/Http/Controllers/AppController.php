<?php

namespace Phobos\Framework\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Phobos\Framework\app\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    public function config()
    {
        $user = Auth::guard('api')->user();

        return [
            'user' => $user ? UserResource::make($user) : null,
            //'version' => \Version::compact(),
            'locales' => config('translatable.locales'),
            'defaultLocale' => \App::getLocale(),
        ];
    }

    public function me()
    {
        return $this->apiSuccess([
            'user' => UserResource::make(Auth::guard('api')->user())
        ]);
    }

    public function upload(Request $request)
    {
        switch ($request->get('type')) {
            case 'image':
                foreach ($request->allFiles() as $field => $file) {
                    /** @var \Illuminate\Http\UploadedFile $file */
                    $ret[$field] = $file->store('images', 'public');
                }
                return $this->apiSuccess($ret);
            case 'video':
                foreach ($request->allFiles() as $field => $file) {
                    /** @var \Illuminate\Http\UploadedFile $file */
                    $ret[$field] = $file->store('videos', 'public');
                }
                return $this->apiSuccess($ret);
            default:
                return $this->apiError();
        }
    }
}

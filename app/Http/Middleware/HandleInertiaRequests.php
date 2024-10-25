<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $file = lang_path(App::currentLocale() . ".json");
        $settings = [];
        if ($request->user()) {
            foreach ($request->user()->restaurant->settings as $setting) {
                $settings[$setting->key] = $setting->value;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'class' => fn() => $request->session()->get('class')
            ],
            'locale' => App::currentLocale(),
            'locales' => config('app.available_locales'),
            'translations' => File::exists($file) ? File::json($file) : [],
            'settings' => $settings
        ];
    }
}

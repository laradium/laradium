<?php

namespace Laradium\Laradium\ViewComposers;

use Illuminate\View\View;
use JavaScript;

class VariableComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        try {
            $publicSettings = [];
            foreach (config('laradium-setting.public_settings') as $settingKey) {
                $publicSettings[$settingKey] = setting()->get($settingKey);
            }

            JavaScript::put([
                'settings' => $publicSettings
            ]);
        } catch (\Exception $e) {
            JavaScript::put([
                'settings' => []
            ]);
        }
    }
}
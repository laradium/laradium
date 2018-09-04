<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Models\Setting;

class SettingsRepository
{

    /**
     * @var mixed
     */
    protected $cachedSettings;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $cacheKey;

    /**
     * SettingRepository constructor.
     */
    public function __construct()
    {
        $this->cacheKey = config('laradium-setting.cache_key', 'settings');
        $this->cachedSettings = cache()->rememberForever($this->cacheKey, function () {
            return Setting::all()->keyBy('key');
        });
    }

    /**
     * Get specified setting/s
     *
     * @param array|string $keys
     * @param array|null $default
     * @return array|string
     */
    public function get($keys, $default = null)
    {
        $settings = $this->cachedSettings;

        if (is_array($keys)) {
            $array = [];
            foreach ($keys as $index => $key) {
                $setting = $settings->get($key);
                $array[$key] = $setting ? $setting->getValue() : (is_array($default) ? (isset($default[$index]) ? $default[$index] : null) : $default);
            }

            return $array;
        }

        $setting = $settings->get($keys);

        return $setting ? $setting->getValue() : (is_array($default) ? (isset($default[0]) ? $default[0] : null) : $default);
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function all(): array
    {
        return $this->cachedSettings->pluck('value', 'key')->toArray();
    }

    /**
     * Get grouped settings
     *
     * @return array
     */
    public function grouped()
    {
        return $this->cachedSettings->groupBy('group');
    }

    /**
     * Seed settings
     *
     * @param $data
     * @return void
     * @throws \Exception
     */
    public function seed($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Passed settings should be an array');
        }

        foreach ($data as $item) {
            if (!isset($item['group'])) {
                throw new \Exception('Group does not exist for key: ' . $item['key']);
            }

            $item['key'] = implode('.', [
                $item['group'],
                $item['key']
            ]);

            $setting = Setting::firstOrCreate([
                'key' => $item['key']
            ], array_except($item, 'value'));

            $translations = [];
            if (isset($item['value']) && is_array($item['value'])) {
                foreach ($item['value'] as $locale => $value) {
                    $translations[] = [
                        'locale' => $locale,
                        'value'  => $value
                    ];
                }
            } else {
                $setting->update([
                    'non_translatable_value' => isset($item['value']) ? $item['value'] : ''
                ]);
            }

            foreach ($translations as $translation) {
                $setting->translations()->firstOrCreate($translation);
            }
        }
    }

    /**
     * Clear cache
     *
     * @return bool
     * @throws \Exception
     */
    public function clear_cache(): bool
    {
        return cache()->forget($this->cacheKey);
    }
}
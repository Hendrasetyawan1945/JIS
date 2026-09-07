<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * WMO Weather Code → label Indonesia + emoji + apakah "buruk" untuk wisata outdoor.
     * Referensi: https://open-meteo.com/en/docs#weathervariables
     */
    private const WMO = [
        0 => ['label' => 'Cerah',                  'emoji' => '☀️',  'buruk' => false],
        1 => ['label' => 'Sebagian cerah',          'emoji' => '🌤️',  'buruk' => false],
        2 => ['label' => 'Berawan sebagian',        'emoji' => '⛅',  'buruk' => false],
        3 => ['label' => 'Mendung',                 'emoji' => '☁️',  'buruk' => false],
        45 => ['label' => 'Berkabut',                'emoji' => '🌫️',  'buruk' => true],
        48 => ['label' => 'Kabut beku',              'emoji' => '🌫️',  'buruk' => true],
        51 => ['label' => 'Gerimis ringan',          'emoji' => '🌦️',  'buruk' => false],
        53 => ['label' => 'Gerimis sedang',          'emoji' => '🌦️',  'buruk' => false],
        55 => ['label' => 'Gerimis lebat',           'emoji' => '🌧️',  'buruk' => true],
        61 => ['label' => 'Hujan ringan',            'emoji' => '🌧️',  'buruk' => false],
        63 => ['label' => 'Hujan sedang',            'emoji' => '🌧️',  'buruk' => true],
        65 => ['label' => 'Hujan lebat',             'emoji' => '🌧️',  'buruk' => true],
        71 => ['label' => 'Salju ringan',            'emoji' => '🌨️',  'buruk' => true],
        73 => ['label' => 'Salju sedang',            'emoji' => '🌨️',  'buruk' => true],
        75 => ['label' => 'Salju lebat',             'emoji' => '🌨️',  'buruk' => true],
        80 => ['label' => 'Hujan shower ringan',     'emoji' => '🌦️',  'buruk' => false],
        81 => ['label' => 'Hujan shower sedang',     'emoji' => '🌧️',  'buruk' => true],
        82 => ['label' => 'Hujan shower lebat',      'emoji' => '⛈️',  'buruk' => true],
        95 => ['label' => 'Badai petir',             'emoji' => '⛈️',  'buruk' => true],
        96 => ['label' => 'Badai petir + hujan es',  'emoji' => '⛈️',  'buruk' => true],
        99 => ['label' => 'Badai petir + hujan es',  'emoji' => '⛈️',  'buruk' => true],
    ];

    /**
     * Ambil cuaca terkini untuk satu koordinat.
     * Cache 15 menit per koordinat (dibulatkan 2 desimal) agar tidak spam API.
     *
     * @return array{
     *   weather_code: int,
     *   label: string,
     *   emoji: string,
     *   suhu: float,
     *   curah_hujan: float,
     *   angin_kmh: float,
     *   kelembaban: int,
     *   buruk: bool,
     *   ringkasan: string
     * }|null   null jika API gagal
     */
    public function get(float $lat, float $lng): ?array
    {
        // Bulatkan 2 desimal sebagai cache key (~1km presisi)
        $key = 'cuaca_'.round($lat, 2).'_'.round($lng, 2);

        return Cache::remember($key, now()->addMinutes(15), function () use ($lat, $lng) {
            return $this->fetch($lat, $lng);
        });
    }

    /**
     * Ambil cuaca untuk banyak wisata sekaligus.
     * Wisata yang berdekatan (< ~1km) otomatis pakai cache yang sama.
     *
     * @param  array<array{id: int|string, lat: float, lng: float}>  $wisataList
     * @return array<int|string, array> key = wisata id
     */
    public function getBatch(array $wisataList): array
    {
        $result = [];
        foreach ($wisataList as $w) {
            $cuaca = $this->get((float) $w['lat'], (float) $w['lng']);
            if ($cuaca !== null) {
                $result[$w['id']] = $cuaca;
            }
        }

        return $result;
    }

    private function fetch(float $lat, float $lng): ?array
    {
        try {
            $response = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current' => 'weather_code,temperature_2m,precipitation,wind_speed_10m,relative_humidity_2m',
                'timezone' => 'Asia/Jakarta',
                'forecast_days' => 1,
            ]);

            if ($response->failed()) {
                Log::warning('WeatherService: API gagal', ['status' => $response->status()]);

                return null;
            }

            $cur = $response->json('current');
            $code = (int) ($cur['weather_code'] ?? 0);
            $meta = self::WMO[$code] ?? ['label' => 'Tidak diketahui', 'emoji' => '🌡️', 'buruk' => false];

            $suhu = (float) ($cur['temperature_2m'] ?? 0);
            $hujan = (float) ($cur['precipitation'] ?? 0);
            $angin = (float) ($cur['wind_speed_10m'] ?? 0);
            $kelembaban = (int) ($cur['relative_humidity_2m'] ?? 0);

            return [
                'weather_code' => $code,
                'label' => $meta['label'],
                'emoji' => $meta['emoji'],
                'suhu' => $suhu,
                'curah_hujan' => $hujan,
                'angin_kmh' => $angin,
                'kelembaban' => $kelembaban,
                'buruk' => $meta['buruk'],
                'ringkasan' => "{$meta['emoji']} {$meta['label']}, {$suhu}°C, angin {$angin} km/h",
            ];

        } catch (\Throwable $e) {
            Log::warning('WeatherService: exception', ['error' => $e->getMessage()]);

            return null;
        }
    }
}

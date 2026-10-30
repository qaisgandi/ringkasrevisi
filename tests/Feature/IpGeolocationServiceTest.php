<?php

namespace Tests\Feature;

use App\Models\VisitLog;
use App\Services\IpGeolocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IpGeolocationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_region_names_are_normalized_to_indonesian(): void
    {
        config([
            'services.geoip.enabled' => true,
            'services.geoip.endpoint' => 'https://geo.example/{ip}/json/',
        ]);
        Http::fake([
            'geo.example/*' => Http::response(['success' => true, 'region' => 'West Java', 'city' => 'Bandung']),
        ]);

        $location = app(IpGeolocationService::class)->locate('36.1.1.10');

        $this->assertSame('Jawa Barat', $location['region']);
        $this->assertSame('Bandung', $location['city']);
    }

    public function test_ip_api_style_response_is_still_supported(): void
    {
        config([
            'services.geoip.enabled' => true,
            'services.geoip.endpoint' => 'http://geo.example/json',
        ]);
        Http::fake([
            'geo.example/*' => Http::response(['status' => 'success', 'regionName' => 'North Sumatra', 'city' => 'Medan']),
        ]);

        $location = app(IpGeolocationService::class)->locate('36.1.1.20');

        $this->assertSame('Sumatera Utara', $location['region']);
        $this->assertSame('Medan', $location['city']);
    }

    public function test_last_known_region_is_reused_without_calling_the_provider(): void
    {
        config([
            'services.geoip.enabled' => true,
            'services.geoip.endpoint' => 'https://geo.example/{ip}/json/',
        ]);
        Http::fake();

        VisitLog::create([
            'event_type' => 'view',
            'ip_address' => '36.1.1.11',
            'region' => 'Bali',
            'city' => 'Denpasar',
            'url' => '/home',
        ]);

        $location = app(IpGeolocationService::class)->locate('36.1.1.11');

        $this->assertSame('Bali', $location['region']);
        $this->assertSame('Denpasar', $location['city']);
        Http::assertNothingSent();
    }

    public function test_private_ips_are_never_sent_to_the_provider(): void
    {
        config(['services.geoip.enabled' => true]);
        Http::fake();

        $location = app(IpGeolocationService::class)->locate('192.168.1.5');

        $this->assertSame('Lokal', $location['region']);
        Http::assertNothingSent();
    }
}

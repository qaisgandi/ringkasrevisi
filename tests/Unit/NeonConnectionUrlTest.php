<?php

namespace Tests\Unit;

use App\Support\NeonConnectionUrl;
use PHPUnit\Framework\TestCase;

class NeonConnectionUrlTest extends TestCase
{
    public function test_it_adds_the_neon_endpoint_to_the_encoded_password(): void
    {
        $url = 'postgresql://neondb_owner:p%40ss%3Bword@ep-lingering-wind-b4mf0591-pooler.c-6.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require';

        $result = NeonConnectionUrl::addEndpointForLegacyLibpq($url);
        $parts = parse_url($result);

        $this->assertSame(
            'endpoint=ep-lingering-wind-b4mf0591$p@ss;word',
            rawurldecode($parts['pass'])
        );
        $this->assertSame('sslmode=require&channel_binding=require', $parts['query']);
    }

    public function test_it_does_not_modify_non_neon_or_already_configured_urls(): void
    {
        $postgresUrl = 'postgresql://user:password@db.example.com/app';
        $neonWithEndpoint = 'postgresql://user:endpoint%3Dep-demo$secret@ep-demo.neon.tech/app';

        $this->assertSame($postgresUrl, NeonConnectionUrl::addEndpointForLegacyLibpq($postgresUrl));
        $this->assertSame($neonWithEndpoint, NeonConnectionUrl::addEndpointForLegacyLibpq($neonWithEndpoint));
    }
}

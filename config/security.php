<?php

return [
    // Set to exact proxy IPs/CIDRs controlled by the deployment operator.
    'trusted_proxies' => array_values(array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', ''))))),
];

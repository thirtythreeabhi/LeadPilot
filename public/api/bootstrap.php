<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';
lp_method('GET');
try {
    $settings = lp_ai_settings();
    lp_response(200, [
        'demo'=>true,
        'conversations'=>lp_demo_conversations(),
        'templates'=>lp_templates(),
        'ai'=>[
            'configured'=>$settings !== null,
            'provider'=>$settings['provider'] ?? 'openrouter',
            'model'=>$settings['model'] ?? null,
        ],
    ]);
} catch (Throwable $e) { lp_api_error($e); }

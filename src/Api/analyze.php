<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';
lp_method('POST');
try {
    $input = lp_body();
    $conversation = lp_validate_conversation($input);
    $templates = lp_templates();
    $analysis = lp_analyze($conversation, $templates, ($input['try_ai'] ?? true) !== false);
    lp_response(200, ['analysis'=>$analysis]);
} catch (Throwable $e) { lp_api_error($e); }

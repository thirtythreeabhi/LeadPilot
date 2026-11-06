<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/Inference.php';

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function lp_response(int $status, array $result): never
{
    http_response_code($status);
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function lp_method(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $method) {
        header('Allow: ' . $method);
        lp_response(405, ['error'=>'Method not allowed.']);
    }
}

function lp_body(): array
{
    if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
        lp_response(415, ['error'=>'Use application/json.']);
    }
    if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 65000) {
        lp_response(413, ['error'=>'Request exceeds 65KB.']);
    }
    $raw = file_get_contents('php://input');
    if ($raw === false || strlen($raw) > 65000) { lp_response(413, ['error'=>'Request exceeds 65KB.']); }
    try { $value = json_decode($raw, true, 512, JSON_THROW_ON_ERROR); }
    catch (JsonException $error) { lp_response(400, ['error'=>'Invalid JSON request.']); }
    if (!is_array($value) || array_is_list($value)) {
        lp_response(400, ['error'=>'JSON object required.']);
    }
    return $value;
}

function lp_api_error(Throwable $e): never
{
    if ($e instanceof LeadPilotError) {
        leadpilot_log_failure($e->reason, ['http_status'=>$e->httpStatus]);
        lp_response($e->httpStatus, ['error'=>$e->getMessage()]);
    }
    leadpilot_log_failure('unexpected_error');
    lp_response(500, ['error'=>'Unexpected server error. Inspect the PHP server log.']);
}

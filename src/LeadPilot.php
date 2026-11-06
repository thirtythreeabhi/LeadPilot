<?php
declare(strict_types=1);

/**
 * Shared, dependency-free PHP utilities for LeadPilot.
 * API keys and saved messages live outside the public document root.
 */
final class LeadPilotError extends RuntimeException
{
    public int $httpStatus;
    public string $reason;

    public function __construct(string $message, int $httpStatus = 500, string $reason = 'application_error')
    {
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->reason = $reason;
    }
}

/** @return array<string,string> */
function leadpilot_env_file(string $path): array
{
    if (!is_file($path)) { return []; }
    $lines = @file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        throw new LeadPilotError('Could not read local configuration.', 500, 'configuration_error');
    }
    $values = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') { continue; }
        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/', $line, $match)) { continue; }
        $value = trim($match[2]);
        if (strlen($value) >= 2 && (
            ($value[0] === '"' && substr($value, -1) === '"')
            || ($value[0] === "'" && substr($value, -1) === "'")
        )) { $value = substr($value, 1, -1); }
        $values[$match[1]] = $value;
    }
    return $values;
}

/**
 * Extract only customer-facing text from OpenRouter, never reasoning metadata.
 * @param array<string,mixed> $response
 */
function leadpilot_extract_reply(array $response): string
{
    $content = $response['choices'][0]['message']['content'] ?? null;
    if (is_string($content)) {
        $reply = $content;
    } elseif (is_array($content)) {
        $segments = [];
        foreach ($content as $part) {
            if (is_array($part) && ($part['type'] ?? 'text') === 'text' && is_string($part['text'] ?? null)) {
                $segments[] = $part['text'];
            }
        }
        $reply = implode("\n", $segments);
    } else {
        $reply = '';
    }
    $reply = preg_replace('/<think\b[^>]*>.*?<\/think>/is', '', $reply) ?? '';
    if (preg_match('/<think\b/i', $reply)) { return ''; }
    return trim($reply);
}

/** @param array<string,mixed> $record */
function leadpilot_append_jsonl(string $filename, array $record): void
{
    if (!preg_match('/^[a-z0-9_-]+\.jsonl$/', $filename)) {
        throw new LeadPilotError('Unsupported storage filename.', 500, 'storage_error');
    }
    $directory = dirname(__DIR__) . '/data';
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new LeadPilotError('Could not create data directory.', 500, 'storage_error');
    }
    $line = json_encode($record, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    $path = $directory . '/' . $filename;
    if (@file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
        throw new LeadPilotError('Could not save a record to data/.', 500, 'storage_error');
    }
    @chmod($path, 0600);
}

/** @param array<string,mixed> $details */
function leadpilot_log_failure(string $reason, array $details = []): void
{
    try {
        leadpilot_append_jsonl('errors.jsonl', array_merge(['at'=>gmdate('c'),'reason'=>$reason], $details));
    } catch (Throwable $ignored) {
        // Logging should not hide the original failure.
        // Never write API keys, request headers or prompt text to error logs.
    }
}

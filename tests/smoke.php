<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/LeadPilot.php';

function test_assert(bool $condition, string $description): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$description}\n");
        exit(1);
    }
    echo "PASS: {$description}\n";
}

$tmpFile = tempnam(sys_get_temp_dir(), 'leadpilot-test-');
if ($tmpFile === false) {
    fwrite(STDERR, "Could not create temporary test file.\n");
    exit(1);
}

try {
    file_put_contents($tmpFile, "# Comment\nOPENROUTER_API_KEY='fake-test-secret'\nOPENROUTER_MODEL=example/free\n");
    $env = leadpilot_env_file($tmpFile);
    test_assert(($env['OPENROUTER_API_KEY'] ?? null) === 'fake-test-secret', 'parse quoted dotenv values');
    test_assert(($env['OPENROUTER_MODEL'] ?? null) === 'example/free', 'parse plain dotenv values');

    test_assert(
        leadpilot_extract_reply(['choices' => [['message' => ['content' => ' Thank you for reaching out. ']]]]) === 'Thank you for reaching out.',
        'extract plain customer-facing text'
    );

    test_assert(
        leadpilot_extract_reply(['choices' => [['message' => [
            'content' => '',
            'reasoning' => 'internal output',
        ]]]]) === '',
        'reject reasoning-only responses'
    );

    test_assert(
        leadpilot_extract_reply(['choices' => [['message' => [
            'content' => '<think>private reasoning</think>Dear customer, thank you.',
        ]]]]) === 'Dear customer, thank you.',
        'remove reasoning tags from content'
    );

    test_assert(
        leadpilot_extract_reply(['choices' => [['message' => [
            'content' => '<think>unfinished internal reasoning',
        ]]]]) === '',
        'reject incomplete reasoning output'
    );

    test_assert(
        leadpilot_extract_reply(['choices' => [['message' => [
            'content' => [['type' => 'text', 'text' => 'Hello'], ['type' => 'text', 'text' => 'there']],
        ]]]]) === "Hello\nthere",
        'extract array-format content'
    );

    echo "All local smoke tests passed.\n";
} finally {
    @unlink($tmpFile);
}

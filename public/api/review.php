<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';
lp_method('POST');
try {
    $input = lp_body();
    $id = lp_text($input['id'] ?? '', 60, 'Draft id', true);
    if (!preg_match('/^[a-f0-9]{20}$/', $id)) { lp_response(422, ['error'=>'Invalid draft ID.']); }
    $reply = lp_text($input['reply'] ?? '', 12000, 'Edited reply', true);
    $subject = lp_text($input['subject'] ?? '', 240, 'Email subject');
    $status = $input['status'] ?? 'edited';
    if (!in_array($status, ['edited','approved'], true)) {
        lp_response(422, ['error'=>'Status must be edited or approved.']);
    }
    // Confirm the id exists: revisions must not impersonate unknown generations.
    $path = lp_data_path('replies.jsonl');
    $found = false;
    if (is_file($path)) {
        $handle = fopen($path,'rb');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $record = json_decode($line, true);
                if (($record['id'] ?? '') === $id) { $found = true; break; }
            }
            fclose($handle);
        }
    }
    if (!$found) { lp_response(404, ['error'=>'Saved draft not found.']); }
    leadpilot_append_jsonl('revisions.jsonl', [
        'at'=>gmdate('c'), 'id'=>$id, 'status'=>$status,
        'subject'=>$subject ?: null, 'reply'=>$reply,
    ]);
    lp_response(200, ['saved'=>true,'status'=>$status]);
} catch (Throwable $e) { lp_api_error($e); }

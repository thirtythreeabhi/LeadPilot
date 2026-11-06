<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';
lp_method('POST');
try {
    $input = lp_body();
    $conversation = lp_validate_conversation($input);
    $mode = $input['mode'] ?? 'template';
    $tone = $input['tone'] ?? 'Professional';
    $customInstructions = $input['custom_instructions'] ?? '';
    $revisionSuggestions = $input['revision_suggestions'] ?? '';
    $previousReply = $input['previous_reply'] ?? '';
    if (!in_array($mode, ['template','hybrid','ai'], true)) {
        lp_response(422, ['error'=>'Choose template, hybrid or AI mode.']);
    }
    if (!in_array($tone, ['Professional','Friendly','Concise'], true)) {
        lp_response(422, ['error'=>'Select a supported tone.']);
    }
    if (!is_string($customInstructions) || strlen($customInstructions) > 1200) {
        lp_response(422, ['error'=>'Custom response instructions must be text under 1,200 characters.']);
    }
    if (!is_string($revisionSuggestions) || strlen($revisionSuggestions) > 1200 || !is_string($previousReply) || strlen($previousReply) > 12000) {
        lp_response(422, ['error'=>'Revision instructions or previous response exceed the allowed length.']);
    }
    if (trim($revisionSuggestions) !== '' && $mode === 'template') {
        lp_response(422, ['error'=>'Choose AI + template or AI generated mode to request an AI revision.']);
    }
    $templates = lp_templates();
    if (!$templates) { lp_response(422, ['error'=>'Create at least one template.']); }
    $analysis = lp_analyze($conversation, $templates, ($input['try_ai'] ?? true) !== false);
    $manual = $input['template_ids'] ?? null;
    if ($manual !== null) {
        if (!is_array($manual) || !array_is_list($manual) || count($manual) > 6) {
            lp_response(422, ['error'=>'Select at most six templates.']);
        }
        $valid = array_column(array_values(array_filter($templates, static fn($t)=>$t['active'])), 'id');
        foreach ($manual as $id) {
            if (!is_string($id) || !in_array($id, $valid, true)) {
                lp_response(422, ['error'=>'Unknown or inactive template selected.']);
            }
        }
        if ($manual) {
            $analysis['template_ids'] = array_values(array_unique($manual));
            $analysis['selection_method'] = 'Manual override';
        }
    }
    $draft = lp_generate_reply($conversation, $analysis, $templates, $mode, $tone, trim($customInstructions), trim($revisionSuggestions), trim($previousReply));
    $id = bin2hex(random_bytes(10));
    $created = gmdate('c');
    leadpilot_append_jsonl('replies.jsonl', [
        'id'=>$id, 'created_at'=>$created, 'conversation_id'=>$conversation['conversation_id'],
        'lead_name'=>$conversation['name'], 'channel'=>lp_channel($conversation),
        'messages'=>$conversation['messages'], 'mode'=>$mode, 'tone'=>$tone,
        'template_ids'=>$analysis['template_ids'], 'template_versions'=>array_map(
            static fn($t)=>['id'=>$t['id'],'version'=>$t['version']],
            lp_selected_templates($templates, $analysis['template_ids'])
        ),
        'analysis'=>$analysis, 'subject'=>$draft['subject'],
        'revision_suggestions'=>trim($revisionSuggestions),
        'reply'=>$draft['reply'], 'model'=>$draft['model'], 'tokens'=>$draft['tokens'],
        'review_status'=>'draft',
    ]);
    lp_response(200, [
        'id'=>$id, 'created_at'=>$created, 'saved'=>true,
        'reply'=>$draft['reply'],'subject'=>$draft['subject'],'model'=>$draft['model'],
        'mode'=>$mode, 'analysis'=>$analysis,
    ]);
} catch (Throwable $e) { lp_api_error($e); }

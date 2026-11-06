<?php
declare(strict_types=1);
require_once __DIR__ . '/_common.php';

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
        lp_response(200, ['templates'=>lp_templates()]);
    }
    lp_method('POST');
    $input = lp_body();
    $templates = lp_templates();
    if (($input['action'] ?? '') === 'reset') {
        lp_save_templates(lp_default_templates());
        leadpilot_append_jsonl('template_history.jsonl', ['at'=>gmdate('c'),'action'=>'reset']);
        lp_response(200, ['templates'=>lp_default_templates()]);
    }
    if (($input['action'] ?? '') !== 'save' || !is_array($input['template'] ?? null)) {
        lp_response(422, ['error'=>'Provide action=save and a template object.']);
    }
    $data = $input['template'];
    $id = $data['id'] ?? null;
    $index = null;
    if ($id !== null && $id !== '') {
        if (!is_string($id)) { lp_response(422, ['error'=>'Template ID must be text.']); }
        foreach ($templates as $i=>$t) {
            if ($t['id'] === $id) { $index=$i; break; }
        }
        if ($index === null) { lp_response(404, ['error'=>'Template not found.']); }
        if ((int)($data['version'] ?? -1) !== $templates[$index]['version']) {
            lp_response(409, ['error'=>'Template changed since you opened it. Refresh before saving.']);
        }
    }
    $old = $index !== null ? $templates[$index] : null;
    $saved = lp_validate_template($data, $old);
    if ($index === null) { $templates[]=$saved; } else { $templates[$index]=$saved; }
    lp_save_templates($templates);
    leadpilot_append_jsonl('template_history.jsonl', [
        'at'=>gmdate('c'),'action'=>$old ? 'update' : 'create',
        'template_id'=>$saved['id'],'version'=>$saved['version'],
        'previous'=>$old, 'current'=>$saved,
    ]);
    lp_response(200, ['template'=>$saved, 'templates'=>$templates]);
} catch (Throwable $e) { lp_api_error($e); }

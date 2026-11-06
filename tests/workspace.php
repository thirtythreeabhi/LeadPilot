<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Inference.php';

function verify(bool $condition, string $label): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}
$templates = lp_default_templates();
$fixtures = lp_demo_conversations();
verify(count($fixtures) >= 8, 'multiple fictional demo conversations exist');
verify(count($templates) >= 10, 'template library covers core enquiry categories');
$dubai = $fixtures[0];
$conversation = lp_validate_conversation([
    'name'=>$dubai['name'], 'company'=>$dubai['company'], 'stage'=>$dubai['stage'],
    'channel'=>$dubai['channel'], 'conversation_id'=>$dubai['id'], 'messages'=>$dubai['messages'],
]);
verify(count($conversation['messages']) === 3, 'multi-message thread kept intact');
$analysis = lp_rule_analysis($conversation, $templates);
foreach (['incorporation','visa','pricing','documentation','consultation'] as $id) {
    verify(in_array($id, $analysis['template_ids'], true), 'detects multi-intent template ' . $id);
}
verify($analysis['warmth'] >= 0 && $analysis['warmth'] <= 100, 'warmth remains 0-100');
verify(stripos($analysis['warmth_description'], 'probability') !== false, 'warmth not represented as conversion probability');
$draft = lp_generate_reply($conversation, $analysis, $templates, 'template', 'Professional');
verify($draft['subject'] === null, 'website draft has no email subject');
verify(stripos($draft['reply'], 'visa') !== false, 'template response covers visa request');
verify(stripos($draft['reply'], 'quotation') !== false, 'template response includes quote caveat');
verify(stripos($draft['reply'], 'appointment') !== false, 'mandatory wording preserved exactly');
verify(stripos($draft['reply'], '95%') === false && stripos($draft['reply'], 'lead warmth') === false, 'internal intelligence excluded from customer reply');
$email = $fixtures[1];
$emailConversation = lp_validate_conversation([
    'name'=>$email['name'], 'company'=>$email['company'], 'stage'=>$email['stage'],
    'channel'=>$email['channel'], 'conversation_id'=>$email['id'], 'messages'=>$email['messages'],
]);
$emailDraft=lp_generate_reply($emailConversation, lp_rule_analysis($emailConversation,$templates), $templates, 'template', 'Professional');
verify(str_starts_with($emailDraft['subject'] ?? '', 'Re:'), 'email channel gets a subject');
verify(str_contains($emailDraft['reply'], 'Kind regards'), 'email channel has a signature');
$client=$fixtures[2];
$clientConversation=lp_validate_conversation([
    'name'=>$client['name'], 'company'=>$client['company'], 'stage'=>$client['stage'],
    'channel'=>$client['channel'], 'conversation_id'=>$client['id'], 'messages'=>$client['messages'],
]);
$clientAnalysis=lp_rule_analysis($clientConversation, $templates);
verify(stripos($clientAnalysis['warmth_description'],'Existing-client') !== false, 'existing clients treated separately from sales');
verify(str_contains(lp_transcript($clientConversation['messages']), 'OUTBOUND'), 'earlier team messages included in context');
try {
    lp_validate_conversation(['messages'=>[['direction'=>'inbound','body'=>str_repeat('x',4001)]]]);
    verify(false,'reject oversized message');
} catch (LeadPilotError $e) {
    verify($e->httpStatus===422,'reject oversized message');
}
try {
    lp_validate_conversation(['messages'=>[['direction'=>'outbound','body'=>'Only outbound']]]);
    verify(false,'reject thread without inbound messages');
} catch (LeadPilotError $e) {
    verify($e->httpStatus===422,'reject thread without inbound messages');
}
$edited=lp_validate_template([
    'name'=>'Custom', 'body'=>'Hello {{customer_name}}', 'keywords'=>'test',
    'description'=>'Example', 'required_text'=>'Approved disclaimer', 'active'=>true,
]);
verify($edited['version']===1 && str_starts_with($edited['id'],'custom-'), 'new template gets id and version');
$next=lp_validate_template([
    'name'=>'Custom', 'body'=>'New content', 'keywords'=>'test', 'description'=>'Example',
    'required_text'=>'Approved disclaimer', 'active'=>false,
], $edited);
verify($next['version']===2 && $next['id']===$edited['id'] && !$next['active'], 'template update preserves ID, increases version');
echo "All workspace tests passed.\n";

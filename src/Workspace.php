<?php
declare(strict_types=1);

require_once __DIR__ . '/LeadPilot.php';

/**
 * Fictional demo conversations. Never treat this dataset as a Salesforce feed.
 * An entire thread, not a single message, is the unit of work.
 * @return list<array<string,mixed>>
 */
function lp_demo_conversations(): array
{
    return [
        [
            'id' => 'demo-dubai', 'name' => 'Rahul Mehta', 'company' => 'Mehta Digital', 'channel' => 'website',
            'stage' => 'New lead', 'updated_at' => '2026-11-06T09:25:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-06T09:10:00Z','body'=>'Hello, we run an IT consulting company in India and want to register a business in Dubai. How do we begin?'],
                ['id'=>'m2','direction'=>'inbound','at'=>'2026-11-06T09:13:00Z','body'=>'Also need two employment visas, and please share the total estimated cost and documents.'],
                ['id'=>'m3','direction'=>'inbound','at'=>'2026-11-06T09:25:00Z','body'=>'We are hoping to start within the next month. Can someone call me this week?'],
            ],
        ],
        [
            'id' => 'demo-uk', 'name' => 'Sarah Williams', 'company' => 'Northfield Studio', 'channel' => 'email',
            'stage' => 'New lead', 'updated_at' => '2026-11-06T08:40:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-06T08:40:00Z','body' => "Subject: UK expansion\n\nHi there, we are evaluating UK company registration for our design firm. Could you explain the process and expected timeline? We would also like to schedule a consultation. Best, Sarah"],
            ],
        ],
        [
            'id' => 'demo-status', 'name' => 'Priya Sharma', 'company' => 'Sharma Exports', 'channel' => 'portal',
            'stage' => 'Existing client', 'updated_at' => '2026-11-06T07:15:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-04T10:05:00Z','body'=>'I uploaded the company documents last week. Can you confirm if everything is okay?'],
                ['id'=>'m2','direction'=>'outbound','at'=>'2026-11-04T11:15:00Z','body'=>'Thank you, Priya. The team will review the submitted documents and update you.'],
                ['id'=>'m3','direction'=>'inbound','at'=>'2026-11-06T07:15:00Z','body'=>'Following up. I still cannot see any update in my portal. Can you tell me the application status and whether I need to submit anything else?'],
            ],
        ],
        [
            'id' => 'demo-payment', 'name' => 'Michael Brown', 'company' => 'Westbridge Ltd', 'channel' => 'email',
            'stage' => 'Existing client', 'updated_at' => '2026-11-05T16:45:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-05T16:39:00Z','body' => "Subject: Invoice discrepancy\n\nThe invoice I received does not match the amount discussed in our consultation. Please explain before I pay."],
                ['id'=>'m2','direction'=>'inbound','at'=>'2026-11-05T16:45:00Z','body'=>'I have already emailed twice. This is urgent and I am unhappy with the delay.'],
            ],
        ],
        [
            'id' => 'demo-visa', 'name' => 'Ahmed Khan', 'company' => 'Al Noor Trading', 'channel' => 'whatsapp',
            'stage' => 'Qualified', 'updated_at' => '2026-11-06T10:12:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-06T10:10:00Z','body'=>'Hi, can you help move 3 employees to Dubai? Need visas'],
                ['id'=>'m2','direction'=>'inbound','at'=>'2026-11-06T10:12:00Z','body'=>'Are you free for a call today? Need to decide by Friday.'],
            ],
        ],
        [
            'id' => 'demo-followup', 'name' => 'Neha Kapoor', 'company' => 'Kapoor Ventures', 'channel' => 'website',
            'stage' => 'Contacted', 'updated_at' => '2026-11-05T13:20:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-03T12:00:00Z','body'=>'Would your team be able to assist with setting up a company in Singapore?'],
                ['id'=>'m2','direction'=>'outbound','at'=>'2026-11-03T14:10:00Z','body'=>'We can discuss the available options. What kind of business would you like to operate?'],
                ['id'=>'m3','direction'=>'inbound','at'=>'2026-11-05T13:20:00Z','body'=>'It is a software business. Can you send a proposal and a rough quotation for the setup?'],
            ],
        ],
        [
            'id' => 'demo-general', 'name' => 'Alex Morgan', 'company' => '', 'channel' => 'website',
            'stage' => 'New lead', 'updated_at' => '2026-11-06T08:01:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-06T08:01:00Z','body'=>'Hello, can someone tell me more about your services?'],
            ],
        ],
        [
            'id' => 'demo-docs', 'name' => 'Fatima Ali', 'company' => 'FA Consultancy', 'channel' => 'email',
            'stage' => 'Existing client', 'updated_at' => '2026-11-05T15:05:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-05T15:00:00Z','body'=>'Hello team, I received a request for additional documents but I am not sure which files are missing.'],
                ['id'=>'m2','direction'=>'inbound','at'=>'2026-11-05T15:05:00Z','body'=>'Could you also confirm where I should upload them and whether this affects the timeline?'],
            ],
        ],
        [
            'id' => 'demo-arabic', 'name' => 'Layla Hassan', 'company' => 'LH Commerce', 'channel' => 'whatsapp',
            'stage' => 'New lead', 'updated_at' => '2026-11-06T07:55:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-06T07:55:00Z','body'=>'مرحبا، أريد تأسيس شركة في دبي. ما هي المستندات المطلوبة وهل يمكن تحديد موعد مكالمة؟'],
            ],
        ],
        [
            'id' => 'demo-partnership', 'name' => 'James Carter', 'company' => 'Carter Advisory', 'channel' => 'email',
            'stage' => 'New lead', 'updated_at' => '2026-11-05T11:20:00Z',
            'messages' => [
                ['id'=>'m1','direction'=>'inbound','at'=>'2026-11-05T11:20:00Z','body' => "Subject: Partner enquiry\n\nWe advise businesses entering Europe and would like to explore a referral partnership. Who would be the right person to speak to?"],
            ],
        ],
    ];
}

/** @return list<array<string,mixed>> */
function lp_default_templates(): array
{
    $definitions = [
        ['incorporation','Company registration','Company incorporation enquiries','company, register, incorporation, incorporate, setup, set up, establish, business, تأسيس, شركة',
            'Thank you for your interest in company incorporation in {{destination}}. Our team can discuss the appropriate registration route, the applicable requirements and next steps for your business.', ''],
        ['visa','Employee visas & relocation','Visas and workforce mobility','visa, visas, employee, employees, relocate, relocation, immigration, work permit, تأشيرة',
            'We also noted your employee visa or relocation request. The team can confirm the appropriate visa route, eligibility and requirements after reviewing the details.', ''],
        ['pricing','Pricing & quotations','Pricing, estimates and proposals','cost, costs, pricing, price, prices, quote, quotation, budget, proposal, fees, invoice estimate, كم',
            'For pricing, we can request an itemised estimate covering the relevant services after confirming the scope and jurisdiction.', 'We cannot confirm fees or issue a binding quotation without review.'],
        ['consultation','Consultation & callback','Call, meeting, calendar requests','call, calls, callback, meeting, consultation, appointment, schedule, موعد, مكالمة',
            'We can help coordinate a consultation. Please share your preferred time and time zone so a team member can confirm availability.', 'No appointment has been booked by this draft.'],
        ['documentation','Document requirements','Documents and onboarding','document, documents, files, upload, uploaded, paper, passport, onboarding, مستندات',
            'Regarding documentation, the team can confirm the service-specific checklist and a secure submission method.', 'Please do not send sensitive identity documents through an unverified channel.'],
        ['payments','Payments & invoices','Invoices, discrepancies and payments','invoice, invoices, payment, payments, paid, billing, refund, charged, discrepancy',
            'Thank you for flagging the billing concern. We will request a review of the relevant invoice and the agreed scope. Please provide an invoice reference through your usual secure channel so the team can investigate.', 'We have not verified or modified any invoice or payment.'],
        ['followup','Follow-up and proposals','Existing enquiry follow-up','follow up, following up, no reply, sent earlier, get back, proposal',
            'Thank you for following up. We appreciate your patience. To help the team respond accurately, could you confirm the outstanding request and a convenient contact method?', ''],
        ['status','Case & application status','Application and portal updates','status, progress, pending, application, applications, portal, update, tracking',
            'We understand you are looking for an update on your application. We cannot view live case status in this demo. Please provide your case reference through the official channel so the assigned team can check.', 'No live case record or status has been accessed.'],
        ['complaint','Complaint & escalation','Delayed replies and service issues','unhappy, complain, complaint, frustrated, delays, delay, urgent, disappointed, twice, unacceptable, dissatisfied',
            'We are sorry to hear about your experience and understand why you would like a prompt resolution. We can flag your concern for a team member to review and follow up.', 'This draft has not created an escalation ticket.'],
        ['partnership','Partnership enquiries','Referral and business partnerships','partner, partnership, referral, collaborate, alliance',
            'Thank you for your interest in exploring a partnership. Could you share a brief overview of your organisation, the markets you cover, and the proposed collaboration?', ''],
        ['general','General enquiry','General enquiries that need triage','help, information, interested, services, enquiry, question',
            'Thank you for contacting us. Could you tell us a little more about the service you are interested in and the country involved, so we can connect you with the appropriate team?', ''],
    ];
    $out = [];
    foreach ($definitions as $d) {
        $out[] = [
            'id' => $d[0], 'name' => $d[1], 'description' => $d[2], 'keywords' => $d[3],
            'body' => $d[4], 'required_text' => $d[5], 'active' => true,
            'version' => 1, 'updated_at' => '2026-11-06T00:00:00Z',
        ];
    }
    return $out;
}

function lp_data_path(string $filename): string
{
    return leadpilot_data_directory() . '/' . $filename;
}

/** @return list<array<string,mixed>> */
function lp_templates(): array
{
    $path = lp_data_path('templates.json');
    if (!is_file($path)) {
        return lp_default_templates();
    }
    $raw = @file_get_contents($path);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if (!is_array($decoded) || !array_is_list($decoded)) {
        throw new LeadPilotError('Template data could not be loaded.', 500, 'template_storage');
    }
    return $decoded;
}

/** @param list<array<string,mixed>> $templates */
function lp_save_templates(array $templates): void
{
    $directory = dirname(lp_data_path('templates.json'));
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new LeadPilotError('Cannot create the data directory.', 500, 'storage_error');
    }
    $path = lp_data_path('templates.json');
    $tmp = @tempnam($directory, '.templates-');
    if ($tmp === false) {
        throw new LeadPilotError('Cannot save templates.', 500, 'storage_error');
    }
    try {
        $json = json_encode($templates, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (@file_put_contents($tmp, $json, LOCK_EX) === false || !@rename($tmp, $path)) {
            throw new LeadPilotError('Cannot save templates.', 500, 'storage_error');
        }
        @chmod($path, 0600);
    } finally {
        if (is_file($tmp)) { @unlink($tmp); }
    }
}

function lp_text(mixed $value, int $max, string $field, bool $required = false): string
{
    if (!is_string($value)) {
        throw new LeadPilotError($field . ' must be text.', 422, 'invalid_input');
    }
    $value = trim($value);
    if (strlen($value) > $max || ($required && $value === '')) {
        throw new LeadPilotError($field . ' has an invalid length.', 422, 'invalid_input');
    }
    return $value;
}

/** @return array<string,mixed> */
function lp_validate_template(array $input, ?array $existing = null): array
{
    $name = lp_text($input['name'] ?? '', 100, 'Template name', true);
    $body = lp_text($input['body'] ?? '', 3000, 'Template body', true);
    $keywords = lp_text($input['keywords'] ?? '', 500, 'Matching keywords');
    $description = lp_text($input['description'] ?? '', 240, 'Description');
    $requiredText = lp_text($input['required_text'] ?? '', 1200, 'Required wording');
    $active = $input['active'] ?? true;
    if (!is_bool($active)) { throw new LeadPilotError('Template active must be a boolean.', 422, 'invalid_input'); }
    return [
        'id' => $existing['id'] ?? 'custom-' . bin2hex(random_bytes(6)),
        'name' => $name, 'description' => $description, 'keywords' => $keywords,
        'body' => $body, 'required_text' => $requiredText, 'active' => $active,
        'version' => ($existing['version'] ?? 0) + 1, 'updated_at' => gmdate('c'),
    ];
}

/**
 * Ensures conversations cannot hide oversized or unsupported messages.
 * @return array{name:string,company:string,channel:string,stage:string,conversation_id:string,messages:list<array<string,string>>}
 */
function lp_validate_conversation(array $input): array
{
    $raw = $input['messages'] ?? null;
    if (!is_array($raw) || count($raw) === 0 || count($raw) > 50 || !array_is_list($raw)) {
        throw new LeadPilotError('Provide between 1 and 50 conversation messages.', 422, 'invalid_messages');
    }
    $messages = []; $total = 0; $inbound = 0;
    foreach ($raw as $item) {
        if (!is_array($item)) {
            throw new LeadPilotError('Each message must be an object.', 422, 'invalid_messages');
        }
        $direction = $item['direction'] ?? '';
        if (!in_array($direction, ['inbound','outbound'], true)) {
            throw new LeadPilotError('Message direction must be inbound or outbound.', 422, 'invalid_messages');
        }
        $body = lp_text($item['body'] ?? '', 4000, 'Message body', true);
        $total += strlen($body);
        if ($direction === 'inbound') { $inbound++; }
        $messages[] = [
            'id' => lp_text($item['id'] ?? '', 100, 'Message id'),
            'direction' => $direction, 'body' => $body,
            'at' => lp_text($item['at'] ?? '', 40, 'Message date'),
        ];
    }
    if (!$inbound || $total > 30000) {
        throw new LeadPilotError('Conversation needs an inbound message and must be under 30KB.', 422, 'invalid_messages');
    }
    $channel = lp_text($input['channel'] ?? 'auto', 30, 'Channel');
    if (!in_array($channel, ['auto','email','whatsapp','website','portal','chat'], true)) {
        throw new LeadPilotError('Unsupported channel.', 422, 'invalid_channel');
    }
    return [
        'name' => lp_text($input['name'] ?? '', 100, 'Lead name'),
        'company' => lp_text($input['company'] ?? '', 100, 'Company'),
        'stage' => lp_text($input['stage'] ?? 'New lead', 60, 'Lead stage'),
        'channel' => $channel,
        'conversation_id' => lp_text($input['conversation_id'] ?? 'manual', 100, 'Conversation ID'),
        'messages' => $messages,
    ];
}

function lp_transcript(array $messages): string
{
    $lines = [];
    foreach ($messages as $m) {
        $lines[] = strtoupper($m['direction']) . ($m['at'] !== '' ? ' (' . $m['at'] . ')' : '') . ': ' . $m['body'];
    }
    return implode("\n\n", $lines);
}

/** @return list<string> */
function lp_keywords(string $keywords): array
{
    return array_values(array_filter(array_map('trim', explode(',', lp_lower($keywords)))));
}

function lp_lower(string $s): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
}

function lp_has_keyword(string $message, string $keyword): bool
{
    $word = trim($keyword);
    return $word !== '' && preg_match('/(?<![\\p{L}\\p{N}])' . preg_quote($word, '/') . '(?![\\p{L}\\p{N}])/ui', $message) === 1;
}

/**
 * Small transparent fallback for when AI classification isn't available.
 * Score indicates *signals*, not a calibrated probability of conversion.
 * @param list<array<string,mixed>> $templates
 * @return array<string,mixed>
 */
function lp_rule_analysis(array $conversation, array $templates): array
{
    $inbound = array_values(array_filter($conversation['messages'], static fn ($m) => $m['direction'] === 'inbound'));
    $full = lp_lower(implode(' ', array_column($inbound, 'body')));
    $latest = lp_lower((string) end($inbound)['body']);
    $candidates = [];
    foreach ($templates as $t) {
        if (!($t['active'] ?? false)) { continue; }
        $score = 0;
        foreach (array_filter(array_map('trim', explode(',', lp_lower($t['keywords'] ?? '')))) as $term) {
            if (lp_has_keyword($full, $term)) {
                $score += strlen($term) >= 7 ? 3 : 2;
                if (lp_has_keyword($latest, $term)) { $score += 1; }
            }
        }
        if ($score > 0) { $candidates[] = ['id'=>$t['id'], 'name'=>$t['name'], 'score'=>$score]; }
    }
    usort($candidates, static fn($a,$b) => $b['score'] <=> $a['score']);
    $selected = [];
    foreach ($candidates as $candidate) {
        if (count($selected) >= 6) { break; }
        if ($candidate['id'] === 'general' && count($candidates) > 1) { continue; }
        $selected[] = $candidate['id'];
    }
    if (!$selected) {
        foreach ($templates as $t) {
            if (($t['id'] ?? '') === 'general' && ($t['active'] ?? false)) { $selected = ['general']; break; }
        }
    }
    if (!$selected) { $selected = [$templates[0]['id'] ?? '']; }
    $intents = [];
    foreach ($selected as $id) {
        foreach ($templates as $t) {
            if ($t['id'] === $id) { $intents[] = $t['name']; break; }
        }
    }
    $score = 20; $signals = [];
    $isClient = stripos($conversation['stage'], 'client') !== false;
    if ($isClient) { $score = 12; $signals[] = 'Existing-client service conversation, not a new sales opportunity'; }
    if (preg_match('/\b(register|incorporat|set up|setup|start|move|relocat|establish|تأسيس)\b/ui', $full)) {
        $score += 18; $signals[] = 'Clear service interest';
    }
    if (preg_match('/\b(dubai|uk|singapore|germany|europe|canada|uae|india)\b/i', $full)) {
        $score += 10; $signals[] = 'Destination or target market specified';
    }
    if (preg_match('/\b(quote|quotation|pricing|cost|fees|budget|proposal)\b/i', $full)) {
        $score += 14; $signals[] = 'Asked for commercial details';
    }
    if (preg_match('/\b(next (week|month)|this week|today|tomorrow|by friday|urgent|asap|within \d+ days|timeline|start date)\b/i', $full)) {
        $score += 15; $signals[] = 'Time-sensitive request';
    }
    if (preg_match('/\b(call|schedule|meeting|consultation|contact)\b/i', $full)) {
        $score += 12; $signals[] = 'Requested direct engagement';
    }
    if (count($inbound) >= 2) { $score += 7; $signals[] = 'Follow-up engagement'; }
    $score = min(95, max(5, $score));
    $frustrated = (bool) preg_match('/\b(unhappy|frustrat\w*|disappoint\w*|complain\w*|delay\w*|twice|urgent|unacceptable)\b/i', $full);
    $urgent = (bool) preg_match('/\b(urgent|today|tomorrow|asap|by friday|this week)\b/i', $latest);
    $stageNote = $isClient ? 'Treat as a support case; warmth is not a customer-satisfaction measure.' : 'Indicative interest based on explicit messages, not a conversion probability.';
    $hasTeamReply = count(array_filter($conversation['messages'], static fn($m) => $m['direction'] === 'outbound')) > 0;
    $needsReply = end($conversation['messages'])['direction'] === 'inbound';
    $requests = array_map(static fn($label) => [
        'label' => $label,
        'status' => $hasTeamReply ? 'Needs review' : 'Open',
    ], $intents);
    return [
        'intents' => $intents,
        'requests' => $requests,
        'needs_reply' => $needsReply,
        'template_ids' => array_values(array_filter($selected)),
        'warmth' => $score,
        'warmth_label' => $score >= 70 ? 'Hot' : ($score >= 40 ? 'Warm' : 'Early'),
        'warmth_description' => implode('. ', array_slice($signals, 0, 3)) . ($signals ? '. ' : '') . $stageNote,
        'signals' => $signals,
        'urgency' => $urgent ? 'High' : 'Normal',
        'sentiment' => $frustrated ? 'Concerned / negative' : 'Neutral / engaged',
        'next_action' => !$needsReply ? 'Latest message is from the team; confirm a new reply is needed.' : ($frustrated ? 'Request a human review before responding.' : ($urgent ? 'Prioritise a human follow-up.' : 'Clarify missing details before committing.')),
        'confidence' => $candidates ? 'Medium' : 'Low',
        'selection_method' => 'Rules fallback',
        'needs_review' => !$needsReply || $frustrated || count($candidates) === 0,
        'candidates' => array_slice($candidates, 0, 5),
    ];
}

/** @return array<string,string> */
function lp_placeholders(array $conversation, array $analysis): array
{
    $text = implode(' ', array_column($conversation['messages'], 'body'));
    $destination = 'your preferred jurisdiction';
    foreach (['Dubai','Singapore','United Kingdom','UK','Germany','Canada','UAE','India'] as $place) {
        if (stripos($text, $place) !== false) { $destination = $place; break; }
    }
    return [
        '{{customer_name}}' => $conversation['name'] ?: 'there',
        '{{company_name}}' => $conversation['company'] ?: 'your organisation',
        '{{destination}}' => $destination,
        '{{service}}' => $analysis['intents'][0] ?? 'your enquiry',
    ];
}

/** @param list<array<string,mixed>> $templates @return list<array<string,mixed>> */
function lp_selected_templates(array $templates, array $ids): array
{
    $selected = [];
    foreach (array_values(array_unique($ids)) as $id) {
        foreach ($templates as $template) {
            if ($template['id'] === $id && $template['active']) { $selected[] = $template; break; }
        }
    }
    return array_slice($selected, 0, 6);
}

function lp_channel(array $conversation): string
{
    return $conversation['channel'] === 'auto' ? 'chat' : $conversation['channel'];
}

function lp_email_subject(array $conversation, array $analysis): string
{
    foreach ($conversation['messages'] as $message) {
        if ($message['direction'] === 'inbound' && preg_match('/^Subject:\\s*(.{1,160})/im', $message['body'], $match)) {
            return 'Re: ' . preg_replace('/^(?:re:\\s*)+/i', '', trim($match[1]));
        }
    }
    return 'Re: ' . ($analysis['intents'][0] ?? 'Your enquiry');
}

/** @param list<array<string,mixed>> $templates @return array{subject:?string,reply:string} */
function lp_template_reply(array $conversation, array $analysis, array $templates): array
{
    $selected = lp_selected_templates($templates, $analysis['template_ids'] ?? []);
    if (!$selected) { throw new LeadPilotError('No matching active template. Choose one manually.', 422, 'no_template'); }
    $vars = lp_placeholders($conversation, $analysis);
    $name = $conversation['name'] !== '' ? explode(' ', $conversation['name'])[0] : 'there';
    $channel = lp_channel($conversation);
    $email = $channel === 'email';
    $sections = [];
    foreach ($selected as $t) {
        $sections[] = strtr($t['body'], $vars);
    }
    $required = [];
    foreach ($selected as $t) {
        if (trim($t['required_text']) !== '') { $required[] = strtr($t['required_text'], $vars); }
    }
    $greeting = $email ? 'Dear ' . $name . ',' : 'Hi ' . $name . ',';
    $reply = $greeting . "\n\n" . implode("\n\n", $sections);
    if ($required) { $reply .= "\n\n" . implode("\n", $required); }
    if ($email) { $reply .= "\n\nKind regards,\nCustomer Services Team"; }
    $subject = $email ? lp_email_subject($conversation, $analysis) : null;
    return ['subject'=>$subject, 'reply'=>$reply];
}

import { TEMPLATE_ICONS } from './config.js';

export async function request(url, options = {}) {
  const response = await fetch(url, options);
  let data;
  try { data = await response.json(); }
  catch (_) { throw new Error('The PHP server returned an invalid response.'); }
  if (!response.ok) throw new Error(data.error || 'Request failed.');
  return data;
}

export function post(url, data) {
  return request(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
}

export function cls(...names) { return names.filter(Boolean).join(' '); }
export function initial(name) { return (name || '?').trim().split(/\s+/).slice(0, 2).map(s => s[0].toUpperCase()).join(''); }
export function shortTime(value) { try { return new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); } catch (_) { return ''; } }
export function shortDate(value) { try { return new Date(value).toLocaleDateString([], { month: 'short', day: 'numeric' }); } catch (_) { return ''; } }
export function nameOf(conversation) { return conversation?.name || 'Unnamed prospect'; }
export function getPreview(conversation) { return [...(conversation.messages || [])].reverse().find(message => message.direction === 'inbound')?.body || 'No incoming message'; }

export function payloadFor(conversation, channel) {
  return {
    name: conversation.name,
    company: conversation.company,
    stage: conversation.stage,
    conversation_id: conversation.id,
    channel: channel === 'auto' ? conversation.channel : channel,
    messages: conversation.messages,
  };
}

export function templateIcon(id) { return TEMPLATE_ICONS[id] || '▣'; }

export function requestKind(label) {
  const value = String(label || '').toLowerCase();
  if (/visa|relocat|immigration|residen/.test(value)) return 'visa';
  if (/pric|cost|quot|fee|payment/.test(value)) return 'pricing';
  if (/document|paper|proof|requirement/.test(value)) return 'documentation';
  if (/consult|call|callback|meeting|speak/.test(value)) return 'consultation';
  if (/company|business|registr|incorporat|formation/.test(value)) return 'incorporation';
  return 'general';
}

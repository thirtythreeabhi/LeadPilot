export const MODES = [
  { id: 'template', title: 'Template only', hint: 'Approved wording · works offline' },
  { id: 'hybrid', title: 'AI + template', hint: 'Personalised · retains required clauses' },
  { id: 'ai', title: 'AI generated', hint: 'Fresh draft · no template needed' },
];

export const CHANNELS = ['auto', 'email', 'whatsapp', 'website', 'portal', 'chat'];

export const TEMPLATE_ICONS = {
  incorporation: '🏢', visa: '🛂', pricing: '💰', consultation: '📞',
  documentation: '📄', payments: '💳', followup: '🔁', status: '📋',
  complaint: '⚠️', partnership: '🤝', general: '💬',
};

export const LEAD_STAGES = ['New lead', 'Contacted', 'Qualified', 'Existing client'];

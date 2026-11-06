import React from 'react';

export default function TemplateEditorDialog({ template, setTemplate, saving, alert, onClose, onSave }) {
  if (!template) return null;
  const update = (field, value) => setTemplate(current => ({ ...current, [field]: value }));

  return (
    <div className="modal-backdrop" onMouseDown={event => { if (event.target === event.currentTarget) onClose(); }}>
      <div className="modal template-modal" role="dialog" aria-modal="true" aria-labelledby="template-title">
        <div className="modal-header"><div><p className="eyebrow">TEMPLATE EDITOR</p><h2 id="template-title">{template.id ? 'Edit template' : 'New template'}</h2></div><button className="close-button" onClick={onClose}>×</button></div>
        <div className="modal-body">
          {alert && <div className="modal-alert">{alert}</div>}
          <div className="double-field">
            <label className="control-label">Template name<input maxLength={100} value={template.name} onChange={event => update('name', event.target.value)}/></label>
            <label className="control-label">Version<input disabled value={template.version || 'New'}/></label>
          </div>
          <label className="control-label">Description<input maxLength={240} value={template.description} onChange={event => update('description', event.target.value)}/></label>
          <label className="control-label">Trigger keywords <small>Comma-separated. Used for fallback matching.</small><textarea rows={2} maxLength={500} value={template.keywords} onChange={event => update('keywords', event.target.value)}/></label>
          <label className="control-label">Approved response body <small>Placeholders: customer_name, company_name, destination, service</small><textarea rows={5} maxLength={3000} value={template.body} onChange={event => update('body', event.target.value)}/></label>
          <label className="control-label">Mandatory wording <small>Appended verbatim in template and hybrid modes</small><textarea rows={3} maxLength={1200} value={template.required_text} onChange={event => update('required_text', event.target.value)}/></label>
          <label className="switch-label"><input type="checkbox" checked={!!template.active} onChange={event => update('active', event.target.checked)}/> Template active for matching</label>
        </div>
        <div className="modal-actions"><button className="button secondary" onClick={onClose}>Cancel</button><button className="button primary" disabled={saving} onClick={onSave}>{saving ? 'Saving…' : 'Save template'}</button></div>
      </div>
    </div>
  );
}

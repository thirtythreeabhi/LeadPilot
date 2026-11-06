import React from 'react';
import { CHANNELS } from '../config.js';
import CustomSelect from './CustomSelect.jsx';

export default function NewConversationDialog({ lead, setLead, onClose, onCreate }) {
  return (
    <div className="modal-backdrop" onMouseDown={event => { if (event.target === event.currentTarget) onClose(); }}>
      <div className="modal" role="dialog" aria-modal="true" aria-labelledby="new-title">
        <div className="modal-header">
          <div><p className="eyebrow">NEW TEST THREAD</p><h2 id="new-title">Create a conversation</h2></div>
          <button className="close-button" onClick={onClose}>×</button>
        </div>
        <div className="modal-body">
          <label className="control-label">Lead name<input maxLength={100} value={lead.name} onChange={event => setLead(value => ({ ...value, name: event.target.value }))} placeholder="e.g. Jessica Lee"/></label>
          <label className="control-label">Company<input maxLength={100} value={lead.company} onChange={event => setLead(value => ({ ...value, company: event.target.value }))} placeholder="Optional"/></label>
          <label className="control-label">Inbound channel<CustomSelect ariaLabel="Inbound channel" value={lead.channel} options={CHANNELS.filter(channel => channel !== 'auto').map(channel => ({ value: channel, label: channel }))} onChange={channel => setLead(value => ({ ...value, channel }))}/></label>
          <label className="control-label">First inbound message<textarea rows={5} maxLength={4000} value={lead.body} onChange={event => setLead(value => ({ ...value, body: event.target.value }))} placeholder="Paste or type a test enquiry..."/></label>
        </div>
        <div className="modal-actions"><button className="button secondary" onClick={onClose}>Cancel</button><button className="button primary" onClick={onCreate}>Create conversation →</button></div>
      </div>
    </div>
  );
}

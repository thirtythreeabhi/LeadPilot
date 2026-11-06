import React from 'react';
import { cls } from '../utils.js';

export default function Sidebar({ page, setPage, conversationCount, templateCount }) {
  return (
    <aside className="sidebar">
      <div className="brand">
        <div className="brand-logo">L<span>✳</span></div>
        <div><strong>LeadPilot</strong><small>Intelligent reply workspace</small></div>
      </div>
      <div className="workspace-switch">
        <span className="workspace-avatar">OD</span>
        <span><b>OnDemand sandbox</b><small>Demo organisation</small></span>
        <span className="muted-symbol">⌄</span>
      </div>
      <nav className="nav-stack" aria-label="Workspace">
        <button className={cls('nav-item', page === 'inbox' && 'active')} onClick={() => setPage('inbox')}>
          <span className="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5h16v13H4z"/><path d="M4 13h4l1.5 2h5L16 13h4"/></svg></span>
          Inbox <span className="nav-count">{conversationCount}</span>
        </button>
        <button className={cls('nav-item', page === 'templates' && 'active')} onClick={() => setPage('templates')}>
          <span className="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span>
          Templates <span className="nav-count">{templateCount}</span>
        </button>
      </nav>
      <div className="sidebar-bottom"><span className="demo-dot"/><div><strong>Local demo mode</strong><small>Fictional leads · no CRM sync</small></div></div>
    </aside>
  );
}

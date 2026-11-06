import React from 'react';
import { cls, shortDate, templateIcon } from '../utils.js';

export default function TemplateLibrary({ templates, filter, onFilterChange, onCreate, onEdit, onReset }) {
  const visibleTemplates = templates.filter(template => (
    `${template.name} ${template.description} ${template.keywords}`.toLowerCase().includes(filter.toLowerCase())
  ));
  const activeCount = templates.filter(template => template.active).length;

  return (
    <section className="templates-page">
      <div className="page-title-row">
        <div>
          <p className="eyebrow">CONTENT SYSTEM</p>
          <h1>Template library</h1>
          <p>Manage approved responses, trigger keywords, and mandatory wording. The AI can match templates to conversation intent.</p>
        </div>
        <button className="button primary" onClick={onCreate}>＋ New template</button>
      </div>
      <div className="template-toolbar">
        <input value={filter} onChange={event => onFilterChange(event.target.value)} placeholder="Search templates..." aria-label="Search templates"/>
        <div>{activeCount} active · {templates.length} total</div>
      </div>
      <div className="template-grid">
        {visibleTemplates.map(template => (
          <article className="template-card" key={template.id}>
            <div className="template-head">
              <span className="template-icon" aria-hidden="true">{templateIcon(template.id)}</span>
              <span className={cls('status-tag', !template.active && 'disabled')}>{template.active ? 'Active' : 'Inactive'}</span>
            </div>
            <h3>{template.name}</h3>
            <p>{template.description || 'No description provided.'}</p>
            <div className="template-keywords">
              {(template.keywords || '').split(',').slice(0, 3).map((word, index) => <span key={index}>{word.trim()}</span>)}
            </div>
            <div className="template-footer">
              <small>v{template.version} · {shortDate(template.updated_at)}</small>
              <button className="button subtle small" onClick={() => onEdit(template)}>Edit <span>→</span></button>
            </div>
          </article>
        ))}
      </div>
      <div className="page-footnote">
        All sample templates are editable. Changes are stored in <code>data/templates.json</code> and revisions are logged in <code>data/template_history.jsonl</code>.{' '}
        <button className="text-button" onClick={onReset}>Reset template library</button>
      </div>
    </section>
  );
}

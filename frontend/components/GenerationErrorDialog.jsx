import React from 'react';

export default function GenerationErrorDialog({ message, onClose, onRetry }) {
  if (!message) return null;
  return (
    <div className="dialog-backdrop" role="presentation" onMouseDown={event => { if (event.target === event.currentTarget) onClose(); }}>
      <section className="error-dialog" role="alertdialog" aria-modal="true" aria-labelledby="generation-error-title" aria-describedby="generation-error-description">
        <div className="error-dialog-icon" aria-hidden="true">!</div>
        <p className="eyebrow">REPLY ASSISTANT</p>
        <h2 id="generation-error-title">Couldn’t generate this reply</h2>
        <p id="generation-error-description" className="error-dialog-message">{message}</p>
        <p className="error-dialog-hint">Your conversation is still here. You can try again, or switch to Template only mode.</p>
        <div className="error-dialog-actions">
          <button className="button secondary" onClick={onClose}>Close</button>
          <button className="button primary" onClick={onRetry}>Try again <span>→</span></button>
        </div>
      </section>
    </div>
  );
}

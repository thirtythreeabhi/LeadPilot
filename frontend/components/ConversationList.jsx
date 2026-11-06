import React from 'react';
import { cls, getPreview, initial, nameOf, shortDate } from '../utils.js';

export default function ConversationList({ threads, filtered, selected, loading, search, onSearch, onReset, onCreate, onSelect }) {
  return (
    <div className="conversation-list panel">
      <div className="list-header">
        <div><p className="eyebrow">INBOX</p><h2>Conversations <span className="light-count">{threads.length}</span></h2></div>
        <button className="round-button" title="New conversation" aria-label="New conversation" onClick={onCreate}>＋</button>
      </div>
      <label className="searchbox"><span>⌕</span><input type="search" value={search} placeholder="Search conversations" onChange={event => onSearch(event.target.value)}/></label>
      <div className="list-caption"><span>ALL CONVERSATIONS</span><button onClick={onReset} title="Restore demo conversations">↻ Reset demo</button></div>
      <div className="thread-list">
        {loading ? <p className="center-message">Loading sample inbox…</p> : filtered.length ? filtered.map(conversation => (
          <button key={conversation.id} onClick={() => onSelect(conversation.id)} className={cls('thread-item', selected?.id === conversation.id && 'selected')}>
            <div className="thread-avatar">{initial(conversation.name)}</div>
            <div className="thread-content">
              <div className="thread-meta"><strong>{nameOf(conversation)}</strong><span>{shortDate(conversation.updated_at)}</span></div>
              <div className="thread-company">{conversation.company || 'Individual enquiry'} <span>·</span> {conversation.channel}</div>
              <p>{getPreview(conversation)}</p>
              <div className="thread-tags"><span>{conversation.stage}</span><span>{conversation.messages.length} messages</span></div>
            </div>
          </button>
        )) : <p className="center-message">No conversations match.</p>}
      </div>
      <div className="list-footer"><span>◉</span> Sample data — not connected to Salesforce</div>
    </div>
  );
}

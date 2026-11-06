import React, { useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { CHANNELS, LEAD_STAGES, MODES } from './config.js';
import CustomSelect from './components/CustomSelect.jsx';
import Sidebar from './components/Sidebar.jsx';
import TemplateLibrary from './components/TemplateLibrary.jsx';
import GenerationErrorDialog from './components/GenerationErrorDialog.jsx';
import NewConversationDialog from './components/NewConversationDialog.jsx';
import TemplateEditorDialog from './components/TemplateEditorDialog.jsx';
import ConversationList from './components/ConversationList.jsx';
import { cls, getPreview, initial, nameOf, payloadFor, post, request, requestKind, shortTime, templateIcon } from './utils.js';

function App() {
  const [page, setPage] = useState('inbox');
  const [assistantTab, setAssistantTab] = useState('overview');
  const [seed, setSeed] = useState([]);
  const [threads, setThreads] = useState([]);
  const [templates, setTemplates] = useState([]);
  const [ai, setAi] = useState({configured:false, provider:'openrouter'});
  const [activeId, setActiveId] = useState('');
  const [loading, setLoading] = useState(true);
  const [alert, setAlert] = useState('');
  const [copyNotice, setCopyNotice] = useState(false);
  const [generationError, setGenerationError] = useState('');
  const [search, setSearch] = useState('');
  const [analysis, setAnalysis] = useState(null);
  const [analysisBusy, setAnalysisBusy] = useState(false);
  const [mode, setMode] = useState('ai');
  const [tone, setTone] = useState('Professional');
  const [customInstructions, setCustomInstructions] = useState('');
  const [channel, setChannel] = useState('auto');
  const [manualIds, setManualIds] = useState(null);
  const [aiMatching, setAiMatching] = useState(false);
  const [draft, setDraft] = useState(null);
  const [responseHistory, setResponseHistory] = useState([]);
  const [revisionSuggestions, setRevisionSuggestions] = useState('');
  const [generating, setGenerating] = useState(false);
  const [saving, setSaving] = useState(false);
  const [newMessage, setNewMessage] = useState('');
  const [direction, setDirection] = useState('inbound');
  const [isNewOpen, setIsNewOpen] = useState(false);
  const [newLead, setNewLead] = useState({name:'', company:'',channel:'email',body:''});
  const [templateEditor, setTemplateEditor] = useState(null);
  const [templateSaving, setTemplateSaving] = useState(false);
  const [templateFilter, setTemplateFilter] = useState('');
  const generationSeq = useRef(0);

  const selected = threads.find(c => c.id === activeId) || threads[0] || null;
  const filtered = useMemo(() => threads.filter(c => (
    [c.name, c.company, c.channel, getPreview(c)].join(' ').toLowerCase().includes(search.toLowerCase())
  )), [threads,search]);
  const activeTemplates = templates.filter(t => t.active);
  const selectedIds = manualIds === null ? (analysis?.template_ids || []) : manualIds;

  useEffect(() => {
    request('/api/bootstrap.php').then(data => {
      setSeed(data.conversations);
      setThreads(data.conversations);
      setTemplates(data.templates);
      setAi(data.ai);
      setActiveId(data.conversations[0]?.id || '');
    }).catch(err => setAlert(err.message)).finally(() => setLoading(false));
  }, []);

  useEffect(() => {
    setDraft(null);
    setResponseHistory([]);
    setRevisionSuggestions('');
    setAssistantTab('overview');
    setGenerating(false);
    setManualIds(null);
    setChannel('auto');
    setAiMatching(false);
    setNewMessage('');
    setAlert('');
    generationSeq.current++;
  }, [activeId]);

  const transcriptKey = selected ? JSON.stringify([selected.id, selected.messages, selected.stage, selected.channel, templates.map(t => [t.id,t.version,t.active])]) : '';
  useEffect(() => {
    if (!selected) return;
    const controller = new AbortController();
    setAnalysis(null);
    setAnalysisBusy(true);
    fetch('/api/analyze.php', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({...payloadFor(selected, 'auto'), try_ai:false}), signal:controller.signal
    }).then(async response => {
      const data=await response.json();
      if (!response.ok) throw new Error(data.error || 'Analysis failed.');
      if (!controller.signal.aborted) setAnalysis(data.analysis);
    }).catch(err => { if (err.name !== 'AbortError') setAlert(err.message); })
      .finally(() => { if (!controller.signal.aborted) setAnalysisBusy(false); });
    return () => controller.abort();
  }, [transcriptKey]);

  function changeThread(id) { setActiveId(id); setPage('inbox'); }
  function updateThread(patch) {
    if (!selected) return;
    generationSeq.current++;
    setGenerating(false);
    setThreads(list => list.map(c => c.id === selected.id ? {...c, ...patch} : c));
    setAnalysis(null);
    setDraft(null); setManualIds(null);
    setResponseHistory([]);
    setAssistantTab('overview');
  }
  function addMessage() {
    const body = newMessage.trim();
    if (selected.messages.length >= 50) { setAlert('Maximum 50 messages per thread.'); return; }
    if (!body || body.length > 4000) { setAlert('Message must contain 1–4,000 characters.'); return; }
    const at = new Date().toISOString();
    const nextMessages = [...selected.messages, {id:'m'+(selected.messages.length+1)+'-'+Date.now(), direction, body, at}];
    updateThread({messages:nextMessages, updated_at:at});
    setNewMessage('');
    setAlert('');
  }
  function createLead() {
    if (newLead.body.trim().length < 1 || newLead.body.trim().length > 4000) { setAlert('Enter an initial customer message.'); return; }
    const id='local-'+Date.now();
    const at=new Date().toISOString();
    const lead={id,name:newLead.name.trim(),company:newLead.company.trim(),channel:newLead.channel,stage:'New lead',updated_at:at,
      messages:[{id:'m1',direction:'inbound',body:newLead.body.trim(),at}]};
    setThreads(list=>[lead,...list]); setActiveId(id); setPage('inbox'); setNewLead({name:'',company:'',channel:'email',body:''});
    setIsNewOpen(false); setAlert('');
  }
  function resetDemo() {
    if (!window.confirm('Discard in-browser demo changes and restore the original fictional inbox? Saved drafts and custom templates are unaffected.')) return;
    generationSeq.current++;
    setGenerating(false);
    setThreads(seed.map(c => ({...c, messages:c.messages.map(m=>({...m}))})));
    setActiveId(seed[0]?.id || '');
    setDraft(null); setResponseHistory([]); setRevisionSuggestions(''); setManualIds(null); setAssistantTab('overview'); setAlert('');
  }
  async function analyzeWithAi() {
    if (!selected || analysisBusy) return;
    setAnalysisBusy(true);setAlert('');
    try { const data = await post('/api/analyze.php', {...payloadFor(selected,channel),try_ai:true});setAnalysis(data.analysis);setManualIds(null);setDraft(null);setResponseHistory([]);setAiMatching(true); }
    catch(err) { setAlert(err.message); }
    finally { setAnalysisBusy(false); }
  }
  async function generate() {
    if (!selected || generating) return;
    if (manualIds !== null && manualIds.length === 0 && mode !== 'ai') {
      setAlert('Choose at least one template or switch back to automatic matching.'); return;
    }
    const seq = ++generationSeq.current;
    setGenerating(true);setAlert('');setDraft(null);setResponseHistory([]);setRevisionSuggestions('');
    try {
      const response = await post('/api/generate.php', {
        ...payloadFor(selected,channel),mode,tone,custom_instructions:customInstructions,try_ai:aiMatching && manualIds===null,
        ...(manualIds !== null ? {template_ids:manualIds} : {})
      });
      if (generationSeq.current !== seq) return;
      setAnalysis(response.analysis);
      const firstDraft={id:response.id,subject:response.subject || '',reply:response.reply,model:response.model,status:'saved',generatedMode:mode};
      setDraft(firstDraft);setResponseHistory([firstDraft]);
      setAssistantTab('draft');
    } catch(err) {
      if (generationSeq.current === seq) setGenerationError(err.message);
    } finally { if (generationSeq.current === seq) setGenerating(false); }
  }
  async function reviseDraft() {
    if (!selected || !draft || generating) return;
    const suggestions=revisionSuggestions.trim();
    if (!suggestions) { setAlert('Add a suggestion for the next response.'); return; }
    const seq=++generationSeq.current;
    setGenerating(true);setAlert('');
    try {
      const response=await post('/api/generate.php', {
        ...payloadFor(selected,channel),mode,tone,custom_instructions:customInstructions,
        revision_suggestions:suggestions,previous_reply:draft.reply,
        try_ai:aiMatching && manualIds===null,
        ...(manualIds !== null ? {template_ids:manualIds} : {})
      });
      if (generationSeq.current !== seq) return;
      const nextDraft={id:response.id,subject:response.subject || '',reply:response.reply,model:response.model,status:'saved',generatedMode:mode};
      setAnalysis(response.analysis);setDraft(nextDraft);setResponseHistory(items=>[...items,nextDraft]);
      setRevisionSuggestions('');setAssistantTab('draft');
    } catch(err) {
      if (generationSeq.current === seq) setGenerationError(err.message);
    } finally { if (generationSeq.current === seq) setGenerating(false); }
  }
  function editDraft(patch) {
    if (!draft) return;
    setDraft({...draft,...patch,status:'unsaved'});
    setResponseHistory(items=>items.map((item,index)=>index===items.length-1 && item.id===draft.id ? {...item,...patch,status:'unsaved'} : item));
  }
  async function saveReview(status) {
    if (!draft?.id || saving) return;
    setSaving(true);setAlert('');
    try {
      const data=await post('/api/review.php',{id:draft.id,reply:draft.reply,subject:draft.subject,status});
      setDraft(d=>({...d,status:data.status}));
      setResponseHistory(items=>items.map(item=>item.id===draft.id?{...item,status:data.status}:item));
    } catch(err) { setAlert(err.message); }
    finally { setSaving(false); }
  }
  async function copyDraft() {
    if (!draft) return;
    try {
      await navigator.clipboard.writeText((draft.subject ? 'Subject: '+draft.subject+'\n\n' : '')+draft.reply);
      setCopyNotice(true);
      window.setTimeout(() => setCopyNotice(false), 2400);
    } catch(_) { setAlert('Clipboard access was blocked. Select and copy the draft manually.'); }
  }
  async function copyResponse(response) {
    try {
      await navigator.clipboard.writeText((response.subject ? 'Subject: '+response.subject+'\n\n' : '')+response.reply);
      setCopyNotice(true);window.setTimeout(()=>setCopyNotice(false),2400);
    } catch(_) { setAlert('Clipboard access was blocked. Select and copy the response manually.'); }
  }
  function changeTemplateSelection(id) {
    setManualIds(old => {
      const now = old === null ? (analysis?.template_ids || []) : old;
      return now.includes(id) ? now.filter(t => t !== id) : [...now,id].slice(0,6);
    });
    setDraft(null);
  }
  function editTemplate(t) {
    setTemplateEditor(t ? {...t} : {name:'',description:'',keywords:'',body:'',required_text:'',active:true});
    setAlert('');
  }
  async function saveTemplate() {
    if (!templateEditor || templateSaving) return;
    setTemplateSaving(true);setAlert('');
    try {
      const data=await post('/api/templates.php',{action:'save',template:templateEditor});
      setTemplates(data.templates);setTemplateEditor(null);
      setDraft(null);setManualIds(null);
      setAlert('Template saved. Version history recorded locally.');
    } catch(err) { setAlert(err.message); }
    finally { setTemplateSaving(false); }
  }
  async function resetTemplates() {
    if (!window.confirm('Reset all templates to their original sample versions? Your template edit history will remain available in data/.')) return;
    try {
      const data=await post('/api/templates.php',{action:'reset'});
      setTemplates(data.templates);setTemplateEditor(null);setAlert('Templates restored.');
    } catch(err) { setAlert(err.message); }
  }

  return <div className="app">
    <Sidebar page={page} setPage={setPage} conversationCount={threads.length} templateCount={templates.length} />
    <main className="main">
      <header className="topbar">
        <div className="breadcrumbs">Workspace <span>/</span> <b>{page === 'inbox' ? 'Lead inbox' : 'Template library'}</b></div>
        <div className="top-actions"><span className={cls('connection-badge',ai.configured?'online':'offline')}><span className="connection-dot"></span>{ai.configured ? 'AI configured' : 'Templates available offline'}</span><span className="avatar">LP</span></div>
      </header>
      {copyNotice && <div className="copy-toast" role="status"><span aria-hidden="true">✓</span> Draft copied</div>}
      {alert && <div className="notice" role="status"><span>{alert}</span><button aria-label="Dismiss message" onClick={()=>setAlert('')}>×</button></div>}
      <GenerationErrorDialog message={generationError} onClose={()=>setGenerationError('')} onRetry={()=>{setGenerationError('');generate();}} />
      {page === 'templates'
        ? <TemplateLibrary templates={templates} filter={templateFilter} onFilterChange={setTemplateFilter} onCreate={()=>editTemplate(null)} onEdit={editTemplate} onReset={resetTemplates} />
        : <section className="inbox-layout">
          <ConversationList threads={threads} filtered={filtered} selected={selected} loading={loading} search={search} onSearch={setSearch} onReset={resetDemo} onCreate={()=>setIsNewOpen(true)} onSelect={changeThread} />
          {selected ? <>
            <div className="thread-view panel">
              <div className="conversation-header">
                <div className="contact-avatar">{initial(selected.name)}</div>
                <div className="contact-info"><h2>{nameOf(selected)}</h2><div>{selected.company || 'Individual prospect'} <span>·</span> <select className="stage-select" value={selected.stage} onChange={e=>updateThread({stage:e.target.value})}>{LEAD_STAGES.map(x=><option key={x}>{x}</option>)}</select></div></div>
                <span className="channel-tag">{selected.channel}</span>
              </div>
              <div className="thread-context"><span className="green-dot"></span> Conversation history <span>·</span> {selected.messages.filter(m=>m.direction==='inbound').length} inbound <span>·</span> {selected.messages.length} total</div>
              <div className="messages" key={selected.id}>
                <div className="thread-date"><span>Customer conversation</span></div>
                {selected.messages.map((m,i)=><div className={cls('message-row', m.direction==='outbound'?'outbound':'inbound')} key={m.id||i}>
                  {m.direction==='inbound' && <div className="bubble-avatar">{initial(selected.name)}</div>}
                  <div className="bubble-wrap"><div className="bubble">{m.body}</div><div className="bubble-time">{m.direction==='outbound'?'Team reply':'Customer'} · {shortTime(m.at)}</div></div>
                </div>)}
              </div>
              <div className="append-composer">
                <div className="composer-head"><strong>Add a message to test</strong><select value={direction} onChange={e=>setDirection(e.target.value)} aria-label="Message direction"><option value="inbound">↙ Customer inbound</option><option value="outbound">↗ Previous team reply</option></select></div>
                <textarea rows="3" maxLength="4000" value={newMessage} onChange={e=>setNewMessage(e.target.value)} onKeyDown={e=>{if((e.ctrlKey||e.metaKey) && e.key==='Enter')addMessage();}} placeholder="Add another customer request, a follow-up or an existing team reply…"/>
                <div className="composer-footer"><span>Multiple messages are analysed as one thread · Ctrl+Enter</span><button className="button secondary" onClick={addMessage} disabled={!newMessage.trim() || selected.messages.length>=50}>Add message ↗</button></div>
              </div>
            </div>
            <div className="insights-panel">
              <div className="insight-header"><div><p className="eyebrow">AI-ASSISTED WORKSPACE</p><h2>Reply assistant</h2></div><button className="icon-button" title="Reanalyse conversation" aria-label="Reanalyse conversation" onClick={analyzeWithAi} disabled={analysisBusy}>{analysisBusy?'◌':'↻'}</button></div>
              <div className="assistant-tabbar" role="tablist" aria-label="Reply assistant panels">
                <button id="assistant-tab-overview" role="tab" aria-selected={assistantTab==='overview'} aria-controls="assistant-tab-content" className={cls('assistant-tab',assistantTab==='overview'&&'active')} onClick={()=>setAssistantTab('overview')}>Overview</button>
                <button id="assistant-tab-draft" role="tab" aria-selected={assistantTab==='draft'} aria-controls="assistant-tab-content" className={cls('assistant-tab',assistantTab==='draft'&&'active')} onClick={()=>setAssistantTab('draft')}>Draft {draft&&<span className="draft-indicator" aria-label="Draft available" />}</button>
              </div>
              <div className="assistant-scroll" id="assistant-tab-content" role="tabpanel" aria-labelledby={assistantTab==='overview'?'assistant-tab-overview':'assistant-tab-draft'}>
              {assistantTab==='overview' ? <>
              <section className="insight-card">
                <div className="card-label">RESPONSE SETTINGS</div>
                <label className="control-label">Draft mode</label>
                <div className="mode-options">{MODES.map(m=><button type="button" className={cls('mode-option',mode===m.id&&'chosen')} key={m.id} onClick={()=>{setMode(m.id);setDraft(null);}}>
                  <span className={cls('radio-indicator',mode===m.id && 'active')}></span><span><b>{m.title}</b><small>{m.hint}</small></span>
                </button>)}</div>
                <div className="double-field">
                  <label className="control-label">Channel<CustomSelect ariaLabel="Channel" value={channel} options={CHANNELS.map(c=>({value:c,label:c==='auto'?'Auto ('+selected.channel+')':c}))} onChange={value=>{setChannel(value);setDraft(null);}} /></label>
                  <label className="control-label">Tone<CustomSelect ariaLabel="Tone" value={tone} options={['Professional','Friendly','Concise'].map(t=>({value:t,label:t}))} onChange={value=>{setTone(value);setDraft(null);}} /></label>
                </div>
                <label className="control-label">Custom response instructions <small>Optional · up to 1,200 characters</small><textarea rows="3" maxLength={1200} value={customInstructions} onChange={e=>setCustomInstructions(e.target.value)} placeholder="For example: acknowledge each request, avoid listing documents, and ask only for a suitable callback time."/><small>Used in AI modes. Template only uses approved wording.</small></label>
                <label className="switch-label ai-matching"><input type="checkbox" checked={aiMatching} onChange={e=>setAiMatching(e.target.checked)} />Use AI to choose templates (may take longer)</label>
                <button className="button primary generate-button" disabled={generating||analysisBusy||!analysis||!selected.messages.length} onClick={generate}>{generating?'Generating draft…':'✧ Generate response'} <span>→</span></button>
                <div className="matching-header"><label className="control-label">Matched templates</label><button className="text-button" onClick={()=>setManualIds(null)} disabled={manualIds===null}>Use automatic</button></div>
                <div className="matching-list">
                  {activeTemplates.map(t=><label className="template-choice" key={t.id}>
                    <input type="checkbox" checked={selectedIds.includes(t.id)} onChange={()=>changeTemplateSelection(t.id)} disabled={!selectedIds.includes(t.id)&&selectedIds.length>=6}/>
                    <span className="template-choice-label"><span className="template-choice-icon" aria-hidden="true">{templateIcon(t.id)}</span><span>{t.name}</span></span></label>)}
                </div>
                <p className="method-note">{manualIds===null?(aiMatching?'AI matching requested, with rules fallback.':'Fast keyword matching; turn on AI matching when needed.'):'Manual override: selected templates will be used.'}</p>
              </section>
              <section className="insight-card warmth-card">
                <div className="card-label"><span>LEAD INTELLIGENCE</span><span className="subtle-chip">Internal only</span></div>
                {analysisBusy && !analysis ? <div className="inline-loading">Analysing conversation…</div> : analysis ? <>
                  <div className="warmth-head"><div><span className="warmth-number">{analysis.warmth}<small>%</small></span><div className="warmth-caption">Estimated lead warmth</div></div><span className={cls('warmth-badge',analysis.warmth>=70?'hot':analysis.warmth>=40?'warm':'early')}>{analysis.warmth_label}</span></div>
                  <div className="progress-track"><div style={{width:analysis.warmth+'%'}} /></div>
                  <p className="analysis-description">{analysis.warmth_description}</p>
                  <div className="signal-pair"><span>Urgency</span><strong className={analysis.urgency==='High'?'danger-text':''}>{analysis.urgency}</strong></div>
                  <div className="signal-pair"><span>Sentiment</span><strong className={analysis.sentiment==='Concerned / negative'?'signal-badge sentiment-negative':'signal-badge sentiment-neutral'}>{analysis.sentiment}</strong></div>
                  <div className="signal-pair"><span>Confidence</span><strong className={'signal-badge confidence-'+String(analysis.confidence||'').toLowerCase()}>{analysis.confidence}</strong></div>
                  <div className="analysis-note">Lead warmth is an indicative signal, not a conversion probability.</div>
                </> : <p className="muted">Analysis will appear here.</p>}
              </section>
              <section className="insight-card request-card" tabIndex={0} aria-label="Detected requests. Hover or focus to expand.">
                <div className="card-label"><span>DETECTED REQUESTS</span><span className="subtle-chip">{analysis?.intents?.length || 0}<span className="expand-chevron" aria-hidden="true">⌄</span></span></div>
                <div className="request-reveal">
                  <div className="request-list">{(analysis?.requests || (analysis?.intents || []).map(label=>({label,status:'Needs review'}))).map((x,i)=>{const kind=requestKind(x.label);return <div key={i} className="request-line" data-kind={kind}><span className="request-line-label"><span className="request-icon" aria-hidden="true">{templateIcon(kind)}</span><span>{x.label}</span></span><small>{x.status}</small></div>;})}{!analysis && <span>Loading…</span>}</div>
                  {analysis && !analysis.needs_reply && <p className="needs-reply-warning">Last message is from the team. Check whether a new reply is needed.</p>}
                  <div className="recommendation"><b>Suggested next action</b><p>{analysis?.next_action || 'Review the full conversation.'}</p></div>
                  <button className="inline-link" disabled={analysisBusy} onClick={analyzeWithAi}>✧ {analysisBusy?'Analysing…':'Analyse with AI if available'} <span>→</span></button>
                  <div className="method-note">{analysis?.selection_method || 'Pending'}{analysis?.selection_note ? ' · '+analysis.selection_note : ''}</div>
                </div>
              </section>
              </> : <div className="draft-section">
            <div className="draft-title"><div><p className="eyebrow">RESPONSE COMPOSER</p><h2>Suggested response</h2><p>Review and edit. Nothing is sent automatically.</p></div><div className="draft-controls">{draft&&<span className={cls('draft-state',draft.status==='approved'?'approved':'')}>{draft.status==='approved'?'✓ Approved':draft.status==='unsaved'?'Unsaved edits':draft.status==='edited'?'Revision saved':'Saved draft'}</span>}</div></div>
            {!draft ? <div className="draft-empty"><div className="draft-empty-icon">✧</div><h3>{generating?'Creating a response…':'Your draft will appear here'}</h3><p>{generating?'Analysing requests and preparing a channel-aware reply.':'Select any demo conversation, choose a mode, and generate a response.'}</p></div>
              : <div className="draft-edit">
                {responseHistory.length>0 && <section className="response-history" aria-label="Generated response history">
                  <div className="response-history-heading"><strong>All responses</strong><span>{responseHistory.length} {responseHistory.length===1?'version':'versions'}</span></div>
                  <div className="response-history-list">{responseHistory.map((response,index)=><article className="response-version" key={response.id}>
                    <div className="response-version-heading"><strong>Response {index+1}{index===responseHistory.length-1?' · Latest':''}</strong><button className="text-button" onClick={()=>copyResponse(response)}>⧉ Copy</button></div>
                    {response.subject && <div className="response-version-subject">{response.subject}</div>}
                    <pre className="response-version-body" tabIndex={0}>{response.reply}</pre>
                  </article>)}</div>
                </section>}
                {mode!=='template' && <div className="revision-box">
                  <label className="control-label">Suggest edits<textarea rows="3" maxLength={1200} value={revisionSuggestions} onChange={e=>setRevisionSuggestions(e.target.value)} placeholder="For example: make it warmer, shorten the opening, and ask only for a preferred call time."/></label>
                  <button className="button secondary" disabled={generating||!revisionSuggestions.trim()} onClick={reviseDraft}>{generating?'Generating response…':'✧ Generate revised response'}</button>
                </div>}
                {draft.subject !== '' && <label className="control-label">Email subject<input value={draft.subject} maxLength={240} onChange={e=>editDraft({subject:e.target.value})}/></label>}
                <label className="control-label">Current response<textarea rows="11" maxLength={12000} value={draft.reply} onChange={e=>editDraft({reply:e.target.value})} /></label>
                <div className="draft-meta"><span>{draft.model}</span><span>·</span><span>{draft.generatedMode}</span><span>·</span><span>Local draft ID {draft.id.slice(0,8)}</span></div>
                <div className="draft-actions"><button className="button secondary" onClick={copyDraft}>⧉ Copy</button><button className="button secondary" disabled={saving||!draft.reply.trim()} onClick={()=>saveReview('edited')}>{saving?'Saving…':'Save revision'}</button><button className="button primary" disabled={saving||!draft.reply.trim()} onClick={()=>saveReview('approved')}>✓ Approve draft</button></div>
              </div>}
          </div>}
              </div>
            </div>
          </> : <div className="empty-inbox">Add a conversation to begin.</div>}
        </section>}
      <footer className="app-footer"><span>LeadPilot · React + PHP · local demonstration</span><span>Demo messages are fictional. Review all outputs before external use.</span></footer>
    </main>
    {isNewOpen && <NewConversationDialog lead={newLead} setLead={setNewLead} onClose={()=>setIsNewOpen(false)} onCreate={createLead} />}
    <TemplateEditorDialog template={templateEditor} setTemplate={setTemplateEditor} saving={templateSaving} alert={alert} onClose={()=>setTemplateEditor(null)} onSave={saveTemplate} />
  </div>;
}
createRoot(document.getElementById('root')).render(<App />);

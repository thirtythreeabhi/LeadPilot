# LeadPilot — Conversation-Aware Reply Assistant

A **React + PHP** local demo for the OnDemand junior-developer assignment, **Option 2: Free AI Reply**. It turns whole inbound customer conversations into draft replies using templates, a free OpenRouter model, or an optional locally running Ollama model.

This is a **working mock lead inbox**, not a Salesforce integration. All bundled leads and messages are fictional. **No email, WhatsApp message or CRM update is ever sent.** Approval means *approved locally*, not delivered.

## Quick start — no npm install required

Requirements: **PHP 8.0+**. The committed, prebuilt React bundle is already included.

From the repo root:

    cp .env.example .env
    cd public
    php -S localhost:8000

Visit **http://localhost:8000**.

Pick any demo conversation and click **Generate response**. The default **Template only** mode works even **without** an API key or internet connection. To enable actual AI generation, configure one of the providers below.

Keep .env only in the **project root**, not under public/. Never use the project root as PHP's document root. The app is for **localhost only** (no authentication or rate limiting).

## AI API and why it was chosen

- **Hosted option:** [OpenRouter Chat Completions](https://openrouter.ai/docs/api-reference/chat-completion), with the configurable default liquid/lfm-2.5-2.6b:free. This provides free hosted inference on ordinary computers without running a large model locally. Free model availability and limits change; if the model is unavailable, replace OPENROUTER_MODEL with an available free model.
- **Local option:** [Ollama](https://ollama.com/) using a small local model. Set AI_PROVIDER=ollama, install and run Ollama, and set OLLAMA_MODEL to a model you've pulled. Local inference does not send customer messages to OpenRouter.
- **No paid API is required.** There is no AI key in frontend source, compiled JS, or the tracked repository.
- **AI failure behaviour:** Template-only mode falls back to transparent keyword selection; AI + Template and AI Generated modes report an error if generation fails. The app does not invent a model answer.

For OpenRouter: set AI_PROVIDER=openrouter, paste a free key into OPENROUTER_API_KEY in .env and choose a free serving model. For Ollama: set AI_PROVIDER=ollama and start your local Ollama server.

## What the reviewer can test

1. **Mock inbox:** 10 fictional lead/client conversations covering incorporation, visas, quotations, meetings, status, documents, billing disputes, complaints, partnerships, multilingual chats and generic enquiries.
2. **Whole conversation context:** A thread can contain multiple inbound messages, previous outbound replies, and multiple distinct requests. Add messages to test re-analysis. The thread is sent as one unit.
3. **Lead intelligence:** Internal-only warmth score (0–100), explanation, urgency, sentiment, request list and recommended next action. **Warmth is a rough signal, not a calibrated probability of conversion.** When earlier replies exist, unresolved status is marked for review rather than confidently guessed.
4. **Template library:** Create/edit/disable templates. Configure trigger terms, approved wording and mandatory clauses. Version history is stored locally.
5. **Template matching:** Fast transparent keyword matching works offline. Turn on **Use AI to choose templates** or click **Analyse with AI** to request an LLM-based match; if that classification fails, the interface shows that it fell back to rules. Multiple templates can be used for one conversation. Manual selection is also available.
6. **Draft modes:**
   - **Template only:** Concatenates selected, approved templates, fills known placeholders, and keeps required clauses verbatim. No generative model required.
   - **AI + template:** Uses chosen template bodies as guidance, personalises with a generative model, and appends required clauses verbatim.
   - **AI generated:** Creates a reply from conversation context without relying on template body wording.
7. **Channel-aware output:** Email gets a subject line, greeting and signature; website, portal, WhatsApp and chat get message-style replies. Use **Channel: Auto** to adopt the lead's known channel.
8. **Human review:** Edit drafts, copy them, save revised text and mark them approved. Nothing is sent.
9. **Easy demo reset:** Restore the fictional inbox with one click. Saved drafts and edited templates remain in their own local files.

### Template placeholders

The supported fields are customer_name, company_name, destination, and service, each surrounded by double curly braces in a template. If no destination or company is known, a neutral value is used. Service-specific pricing, case status, legal advice and appointments are **not** fabricated.

## File and application structure

- frontend/app.jsx — editable React interface.
- public/assets/app.js — prebuilt React bundle, **committed to the repo** so PHP alone can serve it.
- public/assets/styles.css — responsive UI.
- public/index.php — static UI host.
- public/api/bootstrap.php — fictional inbox and template catalog.
- public/api/analyze.php — internal lead analysis and template suggestion.
- public/api/generate.php — draft generation and JSONL persistence.
- public/api/templates.php — template catalog, create/edit, reset.
- public/api/review.php — saved edit and approval records.
- src/Workspace.php — fictional conversations, templates, validation, offline matching, warmth analysis, deterministic rendering.
- src/Inference.php — optional OpenRouter/Ollama calls, AI classification, hybrid and generated drafts.
- src/LeadPilot.php — config parsing, safe text extraction, storage and error utilities.
- tests/ — offline PHP smoke and workspace tests.

## Saved local results

All runtime data stays **outside** public/, in the ignored data/ folder:

| File | Content |
|---|---|
| data/replies.jsonl | Generated drafts, thread IDs, messages, template versions, internal analysis and model info |
| data/revisions.jsonl | Reviewed/edited/approved draft history |
| data/templates.json | Customised template catalog |
| data/template_history.jsonl | Template edits with previous/current version |
| data/errors.jsonl | Minimal failure metadata without prompts, API credentials or headers |

The original fictional demo conversations are in src/Workspace.php. Browser-side changes to those conversations exist only for the current page session; they reset on refresh. Generated drafts, approved revisions and template changes are persistent locally.

## Development and tests

After changing frontend/app.jsx, rebuild the committed JS:

    npm install
    npm run build

Then from the repo root:

    php tests/smoke.php
    php tests/workspace.php

The tests are entirely offline. They verify configuration parsing, reasoning-only output rejection, multi-message and multi-intent detection, warmth, email formatting, required template wording, input validation and template version increments. Live inference requires a configured running provider and has to be tested separately.

## Important boundaries

- **This is a demo, not a production CRM.** No Salesforce records, customer portal, emails, invoices or calendars are accessed. UI source labels are fictional.
- No login, organisation isolation, data retention policy, rate limits, or outbound integrations: **do not expose this service to the internet**.
- For OpenRouter, message content is sent to the chosen remote inference provider; do not test with sensitive customer data without approval.
- AI classification can misinterpret mixed intents or status; defaults are conservative and manual review is supported.
- Approved/mandatory wording is stored as text and appended in template/hybrid modes; do not use this as a regulatory/compliance automation engine without validation.

<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="LeadPilot: a local conversation-aware reply and template assistant for fictional business enquiries.">
  <meta name="color-scheme" content="light">
  <title>LeadPilot · Conversation Workspace</title>
  <link rel="stylesheet" href="/assets/styles.css?v=<?= (int) filemtime(__DIR__ . '/assets/styles.css') ?>">
  <script defer src="/assets/app.js?v=<?= (int) filemtime(__DIR__ . '/assets/app.js') ?>"></script>
</head>
<body>
  <div id="root"><p style="font-family:system-ui;padding:35px;color:#57706d">Loading LeadPilot…</p></div>
</body>
</html>

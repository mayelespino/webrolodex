<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Web Rolodex</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'JetBrains Mono', 'Fira Mono', 'Courier New', monospace;
      background: #1e2130;
      color: #cdd6f4;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    .card {
      width: 100%;
      max-width: 680px;
      background: #252836;
      border: 1px solid #363a4f;
      border-radius: 12px;
      overflow: hidden;
    }
    .card-header {
      background: #2a2d3e;
      border-bottom: 1px solid #363a4f;
      padding: 0.875rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .dots { display: flex; gap: 6px; }
    .dot { width: 11px; height: 11px; border-radius: 50%; }
    .dot-r { background: #e24b4a; }
    .dot-y { background: #ef9f27; }
    .dot-g { background: #639922; }
    .card-title {
      font-size: 12px; color: #888;
      letter-spacing: 0.1em; text-transform: uppercase; margin-left: 8px;
    }
    .card-body { padding: 2rem 1.5rem; }
    .card-footer {
      border-top: 1px solid #363a4f;
      padding: 10px 1.5rem;
      font-size: 11px; color: #666;
      display: flex; justify-content: space-between;
    }
    .status-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: #639922; display: inline-block; margin-right: 5px;
    }
    .field-label {
      font-size: 11px; color: #888;
      text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
      display: flex; align-items: center; gap: 5px;
    }
    .url-box {
      background: #2a2d3e; border: 1px solid #363a4f; border-radius: 8px;
      padding: 8px 12px; font-size: 12px; color: #378add;
      word-break: break-all; margin-bottom: 1.25rem;
    }
    .key-box {
      background: #2a2d3e;
      border: 1px solid #363a4f;
      border-left: 2px solid #378add;
      border-radius: 8px;
      padding: 12px; font-size: 12px; color: #a6e3a1;
      white-space: pre-wrap; word-break: break-all; line-height: 1.65;
    }
    .user-row {
      display: flex; align-items: center; gap: 12px; margin-bottom: 1.25rem;
    }
    .avatar {
      width: 42px; height: 42px; border-radius: 50%;
      background: #1e2a3a; border: 1px solid #2a4060;
      color: #378add; font-size: 15px; font-weight: 500;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .username { font-size: 17px; font-weight: 500; }
    .tag {
      display: inline-block; font-size: 11px;
      padding: 2px 8px; border-radius: 6px; margin-left: 8px; vertical-align: middle;
    }
    .tag-found { background: #1e3a25; color: #a6e3a1; }
    .tag-err   { background: #3a1e1e; color: #f38ba8; }
    .alert-error {
      border: 1px solid #5a2525;
      border-left: 3px solid #e24b4a;
      border-radius: 8px;
      background: #2e1f1f;
      overflow: hidden;
    }
    .alert-error-header {
      display: flex; align-items: center; gap: 10px;
      padding: 12px 14px;
      border-bottom: 1px solid #3d2020;
    }
    .alert-error-header i { font-size: 18px; color: #e24b4a; flex-shrink: 0; }
    .alert-error-title { font-size: 14px; font-weight: 500; color: #f38ba8; }
    .alert-error-body {
      padding: 12px 14px;
      font-size: 12px; color: #a08080; line-height: 1.7;
    }
    .alert-error-body code {
      background: #3a2525; color: #f38ba8;
      padding: 1px 6px; border-radius: 4px; font-family: inherit; font-size: 11px;
    }
    .alert-error-body a {
      color: #89b4fa; text-decoration: none; border-bottom: 1px solid #2a3a5a;
    }
    .alert-error-body a:hover { color: #cdd6f4; }
    hr { border: none; border-top: 1px solid #363a4f; margin: 0 0 1.5rem; }
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <div class="dots">
      <div class="dot dot-r"></div>
      <div class="dot dot-y"></div>
      <div class="dot dot-g"></div>
    </div>
    <span class="card-title"><i class="ti ti-address-book"></i>&nbsp; web rolodex</span>
  </div>
  <div class="card-body">
    <form method="POST">
      <div style="display:flex; gap:10px; align-items:center; margin-bottom:1.5rem;">
        <span style="font-size:13px; color:#639922; white-space:nowrap; font-weight:500;">~/rolodex $</span>
        <input
          type="text"
          name="username"
          value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
          placeholder="enter username..."
          autocomplete="off"
          style="flex:1; background:#2a2d3e; border:1px solid #363a4f; border-radius:8px;
                 color:#cdd6f4; font-family:inherit; font-size:14px; padding:8px 12px; outline:none;"
        />
        <button type="submit"
          style="background:#2a2d3e; border:1px solid #363a4f; border-radius:8px;
                 color:#cdd6f4; font-family:inherit; font-size:13px; padding:8px 16px;
                 cursor:pointer; display:flex; align-items:center; gap:6px;">
          <i class="ti ti-search"></i> lookup
        </button>
      </div>
    </form>

    <?php if (!empty($_POST['username'])): ?>
      <?php
        $username = htmlspecialchars($_POST['username']);
        $format   = 'https://raw.githubusercontent.com/mayelespino/webrolodex/refs/heads/main/public_keys/%s.pub';
        $url      = sprintf($format, rawurlencode($_POST['username']));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        $initials = strtoupper(substr($username, 0, 2));
      ?>
      <hr/>
      <?php if ($httpCode === 200): ?>
        <div class="user-row">
          <div class="avatar"><?php echo $initials; ?></div>
          <div>
            <span class="username"><?php echo $username; ?></span>
            <span class="tag tag-found"><i class="ti ti-check"></i> key found</span>
          </div>
        </div>
        <div class="field-label"><i class="ti ti-link"></i> source url</div>
        <div class="url-box"><?php echo htmlspecialchars($url); ?></div>
        <div class="field-label"><i class="ti ti-key"></i> public key</div>
        <div class="key-box"><?php echo htmlspecialchars($response); ?></div>

      <?php elseif ($curlError): ?>
        <div class="alert-error">
          <div class="alert-error-header">
            <i class="ti ti-wifi-off"></i>
            <span class="alert-error-title">connection error</span>
          </div>
          <div class="alert-error-body">
            Could not reach the key server. Please check your connection and try again.<br/>
            <br/>Details: <code><?php echo htmlspecialchars($curlError); ?></code>
          </div>
        </div>

      <?php elseif ($httpCode === 404): ?>
        <div class="alert-error">
          <div class="alert-error-header">
            <i class="ti ti-user-off"></i>
            <span class="alert-error-title">user not found — <em><?php echo $username; ?></em></span>
          </div>
          <div class="alert-error-body">
            No public key on record for <code><?php echo $username; ?></code>.<br/>
            <br/>
            To add a key, create a file named <code><?php echo $username; ?>.pub</code> containing only
            your public key and submit a pull request to the
            <a href="https://github.com/mayelespino/webrolodex/tree/main/public_keys" target="_blank">
              webrolodex/public_keys
            </a> directory.
          </div>
        </div>

      <?php else: ?>
        <div class="alert-error">
          <div class="alert-error-header">
            <i class="ti ti-circle-x"></i>
            <span class="alert-error-title">unexpected error</span>
          </div>
          <div class="alert-error-body">
            The key server returned an unexpected response.
            <code>HTTP <?php echo (int)$httpCode; ?></code><br/>
            <br/>Please try again later or check the
            <a href="https://github.com/mayelespino/webrolodex" target="_blank">repository</a>
            for known issues.
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="card-footer">
    <span><span class="status-dot"></span>github.com/mayelespino/webrolodex</span>
    <span>ssh key lookup</span>
  </div>
</div>
</body>
</html>

<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();

$feedbackMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['message'])) {
    $senderEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $userMsg = htmlspecialchars($_POST['message']);
    
    $to = "contato@4u.ia.br";
    $subject = "=?UTF-8?B?" . base64_encode("Acrobat Pro — Nova Mensagem de Suporte") . "?=";
    $body = "Nova mensagem enviada pelo Acrobat Pro Suporte:\n\nDe: " . $senderEmail . "\nData: " . date('d/m/Y H:i') . "\n\nMensagem:\n" . $userMsg;
    
    $headers = "From: contato@4u.ia.br\r\n" .
               "Reply-To: " . $senderEmail . "\r\n" .
               "MIME-Version: 1.0\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n" .
               "X-Mailer: PHP/" . phpversion();

    @mail($to, $subject, $body, $headers);

    // Save backup to server log
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $logFile = $uploadDir . 'messages_log.json';
    $existing = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
    $existing[] = [
        'id' => uniqid('msg_', true),
        'app' => 'Acrobat Pro',
        'from' => $senderEmail,
        'date' => date('Y-m-d H:i:s'),
        'message' => $_POST['message']
    ];
    file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT));

    $feedbackMsg = "Mensagem enviada com sucesso! Nossa equipe responderá em breve.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Suporte & Ajuda — Acrobat Pro PDF Editor</title>
  <meta name="description" content="Central de Suporte e Perguntas Frequentes do Acrobat Pro PDF Editor.">
  <link rel="icon" type="image/svg+xml" href="favicon.svg">
  <link rel="stylesheet" href="style.css?v=<?php echo $assetVersion; ?>">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <style>
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
    .legal-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 2rem;
      background: rgba(22, 22, 35, 0.95);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      line-height: 1.7;
      color: #e2e8f0;
    }
    .legal-container h1 { font-size: 1.8rem; margin-bottom: 0.5rem; color: #ef4444; font-weight: 700; }
    .legal-container h2 { font-size: 1.25rem; margin: 1.5rem 0 0.5rem; color: #f43f5e; font-weight: 600; }
    .legal-container p, .legal-container ul { font-size: 0.9rem; color: #94a3b8; margin-bottom: 1rem; }
    .faq-item { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
    .faq-q { font-weight: 700; color: #fff; font-size: 0.95rem; margin-bottom: 0.3rem; display: flex; align-items: center; gap: 0.5rem; }
    .faq-a { color: #94a3b8; font-size: 0.85rem; line-height: 1.5; }
    .contact-card { background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 1.25rem; margin-top: 1.5rem; }
    .input-field { width: 100%; background: #0a0a10; border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 0.75rem; color: #fff; font-size: 0.9rem; outline: none; }
    .btn-submit { background: linear-gradient(135deg, #ef4444, #f43f5e); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
    .back-btn { display: inline-flex; align-items: center; gap: 0.4rem; color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.875rem; margin-bottom: 1.5rem; }
    .app-header-legal { padding: 1rem 1.5rem; background: #140608; border-bottom: 1px solid rgba(239, 68, 68, 0.2); }
    .app-footer-legal { text-align: center; padding: 1.25rem; font-size: 0.775rem; color: #64748b; border-top: 1px solid rgba(255,255,255,0.08); margin-top: auto; }
  </style>
</head>
<body style="background:#0a0a10; color:#fff; min-height:100vh; display:flex; flex-direction:column;">
  
  <header class="app-header-legal">
    <div style="max-width:1200px; margin:0 auto; display:flex; align-items:center; justify-content:space-between;">
      <a href="index.php" style="display:flex; align-items:center; gap:0.6rem; text-decoration:none; color:#fff; font-weight:800; font-size:1.3rem;">
        <img src="favicon.svg" style="width:32px; height:32px; object-fit:contain;">
        <span>Acrobat<span style="color:#ef4444;">Pro</span></span>
      </a>
    </div>

  </header>

  <main style="flex:1;">
    <div class="legal-container">
      <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Voltar ao Acrobat Pro</a>
      
      <h1>Central de Suporte & Ajuda</h1>
      <p>Dúvidas sobre o funcionamento ou suporte técnico do Acrobat Pro PDF Editor.</p>

      <h2>Perguntas Frequentes (FAQ)</h2>

      <div class="faq-item">
        <div class="faq-q"><i class="fa-solid fa-shield-halved" style="color:#ef4444;"></i> Meus PDFs são salvos em algum servidor?</div>
        <div class="faq-a">Não! Todo o processamento é feito <strong>100% no seu navegador</strong>. Seus documentos nunca saem do seu computador ou smartphone.</div>
      </div>

      <div class="faq-item">
        <div class="faq-q"><i class="fa-solid fa-signature" style="color:#f43f5e;"></i> Posso assinar e desenhar nos arquivos PDF?</div>
        <div class="faq-a">Sim! O Acrobat Pro inclui ferramentas de desenho, assinatura digital, inserção de texto, grifador de texto e reorganização de páginas.</div>
      </div>

      <div class="faq-item">
        <div class="faq-q"><i class="fa-solid fa-download" style="color:#00f2fe;"></i> O aplicativo funciona offline?</div>
        <div class="faq-a">Sim! Instale o aplicativo no seu dispositivo clicando no botão <strong>"Instalar App"</strong> no topo para ter acesso instantâneo mesmo sem internet.</div>
      </div>

      <h2>Entre em Contato</h2>
      <div class="contact-card">
        <?php if ($feedbackMsg): ?>
          <div style="padding: 0.75rem; background: rgba(16,185,129,0.15); border: 1px solid #10b981; color: #10b981; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem;">
            <?php echo $feedbackMsg; ?>
          </div>
        <?php endif; ?>

        <p style="font-size: 0.85rem; margin-bottom: 1rem;">Envie suas dúvidas ou sugestões para <code>contato@4u.ia.br</code> ou preencha o formulário abaixo:</p>

        <form method="POST" action="suporte.php" style="display: flex; flex-direction: column; gap: 0.85rem;">
          <input type="email" name="email" placeholder="Seu e-mail de contato" class="input-field" required>
          <textarea name="message" rows="4" placeholder="Escreva sua dúvida ou mensagem..." class="input-field" style="resize: vertical;" required></textarea>
          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-paper-plane"></i> Enviar Mensagem
          </button>
        </form>
      </div>

    </div>
  </main>

  <footer class="app-footer-legal">
    <p>Acrobat Pro PDF Editor — Processamento 100% Privado no Navegador • <a href="privacidade.php" style="color:#64748b; text-decoration:underline;">Privacidade</a> | <a href="termos.php" style="color:#64748b; text-decoration:underline;">Termos</a> | <a href="suporte.php" style="color:#64748b; text-decoration:underline;">Suporte</a></p>
  </footer>

</body>
</html>

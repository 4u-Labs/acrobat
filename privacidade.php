<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Política de Privacidade — Acrobat Pro PDF Editor</title>
  <meta name="description" content="Política de Privacidade do Acrobat Pro. Processamento 100% local no seu navegador com zero upload de documentos PDF.">
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
    .legal-container ul { padding-left: 1.2rem; }
    .legal-container li { margin-bottom: 0.4rem; }
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
      
      <h1>Política de Privacidade</h1>
      <p>Última atualização: <?php echo date('d/m/Y'); ?></p>

      <h2>1. Processamento 100% Local (Client-Side Privacy)</h2>
      <p>O <strong>Acrobat Pro PDF Editor</strong> opera sob a arquitetura de <em>Zero Data Upload</em>. Seus documentos em PDF, imagens, assinaturas e anotações <strong>nunca são enviados para servidores externos</strong>. Todo o processamento de visualização, edição, conversão e exportação é realizado 100% localmente no navegador do seu dispositivo.</p>

      <h2>2. Coleta de Informações</h2>
      <p>Não coletamos, armazenamos ou transmitimos nenhum dado pessoal, conteúdo de arquivos PDF ou informações bancárias. O aplicativo funciona sem necessidade de cadastro, criação de conta ou logins.</p>

      <h2>3. Armazenamento de Preferências no Navegador</h2>
      <p>Utilizamos apenas o <code>localStorage</code> do seu próprio navegador para salvar preferências temporárias de uso, como configurações de tema, ferramentas selecionadas ou modelos de assinatura criados por você localmente.</p>

      <h2>4. Segurança e Criptografia</h2>
      <p>Como os arquivos permanecem exclusivamente na memória do seu computador ou smartphone, seus dados confidenciais e sigilosos desfrutam do mais alto nível de privacidade e segurança existente.</p>

      <h2>5. Contato</h2>
      <p>Para dúvidas sobre nossa política de privacidade, visite nossa <a href="suporte.php" style="color:#ef4444;">Página de Suporte</a> ou envie um e-mail para <code>contato@4u.ia.br</code>.</p>
    </div>
  </main>

  <footer class="app-footer-legal">
    <p>Acrobat Pro PDF Editor — Processamento 100% Privado no Navegador • <a href="privacidade.php" style="color:#64748b; text-decoration:underline;">Privacidade</a> | <a href="termos.php" style="color:#64748b; text-decoration:underline;">Termos</a> | <a href="suporte.php" style="color:#64748b; text-decoration:underline;">Suporte</a></p>
  </footer>

</body>
</html>

<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <title>Termos de Uso — Acrobat Pro PDF Editor</title>
  <meta name="description" content="Termos de Uso e Condições do Serviço Acrobat Pro PDF Editor.">
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
      
      <h1>Termos de Uso e Licença</h1>
      <p>Última atualização: <?php echo date('d/m/Y'); ?></p>

      <h2>1. Aceitação dos Termos</h2>
      <p>Ao utilizar o <strong>Acrobat Pro PDF Editor</strong>, você concorda com os presentes termos e condições de uso. O serviço é disponibilizado gratuitamente para edição e manipulação de documentos PDF.</p>

      <h2>2. Licença e Uso Permitido</h2>
      <p>É concedida uma licença gratuita e não exclusiva para utilizar o aplicativo para fins pessoais ou comerciais. É proibido utilizar a ferramenta para criar códigos maliciosos ou descompilar a aplicação.</p>

      <h2>3. Isenção de Responsabilidade</h2>
      <p>Como os arquivos são editados e salvos diretamente na memória local do navegador do usuário, o Acrobat Pro não se responsabiliza por eventuais perdas de dados decorrentes de fechamento acidental de abas ou falhas do sistema operacional do usuário.</p>

      <h2>4. Atualizações dos Termos</h2>
      <p>Estes termos podem ser revisados periodicamente para atender às diretrizes de distribuição de lojas do Google (Chrome Web Store / Google Play Store).</p>
    </div>
  </main>

  <footer class="app-footer-legal">
    <p>Acrobat Pro PDF Editor — Processamento 100% Privado no Navegador • <a href="privacidade.php" style="color:#64748b; text-decoration:underline;">Privacidade</a> | <a href="termos.php" style="color:#64748b; text-decoration:underline;">Termos</a> | <a href="suporte.php" style="color:#64748b; text-decoration:underline;">Suporte</a></p>
  </footer>

</body>
</html>

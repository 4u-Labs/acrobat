<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Acrobat Pro - Editor de PDF 100% Privado</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="apple-touch-icon" href="favicon.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ef4444">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Adobe+Clean:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #2a2a2a; }
        ::-webkit-scrollbar-thumb { background: #555; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #666; }
        
        .acrobat-gradient { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); }
        .tool-btn { transition: all 0.15s ease; }
        .tool-btn:hover { background: rgba(255,255,255,0.1); }
        .tool-btn.active { background: #e63946; color: white; }
        .sidebar-item:hover { background: rgba(255,255,255,0.05); }
        .sidebar-item.active { background: rgba(230, 57, 70, 0.2); border-left: 3px solid #e63946; }
        
        .page-thumbnail { 
            border: 2px solid transparent; 
            transition: all 0.2s;
        }
        .page-thumbnail:hover { border-color: #666; }
        .page-thumbnail.active { border-color: #e63946; }
        
        .annotation-layer {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        .annotation-layer.drawing { pointer-events: auto; cursor: crosshair; }
        .annotation-layer.highlighting { pointer-events: auto; cursor: crosshair; }
        .annotation-layer.text-mode { pointer-events: auto; cursor: text; }
        
        /* Custom cursors for annotation tools - hotspot at arrow tip (0,0) */
        .annotation-layer.highlight-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><rect x="18" y="10" width="10" height="8" fill="%23FFFF00" stroke="%23000" stroke-width="1" opacity="0.8"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        .annotation-layer.underline-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><line x1="18" y1="20" x2="30" y2="20" stroke="%2322c55e" stroke-width="3"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        .annotation-layer.strikethrough-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><line x1="18" y1="14" x2="30" y2="14" stroke="%23ef4444" stroke-width="3"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        .annotation-layer.eraser-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><rect x="16" y="8" width="12" height="12" fill="%23fff" stroke="%23666" stroke-width="2" rx="2"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        .annotation-layer.draw-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><path d="M18 20 L18 16 L28 6 L30 8 L20 18 Z" fill="%23e63946" stroke="%23000" stroke-width="1"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        .annotation-layer.shape-cursor {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M4 4 L4 18 L7 15 L10 22 L13 21 L10 14 L14 14 Z" fill="%23000" stroke="%23fff" stroke-width="1"/><rect x="17" y="9" width="12" height="10" fill="none" stroke="%23e63946" stroke-width="2"/></svg>') 4 4, crosshair;
            pointer-events: auto;
        }
        
        .highlight-annotation {
            position: absolute;
            background: rgba(255, 255, 0, 0.4);
            pointer-events: auto;
            cursor: pointer;
        }
        
        .text-annotation {
            position: absolute;
            background: #ffffa5;
            border: 1px solid #e6e600;
            padding: 8px;
            min-width: 150px;
            min-height: 50px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            cursor: move;
            font-size: 12px;
            color: #000;
            z-index: 100;
        }
        
        .comment-marker {
            position: absolute;
            width: 24px;
            height: 24px;
            background: #e63946;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            cursor: pointer;
            pointer-events: auto;
            z-index: 100;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #2d2d2d;
            border: 1px solid #444;
            border-radius: 4px;
            min-width: 200px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .dropdown:hover .dropdown-menu { display: block; }
        .dropdown-item { padding: 8px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .dropdown-item:hover { background: #3d3d3d; }
        .dropdown-divider { border-top: 1px solid #444; margin: 4px 0; }
        
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .modal-overlay.show {
            display: flex;
        }
        
        .loading-spinner {
            border: 4px solid #333;
            border-top: 4px solid #e63946;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .pdf-page-container {
            position: relative;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        .context-menu {
            position: fixed;
            background: #2d2d2d;
            border: 1px solid #444;
            border-radius: 4px;
            min-width: 180px;
            z-index: 3000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            display: none;
        }
        .context-menu.show {
            display: block;
        }
        
        .hand-tool {
            cursor: grab;
        }
        .hand-tool:active {
            cursor: grabbing;
        }
        
        .search-highlight {
            background: rgba(255, 165, 0, 0.5);
            position: absolute;
            pointer-events: none;
        }
        
        .stamp-element {
            position: absolute;
            cursor: move;
            font-weight: bold;
            font-size: 24px;
            transform: rotate(-15deg);
            padding: 10px 30px;
            border-radius: 8px;
            opacity: 0.8;
            pointer-events: auto;
            z-index: 100;
            border: 4px solid;
            user-select: none;
        }

        /* Rodapé Estilo Premium 4U.IA.BR */
        .footer-clean { padding: 0.5rem 1rem; color: #94a3b8; background: #0d0405; border-top: 1px solid rgba(255,255,255,0.1); }
        .footer-link-group { display: flex; align-items: center; justify-content: center; gap: 1rem; font-size: 0.7rem; }
        .footer-dot { width: 3px; height: 3px; border-radius: 50%; background: rgba(239, 68, 68, 0.4); }
        .footer-a { transition: all 0.2s; text-decoration: underline; color: #94a3b8; }
        .footer-a:hover { color: #ef4444; }

    </style>
</head>
<body class="bg-gray-900 text-white overflow-hidden h-screen flex flex-col">
    <!-- Top Menu Bar -->
    <div class="bg-[#1a1a1a] border-b border-gray-700 flex items-center px-2 h-8 text-sm">
        <div class="flex items-center gap-1">
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Arquivo</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="openFile()"><i class="fas fa-folder-open w-4"></i> Abrir...</div>
                    <div class="dropdown-item" onclick="createNewPDF()"><i class="fas fa-plus w-4"></i> Criar PDF</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="saveProject()"><i class="fas fa-save w-4"></i> Salvar Projeto</div>
                    <div class="dropdown-item" onclick="loadProject()"><i class="fas fa-folder-open w-4"></i> Abrir Projeto</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="exportAsPNG()"><i class="fas fa-file-image w-4"></i> Exportar como PNG</div>
                    <div class="dropdown-item" onclick="exportAsJPG()"><i class="fas fa-file-image w-4"></i> Exportar como JPG</div>
                    <div class="dropdown-item" onclick="exportAsWebP()"><i class="fas fa-file-image w-4"></i> Exportar como WebP</div>
                    <div class="dropdown-item" onclick="exportAsSVG()"><i class="fas fa-file-code w-4"></i> Exportar como SVG</div>
                    <div class="dropdown-item" onclick="exportAllPages()"><i class="fas fa-images w-4"></i> Exportar Todas as Páginas</div>
                    <div class="dropdown-item" onclick="exportAsJSON()"><i class="fas fa-file-alt w-4"></i> Exportar Anotações (JSON)</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="printPDF()"><i class="fas fa-print w-4"></i> Imprimir</div>
                    <div class="dropdown-item"><i class="fas fa-share w-4"></i> Compartilhar</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="showProperties()"><i class="fas fa-cog w-4"></i> Propriedades</div>
                </div>
            </div>
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Editar</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="undo()"><i class="fas fa-undo w-4"></i> Desfazer <span class="ml-auto text-gray-500">Ctrl+Z</span></div>
                    <div class="dropdown-item" onclick="redo()"><i class="fas fa-redo w-4"></i> Refazer <span class="ml-auto text-gray-500">Ctrl+Y</span></div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="clearAllAnnotations()"><i class="fas fa-trash w-4"></i> Limpar anotações</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="toggleSearch()"><i class="fas fa-search w-4"></i> Localizar</div>
                </div>
            </div>
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Exibir</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="setViewMode('single')"><i class="fas fa-file w-4"></i> Página única</div>
                    <div class="dropdown-item" onclick="setViewMode('continuous')"><i class="fas fa-scroll w-4"></i> Rolagem contínua</div>
                    <div class="dropdown-item" onclick="setViewMode('two-page')"><i class="fas fa-book-open w-4"></i> Duas páginas</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="zoomIn()"><i class="fas fa-search-plus w-4"></i> Aumentar zoom</div>
                    <div class="dropdown-item" onclick="zoomOut()"><i class="fas fa-search-minus w-4"></i> Diminuir zoom</div>
                    <div class="dropdown-item" onclick="fitToWidth()"><i class="fas fa-arrows-alt-h w-4"></i> Ajustar à largura</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="toggleFullscreen()"><i class="fas fa-expand w-4"></i> Tela cheia</div>
                </div>
            </div>
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Ferramentas</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="setTool('select')"><i class="fas fa-mouse-pointer w-4"></i> Selecionar</div>
                    <div class="dropdown-item" onclick="setTool('hand')"><i class="fas fa-hand-paper w-4"></i> Mão</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="setTool('highlight')"><i class="fas fa-highlighter w-4"></i> Realçar</div>
                    <div class="dropdown-item" onclick="setTool('underline')"><i class="fas fa-underline w-4"></i> Sublinhar</div>
                    <div class="dropdown-item" onclick="setTool('strikethrough')"><i class="fas fa-strikethrough w-4"></i> Tachado</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="setTool('draw')"><i class="fas fa-pencil-alt w-4"></i> Desenhar</div>
                    <div class="dropdown-item" onclick="setTool('text')"><i class="fas fa-font w-4"></i> Adicionar texto</div>
                    <div class="dropdown-item" onclick="setTool('comment')"><i class="fas fa-comment w-4"></i> Comentário</div>
                    <div class="dropdown-item" onclick="setTool('stamp')"><i class="fas fa-stamp w-4"></i> Carimbo</div>
                </div>
            </div>
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Janela</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="toggleSidebar()"><i class="fas fa-columns w-4"></i> Painel lateral</div>
                    <div class="dropdown-item" onclick="toggleCommentsPanel()"><i class="fas fa-comments w-4"></i> Painel de comentários</div>
                </div>
            </div>
            <div class="dropdown relative">
                <button class="px-3 py-1 hover:bg-gray-700 rounded">Ajuda</button>
                <div class="dropdown-menu">
                    <div class="dropdown-item" onclick="showShortcuts()"><i class="fas fa-keyboard w-4"></i> Atalhos de teclado</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item" onclick="showAbout()"><i class="fas fa-info-circle w-4"></i> Sobre</div>
                </div>
            </div>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <img src="favicon.svg" class="w-4 h-4">
            <span class="text-gray-300 text-xs font-semibold">Acrobat <span class="text-red-500">Pro</span></span>
        </div>

    </div>

    <!-- Main Toolbar -->
    <div class="bg-[#2d2d2d] border-b border-gray-700 flex items-center px-3 py-1.5 gap-2">
        <!-- File Actions -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="openFile()" class="tool-btn p-2 rounded" title="Abrir arquivo">
                <i class="fas fa-folder-open"></i>
            </button>
            <button onclick="saveProject()" class="tool-btn p-2 rounded" title="Salvar Projeto">
                <i class="fas fa-save"></i>
            </button>
            <button onclick="printPDF()" class="tool-btn p-2 rounded" title="Imprimir">
                <i class="fas fa-print"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="previousPage()" class="tool-btn p-2 rounded" title="Página anterior">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="flex items-center gap-1 bg-[#1a1a1a] rounded px-2">
                <input type="text" id="currentPageInput" class="w-10 bg-transparent text-center text-sm outline-none" value="1" onchange="goToPage(this.value)">
                <span class="text-gray-500">/</span>
                <span id="totalPages" class="text-sm">0</span>
            </div>
            <button onclick="nextPage()" class="tool-btn p-2 rounded" title="Próxima página">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Zoom -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="zoomOut()" class="tool-btn p-2 rounded" title="Diminuir zoom">
                <i class="fas fa-search-minus"></i>
            </button>
            <select id="zoomSelect" class="bg-[#1a1a1a] text-sm rounded px-2 py-1 outline-none" onchange="setZoom(this.value)">
                <option value="0.5">50%</option>
                <option value="0.75">75%</option>
                <option value="1" selected>100%</option>
                <option value="1.25">125%</option>
                <option value="1.5">150%</option>
                <option value="2">200%</option>
                <option value="fit-width">Ajustar à largura</option>
                <option value="fit-page">Ajustar à página</option>
            </select>
            <button onclick="zoomIn()" class="tool-btn p-2 rounded" title="Aumentar zoom">
                <i class="fas fa-search-plus"></i>
            </button>
        </div>

        <!-- Tools -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="setTool('select')" class="tool-btn p-2 rounded active" id="tool-select" title="Selecionar">
                <i class="fas fa-mouse-pointer"></i>
            </button>
            <button onclick="setTool('hand')" class="tool-btn p-2 rounded" id="tool-hand" title="Ferramenta Mão">
                <i class="fas fa-hand-paper"></i>
            </button>
        </div>

        <!-- Annotation Tools -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="setTool('highlight')" class="tool-btn p-2 rounded" id="tool-highlight" title="Realçar">
                <i class="fas fa-highlighter text-yellow-400"></i>
            </button>
            <button onclick="setTool('underline')" class="tool-btn p-2 rounded" id="tool-underline" title="Sublinhar">
                <i class="fas fa-underline text-green-400"></i>
            </button>
            <button onclick="setTool('strikethrough')" class="tool-btn p-2 rounded" id="tool-strikethrough" title="Tachado">
                <i class="fas fa-strikethrough text-red-400"></i>
            </button>
        </div>

        <!-- Drawing Tools -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="setTool('draw')" class="tool-btn p-2 rounded" id="tool-draw" title="Desenhar">
                <i class="fas fa-pencil-alt"></i>
            </button>
            <button onclick="setTool('eraser')" class="tool-btn p-2 rounded" id="tool-eraser" title="Borracha">
                <i class="fas fa-eraser"></i>
            </button>
            <input type="color" id="drawColor" value="#e63946" class="w-6 h-6 rounded cursor-pointer" title="Cor do desenho">
            <select id="strokeWidth" class="bg-[#1a1a1a] text-sm rounded px-1 py-1 outline-none w-16" title="Espessura">
                <option value="2">2px</option>
                <option value="4" selected>4px</option>
                <option value="6">6px</option>
                <option value="8">8px</option>
                <option value="12">12px</option>
                <option value="16">16px</option>
                <option value="24">24px</option>
            </select>
        </div>

        <!-- Text & Comment -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="setTool('text')" class="tool-btn p-2 rounded" id="tool-text" title="Adicionar texto">
                <i class="fas fa-font"></i>
            </button>
            <button onclick="setTool('comment')" class="tool-btn p-2 rounded" id="tool-comment" title="Adicionar comentário">
                <i class="fas fa-comment"></i>
            </button>
            <button onclick="setTool('stamp')" class="tool-btn p-2 rounded" id="tool-stamp" title="Carimbo">
                <i class="fas fa-stamp"></i>
            </button>
        </div>

        <!-- Shapes -->
        <div class="flex items-center gap-1 border-r border-gray-600 pr-3">
            <button onclick="setTool('rectangle')" class="tool-btn p-2 rounded" id="tool-rectangle" title="Retângulo">
                <i class="far fa-square"></i>
            </button>
            <button onclick="setTool('circle')" class="tool-btn p-2 rounded" id="tool-circle" title="Círculo">
                <i class="far fa-circle"></i>
            </button>
            <button onclick="setTool('arrow')" class="tool-btn p-2 rounded" id="tool-arrow" title="Seta">
                <i class="fas fa-long-arrow-alt-right"></i>
            </button>
            <button onclick="setTool('line')" class="tool-btn p-2 rounded" id="tool-line" title="Linha">
                <i class="fas fa-minus"></i>
            </button>
        </div>

        <!-- Undo/Redo -->
        <div class="flex items-center gap-1">
            <button onclick="undo()" class="tool-btn p-2 rounded" id="btn-undo" title="Desfazer (Ctrl+Z)">
                <i class="fas fa-undo"></i>
            </button>
            <button onclick="redo()" class="tool-btn p-2 rounded" id="btn-redo" title="Refazer (Ctrl+Y)">
                <i class="fas fa-redo"></i>
            </button>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <button onclick="toggleSearch()" class="tool-btn p-2 rounded" title="Pesquisar">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <!-- Search Bar (hidden by default) -->
    <div id="searchBar" class="bg-[#3d3d3d] border-b border-gray-700 px-4 py-2 flex items-center gap-3" style="display: none;">
        <input type="text" id="searchInput" placeholder="Pesquisar no documento..." class="bg-[#1a1a1a] px-3 py-1.5 rounded outline-none flex-1 max-w-md" onkeyup="handleSearchKeyup(event)">
        <button onclick="searchInDocument()" class="tool-btn p-2 rounded bg-[#e63946]"><i class="fas fa-search"></i></button>
        <button onclick="searchPrev()" class="tool-btn p-2 rounded"><i class="fas fa-chevron-up"></i></button>
        <button onclick="searchNext()" class="tool-btn p-2 rounded"><i class="fas fa-chevron-down"></i></button>
        <span id="searchResults" class="text-sm text-gray-400">0 resultados</span>
        <button onclick="clearSearch()" class="tool-btn p-2 rounded" title="Limpar busca"><i class="fas fa-times-circle"></i></button>
        <button onclick="toggleSearch()" class="tool-btn p-2 rounded ml-auto"><i class="fas fa-times"></i></button>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Left Sidebar -->
        <div id="leftSidebar" class="w-64 bg-[#252525] border-r border-gray-700 flex flex-col">
            <!-- Sidebar Tabs -->
            <div class="flex border-b border-gray-700">
                <button onclick="setSidebarTab('thumbnails')" class="flex-1 py-2 text-sm sidebar-tab active" id="tab-thumbnails" style="border-bottom: 2px solid #e63946;">
                    <i class="fas fa-th-large mr-1"></i> Miniaturas
                </button>
                <button onclick="setSidebarTab('bookmarks')" class="flex-1 py-2 text-sm sidebar-tab" id="tab-bookmarks">
                    <i class="fas fa-bookmark mr-1"></i> Marcadores
                </button>
            </div>

            <!-- Thumbnails Panel -->
            <div id="panel-thumbnails" class="flex-1 overflow-y-auto p-3">
                <div id="thumbnailsContainer" class="space-y-3">
                    <div class="text-gray-500 text-center text-sm py-8">
                        <i class="fas fa-file-pdf text-4xl mb-3 block opacity-50"></i>
                        Nenhum documento aberto
                    </div>
                </div>
            </div>

            <!-- Bookmarks Panel -->
            <div id="panel-bookmarks" class="flex-1 overflow-y-auto p-3" style="display: none;">
                <div id="bookmarksContainer" class="space-y-1">
                    <div class="text-gray-500 text-center text-sm py-8">
                        <i class="fas fa-bookmark text-4xl mb-3 block opacity-50"></i>
                        Nenhum marcador
                    </div>
                </div>
            </div>

            <!-- Add Bookmark Button -->
            <div class="p-2 border-t border-gray-700">
                <button onclick="addBookmark()" class="w-full py-2 bg-[#1a1a1a] hover:bg-[#333] rounded text-sm flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-plus"></i> Adicionar marcador
                </button>
            </div>
        </div>

        <!-- PDF Viewer Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-[#525659]">
            <!-- PDF Container -->
            <div id="pdfContainer" class="flex-1 overflow-auto p-4 flex flex-col items-center" oncontextmenu="showContextMenu(event)">
                <!-- Welcome Screen -->
                <div id="welcomeScreen" class="flex flex-col items-center justify-center h-full text-center">
                    <div class="bg-[#1a1a1a] rounded-2xl p-12 max-w-lg shadow-2xl">
                        <i class="fas fa-file-pdf text-8xl text-red-500 mb-6"></i>
                        <h1 class="text-2xl font-bold mb-3">Bem-vindo ao Acrobat Pro</h1>

                        <p class="text-gray-400 mb-6">Arraste e solte um arquivo PDF aqui ou clique para abrir</p>
                        <button onclick="openFile()" class="bg-[#e63946] hover:bg-[#d62839] text-white px-8 py-3 rounded-lg font-medium transition cursor-pointer">
                            <i class="fas fa-folder-open mr-2"></i> Abrir PDF
                        </button>
                        <div class="mt-6 text-sm text-gray-500">
                            <p>Ou crie um novo documento</p>
                            <button onclick="createNewPDF()" class="text-blue-400 hover:underline mt-1 cursor-pointer">
                                <i class="fas fa-plus mr-1"></i> Criar PDF em branco
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PDF Pages Container -->
                <div id="pagesContainer" style="display: none;">
                    <!-- Pages will be rendered here -->
                </div>
            </div>
        </div>

        <!-- Right Panel (Comments) -->
        <div id="rightPanel" class="w-72 bg-[#252525] border-l border-gray-700 flex flex-col" style="display: none;">
            <div class="flex items-center justify-between p-3 border-b border-gray-700">
                <h3 class="font-medium"><i class="fas fa-comments mr-2"></i>Comentários</h3>
                <button onclick="toggleCommentsPanel()" class="tool-btn p-1 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="commentsContainer" class="flex-1 overflow-y-auto p-3 space-y-3">
                <div class="text-gray-500 text-center text-sm py-8">
                    <i class="fas fa-comment-slash text-4xl mb-3 block opacity-50"></i>
                    Nenhum comentário
                </div>
            </div>
            <div class="p-3 border-t border-gray-700">
                <button onclick="exportComments()" class="w-full py-2 bg-[#1a1a1a] hover:bg-[#333] rounded text-sm cursor-pointer">
                    <i class="fas fa-download mr-2"></i> Exportar comentários
                </button>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="bg-[#1a1a1a] border-t border-gray-700 px-4 py-1 flex items-center text-xs text-gray-400">
        <span id="documentName">Nenhum documento aberto</span>
        <span class="mx-3">|</span>
        <span id="documentInfo">-</span>
        <div class="ml-auto flex items-center gap-4">
            <span id="historyInfo">Histórico: 0/0</span>
            <span id="zoomLevel">100%</span>
            <span id="currentTool">Ferramenta: Selecionar</span>
        </div>
    </div>

    <!-- Context Menu -->
    <div id="contextMenu" class="context-menu">
        <div class="dropdown-item" onclick="copySelection()"><i class="fas fa-copy w-4"></i> Copiar</div>
        <div class="dropdown-item" onclick="setTool('highlight')"><i class="fas fa-highlighter w-4"></i> Realçar</div>
        <div class="dropdown-item" onclick="setTool('comment')"><i class="fas fa-comment w-4"></i> Adicionar comentário</div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item" onclick="setTool('draw')"><i class="fas fa-pencil-alt w-4"></i> Desenhar</div>
        <div class="dropdown-item" onclick="setTool('text')"><i class="fas fa-font w-4"></i> Adicionar texto</div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-item" onclick="clearAllAnnotations()"><i class="fas fa-trash w-4"></i> Limpar anotações</div>
    </div>

    <!-- Stamp Modal -->
    <div id="stampModal" class="modal-overlay">
        <div class="bg-[#2d2d2d] rounded-lg p-6 max-w-lg w-full">
            <h3 class="text-lg font-bold mb-4">Selecionar Carimbo</h3>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <button onclick="addStamp('approved')" class="p-4 bg-green-900/30 border border-green-500 rounded hover:bg-green-900/50 text-green-400 font-bold cursor-pointer">APROVADO</button>
                <button onclick="addStamp('rejected')" class="p-4 bg-red-900/30 border border-red-500 rounded hover:bg-red-900/50 text-red-400 font-bold cursor-pointer">REJEITADO</button>
                <button onclick="addStamp('draft')" class="p-4 bg-yellow-900/30 border border-yellow-500 rounded hover:bg-yellow-900/50 text-yellow-400 font-bold cursor-pointer">RASCUNHO</button>
                <button onclick="addStamp('confidential')" class="p-4 bg-purple-900/30 border border-purple-500 rounded hover:bg-purple-900/50 text-purple-400 font-bold cursor-pointer">CONFIDENCIAL</button>
                <button onclick="addStamp('final')" class="p-4 bg-blue-900/30 border border-blue-500 rounded hover:bg-blue-900/50 text-blue-400 font-bold cursor-pointer">FINAL</button>
                <button onclick="addStamp('void')" class="p-4 bg-gray-900/30 border border-gray-500 rounded hover:bg-gray-900/50 text-gray-400 font-bold cursor-pointer">ANULADO</button>
                <button onclick="addStamp('paid')" class="p-4 bg-emerald-900/30 border border-emerald-500 rounded hover:bg-emerald-900/50 text-emerald-400 font-bold cursor-pointer">PAGO</button>
                <button onclick="addStamp('pending')" class="p-4 bg-orange-900/30 border border-orange-500 rounded hover:bg-orange-900/50 text-orange-400 font-bold cursor-pointer">PENDENTE</button>
                <button onclick="addStamp('analysis')" class="p-4 bg-cyan-900/30 border border-cyan-500 rounded hover:bg-cyan-900/50 text-cyan-400 font-bold cursor-pointer">EM ANÁLISE</button>
            </div>
            <button onclick="closeStampModal()" class="w-full py-2 bg-gray-600 hover:bg-gray-500 rounded cursor-pointer">Cancelar</button>
        </div>
    </div>

    <!-- Text Input Modal -->
    <div id="textModal" class="modal-overlay">
        <div class="bg-[#2d2d2d] rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Adicionar Texto</h3>
            <textarea id="textModalInput" class="w-full h-32 bg-[#1a1a1a] border border-gray-600 rounded p-3 text-white outline-none resize-none" placeholder="Digite seu texto aqui..."></textarea>
            <div class="flex gap-3 mt-4">
                <select id="textFontSize" class="bg-[#1a1a1a] border border-gray-600 rounded px-3 py-2 outline-none">
                    <option value="12">12px</option>
                    <option value="14" selected>14px</option>
                    <option value="16">16px</option>
                    <option value="18">18px</option>
                    <option value="24">24px</option>
                    <option value="32">32px</option>
                </select>
                <input type="color" id="textColor" value="#000000" class="w-10 h-10 rounded cursor-pointer">
            </div>
            <div class="flex gap-3 mt-4">
                <button onclick="confirmTextAdd()" class="flex-1 py-2 bg-[#e63946] hover:bg-[#d62839] rounded cursor-pointer">Adicionar</button>
                <button onclick="closeTextModal()" class="flex-1 py-2 bg-gray-600 hover:bg-gray-500 rounded cursor-pointer">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Comment Input Modal -->
    <div id="commentModal" class="modal-overlay">
        <div class="bg-[#2d2d2d] rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold mb-4">Adicionar Comentário</h3>
            <input type="text" id="commentAuthor" class="w-full bg-[#1a1a1a] border border-gray-600 rounded p-3 text-white outline-none mb-3" placeholder="Seu nome" value="Usuário">
            <textarea id="commentModalInput" class="w-full h-32 bg-[#1a1a1a] border border-gray-600 rounded p-3 text-white outline-none resize-none" placeholder="Digite seu comentário..."></textarea>
            <div class="flex gap-3 mt-4">
                <button onclick="confirmCommentAdd()" class="flex-1 py-2 bg-[#e63946] hover:bg-[#d62839] rounded cursor-pointer">Adicionar</button>
                <button onclick="closeCommentModal()" class="flex-1 py-2 bg-gray-600 hover:bg-gray-500 rounded cursor-pointer">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="modal-overlay">
        <div class="flex flex-col items-center">
            <div class="loading-spinner mb-4"></div>
            <p id="loadingText">Carregando documento...</p>
        </div>
    </div>

    <!-- Properties Modal -->
    <div id="propertiesModal" class="modal-overlay" onclick="closeProperties()">
        <div class="bg-[#2d2d2d] rounded-lg p-6 max-w-md w-full" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold mb-4">Propriedades do Documento</h3>
            <div id="propertiesContent" class="space-y-2 text-sm">
                <p><strong>Nome:</strong> <span id="propName">-</span></p>
                <p><strong>Páginas:</strong> <span id="propPages">-</span></p>
                <p><strong>Tamanho:</strong> <span id="propSize">-</span></p>
                <p><strong>Anotações:</strong> <span id="propAnnotations">-</span></p>
                <p><strong>Comentários:</strong> <span id="propComments">-</span></p>
            </div>
            <button onclick="closeProperties()" class="w-full mt-4 py-2 bg-[#e63946] hover:bg-[#d62839] rounded cursor-pointer">Fechar</button>
        </div>
    </div>

    <!-- Shortcuts Modal -->
    <div id="shortcutsModal" class="modal-overlay" onclick="closeShortcuts()">
        <div class="bg-[#2d2d2d] rounded-lg p-6 max-w-md w-full" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold mb-4">Atalhos de Teclado</h3>
            <div class="space-y-2 text-sm">
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+O</kbd> Abrir arquivo</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+S</kbd> Salvar</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+P</kbd> Imprimir</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+Z</kbd> Desfazer</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+Y</kbd> Refazer</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+F</kbd> Buscar</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl++</kbd> Aumentar zoom</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+-</kbd> Diminuir zoom</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">←/→</kbd> Página anterior/próxima</p>
                <p><kbd class="bg-gray-700 px-2 py-1 rounded">Esc</kbd> Cancelar/Selecionar</p>
            </div>
            <button onclick="closeShortcuts()" class="w-full mt-4 py-2 bg-[#e63946] hover:bg-[#d62839] rounded cursor-pointer">Fechar</button>
        </div>
    </div>

    <!-- About Modal -->
    <div id="aboutModal" class="modal-overlay" onclick="closeAbout()">
        <div class="bg-[#2d2d2d] rounded-lg p-8 max-w-md text-center" onclick="event.stopPropagation()">
            <i class="fas fa-file-pdf text-6xl text-red-500 mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Acrobat Pro Clone</h2>
            <p class="text-gray-400 mb-4">Versão 2.0.0</p>
            <p class="text-sm text-gray-500 mb-4">Um clone funcional do Adobe Acrobat Pro criado com HTML, CSS e JavaScript.</p>
            <p class="text-xs text-gray-600">Desenvolvido para fins educacionais</p>
            <button onclick="closeAbout()" class="mt-6 px-6 py-2 bg-[#e63946] hover:bg-[#d62839] rounded cursor-pointer">Fechar</button>
        </div>
    </div>

    <!-- Hidden File Input -->
    <input type="file" id="fileInput" accept=".pdf" style="display: none;">

    <script>
        // Configure PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Application State
        const state = {
            pdfDoc: null,
            currentPage: 1,
            totalPages: 0,
            zoom: 1,
            currentTool: 'select',
            viewMode: 'continuous',
            annotations: [],
            elements: [], // DOM elements for annotations
            comments: [],
            bookmarks: [],
            history: [],
            historyIndex: -1,
            isDrawing: false,
            drawingPath: [],
            selectedAnnotation: null,
            documentName: '',
            pages: [],
            canvasStates: {}, // Store canvas image data for undo/redo
            searchResults: [],
            currentSearchIndex: -1,
            pendingTextPosition: null,
            pendingCommentPosition: null,
            isHandToolActive: false,
            handToolStartX: 0,
            handToolStartY: 0,
            handToolScrollLeft: 0,
            handToolScrollTop: 0
        };

        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
        });

        function initializeApp() {
            const pdfContainer = document.getElementById('pdfContainer');
            const fileInput = document.getElementById('fileInput');

            // Make sure modals are hidden
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.classList.remove('show');
            });

            // File input change handler
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    loadPDF(file);
                }
                fileInput.value = '';
            });

            // Initialize drag and drop
            pdfContainer.addEventListener('dragover', (e) => {
                e.preventDefault();
                pdfContainer.style.backgroundColor = '#4a4a5a';
            });

            pdfContainer.addEventListener('dragleave', () => {
                pdfContainer.style.backgroundColor = '';
            });

            pdfContainer.addEventListener('drop', (e) => {
                e.preventDefault();
                pdfContainer.style.backgroundColor = '';
                const file = e.dataTransfer.files[0];
                if (file && file.type === 'application/pdf') {
                    loadPDF(file);
                }
            });

            // Hand tool functionality
            pdfContainer.addEventListener('mousedown', handleHandToolStart);
            pdfContainer.addEventListener('mousemove', handleHandToolMove);
            pdfContainer.addEventListener('mouseup', handleHandToolEnd);
            pdfContainer.addEventListener('mouseleave', handleHandToolEnd);

            // Initialize history
            saveToHistory();
            updateHistoryInfo();

            console.log('Acrobat Pro Clone v2.0 initialized');
        }

        // Hand Tool Functions
        function handleHandToolStart(e) {
            if (state.currentTool !== 'hand') return;
            
            const pdfContainer = document.getElementById('pdfContainer');
            state.isHandToolActive = true;
            state.handToolStartX = e.pageX;
            state.handToolStartY = e.pageY;
            state.handToolScrollLeft = pdfContainer.scrollLeft;
            state.handToolScrollTop = pdfContainer.scrollTop;
            pdfContainer.style.cursor = 'grabbing';
        }

        function handleHandToolMove(e) {
            if (!state.isHandToolActive || state.currentTool !== 'hand') return;
            
            e.preventDefault();
            const pdfContainer = document.getElementById('pdfContainer');
            const dx = e.pageX - state.handToolStartX;
            const dy = e.pageY - state.handToolStartY;
            pdfContainer.scrollLeft = state.handToolScrollLeft - dx;
            pdfContainer.scrollTop = state.handToolScrollTop - dy;
        }

        function handleHandToolEnd() {
            if (state.currentTool !== 'hand') return;
            
            state.isHandToolActive = false;
            const pdfContainer = document.getElementById('pdfContainer');
            pdfContainer.style.cursor = 'grab';
        }

        // File Operations
        function openFile() {
            document.getElementById('fileInput').click();
        }

        async function loadPDF(file) {
            showLoading('Carregando documento...');
            state.documentName = file.name;
            
            const welcomeScreen = document.getElementById('welcomeScreen');
            const pagesContainer = document.getElementById('pagesContainer');
            
            try {
                const arrayBuffer = await file.arrayBuffer();
                const typedArray = new Uint8Array(arrayBuffer);
                
                state.pdfDoc = await pdfjsLib.getDocument(typedArray).promise;
                state.totalPages = state.pdfDoc.numPages;
                state.currentPage = 1;
                
                document.getElementById('totalPages').textContent = state.totalPages;
                document.getElementById('documentName').textContent = state.documentName;
                document.getElementById('documentInfo').textContent = `${state.totalPages} páginas`;
                
                welcomeScreen.style.display = 'none';
                pagesContainer.style.display = 'flex';
                pagesContainer.style.flexDirection = 'column';
                pagesContainer.style.alignItems = 'center';
                
                await renderAllPages();
                await renderThumbnails();
                
                // Reset history for new document
                state.history = [];
                state.historyIndex = -1;
                state.annotations = [];
                state.elements = [];
                state.comments = [];
                state.canvasStates = {};
                saveToHistory();
                
                hideLoading();
            } catch (error) {
                console.error('Error loading PDF:', error);
                hideLoading();
                alert('Erro ao carregar o PDF: ' + error.message);
            }
        }

        async function renderAllPages() {
            const pagesContainer = document.getElementById('pagesContainer');
            pagesContainer.innerHTML = '';
            state.pages = [];
            
            for (let i = 1; i <= state.totalPages; i++) {
                await renderPage(i);
            }
        }

        async function renderPage(pageNum) {
            const pagesContainer = document.getElementById('pagesContainer');
            const page = await state.pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: state.zoom });
            
            const pageContainer = document.createElement('div');
            pageContainer.className = 'pdf-page-container';
            pageContainer.id = `page-${pageNum}`;
            pageContainer.style.width = `${viewport.width}px`;
            pageContainer.style.height = `${viewport.height}px`;
            
            // PDF Canvas
            const canvas = document.createElement('canvas');
            canvas.className = 'pdf-canvas';
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            const ctx = canvas.getContext('2d');
            await page.render({
                canvasContext: ctx,
                viewport: viewport
            }).promise;
            
            pageContainer.appendChild(canvas);
            
            // Annotation Layer
            const annotationLayer = document.createElement('canvas');
            annotationLayer.className = 'annotation-layer';
            annotationLayer.id = `annotation-${pageNum}`;
            annotationLayer.width = viewport.width;
            annotationLayer.height = viewport.height;
            annotationLayer.style.width = `${viewport.width}px`;
            annotationLayer.style.height = `${viewport.height}px`;
            
            setupAnnotationCanvas(annotationLayer, pageNum);
            pageContainer.appendChild(annotationLayer);
            
            // Page number label
            const pageLabel = document.createElement('div');
            pageLabel.className = 'text-center text-gray-400 text-sm py-2';
            pageLabel.textContent = `Página ${pageNum}`;
            
            const wrapper = document.createElement('div');
            wrapper.appendChild(pageContainer);
            wrapper.appendChild(pageLabel);
            
            pagesContainer.appendChild(wrapper);
            state.pages.push({ canvas, annotationLayer, pageNum });
            
            // Restore canvas state if exists
            if (state.canvasStates[pageNum]) {
                const ctx = annotationLayer.getContext('2d');
                const img = new Image();
                img.onload = () => ctx.drawImage(img, 0, 0);
                img.src = state.canvasStates[pageNum];
            }
        }

        function setupAnnotationCanvas(canvas, pageNum) {
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;
            let startX = 0;
            let startY = 0;

            canvas.addEventListener('mousedown', (e) => {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                if (state.currentTool === 'draw') {
                    isDrawing = true;
                    lastX = x;
                    lastY = y;
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                    ctx.strokeStyle = document.getElementById('drawColor').value;
                    ctx.lineWidth = parseInt(document.getElementById('strokeWidth').value);
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                } else if (state.currentTool === 'eraser') {
                    isDrawing = true;
                    lastX = x;
                    lastY = y;
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                    ctx.lineWidth = parseInt(document.getElementById('strokeWidth').value) * 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                } else if (state.currentTool === 'rectangle' || state.currentTool === 'circle' || 
                           state.currentTool === 'line' || state.currentTool === 'arrow' ||
                           state.currentTool === 'highlight' || state.currentTool === 'underline' ||
                           state.currentTool === 'strikethrough') {
                    isDrawing = true;
                    startX = x;
                    startY = y;
                } else if (state.currentTool === 'text') {
                    state.pendingTextPosition = { x, y, pageNum };
                    document.getElementById('textModalInput').value = '';
                    document.getElementById('textModal').classList.add('show');
                } else if (state.currentTool === 'comment') {
                    state.pendingCommentPosition = { x, y, pageNum };
                    document.getElementById('commentModalInput').value = '';
                    document.getElementById('commentModal').classList.add('show');
                }
            });

            canvas.addEventListener('mousemove', (e) => {
                if (!isDrawing) return;
                
                const rect = canvas.getBoundingClientRect();
                const currentX = e.clientX - rect.left;
                const currentY = e.clientY - rect.top;

                if (state.currentTool === 'draw' || state.currentTool === 'eraser') {
                    ctx.lineTo(currentX, currentY);
                    ctx.stroke();
                    lastX = currentX;
                    lastY = currentY;
                }
            });

            canvas.addEventListener('mouseup', (e) => {
                if (!isDrawing) return;
                isDrawing = false;
                
                // Reset composite operation for eraser
                ctx.globalCompositeOperation = 'source-over';
                
                const rect = canvas.getBoundingClientRect();
                const endX = e.clientX - rect.left;
                const endY = e.clientY - rect.top;

                const color = document.getElementById('drawColor').value;
                const lineWidth = parseInt(document.getElementById('strokeWidth').value);

                if (state.currentTool === 'rectangle') {
                    ctx.strokeStyle = color;
                    ctx.lineWidth = lineWidth;
                    ctx.strokeRect(startX, startY, endX - startX, endY - startY);
                } else if (state.currentTool === 'circle') {
                    const radiusX = Math.abs(endX - startX) / 2;
                    const radiusY = Math.abs(endY - startY) / 2;
                    const centerX = Math.min(startX, endX) + radiusX;
                    const centerY = Math.min(startY, endY) + radiusY;
                    
                    ctx.strokeStyle = color;
                    ctx.lineWidth = lineWidth;
                    ctx.beginPath();
                    ctx.ellipse(centerX, centerY, radiusX, radiusY, 0, 0, 2 * Math.PI);
                    ctx.stroke();
                } else if (state.currentTool === 'line') {
                    ctx.strokeStyle = color;
                    ctx.lineWidth = lineWidth;
                    ctx.beginPath();
                    ctx.moveTo(startX, startY);
                    ctx.lineTo(endX, endY);
                    ctx.stroke();
                } else if (state.currentTool === 'arrow') {
                    drawArrow(ctx, startX, startY, endX, endY, color, lineWidth);
                } else if (state.currentTool === 'highlight') {
                    ctx.fillStyle = 'rgba(255, 255, 0, 0.4)';
                    const height = Math.abs(endY - startY) || 20;
                    ctx.fillRect(Math.min(startX, endX), Math.min(startY, endY), Math.abs(endX - startX), height);
                } else if (state.currentTool === 'underline') {
                    ctx.strokeStyle = '#22c55e';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    const y = Math.max(startY, endY);
                    ctx.moveTo(Math.min(startX, endX), y);
                    ctx.lineTo(Math.max(startX, endX), y);
                    ctx.stroke();
                } else if (state.currentTool === 'strikethrough') {
                    ctx.strokeStyle = '#ef4444';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    const y = (startY + endY) / 2;
                    ctx.moveTo(Math.min(startX, endX), y);
                    ctx.lineTo(Math.max(startX, endX), y);
                    ctx.stroke();
                }
                
                // Save canvas state
                saveCanvasState(pageNum);
                saveToHistory();
            });

            canvas.addEventListener('mouseleave', () => {
                if (isDrawing && (state.currentTool === 'draw' || state.currentTool === 'eraser')) {
                    ctx.globalCompositeOperation = 'source-over';
                    saveCanvasState(pageNum);
                    saveToHistory();
                }
                isDrawing = false;
            });
        }

        function drawArrow(ctx, fromX, fromY, toX, toY, color, lineWidth) {
            const headLength = 15;
            const dx = toX - fromX;
            const dy = toY - fromY;
            const angle = Math.atan2(dy, dx);
            
            ctx.strokeStyle = color;
            ctx.lineWidth = lineWidth;
            ctx.beginPath();
            ctx.moveTo(fromX, fromY);
            ctx.lineTo(toX, toY);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(toX, toY);
            ctx.lineTo(toX - headLength * Math.cos(angle - Math.PI / 6), toY - headLength * Math.sin(angle - Math.PI / 6));
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(toX, toY);
            ctx.lineTo(toX - headLength * Math.cos(angle + Math.PI / 6), toY - headLength * Math.sin(angle + Math.PI / 6));
            ctx.stroke();
        }

        function saveCanvasState(pageNum) {
            const canvas = document.getElementById(`annotation-${pageNum}`);
            if (canvas) {
                state.canvasStates[pageNum] = canvas.toDataURL();
            }
        }

        // Text Modal Functions
        function confirmTextAdd() {
            const text = document.getElementById('textModalInput').value.trim();
            if (!text || !state.pendingTextPosition) {
                closeTextModal();
                return;
            }

            const { x, y, pageNum } = state.pendingTextPosition;
            const fontSize = document.getElementById('textFontSize').value;
            const textColor = document.getElementById('textColor').value;
            
            const pageContainer = document.getElementById(`page-${pageNum}`);
            if (pageContainer) {
                const textBox = document.createElement('div');
                textBox.className = 'text-annotation';
                textBox.style.left = `${x}px`;
                textBox.style.top = `${y}px`;
                textBox.style.fontSize = `${fontSize}px`;
                textBox.style.color = textColor;
                textBox.contentEditable = true;
                textBox.textContent = text;
                
                textBox.addEventListener('mousedown', (e) => {
                    if (state.currentTool === 'select') {
                        e.stopPropagation();
                        makeDraggable(textBox, e);
                    }
                });
                
                // Double click to edit
                textBox.addEventListener('dblclick', () => {
                    textBox.focus();
                });
                
                pageContainer.appendChild(textBox);
                state.elements.push({ type: 'text', element: textBox, pageNum });
                state.annotations.push({ type: 'text', x, y, text, pageNum, fontSize, textColor });
                saveToHistory();
            }

            closeTextModal();
        }

        function closeTextModal() {
            document.getElementById('textModal').classList.remove('show');
            state.pendingTextPosition = null;
        }

        // Comment Modal Functions
        function confirmCommentAdd() {
            const comment = document.getElementById('commentModalInput').value.trim();
            const author = document.getElementById('commentAuthor').value.trim() || 'Usuário';
            
            if (!comment || !state.pendingCommentPosition) {
                closeCommentModal();
                return;
            }

            const { x, y, pageNum } = state.pendingCommentPosition;
            const pageContainer = document.getElementById(`page-${pageNum}`);
            const commentId = Date.now();
            
            if (pageContainer) {
                const marker = document.createElement('div');
                marker.className = 'comment-marker';
                marker.style.left = `${x}px`;
                marker.style.top = `${y}px`;
                marker.innerHTML = '<i class="fas fa-comment-alt"></i>';
                marker.title = `${author}: ${comment}`;
                marker.onclick = () => highlightComment(commentId);
                
                marker.addEventListener('mousedown', (e) => {
                    if (state.currentTool === 'select') {
                        e.stopPropagation();
                        makeDraggable(marker, e);
                    }
                });
                
                pageContainer.appendChild(marker);
                
                const commentObj = {
                    id: commentId,
                    pageNum,
                    x,
                    y,
                    text: comment,
                    author: author,
                    date: new Date().toLocaleString(),
                    element: marker
                };
                state.comments.push(commentObj);
                state.elements.push({ type: 'comment', element: marker, pageNum, id: commentId });
                updateCommentsPanel();
                saveToHistory();
            }

            closeCommentModal();
        }

        function closeCommentModal() {
            document.getElementById('commentModal').classList.remove('show');
            state.pendingCommentPosition = null;
        }

        function updateCommentsPanel() {
            const container = document.getElementById('commentsContainer');
            if (state.comments.length === 0) {
                container.innerHTML = `
                    <div class="text-gray-500 text-center text-sm py-8">
                        <i class="fas fa-comment-slash text-4xl mb-3 block opacity-50"></i>
                        Nenhum comentário
                    </div>
                `;
                return;
            }
            
            container.innerHTML = state.comments.map(c => `
                <div class="bg-[#1a1a1a] rounded p-3 cursor-pointer hover:bg-[#333]" onclick="goToComment(${c.id})">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[#e63946] flex items-center justify-center">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div>
                            <div class="font-medium text-sm">${c.author}</div>
                            <div class="text-xs text-gray-500">${c.date}</div>
                        </div>
                        <button onclick="event.stopPropagation(); deleteComment(${c.id})" class="ml-auto text-gray-500 hover:text-red-500">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-300">${c.text}</p>
                    <div class="text-xs text-gray-500 mt-2">Página ${c.pageNum}</div>
                </div>
            `).join('');
        }

        function goToComment(commentId) {
            const comment = state.comments.find(c => c.id === commentId);
            if (comment) {
                goToPage(comment.pageNum);
                highlightComment(commentId);
            }
        }

        function highlightComment(commentId) {
            const comment = state.comments.find(c => c.id === commentId);
            if (comment && comment.element) {
                comment.element.style.animation = 'none';
                comment.element.offsetHeight; // Trigger reflow
                comment.element.style.animation = 'pulse 0.5s ease-in-out 3';
            }
        }

        function deleteComment(commentId) {
            const index = state.comments.findIndex(c => c.id === commentId);
            if (index > -1) {
                const comment = state.comments[index];
                if (comment.element) {
                    comment.element.remove();
                }
                state.comments.splice(index, 1);
                
                const elemIndex = state.elements.findIndex(e => e.id === commentId);
                if (elemIndex > -1) {
                    state.elements.splice(elemIndex, 1);
                }
                
                updateCommentsPanel();
                saveToHistory();
            }
        }

        // History Functions
        function saveToHistory() {
            // Remove future states if we're not at the end
            state.history = state.history.slice(0, state.historyIndex + 1);
            
            // Save current state
            const currentState = {
                canvasStates: { ...state.canvasStates },
                annotations: JSON.parse(JSON.stringify(state.annotations.map(a => ({...a, element: undefined})))),
                comments: JSON.parse(JSON.stringify(state.comments.map(c => ({...c, element: undefined}))))
            };
            
            state.history.push(currentState);
            state.historyIndex = state.history.length - 1;
            
            // Limit history size
            if (state.history.length > 50) {
                state.history.shift();
                state.historyIndex--;
            }
            
            updateHistoryInfo();
        }

        function updateHistoryInfo() {
            document.getElementById('historyInfo').textContent = `Histórico: ${state.historyIndex + 1}/${state.history.length}`;
        }

        function undo() {
            if (state.historyIndex > 0) {
                state.historyIndex--;
                restoreState(state.history[state.historyIndex]);
                updateHistoryInfo();
                console.log('Undo - Index:', state.historyIndex);
            } else {
                console.log('Nada para desfazer');
            }
        }

        function redo() {
            if (state.historyIndex < state.history.length - 1) {
                state.historyIndex++;
                restoreState(state.history[state.historyIndex]);
                updateHistoryInfo();
                console.log('Redo - Index:', state.historyIndex);
            } else {
                console.log('Nada para refazer');
            }
        }

        function restoreState(savedState) {
            // Restore canvas states
            state.canvasStates = { ...savedState.canvasStates };
            
            // Redraw all annotation canvases
            for (let pageNum in state.canvasStates) {
                const canvas = document.getElementById(`annotation-${pageNum}`);
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    if (state.canvasStates[pageNum]) {
                        const img = new Image();
                        img.onload = () => ctx.drawImage(img, 0, 0);
                        img.src = state.canvasStates[pageNum];
                    }
                }
            }
            
            // Clear canvases that don't have saved state
            state.pages.forEach(page => {
                if (!state.canvasStates[page.pageNum]) {
                    const canvas = document.getElementById(`annotation-${page.pageNum}`);
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }
            });
            
            // Remove all DOM elements
            state.elements.forEach(elem => {
                if (elem.element && elem.element.parentNode) {
                    elem.element.remove();
                }
            });
            state.elements = [];
            
            // Restore text annotations
            state.annotations = savedState.annotations || [];
            state.annotations.forEach(ann => {
                if (ann.type === 'text') {
                    const pageContainer = document.getElementById(`page-${ann.pageNum}`);
                    if (pageContainer) {
                        const textBox = document.createElement('div');
                        textBox.className = 'text-annotation';
                        textBox.style.left = `${ann.x}px`;
                        textBox.style.top = `${ann.y}px`;
                        textBox.style.fontSize = `${ann.fontSize || 14}px`;
                        textBox.style.color = ann.textColor || '#000';
                        textBox.contentEditable = true;
                        textBox.textContent = ann.text;
                        
                        textBox.addEventListener('mousedown', (e) => {
                            if (state.currentTool === 'select') {
                                e.stopPropagation();
                                makeDraggable(textBox, e);
                            }
                        });
                        
                        pageContainer.appendChild(textBox);
                        state.elements.push({ type: 'text', element: textBox, pageNum: ann.pageNum });
                    }
                }
            });
            
            // Restore comments
            state.comments = [];
            (savedState.comments || []).forEach(c => {
                const pageContainer = document.getElementById(`page-${c.pageNum}`);
                if (pageContainer) {
                    const marker = document.createElement('div');
                    marker.className = 'comment-marker';
                    marker.style.left = `${c.x}px`;
                    marker.style.top = `${c.y}px`;
                    marker.innerHTML = '<i class="fas fa-comment-alt"></i>';
                    marker.title = `${c.author}: ${c.text}`;
                    marker.onclick = () => highlightComment(c.id);
                    
                    marker.addEventListener('mousedown', (e) => {
                        if (state.currentTool === 'select') {
                            e.stopPropagation();
                            makeDraggable(marker, e);
                        }
                    });
                    
                    pageContainer.appendChild(marker);
                    
                    state.comments.push({
                        ...c,
                        element: marker
                    });
                    state.elements.push({ type: 'comment', element: marker, pageNum: c.pageNum, id: c.id });
                }
            });
            
            updateCommentsPanel();
        }

        function clearAllAnnotations() {
            if (!confirm('Tem certeza que deseja limpar todas as anotações?')) return;
            
            // Clear all annotation canvases
            state.pages.forEach(page => {
                const canvas = document.getElementById(`annotation-${page.pageNum}`);
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            });
            
            // Remove all DOM elements
            state.elements.forEach(elem => {
                if (elem.element && elem.element.parentNode) {
                    elem.element.remove();
                }
            });
            
            state.annotations = [];
            state.elements = [];
            state.comments = [];
            state.canvasStates = {};
            
            updateCommentsPanel();
            saveToHistory();
        }

        // Thumbnail Functions
        async function renderThumbnails() {
            const thumbnailsContainer = document.getElementById('thumbnailsContainer');
            thumbnailsContainer.innerHTML = '';
            
            for (let i = 1; i <= state.totalPages; i++) {
                const page = await state.pdfDoc.getPage(i);
                const viewport = page.getViewport({ scale: 0.2 });
                
                const thumbContainer = document.createElement('div');
                thumbContainer.className = `page-thumbnail cursor-pointer p-2 rounded ${i === state.currentPage ? 'active' : ''}`;
                thumbContainer.onclick = () => goToPage(i);
                
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                const ctx = canvas.getContext('2d');
                await page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise;
                
                const pageLabel = document.createElement('div');
                pageLabel.className = 'text-center text-xs text-gray-400 mt-1';
                pageLabel.textContent = i;
                
                thumbContainer.appendChild(canvas);
                thumbContainer.appendChild(pageLabel);
                thumbnailsContainer.appendChild(thumbContainer);
            }
        }

        // Navigation
        function previousPage() {
            if (state.currentPage > 1) {
                goToPage(state.currentPage - 1);
            }
        }

        function nextPage() {
            if (state.currentPage < state.totalPages) {
                goToPage(state.currentPage + 1);
            }
        }

        function goToPage(pageNum) {
            pageNum = parseInt(pageNum);
            if (pageNum < 1 || pageNum > state.totalPages || isNaN(pageNum)) return;
            
            state.currentPage = pageNum;
            document.getElementById('currentPageInput').value = pageNum;
            
            const pageElement = document.getElementById(`page-${pageNum}`);
            if (pageElement) {
                pageElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            
            document.querySelectorAll('.page-thumbnail').forEach((thumb, index) => {
                thumb.classList.toggle('active', index + 1 === pageNum);
            });
        }

        // Zoom
        function zoomIn() {
            setZoom(Math.min(state.zoom + 0.25, 3));
        }

        function zoomOut() {
            setZoom(Math.max(state.zoom - 0.25, 0.25));
        }

        function setZoom(value) {
            const pdfContainer = document.getElementById('pdfContainer');
            
            if (value === 'fit-width' || value === 'fit-page') {
                const containerWidth = pdfContainer.clientWidth - 48;
                if (state.pdfDoc) {
                    state.pdfDoc.getPage(1).then(page => {
                        const viewport = page.getViewport({ scale: 1 });
                        state.zoom = containerWidth / viewport.width;
                        document.getElementById('zoomLevel').textContent = `${Math.round(state.zoom * 100)}%`;
                        renderAllPages();
                    });
                }
                return;
            }
            
            state.zoom = parseFloat(value);
            document.getElementById('zoomSelect').value = value;
            document.getElementById('zoomLevel').textContent = `${Math.round(state.zoom * 100)}%`;
            
            if (state.pdfDoc) {
                renderAllPages();
            }
        }

        function fitToWidth() {
            setZoom('fit-width');
        }

        // Tools
        function setTool(tool) {
            state.currentTool = tool;
            
            document.querySelectorAll('[id^="tool-"]').forEach(btn => {
                btn.classList.remove('active');
            });
            const toolBtn = document.getElementById(`tool-${tool}`);
            if (toolBtn) toolBtn.classList.add('active');
            
            const toolNames = {
                'select': 'Selecionar',
                'hand': 'Mão',
                'highlight': 'Realçar',
                'underline': 'Sublinhar',
                'strikethrough': 'Tachado',
                'draw': 'Desenhar',
                'eraser': 'Borracha',
                'text': 'Texto',
                'comment': 'Comentário',
                'stamp': 'Carimbo',
                'rectangle': 'Retângulo',
                'circle': 'Círculo',
                'arrow': 'Seta',
                'line': 'Linha'
            };
            document.getElementById('currentTool').textContent = `Ferramenta: ${toolNames[tool] || tool}`;
            
            // Update cursor
            const pdfContainer = document.getElementById('pdfContainer');
            pdfContainer.classList.remove('hand-tool');
            if (tool === 'hand') {
                pdfContainer.classList.add('hand-tool');
                pdfContainer.style.cursor = 'grab';
            } else {
                pdfContainer.style.cursor = 'default';
            }
            
            // Update annotation layer
            document.querySelectorAll('.annotation-layer').forEach(layer => {
                layer.classList.remove('drawing', 'highlighting', 'text-mode', 'highlight-cursor', 'underline-cursor', 'strikethrough-cursor', 'eraser-cursor', 'draw-cursor', 'shape-cursor');
                if (tool === 'draw') {
                    layer.classList.add('drawing', 'draw-cursor');
                } else if (tool === 'eraser') {
                    layer.classList.add('drawing', 'eraser-cursor');
                } else if (tool === 'rectangle' || tool === 'circle' || tool === 'line' || tool === 'arrow') {
                    layer.classList.add('drawing', 'shape-cursor');
                } else if (tool === 'highlight') {
                    layer.classList.add('highlighting', 'highlight-cursor');
                } else if (tool === 'underline') {
                    layer.classList.add('highlighting', 'underline-cursor');
                } else if (tool === 'strikethrough') {
                    layer.classList.add('highlighting', 'strikethrough-cursor');
                } else if (tool === 'text' || tool === 'comment') {
                    layer.classList.add('text-mode');
                }
            });
            
            if (tool === 'stamp') {
                document.getElementById('stampModal').classList.add('show');
            }
        }

        function addStamp(type) {
            const stamps = {
                'approved': { text: 'APROVADO', color: '#22c55e', border: '#16a34a' },
                'rejected': { text: 'REJEITADO', color: '#ef4444', border: '#dc2626' },
                'draft': { text: 'RASCUNHO', color: '#eab308', border: '#ca8a04' },
                'confidential': { text: 'CONFIDENCIAL', color: '#a855f7', border: '#9333ea' },
                'final': { text: 'FINAL', color: '#3b82f6', border: '#2563eb' },
                'void': { text: 'ANULADO', color: '#6b7280', border: '#4b5563' },
                'paid': { text: 'PAGO', color: '#10b981', border: '#059669' },
                'pending': { text: 'PENDENTE', color: '#f97316', border: '#ea580c' },
                'analysis': { text: 'EM ANÁLISE', color: '#06b6d4', border: '#0891b2' }
            };
            
            const stamp = stamps[type];
            const pageContainer = document.getElementById(`page-${state.currentPage}`);
            
            if (pageContainer) {
                const stampEl = document.createElement('div');
                stampEl.className = 'stamp-element';
                stampEl.style.color = stamp.color;
                stampEl.style.borderColor = stamp.border;
                stampEl.style.left = '50%';
                stampEl.style.top = '50%';
                stampEl.textContent = stamp.text;
                
                stampEl.addEventListener('mousedown', (e) => {
                    if (state.currentTool === 'select') {
                        e.stopPropagation();
                        makeDraggable(stampEl, e);
                    }
                });
                
                pageContainer.appendChild(stampEl);
                state.elements.push({ type: 'stamp', element: stampEl, pageNum: state.currentPage });
                state.annotations.push({ type: 'stamp', stampType: type, pageNum: state.currentPage });
                saveToHistory();
            }
            
            closeStampModal();
            setTool('select');
        }

        function closeStampModal() {
            document.getElementById('stampModal').classList.remove('show');
        }

        function makeDraggable(element, e) {
            e.preventDefault();
            const rect = element.getBoundingClientRect();
            let offsetX = e.clientX - rect.left;
            let offsetY = e.clientY - rect.top;
            
            function move(e) {
                const parentRect = element.parentElement.getBoundingClientRect();
                let newX = e.clientX - parentRect.left - offsetX;
                let newY = e.clientY - parentRect.top - offsetY;
                
                // Keep within bounds
                newX = Math.max(0, Math.min(newX, parentRect.width - rect.width));
                newY = Math.max(0, Math.min(newY, parentRect.height - rect.height));
                
                element.style.left = newX + 'px';
                element.style.top = newY + 'px';
                element.style.transform = element.style.transform.replace(/translate\([^)]*\)/, '');
            }
            
            function stop() {
                document.removeEventListener('mousemove', move);
                document.removeEventListener('mouseup', stop);
                saveToHistory();
            }
            
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', stop);
        }

        // Search Functions
        function toggleSearch() {
            const searchBar = document.getElementById('searchBar');
            searchBar.style.display = searchBar.style.display === 'none' ? 'flex' : 'none';
            if (searchBar.style.display !== 'none') {
                document.getElementById('searchInput').focus();
            }
        }

        function handleSearchKeyup(event) {
            if (event.key === 'Enter') {
                searchInDocument();
            }
        }

        function searchInDocument() {
            const query = document.getElementById('searchInput').value.trim().toLowerCase();
            if (!query) {
                document.getElementById('searchResults').textContent = '0 resultados';
                return;
            }

            // Search in text annotations
            state.searchResults = [];
            
            state.annotations.forEach((ann, index) => {
                if (ann.type === 'text' && ann.text.toLowerCase().includes(query)) {
                    state.searchResults.push({
                        type: 'annotation',
                        index,
                        pageNum: ann.pageNum,
                        text: ann.text
                    });
                }
            });

            // Search in comments
            state.comments.forEach((comment, index) => {
                if (comment.text.toLowerCase().includes(query)) {
                    state.searchResults.push({
                        type: 'comment',
                        index,
                        pageNum: comment.pageNum,
                        text: comment.text,
                        id: comment.id
                    });
                }
            });

            document.getElementById('searchResults').textContent = `${state.searchResults.length} resultados`;
            state.currentSearchIndex = -1;

            if (state.searchResults.length > 0) {
                searchNext();
            }
        }

        function searchNext() {
            if (state.searchResults.length === 0) return;
            
            state.currentSearchIndex = (state.currentSearchIndex + 1) % state.searchResults.length;
            goToSearchResult(state.searchResults[state.currentSearchIndex]);
            updateSearchCounter();
        }

        function searchPrev() {
            if (state.searchResults.length === 0) return;
            
            state.currentSearchIndex = state.currentSearchIndex <= 0 ? 
                state.searchResults.length - 1 : state.currentSearchIndex - 1;
            goToSearchResult(state.searchResults[state.currentSearchIndex]);
            updateSearchCounter();
        }

        function goToSearchResult(result) {
            goToPage(result.pageNum);
            
            if (result.type === 'comment' && result.id) {
                highlightComment(result.id);
            } else if (result.type === 'annotation') {
                // Highlight text annotation
                const elements = state.elements.filter(e => e.type === 'text' && e.pageNum === result.pageNum);
                elements.forEach(e => {
                    if (e.element.textContent === result.text) {
                        e.element.style.boxShadow = '0 0 10px 5px rgba(255, 165, 0, 0.7)';
                        setTimeout(() => {
                            e.element.style.boxShadow = '';
                        }, 2000);
                    }
                });
            }
        }

        function updateSearchCounter() {
            document.getElementById('searchResults').textContent = 
                `${state.currentSearchIndex + 1}/${state.searchResults.length} resultados`;
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').textContent = '0 resultados';
            state.searchResults = [];
            state.currentSearchIndex = -1;
        }

        // Sidebar
        function setSidebarTab(tab) {
            document.querySelectorAll('.sidebar-tab').forEach(t => {
                t.classList.remove('active');
                t.style.borderBottom = 'none';
            });
            const activeTab = document.getElementById(`tab-${tab}`);
            activeTab.classList.add('active');
            activeTab.style.borderBottom = '2px solid #e63946';
            
            document.getElementById('panel-thumbnails').style.display = tab === 'thumbnails' ? 'block' : 'none';
            document.getElementById('panel-bookmarks').style.display = tab === 'bookmarks' ? 'block' : 'none';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('leftSidebar');
            sidebar.style.display = sidebar.style.display === 'none' ? 'flex' : 'none';
        }

        function toggleCommentsPanel() {
            const panel = document.getElementById('rightPanel');
            panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
        }

        // Bookmarks
        function addBookmark() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Abra um documento primeiro');
                return;
            }
            
            const name = prompt('Nome do marcador:', `Página ${state.currentPage}`);
            if (name) {
                state.bookmarks.push({
                    name,
                    pageNum: state.currentPage
                });
                updateBookmarksPanel();
            }
        }

        function updateBookmarksPanel() {
            const container = document.getElementById('bookmarksContainer');
            if (state.bookmarks.length === 0) {
                container.innerHTML = `
                    <div class="text-gray-500 text-center text-sm py-8">
                        <i class="fas fa-bookmark text-4xl mb-3 block opacity-50"></i>
                        Nenhum marcador
                    </div>
                `;
                return;
            }
            
            container.innerHTML = state.bookmarks.map((b, i) => `
                <div class="flex items-center gap-2 p-2 hover:bg-[#333] rounded cursor-pointer" onclick="goToPage(${b.pageNum})">
                    <i class="fas fa-bookmark text-[#e63946]"></i>
                    <span class="flex-1 text-sm">${b.name}</span>
                    <button onclick="event.stopPropagation(); removeBookmark(${i})" class="text-gray-500 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        }

        function removeBookmark(index) {
            state.bookmarks.splice(index, 1);
            updateBookmarksPanel();
        }

        // Context Menu
        function showContextMenu(e) {
            if (state.currentTool !== 'select') return;
            
            e.preventDefault();
            const menu = document.getElementById('contextMenu');
            menu.style.left = `${e.clientX}px`;
            menu.style.top = `${e.clientY}px`;
            menu.classList.add('show');
            
            document.addEventListener('click', hideContextMenu, { once: true });
        }

        function hideContextMenu() {
            document.getElementById('contextMenu').classList.remove('show');
        }

        function copySelection() {
            const selection = window.getSelection();
            if (selection.toString()) {
                navigator.clipboard.writeText(selection.toString());
            }
            hideContextMenu();
        }

        // Save/Export Functions
        function savePDF() {
            saveProject();
        }

        function exportAsPNG() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            showLoading('Exportando como PNG...');
            
            setTimeout(() => {
                try {
                    // Export current page
                    const pageContainer = document.getElementById(`page-${state.currentPage}`);
                    if (!pageContainer) {
                        hideLoading();
                        alert('Erro ao exportar');
                        return;
                    }

                    const pdfCanvas = pageContainer.querySelector('.pdf-canvas');
                    const annotationCanvas = pageContainer.querySelector('.annotation-layer');
                    
                    // Create merged canvas
                    const mergedCanvas = document.createElement('canvas');
                    mergedCanvas.width = pdfCanvas.width;
                    mergedCanvas.height = pdfCanvas.height;
                    const ctx = mergedCanvas.getContext('2d');
                    
                    // Draw PDF
                    ctx.drawImage(pdfCanvas, 0, 0);
                    
                    // Draw annotations
                    ctx.drawImage(annotationCanvas, 0, 0);
                    
                    // Draw text annotations and stamps
                    const elements = pageContainer.querySelectorAll('.text-annotation, .stamp-element, .comment-marker');
                    elements.forEach(el => {
                        const rect = el.getBoundingClientRect();
                        const containerRect = pageContainer.getBoundingClientRect();
                        const x = rect.left - containerRect.left;
                        const y = rect.top - containerRect.top;
                        
                        if (el.classList.contains('text-annotation')) {
                            ctx.fillStyle = '#ffffa5';
                            ctx.fillRect(x, y, rect.width, rect.height);
                            ctx.fillStyle = el.style.color || '#000';
                            ctx.font = el.style.fontSize || '14px';
                            ctx.fillText(el.textContent, x + 8, y + 20);
                        }
                    });
                    
                    // Download
                    const link = document.createElement('a');
                    link.download = `${state.documentName || 'documento'}_pagina${state.currentPage}.png`;
                    link.href = mergedCanvas.toDataURL('image/png');
                    link.click();
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    alert('Erro ao exportar: ' + error.message);
                }
            }, 100);
        }

        function exportAsJPG() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            showLoading('Exportando como JPG...');
            
            setTimeout(() => {
                try {
                    const pageContainer = document.getElementById(`page-${state.currentPage}`);
                    if (!pageContainer) {
                        hideLoading();
                        alert('Erro ao exportar');
                        return;
                    }

                    const pdfCanvas = pageContainer.querySelector('.pdf-canvas');
                    const annotationCanvas = pageContainer.querySelector('.annotation-layer');
                    
                    const mergedCanvas = document.createElement('canvas');
                    mergedCanvas.width = pdfCanvas.width;
                    mergedCanvas.height = pdfCanvas.height;
                    const ctx = mergedCanvas.getContext('2d');
                    
                    // White background for JPG
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, mergedCanvas.width, mergedCanvas.height);
                    
                    ctx.drawImage(pdfCanvas, 0, 0);
                    ctx.drawImage(annotationCanvas, 0, 0);
                    
                    const link = document.createElement('a');
                    link.download = `${state.documentName || 'documento'}_pagina${state.currentPage}.jpg`;
                    link.href = mergedCanvas.toDataURL('image/jpeg', 0.9);
                    link.click();
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    alert('Erro ao exportar: ' + error.message);
                }
            }, 100);
        }

        function exportAsWebP() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            showLoading('Exportando como WebP...');
            
            setTimeout(() => {
                try {
                    const mergedCanvas = getMergedCanvas(state.currentPage);
                    if (!mergedCanvas) {
                        hideLoading();
                        alert('Erro ao exportar');
                        return;
                    }
                    
                    const link = document.createElement('a');
                    link.download = `${state.documentName || 'documento'}_pagina${state.currentPage}.webp`;
                    link.href = mergedCanvas.toDataURL('image/webp', 0.9);
                    link.click();
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    alert('Erro ao exportar: ' + error.message);
                }
            }, 100);
        }

        function exportAsSVG() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            showLoading('Exportando como SVG...');
            
            setTimeout(() => {
                try {
                    const mergedCanvas = getMergedCanvas(state.currentPage);
                    if (!mergedCanvas) {
                        hideLoading();
                        alert('Erro ao exportar');
                        return;
                    }
                    
                    // Create SVG with embedded image
                    const dataUrl = mergedCanvas.toDataURL('image/png');
                    const svgContent = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
     width="${mergedCanvas.width}" height="${mergedCanvas.height}" viewBox="0 0 ${mergedCanvas.width} ${mergedCanvas.height}">
    <image width="${mergedCanvas.width}" height="${mergedCanvas.height}" xlink:href="${dataUrl}"/>
</svg>`;
                    
                    const blob = new Blob([svgContent], { type: 'image/svg+xml' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = `${state.documentName || 'documento'}_pagina${state.currentPage}.svg`;
                    link.href = url;
                    link.click();
                    URL.revokeObjectURL(url);
                    
                    hideLoading();
                } catch (error) {
                    hideLoading();
                    alert('Erro ao exportar: ' + error.message);
                }
            }, 100);
        }

        async function exportAllPages() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            const format = prompt('Escolha o formato (png, jpg, webp):', 'png');
            if (!format || !['png', 'jpg', 'webp'].includes(format.toLowerCase())) {
                alert('Formato inválido');
                return;
            }

            showLoading('Exportando todas as páginas...');
            
            try {
                for (let i = 1; i <= state.totalPages; i++) {
                    document.getElementById('loadingText').textContent = `Exportando página ${i} de ${state.totalPages}...`;
                    
                    const mergedCanvas = getMergedCanvas(i);
                    if (!mergedCanvas) continue;
                    
                    const link = document.createElement('a');
                    const mimeType = format === 'jpg' ? 'image/jpeg' : `image/${format}`;
                    link.download = `${state.documentName || 'documento'}_pagina${i}.${format}`;
                    link.href = mergedCanvas.toDataURL(mimeType, 0.9);
                    link.click();
                    
                    // Wait a bit between downloads
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
                
                hideLoading();
                alert(`${state.totalPages} páginas exportadas com sucesso!`);
            } catch (error) {
                hideLoading();
                alert('Erro ao exportar: ' + error.message);
            }
        }

        function getMergedCanvas(pageNum) {
            const pageContainer = document.getElementById(`page-${pageNum}`);
            if (!pageContainer) return null;

            const pdfCanvas = pageContainer.querySelector('.pdf-canvas');
            const annotationCanvas = pageContainer.querySelector('.annotation-layer');
            
            if (!pdfCanvas) return null;
            
            const mergedCanvas = document.createElement('canvas');
            mergedCanvas.width = pdfCanvas.width;
            mergedCanvas.height = pdfCanvas.height;
            const ctx = mergedCanvas.getContext('2d');
            
            // White background
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, mergedCanvas.width, mergedCanvas.height);
            
            // Draw PDF
            ctx.drawImage(pdfCanvas, 0, 0);
            
            // Draw annotations
            if (annotationCanvas) {
                ctx.drawImage(annotationCanvas, 0, 0);
            }
            
            // Draw text annotations and stamps
            const elements = pageContainer.querySelectorAll('.text-annotation, .stamp-element');
            elements.forEach(el => {
                const rect = el.getBoundingClientRect();
                const containerRect = pageContainer.getBoundingClientRect();
                const x = rect.left - containerRect.left;
                const y = rect.top - containerRect.top;
                
                if (el.classList.contains('text-annotation')) {
                    ctx.fillStyle = '#ffffa5';
                    ctx.fillRect(x, y, rect.width, rect.height);
                    ctx.strokeStyle = '#e6e600';
                    ctx.strokeRect(x, y, rect.width, rect.height);
                    ctx.fillStyle = el.style.color || '#000';
                    ctx.font = `${el.style.fontSize || '14px'} sans-serif`;
                    ctx.fillText(el.textContent, x + 8, y + parseInt(el.style.fontSize || 14) + 4);
                } else if (el.classList.contains('stamp-element')) {
                    ctx.save();
                    ctx.translate(x + rect.width/2, y + rect.height/2);
                    ctx.rotate(-15 * Math.PI / 180);
                    ctx.font = 'bold 24px sans-serif';
                    ctx.fillStyle = el.style.color;
                    ctx.strokeStyle = el.style.borderColor;
                    ctx.lineWidth = 4;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.strokeText(el.textContent, 0, 0);
                    ctx.fillText(el.textContent, 0, 0);
                    ctx.restore();
                }
            });
            
            return mergedCanvas;
        }

        function exportAsJSON() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            const exportData = {
                documentName: state.documentName || 'Novo documento',
                totalPages: state.totalPages,
                exportDate: new Date().toISOString(),
                annotations: state.annotations.map(a => ({...a, element: undefined})),
                comments: state.comments.map(c => ({...c, element: undefined})),
                bookmarks: state.bookmarks
            };
            
            const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = `${state.documentName || 'documento'}_anotacoes.json`;
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
        }

        function saveProject() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }

            showLoading('Salvando projeto...');
            
            setTimeout(() => {
                try {
                    const projectData = {
                        version: '2.0',
                        documentName: state.documentName || 'Novo documento',
                        totalPages: state.totalPages,
                        zoom: state.zoom,
                        currentPage: state.currentPage,
                        savedDate: new Date().toISOString(),
                        canvasStates: state.canvasStates,
                        annotations: state.annotations.map(a => ({...a, element: undefined})),
                        comments: state.comments.map(c => ({...c, element: undefined})),
                        bookmarks: state.bookmarks
                    };
                    
                    const blob = new Blob([JSON.stringify(projectData)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = `${state.documentName || 'documento'}_projeto.acrobat`;
                    link.href = url;
                    link.click();
                    URL.revokeObjectURL(url);
                    
                    hideLoading();
                    alert('Projeto salvo com sucesso!');
                } catch (error) {
                    hideLoading();
                    alert('Erro ao salvar: ' + error.message);
                }
            }, 100);
        }

        function loadProject() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.acrobat,.json';
            
            input.onchange = async (e) => {
                const file = e.target.files[0];
                if (!file) return;
                
                showLoading('Carregando projeto...');
                
                try {
                    const text = await file.text();
                    const projectData = JSON.parse(text);
                    
                    if (!projectData.version) {
                        throw new Error('Arquivo de projeto inválido');
                    }
                    
                    // Create blank document with correct number of pages
                    const welcomeScreen = document.getElementById('welcomeScreen');
                    const pagesContainer = document.getElementById('pagesContainer');
                    
                    welcomeScreen.style.display = 'none';
                    pagesContainer.style.display = 'flex';
                    pagesContainer.style.flexDirection = 'column';
                    pagesContainer.style.alignItems = 'center';
                    pagesContainer.innerHTML = '';
                    
                    state.documentName = projectData.documentName;
                    state.totalPages = projectData.totalPages || 1;
                    state.currentPage = projectData.currentPage || 1;
                    state.zoom = projectData.zoom || 1;
                    state.canvasStates = projectData.canvasStates || {};
                    state.bookmarks = projectData.bookmarks || [];
                    state.pdfDoc = null;
                    state.pages = [];
                    
                    // Create pages
                    for (let i = 1; i <= state.totalPages; i++) {
                        const pageContainer = document.createElement('div');
                        pageContainer.className = 'pdf-page-container bg-white';
                        pageContainer.id = `page-${i}`;
                        pageContainer.style.width = '612px';
                        pageContainer.style.height = '792px';
                        
                        const canvas = document.createElement('canvas');
                        canvas.className = 'pdf-canvas';
                        canvas.width = 612;
                        canvas.height = 792;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = 'white';
                        ctx.fillRect(0, 0, 612, 792);
                        
                        pageContainer.appendChild(canvas);
                        
                        const annotationLayer = document.createElement('canvas');
                        annotationLayer.className = 'annotation-layer';
                        annotationLayer.id = `annotation-${i}`;
                        annotationLayer.width = 612;
                        annotationLayer.height = 792;
                        annotationLayer.style.width = '612px';
                        annotationLayer.style.height = '792px';
                        
                        setupAnnotationCanvas(annotationLayer, i);
                        pageContainer.appendChild(annotationLayer);
                        
                        const pageLabel = document.createElement('div');
                        pageLabel.className = 'text-center text-gray-400 text-sm py-2';
                        pageLabel.textContent = `Página ${i}`;
                        
                        const wrapper = document.createElement('div');
                        wrapper.appendChild(pageContainer);
                        wrapper.appendChild(pageLabel);
                        
                        pagesContainer.appendChild(wrapper);
                        state.pages.push({ canvas, annotationLayer, pageNum: i });
                        
                        // Restore canvas state
                        if (state.canvasStates[i]) {
                            const actx = annotationLayer.getContext('2d');
                            const img = new Image();
                            img.onload = () => actx.drawImage(img, 0, 0);
                            img.src = state.canvasStates[i];
                        }
                    }
                    
                    // Update UI
                    document.getElementById('totalPages').textContent = state.totalPages;
                    document.getElementById('documentName').textContent = state.documentName;
                    document.getElementById('documentInfo').textContent = `${state.totalPages} páginas`;
                    
                    // Restore annotations
                    state.annotations = [];
                    state.elements = [];
                    state.comments = [];
                    
                    (projectData.annotations || []).forEach(ann => {
                        if (ann.type === 'text') {
                            const pageContainer = document.getElementById(`page-${ann.pageNum}`);
                            if (pageContainer) {
                                const textBox = document.createElement('div');
                                textBox.className = 'text-annotation';
                                textBox.style.left = `${ann.x}px`;
                                textBox.style.top = `${ann.y}px`;
                                textBox.style.fontSize = `${ann.fontSize || 14}px`;
                                textBox.style.color = ann.textColor || '#000';
                                textBox.contentEditable = true;
                                textBox.textContent = ann.text;
                                
                                textBox.addEventListener('mousedown', (e) => {
                                    if (state.currentTool === 'select') {
                                        e.stopPropagation();
                                        makeDraggable(textBox, e);
                                    }
                                });
                                
                                pageContainer.appendChild(textBox);
                                state.elements.push({ type: 'text', element: textBox, pageNum: ann.pageNum });
                                state.annotations.push(ann);
                            }
                        }
                    });
                    
                    // Restore comments
                    (projectData.comments || []).forEach(c => {
                        const pageContainer = document.getElementById(`page-${c.pageNum}`);
                        if (pageContainer) {
                            const marker = document.createElement('div');
                            marker.className = 'comment-marker';
                            marker.style.left = `${c.x}px`;
                            marker.style.top = `${c.y}px`;
                            marker.innerHTML = '<i class="fas fa-comment-alt"></i>';
                            marker.title = `${c.author}: ${c.text}`;
                            marker.onclick = () => highlightComment(c.id);
                            
                            marker.addEventListener('mousedown', (e) => {
                                if (state.currentTool === 'select') {
                                    e.stopPropagation();
                                    makeDraggable(marker, e);
                                }
                            });
                            
                            pageContainer.appendChild(marker);
                            
                            state.comments.push({...c, element: marker});
                            state.elements.push({ type: 'comment', element: marker, pageNum: c.pageNum, id: c.id });
                        }
                    });
                    
                    updateCommentsPanel();
                    updateBookmarksPanel();
                    
                    // Reset history
                    state.history = [];
                    state.historyIndex = -1;
                    saveToHistory();
                    
                    hideLoading();
                    alert('Projeto carregado com sucesso!');
                } catch (error) {
                    hideLoading();
                    alert('Erro ao carregar projeto: ' + error.message);
                }
            };
            
            input.click();
        }

        // Hidden input for loading projects
        const projectInput = document.createElement('input');
        projectInput.type = 'file';
        projectInput.accept = '.acrobat,.json';
        projectInput.style.display = 'none';
        document.body.appendChild(projectInput);

        function printPDF() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }
            window.print();
        }

        function exportComments() {
            if (state.comments.length === 0) {
                alert('Nenhum comentário para exportar');
                return;
            }
            
            const text = state.comments.map(c => 
                `[Página ${c.pageNum}] ${c.author} (${c.date}):\n${c.text}\n`
            ).join('\n---\n\n');
            
            const blob = new Blob([text], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'comentarios.txt';
            a.click();
            URL.revokeObjectURL(url);
        }

        // Create blank PDF
        function createNewPDF() {
            const welcomeScreen = document.getElementById('welcomeScreen');
            const pagesContainer = document.getElementById('pagesContainer');
            
            welcomeScreen.style.display = 'none';
            pagesContainer.style.display = 'flex';
            pagesContainer.style.flexDirection = 'column';
            pagesContainer.style.alignItems = 'center';
            pagesContainer.innerHTML = '';
            
            const pageContainer = document.createElement('div');
            pageContainer.className = 'pdf-page-container bg-white';
            pageContainer.id = 'page-1';
            pageContainer.style.width = '612px';
            pageContainer.style.height = '792px';
            
            const canvas = document.createElement('canvas');
            canvas.className = 'pdf-canvas';
            canvas.width = 612;
            canvas.height = 792;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, 612, 792);
            
            pageContainer.appendChild(canvas);
            
            const annotationLayer = document.createElement('canvas');
            annotationLayer.className = 'annotation-layer';
            annotationLayer.id = 'annotation-1';
            annotationLayer.width = 612;
            annotationLayer.height = 792;
            annotationLayer.style.width = '612px';
            annotationLayer.style.height = '792px';
            
            setupAnnotationCanvas(annotationLayer, 1);
            pageContainer.appendChild(annotationLayer);
            
            const pageLabel = document.createElement('div');
            pageLabel.className = 'text-center text-gray-400 text-sm py-2';
            pageLabel.textContent = 'Página 1';
            
            const wrapper = document.createElement('div');
            wrapper.appendChild(pageContainer);
            wrapper.appendChild(pageLabel);
            
            pagesContainer.appendChild(wrapper);
            
            state.totalPages = 1;
            state.currentPage = 1;
            state.pdfDoc = null;
            state.pages = [{ canvas, annotationLayer, pageNum: 1 }];
            
            document.getElementById('totalPages').textContent = '1';
            document.getElementById('documentName').textContent = 'Novo documento';
            document.getElementById('documentInfo').textContent = '1 página';
            
            // Reset state
            state.history = [];
            state.historyIndex = -1;
            state.annotations = [];
            state.elements = [];
            state.comments = [];
            state.canvasStates = {};
            saveToHistory();
            
            // Update thumbnails
            const thumbnailsContainer = document.getElementById('thumbnailsContainer');
            thumbnailsContainer.innerHTML = `
                <div class="page-thumbnail cursor-pointer p-2 rounded active" onclick="goToPage(1)">
                    <div class="bg-white w-full h-32"></div>
                    <div class="text-center text-xs text-gray-400 mt-1">1</div>
                </div>
            `;
        }

        // View Mode
        function setViewMode(mode) {
            const pagesContainer = document.getElementById('pagesContainer');
            state.viewMode = mode;
            if (mode === 'two-page') {
                pagesContainer.style.display = 'grid';
                pagesContainer.style.gridTemplateColumns = 'repeat(2, 1fr)';
                pagesContainer.style.gap = '20px';
            } else {
                pagesContainer.style.display = 'flex';
                pagesContainer.style.flexDirection = 'column';
                pagesContainer.style.alignItems = 'center';
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Properties Modal
        function showProperties() {
            const pagesContainer = document.getElementById('pagesContainer');
            if (pagesContainer.style.display === 'none') {
                alert('Nenhum documento aberto');
                return;
            }
            
            document.getElementById('propName').textContent = state.documentName || 'Novo documento';
            document.getElementById('propPages').textContent = state.totalPages;
            document.getElementById('propSize').textContent = state.pdfDoc ? 'PDF carregado' : 'Documento em branco';
            document.getElementById('propAnnotations').textContent = state.annotations.length;
            document.getElementById('propComments').textContent = state.comments.length;
            
            document.getElementById('propertiesModal').classList.add('show');
        }

        function closeProperties() {
            document.getElementById('propertiesModal').classList.remove('show');
        }

        // Shortcuts Modal
        function showShortcuts() {
            document.getElementById('shortcutsModal').classList.add('show');
        }

        function closeShortcuts() {
            document.getElementById('shortcutsModal').classList.remove('show');
        }

        // Modal helpers
        function showLoading(text) {
            document.getElementById('loadingText').textContent = text;
            document.getElementById('loadingModal').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingModal').classList.remove('show');
        }

        function showAbout() {
            document.getElementById('aboutModal').classList.add('show');
        }

        function closeAbout() {
            document.getElementById('aboutModal').classList.remove('show');
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Check if typing in an input
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                if (e.key === 'Escape') {
                    e.target.blur();
                }
                return;
            }

            if (e.ctrlKey || e.metaKey) {
                switch (e.key.toLowerCase()) {
                    case 'o':
                        e.preventDefault();
                        openFile();
                        break;
                    case 's':
                        e.preventDefault();
                        savePDF();
                        break;
                    case 'p':
                        e.preventDefault();
                        printPDF();
                        break;
                    case 'z':
                        e.preventDefault();
                        undo();
                        break;
                    case 'y':
                        e.preventDefault();
                        redo();
                        break;
                    case 'f':
                        e.preventDefault();
                        toggleSearch();
                        break;
                    case '=':
                    case '+':
                        e.preventDefault();
                        zoomIn();
                        break;
                    case '-':
                        e.preventDefault();
                        zoomOut();
                        break;
                }
            }
            
            if (e.key === 'Escape') {
                hideContextMenu();
                closeStampModal();
                closeTextModal();
                closeCommentModal();
                closeAbout();
                closeProperties();
                closeShortcuts();
                setTool('select');
            }
            
            if (!e.ctrlKey && !e.metaKey) {
                switch (e.key) {
                    case 'ArrowLeft':
                    case 'PageUp':
                        previousPage();
                        break;
                    case 'ArrowRight':
                    case 'PageDown':
                        nextPage();
                        break;
                    case 'Home':
                        goToPage(1);
                        break;
                    case 'End':
                        goToPage(state.totalPages);
                        break;
                }
            }
        });

        // Add CSS animation for comment highlight
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(230, 57, 70, 0.7); }
                50% { transform: scale(1.2); box-shadow: 0 0 10px 5px rgba(230, 57, 70, 0.5); }
            }
        `;
        document.head.appendChild(style);
    </script>
    <footer class="footer-clean text-center py-4 border-t border-white/10 mt-auto bg-[#0d0405]">
        <p class="text-[11px] opacity-70 text-gray-400">&copy; <?php echo date('Y'); ?> Acrobat Pro PDF Editor — Processamento 100% Privado no Navegador • <a href="privacidade.php" class="hover:text-red-400 underline">Privacidade</a> | <a href="termos.php" class="hover:text-red-400 underline">Termos</a> | <a href="suporte.php" class="hover:text-red-400 underline">Suporte & Contato</a></p>
    </footer>
    <script>
      // PWA Service Worker Registration & Anti-Cache
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('sw.js').catch(err => console.log('SW reg error:', err));
        });
      }
    </script>
</body>
</html>



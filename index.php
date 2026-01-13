<?php
/**
 * Tag-Press – Interpreter / Einstiegspunkt
 *
 * Die index.php fungiert als Interpreter des Tag-Press Systems.
 * Sie orchestriert den gesamten Rendering-Prozess:
 *
 * 1. Lädt die zentrale Config (stellt m() bereit)
 * 2. Lädt die Geometrie via m('struktur', ...)
 * 3. Validiert die Struktur und Daten
 * 4. Bindet die benötigten Objekte ein via m('daten', ...)
 * 5. Übergibt das Ergebnis an den Renderer
 *
 * WICHTIG: Diese Datei enthält selbst KEINE Layout- oder Inhaltslogik.
 * Sie ist ein Orchestrator, kein Template.
 *
 * @author Rob de Roy
 * @version 0.2
 * @license MIT
 * @link https://robderoy.de
 */

declare(strict_types=1);

// Fehlerbehandlung: Alle Fehler anzeigen (Entwicklungsmodus)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Lade Hauptklassen (lädt automatisch Config.php mit m())
require_once __DIR__ . '/config/classes/main_classes.php';

/**
 * Hauptausführung
 *
 * Tag-Press rendert eine Seite oder bricht bei Fehlern hart ab.
 * Es gibt keine halben Zustände.
 */
try {
    // Seiten-ID aus URL oder Standard 'A' (Startseite)
    $pageId = $_GET['page'] ?? 'A';

    // Debug-Modus via URL-Parameter ?debug=1
    $debugMode = isset($_GET['debug']) && $_GET['debug'] === '1';

    // Tag-Press initialisieren (nutzt m() intern für alle Pfade)
    $tagPress = new TagPress();
    $content = $tagPress->render($pageId);

    // HTML-Dokument ausgeben (mit optionalem Debug-Panel)
    outputDocument($content, $pageId, $debugMode);

} catch (TagPressException $e) {
    // Tag-Press spezifischer Fehler
    outputError($e->getFormattedMessage());

} catch (Throwable $e) {
    // Allgemeiner Fehler
    outputError("Unerwarteter Fehler: " . $e->getMessage() . "\n\nStacktrace:\n" . $e->getTraceAsString());
}

/**
 * Gibt das vollständige HTML-Dokument aus
 *
 * @param string $content   Der gerenderte Seiteninhalt
 * @param string $pageId    Die Seiten-ID
 * @param bool   $showDebug Wenn true, wird das Debug-Panel angezeigt
 */
function outputDocument(string $content, string $pageId, bool $showDebug = false): void
{
    $title = getPageTitle($pageId);
    $version = TAG_PRESS_VERSION;
    $cssPath = asset('styles.css');

    // Debug-Panel generieren wenn aktiviert
    $debugHtml = $showDebug ? debugPanel(true) : '';

    echo <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Tag-Press v{$version}">
    <title>{$title} | Tag-Press</title>
    <link rel="stylesheet" href="{$cssPath}">
</head>
<body data-page="{$pageId}">
    <!-- Skip Links für Barrierefreiheit -->
    <a href="#main-content" class="skip-link">Zum Hauptinhalt springen</a>
    <a href="#footer-content" class="skip-link">Zum Footer springen</a>
    
    <main id="main-content" class="tag-press-main" role="main" aria-label="Hauptinhalt">
{$content}
    </main>
    
    <footer id="footer-content" class="tag-press-footer" role="contentinfo" aria-label="Seiten-Footer">
        <p>Powered by <strong>Tag-Press</strong> v{$version} | Ein Projekt von <a href="https://robderoy.de" rel="noopener noreferrer">Rob de Roy</a></p>
    </footer>
{$debugHtml}
</body>
</html>
HTML;
}

/**
 * Gibt eine Fehlerseite aus
 *
 * Fehler in Tag-Press sind hart. Die Seite wird nicht gerendert,
 * wenn etwas ungültig ist. Stattdessen wird der Fehler klar angezeigt.
 */
function outputError(string $message): void
{
    http_response_code(500);

    echo <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fehler | Tag-Press</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a2e;
            color: #eee;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-container {
            background: #16213e;
            border: 2px solid #e94560;
            border-radius: 8px;
            padding: 2rem;
            max-width: 800px;
            width: 100%;
        }
        .error-title {
            color: #e94560;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .error-title::before {
            content: '!';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            background: #e94560;
            color: #fff;
            border-radius: 50%;
            font-weight: bold;
        }
        .error-message {
            background: #0f0f23;
            padding: 1rem;
            border-radius: 4px;
            white-space: pre-wrap;
            font-size: 0.9rem;
            line-height: 1.6;
            overflow-x: auto;
        }
        .error-hint {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #333;
            color: #888;
            font-size: 0.85rem;
        }
        .error-hint code {
            background: #0f0f23;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            color: #e94560;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-title">Tag-Press Validierungsfehler</h1>
        <pre class="error-message">{$message}</pre>
        <div class="error-hint">
            <p>Das System ist korrekt oder es existiert nicht.</p>
            <p>Pruefen Sie:</p>
            <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                <li>Die Geometrie-Definition in <code>struktur/main_geometrie.php</code></li>
                <li>Die Datenobjekte in <code>daten/*.php</code></li>
                <li>Die Seitenzuweisungen in <code>page_assignments</code></li>
            </ul>
        </div>
    </div>
</body>
</html>
HTML;

    exit(1);
}

/**
 * Ermittelt den Seitentitel basierend auf der Seiten-ID
 */
function getPageTitle(string $pageId): string
{
    $titles = [
        'A' => 'Startseite',
        'B' => 'Ueber uns',
        'C' => 'Kontakt'
    ];

    return $titles[$pageId] ?? 'Seite ' . $pageId;
}

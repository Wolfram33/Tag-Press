<?php
/**
 * Tag-Press – Zentrale Konfiguration
 *
 * Diese Datei ist die einzige Stelle, die die Projektstruktur kennt.
 * Sie stellt die Funktion m() bereit, die alle Pfade relativ zum
 * Projekt-Root berechnet.
 *
 * WICHTIG:
 * - Alle Pfade im Projekt werden über m() aufgelöst
 * - Keine Hardcodings in anderen Dateien
 * - Änderungen an der Struktur nur hier
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Globale Laufzeit-Statistiken (für Didaktik & Performance-Analyse)
|--------------------------------------------------------------------------
|
| Diese Variablen ermöglichen es, den Pfad-Cache und die Ladeoperationen
| zu überwachen. Ideal für Lernende, um Performance zu verstehen.
|
*/

$GLOBALS['TAG_PRESS_PATH_CACHE'] = [];
$GLOBALS['TAG_PRESS_CACHE_STATS'] = ['hits' => 0, 'misses' => 0];
$GLOBALS['TAG_PRESS_LOAD_STATS'] = ['files' => [], 'total' => 0];

/*
|--------------------------------------------------------------------------
| Projekt-Metadaten
|--------------------------------------------------------------------------
*/

define('TAG_PRESS_VERSION', '0.1');
define('TAG_PRESS_AUTHOR', 'Rob de Roy');
define('TAG_PRESS_WEBSITE', 'https://robderoy.de');
define('TAG_PRESS_LICENSE', 'MIT');

/*
|--------------------------------------------------------------------------
| Projekt-Root ermitteln
|--------------------------------------------------------------------------
|
| Der Root wird relativ zu dieser Datei berechnet.
| Diese Datei liegt in: /config/Config.php
| Also ist Root: /../ (ein Verzeichnis höher)
|
*/

define('TAG_PRESS_ROOT', realpath(__DIR__ . '/..') . '/');

/*
|--------------------------------------------------------------------------
| Pfad-Definitionen
|--------------------------------------------------------------------------
|
| Alle Basis-Pfade des Projekts. Diese werden von m() verwendet.
| Neue Verzeichnisse hier hinzufügen.
|
*/

define('TAG_PRESS_PATHS', [
    // Hauptverzeichnisse
    'root'      => TAG_PRESS_ROOT,
    'assets'    => TAG_PRESS_ROOT . 'assets/',
    'struktur'  => TAG_PRESS_ROOT . 'struktur/',
    'daten'     => TAG_PRESS_ROOT . 'daten/',
    'config'    => TAG_PRESS_ROOT . 'config/',
    'examples'  => TAG_PRESS_ROOT . 'examples/',
    'tests'     => TAG_PRESS_ROOT . 'tests/',

    // Config-Unterverzeichnisse
    'layout'    => TAG_PRESS_ROOT . 'config/layout/',
    'classes'   => TAG_PRESS_ROOT . 'config/classes/',

    // Assets-Unterverzeichnisse
    'css'       => TAG_PRESS_ROOT . 'assets/css/',
    'images'    => TAG_PRESS_ROOT . 'assets/images/',
    'js'        => TAG_PRESS_ROOT . 'assets/js/',
]);

/*
|--------------------------------------------------------------------------
| Pfad-Auflösungsfunktion m()
|--------------------------------------------------------------------------
|
| Die zentrale Funktion zur Pfadauflösung.
|
| Verwendung:
|   m('struktur', 'main_geometrie.php')  → /absoluter/pfad/struktur/main_geometrie.php
|   m('daten', 'o1.php')                 → /absoluter/pfad/daten/o1.php
|   m('layout')                          → /absoluter/pfad/config/layout/
|
| Mit Validierung:
|   m('daten', 'o1.php', true)           → Prüft ob Datei existiert
|   m('struktur', '', true)              → Prüft ob Verzeichnis existiert
|
*/

/**
 * Löst einen Pfad relativ zu einem Basisverzeichnis auf.
 *
 * Diese Funktion nutzt einen internen Cache, um wiederholte Pfadauflösungen
 * zu beschleunigen. Didaktisch interessant: Der Cache zeigt, wie
 * Memoization Performance verbessert, ohne Determinismus zu gefährden.
 *
 * @param string $type     Der Pfadtyp (z.B. 'daten', 'struktur', 'layout')
 * @param string $name     Optionaler Datei-/Unterverzeichnisname
 * @param bool   $validate Wenn true, wird geprüft ob der Pfad existiert
 *
 * @return string Der aufgelöste absolute Pfad
 *
 * @throws TagPressException Wenn Pfadtyp unbekannt oder Pfad nicht existiert
 */
function m(string $type, string $name = '', bool $validate = false): string
{
    // Cache-Key generieren (Pfadtyp + Name + Validierungsflag)
    $cacheKey = "{$type}:{$name}:" . ($validate ? '1' : '0');

    // Cache-Hit prüfen
    if (isset($GLOBALS['TAG_PRESS_PATH_CACHE'][$cacheKey])) {
        $GLOBALS['TAG_PRESS_CACHE_STATS']['hits']++;
        return $GLOBALS['TAG_PRESS_PATH_CACHE'][$cacheKey];
    }

    $GLOBALS['TAG_PRESS_CACHE_STATS']['misses']++;
    $paths = TAG_PRESS_PATHS;

    // Prüfe ob Pfadtyp bekannt ist
    if (!isset($paths[$type])) {
        $available = implode(', ', array_keys($paths));
        throw new TagPressException(
            "Unbekannter Pfadtyp: '{$type}'",
            "Verfügbare Typen: {$available}"
        );
    }

    $fullPath = $paths[$type] . $name;

    // Validierung wenn gewünscht
    if ($validate) {
        if ($name === '') {
            // Verzeichnis-Validierung
            if (!is_dir($fullPath)) {
                throw new TagPressException(
                    "Verzeichnis nicht gefunden",
                    $fullPath
                );
            }
        } else {
            // Datei-Validierung
            if (!file_exists($fullPath)) {
                throw new TagPressException(
                    "Datei nicht gefunden",
                    $fullPath
                );
            }
        }
    }

    // In Cache speichern
    $GLOBALS['TAG_PRESS_PATH_CACHE'][$cacheKey] = $fullPath;

    return $fullPath;
}

/*
|--------------------------------------------------------------------------
| Erweiterte Pfad-Hilfsfunktionen
|--------------------------------------------------------------------------
*/

/**
 * Lädt eine PHP-Datei und gibt deren Return-Wert zurück.
 *
 * Diese Funktion enthält strikte Typprüfung, um sicherzustellen,
 * dass geladene Dateien die erwarteten Datenstrukturen zurückgeben.
 * Didaktisch: Zeigt wie defensive Programmierung aussieht.
 *
 * @param string      $type         Pfadtyp
 * @param string      $name         Dateiname
 * @param string|null $expectType   Erwarteter Typ: 'array', 'string', 'int', 'bool' oder null für beliebig
 *
 * @return mixed Der Return-Wert der Datei
 *
 * @throws TagPressException Wenn Datei nicht existiert, ungültig oder falscher Typ
 */
function load(string $type, string $name, ?string $expectType = 'array'): mixed
{
    $path = m($type, $name, true);

    // Lade-Statistik tracken
    $startTime = microtime(true);
    $result = require $path;
    $loadTime = (microtime(true) - $startTime) * 1000; // ms

    // Statistik speichern
    $GLOBALS['TAG_PRESS_LOAD_STATS']['files'][$path] = [
        'time_ms' => round($loadTime, 3),
        'type' => gettype($result)
    ];
    $GLOBALS['TAG_PRESS_LOAD_STATS']['total']++;

    // Prüfe ob Datei überhaupt etwas zurückgibt
    if ($result === false || $result === 1) {
        throw new TagPressException(
            "Datei gibt keinen Wert zurück",
            $path
        );
    }

    // Strikte Typprüfung wenn erwartet
    if ($expectType !== null) {
        $actualType = gettype($result);
        $typeMap = [
            'array' => 'array',
            'string' => 'string',
            'int' => 'integer',
            'integer' => 'integer',
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'float' => 'double',
            'double' => 'double'
        ];

        $expectedPhpType = $typeMap[$expectType] ?? $expectType;

        if ($actualType !== $expectedPhpType) {
            throw new TagPressException(
                "Typfehler: Erwartet '{$expectType}', erhalten '{$actualType}'",
                $path
            );
        }
    }

    return $result;
}

/**
 * Prüft ob ein Pfad existiert.
 *
 * @param string $type Pfadtyp
 * @param string $name Optionaler Datei-/Verzeichnisname
 *
 * @return bool True wenn Pfad existiert
 */
function pathExists(string $type, string $name = ''): bool
{
    try {
        $path = m($type, $name);
        return $name === '' ? is_dir($path) : file_exists($path);
    } catch (TagPressException $e) {
        return false;
    }
}

/**
 * Listet alle Dateien in einem Verzeichnis auf.
 *
 * @param string $type      Pfadtyp
 * @param string $pattern   Glob-Pattern (z.B. '*.php')
 *
 * @return array Liste der Dateinamen (ohne Pfad)
 */
function listFiles(string $type, string $pattern = '*'): array
{
    $path = m($type, '', true);
    $files = glob($path . $pattern);

    return array_map('basename', $files ?: []);
}

/**
 * Gibt den Web-Pfad für Assets zurück (relativ zum Document Root).
 *
 * @param string $name Dateiname im assets-Verzeichnis
 *
 * @return string Relativer Web-Pfad
 */
function asset(string $name): string
{
    return '/assets/' . ltrim($name, '/');
}

/*
|--------------------------------------------------------------------------
| Debug-Hilfsfunktionen
|--------------------------------------------------------------------------
*/

/**
 * Gibt alle konfigurierten Pfade aus (für Debugging).
 *
 * @return array Alle Pfade mit Existenz-Status
 */
function debugPaths(): array
{
    $result = [];
    foreach (TAG_PRESS_PATHS as $type => $path) {
        $result[$type] = [
            'path' => $path,
            'exists' => is_dir($path) || file_exists($path),
            'writable' => is_writable($path)
        ];
    }
    return $result;
}

/**
 * Gibt Projektinformationen zurück.
 *
 * @return array Projekt-Metadaten
 */
function projectInfo(): array
{
    return [
        'name' => 'Tag-Press',
        'version' => TAG_PRESS_VERSION,
        'author' => TAG_PRESS_AUTHOR,
        'website' => TAG_PRESS_WEBSITE,
        'license' => TAG_PRESS_LICENSE,
        'root' => TAG_PRESS_ROOT,
        'php_version' => PHP_VERSION
    ];
}

/*
|--------------------------------------------------------------------------
| Performance & Cache-Funktionen
|--------------------------------------------------------------------------
*/

/**
 * Gibt Cache-Statistiken zurück.
 *
 * Didaktisch: Zeigt wie effektiv der Cache ist und wie viele
 * Pfadauflösungen eingespart wurden.
 *
 * @return array Cache-Statistiken
 */
function cacheStats(): array
{
    $stats = $GLOBALS['TAG_PRESS_CACHE_STATS'];
    $total = $stats['hits'] + $stats['misses'];

    return [
        'hits' => $stats['hits'],
        'misses' => $stats['misses'],
        'total' => $total,
        'hit_rate' => $total > 0 ? round($stats['hits'] / $total * 100, 1) : 0,
        'cache_size' => count($GLOBALS['TAG_PRESS_PATH_CACHE'])
    ];
}

/**
 * Gibt Lade-Statistiken zurück.
 *
 * @return array Lade-Statistiken aller geladenen Dateien
 */
function loadStats(): array
{
    return $GLOBALS['TAG_PRESS_LOAD_STATS'];
}

/**
 * Leert den Pfad-Cache.
 *
 * Didaktisch: Zeigt den Unterschied zwischen gecachten und
 * ungecachten Zugriffen.
 */
function clearPathCache(): void
{
    $GLOBALS['TAG_PRESS_PATH_CACHE'] = [];
    $GLOBALS['TAG_PRESS_CACHE_STATS'] = ['hits' => 0, 'misses' => 0];
}

/*
|--------------------------------------------------------------------------
| Relative Pfade zwischen Objekten
|--------------------------------------------------------------------------
*/

/**
 * Berechnet den relativen Pfad zwischen zwei Objekten/Dateien.
 *
 * Nützlich wenn Datenobjekte aufeinander verweisen müssen,
 * z.B. ein Bild-Objekt auf ein anderes Bild-Asset.
 *
 * @param string $fromType Quell-Pfadtyp
 * @param string $fromName Quell-Dateiname
 * @param string $toType   Ziel-Pfadtyp
 * @param string $toName   Ziel-Dateiname
 *
 * @return string Relativer Pfad von Quelle zu Ziel
 */
function relativePath(string $fromType, string $fromName, string $toType, string $toName): string
{
    $fromPath = dirname(m($fromType, $fromName));
    $toPath = m($toType, $toName);

    // Pfade in Segmente aufteilen
    $fromParts = explode('/', trim($fromPath, '/'));
    $toParts = explode('/', trim($toPath, '/'));

    // Gemeinsamen Basis-Pfad finden
    $commonLength = 0;
    $minLength = min(count($fromParts), count($toParts));

    for ($i = 0; $i < $minLength; $i++) {
        if ($fromParts[$i] === $toParts[$i]) {
            $commonLength++;
        } else {
            break;
        }
    }

    // Relativen Pfad berechnen
    $upCount = count($fromParts) - $commonLength;
    $relativeParts = array_merge(
        array_fill(0, $upCount, '..'),
        array_slice($toParts, $commonLength)
    );

    return implode('/', $relativeParts);
}

/**
 * Prüft ob ein Objekt auf ein anderes verweisen darf.
 *
 * Didaktisch: Zeigt das Konzept von Abhängigkeitsprüfung.
 *
 * @param string $fromObjectId Quell-Objekt-ID
 * @param string $toObjectId   Ziel-Objekt-ID
 *
 * @return bool True wenn Verweis erlaubt
 */
function canReference(string $fromObjectId, string $toObjectId): bool
{
    // Selbstreferenz ist nicht erlaubt
    if ($fromObjectId === $toObjectId) {
        return false;
    }

    // Beide Objekte müssen existieren
    return pathExists('daten', strtolower($fromObjectId) . '.php')
        && pathExists('daten', strtolower($toObjectId) . '.php');
}

/*
|--------------------------------------------------------------------------
| Debug-Panel für Demo/Ausbildung
|--------------------------------------------------------------------------
*/

/**
 * Generiert ein HTML Debug-Panel.
 *
 * Zeigt live alle Pfade, Cache-Statistiken und Projektinfo an.
 * Ideal für Lernende, um die Struktur zu verstehen.
 *
 * @param bool $expanded Panel standardmäßig ausgeklappt
 *
 * @return string HTML für das Debug-Panel
 */
function debugPanel(bool $expanded = false): string
{
    $info = projectInfo();
    $paths = debugPaths();
    $cache = cacheStats();
    $loads = loadStats();

    $expandedAttr = $expanded ? 'open' : '';
    $pathCount = count($paths);
    $pathRows = '';
    foreach ($paths as $type => $data) {
        $status = $data['exists'] ? '✓' : '✗';
        $statusClass = $data['exists'] ? 'ok' : 'error';
        $pathRows .= "<tr><td><code>{$type}</code></td><td class='path'>{$data['path']}</td><td class='{$statusClass}'>{$status}</td></tr>";
    }

    $loadRows = '';
    foreach ($loads['files'] as $file => $data) {
        $shortPath = str_replace(TAG_PRESS_ROOT, '', $file);
        $loadRows .= "<tr><td class='path'>{$shortPath}</td><td>{$data['time_ms']} ms</td><td>{$data['type']}</td></tr>";
    }

    return <<<HTML
<details class="tag-press-debug-panel" {$expandedAttr}>
    <summary>🔧 Tag-Press Debug-Panel</summary>
    <div class="debug-content">
        <section class="debug-section">
            <h4>Projekt-Info</h4>
            <table>
                <tr><td>Name</td><td><strong>{$info['name']}</strong></td></tr>
                <tr><td>Version</td><td>{$info['version']}</td></tr>
                <tr><td>Autor</td><td>{$info['author']}</td></tr>
                <tr><td>PHP</td><td>{$info['php_version']}</td></tr>
                <tr><td>Root</td><td class="path">{$info['root']}</td></tr>
            </table>
        </section>

        <section class="debug-section">
            <h4>Cache-Statistiken</h4>
            <table>
                <tr><td>Hits</td><td>{$cache['hits']}</td></tr>
                <tr><td>Misses</td><td>{$cache['misses']}</td></tr>
                <tr><td>Hit-Rate</td><td><strong>{$cache['hit_rate']}%</strong></td></tr>
                <tr><td>Cache-Größe</td><td>{$cache['cache_size']} Einträge</td></tr>
            </table>
        </section>

        <section class="debug-section">
            <h4>Pfad-Typen ({$pathCount} Stück)</h4>
            <table>
                <thead><tr><th>Typ</th><th>Pfad</th><th>Status</th></tr></thead>
                <tbody>{$pathRows}</tbody>
            </table>
        </section>

        <section class="debug-section">
            <h4>Geladene Dateien ({$loads['total']} Stück)</h4>
            <table>
                <thead><tr><th>Datei</th><th>Zeit</th><th>Typ</th></tr></thead>
                <tbody>{$loadRows}</tbody>
            </table>
        </section>
    </div>
</details>
<style>
.tag-press-debug-panel {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    background: #1a1a2e;
    color: #eee;
    border: 1px solid #333;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    max-width: 600px;
    max-height: 80vh;
    overflow: auto;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}
.tag-press-debug-panel summary {
    padding: 0.75rem 1rem;
    cursor: pointer;
    background: #16213e;
    border-radius: 8px 8px 0 0;
    font-weight: bold;
}
.tag-press-debug-panel[open] summary {
    border-bottom: 1px solid #333;
}
.tag-press-debug-panel .debug-content {
    padding: 1rem;
}
.tag-press-debug-panel .debug-section {
    margin-bottom: 1rem;
}
.tag-press-debug-panel .debug-section:last-child {
    margin-bottom: 0;
}
.tag-press-debug-panel h4 {
    color: #e94560;
    margin: 0 0 0.5rem 0;
    font-size: 11px;
    text-transform: uppercase;
}
.tag-press-debug-panel table {
    width: 100%;
    border-collapse: collapse;
}
.tag-press-debug-panel td, .tag-press-debug-panel th {
    padding: 0.25rem 0.5rem;
    text-align: left;
    border-bottom: 1px solid #333;
}
.tag-press-debug-panel th {
    color: #888;
    font-weight: normal;
}
.tag-press-debug-panel .path {
    color: #888;
    font-size: 10px;
    word-break: break-all;
}
.tag-press-debug-panel .ok { color: #4ade80; }
.tag-press-debug-panel .error { color: #e94560; }
</style>
HTML;
}

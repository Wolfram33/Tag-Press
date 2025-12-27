<?php
/**
 * Tag-Press – Validator Unit Tests
 *
 * Diese Tests prüfen die Validierungslogik des Tag-Press Systems.
 * Sie können ohne PHPUnit ausgeführt werden (standalone).
 *
 * Ausführung:
 *   php tests/ValidatorTest.php
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

declare(strict_types=1);

// Pfade definieren
define('TAG_PRESS_BASE', dirname(__DIR__));
define('TEST_DATA_PATH', __DIR__ . '/test-data');

// Klassen laden
require_once TAG_PRESS_BASE . '/config/classes/main_classes.php';

/**
 * Einfaches Test-Framework
 */
class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function assert(bool $condition, string $message): void
    {
        if ($condition) {
            $this->passed++;
            echo "  ✓ {$message}\n";
        } else {
            $this->failed++;
            $this->failures[] = $message;
            echo "  ✗ {$message}\n";
        }
    }

    public function expectException(callable $callback, string $exceptionClass, string $message): void
    {
        try {
            $callback();
            $this->failed++;
            $this->failures[] = $message . " (keine Exception geworfen)";
            echo "  ✗ {$message} (keine Exception geworfen)\n";
        } catch (Throwable $e) {
            if ($e instanceof $exceptionClass) {
                $this->passed++;
                echo "  ✓ {$message}\n";
            } else {
                $this->failed++;
                $this->failures[] = $message . " (falsche Exception: " . get_class($e) . ")";
                echo "  ✗ {$message} (falsche Exception: " . get_class($e) . ")\n";
            }
        }
    }

    public function run(string $name, callable $test): void
    {
        echo "\n{$name}\n" . str_repeat('-', strlen($name)) . "\n";
        try {
            $test($this);
        } catch (Throwable $e) {
            $this->failed++;
            $this->failures[] = "{$name}: " . $e->getMessage();
            echo "  ✗ Unerwarteter Fehler: " . $e->getMessage() . "\n";
        }
    }

    public function summary(): int
    {
        echo "\n" . str_repeat('=', 50) . "\n";
        echo "Ergebnis: {$this->passed} bestanden, {$this->failed} fehlgeschlagen\n";

        if (!empty($this->failures)) {
            echo "\nFehlgeschlagene Tests:\n";
            foreach ($this->failures as $failure) {
                echo "  - {$failure}\n";
            }
        }

        echo str_repeat('=', 50) . "\n";

        return $this->failed > 0 ? 1 : 0;
    }
}

// Test-Daten Verzeichnis erstellen
if (!is_dir(TEST_DATA_PATH)) {
    mkdir(TEST_DATA_PATH, 0755, true);
}

/**
 * Hilfsfunktion: Erstellt temporäres Testobjekt
 */
function createTestObject(string $id, array $data): void
{
    $content = "<?php\nreturn " . var_export($data, true) . ";\n";
    file_put_contents(TEST_DATA_PATH . '/' . strtolower($id) . '.php', $content);
}

/**
 * Hilfsfunktion: Löscht Testobjekte
 */
function cleanupTestData(): void
{
    $files = glob(TEST_DATA_PATH . '/*.php');
    foreach ($files as $file) {
        unlink($file);
    }
}

// =============================================================================
// TESTS
// =============================================================================

$runner = new TestRunner();

echo "\n";
echo "╔══════════════════════════════════════════════════╗\n";
echo "║       TAG-PRESS VALIDATOR UNIT TESTS             ║\n";
echo "╚══════════════════════════════════════════════════╝\n";

// -----------------------------------------------------------------------------
// Test 1: Gültige Seite wird akzeptiert
// -----------------------------------------------------------------------------
$runner->run('Test 1: Gültige Seite wird akzeptiert', function(TestRunner $t) {
    $tagPress = new TagPress();
    $validator = $tagPress->getValidator();

    $result = $validator->validatePage('A');
    $t->assert($result === true, 'Seite A sollte validiert werden');
    $t->assert(empty($validator->getErrors()), 'Keine Fehler erwartet');
});

// -----------------------------------------------------------------------------
// Test 2: Nicht existierende Seite wird abgelehnt
// -----------------------------------------------------------------------------
$runner->run('Test 2: Nicht existierende Seite wird abgelehnt', function(TestRunner $t) {
    $tagPress = new TagPress();
    $validator = $tagPress->getValidator();

    $t->expectException(
        fn() => $validator->validatePage('X'),
        TagPressException::class,
        'Seite X sollte TagPressException werfen'
    );
});

// -----------------------------------------------------------------------------
// Test 3: Geometrie-Parser lädt korrekt
// -----------------------------------------------------------------------------
$runner->run('Test 3: Geometrie-Parser lädt korrekt', function(TestRunner $t) {
    $parser = new GeometryParser();
    $geometry = $parser->load();

    $t->assert(isset($geometry['pages']), 'Geometrie enthält pages');
    $t->assert(isset($geometry['object_types']), 'Geometrie enthält object_types');
    $t->assert(isset($geometry['page_assignments']), 'Geometrie enthält page_assignments');
    $t->assert(isset($geometry['pages']['A']), 'Seite A ist definiert');
});

// -----------------------------------------------------------------------------
// Test 4: Tag-Notation wird korrekt geparst
// -----------------------------------------------------------------------------
$runner->run('Test 4: Tag-Notation wird korrekt geparst', function(TestRunner $t) {
    $parser = new GeometryParser();
    $parser->load();

    $result = $parser->parseTagNotation('Z1=O1,O2,O3');
    $t->assert($result['zone'] === 'Z1', 'Zone ist Z1');
    $t->assert(count($result['objects']) === 3, 'Drei Objekte erkannt');
    $t->assert($result['objects'][0] === 'O1', 'Erstes Objekt ist O1');
});

// -----------------------------------------------------------------------------
// Test 5: Ungültige Tag-Notation wird abgelehnt
// -----------------------------------------------------------------------------
$runner->run('Test 5: Ungültige Tag-Notation wird abgelehnt', function(TestRunner $t) {
    $parser = new GeometryParser();
    $parser->load();

    $t->expectException(
        fn() => $parser->parseTagNotation('INVALID'),
        TagPressException::class,
        'Ungültige Notation sollte Exception werfen'
    );

    $t->expectException(
        fn() => $parser->parseTagNotation('Z1-O1,O2'),
        TagPressException::class,
        'Notation mit - statt = sollte Exception werfen'
    );
});

// -----------------------------------------------------------------------------
// Test 6: DataLoader findet Objekte
// -----------------------------------------------------------------------------
$runner->run('Test 6: DataLoader findet Objekte', function(TestRunner $t) {
    $loader = new DataLoader();

    $o1 = $loader->load('O1');
    $t->assert(isset($o1['type']), 'Objekt hat type');
    $t->assert($o1['type'] === 'image', 'O1 ist ein image');

    $o2 = $loader->load('O2');
    $t->assert($o2['type'] === 'text', 'O2 ist ein text');
});

// -----------------------------------------------------------------------------
// Test 7: DataLoader wirft Exception bei fehlendem Objekt
// -----------------------------------------------------------------------------
$runner->run('Test 7: DataLoader wirft Exception bei fehlendem Objekt', function(TestRunner $t) {
    $loader = new DataLoader();

    $t->expectException(
        fn() => $loader->load('NICHT_VORHANDEN'),
        TagPressException::class,
        'Fehlendes Objekt sollte Exception werfen'
    );
});

// -----------------------------------------------------------------------------
// Test 8: Objekttypen sind korrekt definiert
// -----------------------------------------------------------------------------
$runner->run('Test 8: Objekttypen sind korrekt definiert', function(TestRunner $t) {
    $parser = new GeometryParser();
    $geometry = $parser->load();

    $imageType = $geometry['object_types']['image'];
    $t->assert(isset($imageType['type']), 'image hat type-Kategorie');
    $t->assert($imageType['type'] === 'scalar', 'image ist scalar');
    $t->assert(isset($imageType['attributes']), 'image hat attributes');
    $t->assert(isset($imageType['attributes']['src']), 'image hat src-Attribut');
    $t->assert(isset($imageType['attributes']['alt']), 'image hat alt-Attribut');
});

// -----------------------------------------------------------------------------
// Test 9: Pflichtattribute werden geprüft
// -----------------------------------------------------------------------------
$runner->run('Test 9: Pflichtattribute werden geprüft', function(TestRunner $t) {
    // Teste mit den vorhandenen Daten
    $tagPress = new TagPress();
    $loader = $tagPress->getDataLoader();

    $o1 = $loader->load('O1');
    $t->assert(isset($o1['src']), 'O1 hat src');
    $t->assert(isset($o1['alt']), 'O1 hat alt');
    $t->assert(strlen($o1['alt']) >= 5, 'Alt-Text hat mind. 5 Zeichen');
});

// -----------------------------------------------------------------------------
// Test 10: Renderer erzeugt valides HTML
// -----------------------------------------------------------------------------
$runner->run('Test 10: Renderer erzeugt valides HTML', function(TestRunner $t) {
    $tagPress = new TagPress();
    $html = $tagPress->render('A');

    $t->assert(str_contains($html, '<section'), 'HTML enthält section-Tags');
    $t->assert(str_contains($html, 'data-zone="Z1"'), 'HTML enthält Zone-Marker');
    $t->assert(str_contains($html, 'data-object='), 'HTML enthält Objekt-Marker');
    $t->assert(str_contains($html, 'loading="lazy"'), 'Bilder haben lazy loading');
});

// -----------------------------------------------------------------------------
// Test 11: Zonen-Validierung funktioniert
// -----------------------------------------------------------------------------
$runner->run('Test 11: Zonen-Validierung funktioniert', function(TestRunner $t) {
    $parser = new GeometryParser();
    $geometry = $parser->load();

    $pageA = $geometry['pages']['A'];
    $t->assert(isset($pageA['zones']['Z1']), 'Seite A hat Zone Z1');
    $t->assert(isset($pageA['zones']['Z1']['allowed_objects']), 'Z1 hat allowed_objects');
    $t->assert(in_array('O1', $pageA['zones']['Z1']['allowed_objects']), 'O1 ist in Z1 erlaubt');
});

// -----------------------------------------------------------------------------
// Test 12: Grid-Master ist reines Mapping
// -----------------------------------------------------------------------------
$runner->run('Test 12: Grid-Master ist reines Mapping', function(TestRunner $t) {
    $gridMaster = load('layout', 'grid_master.php');

    $t->assert(is_array($gridMaster), 'Grid-Master ist ein Array');
    $t->assert(isset($gridMaster['zones']), 'Grid-Master hat zones');
    $t->assert(isset($gridMaster['objects']), 'Grid-Master hat objects');
    $t->assert(is_string($gridMaster['zones']['Z1']), 'Zone-Mapping ist String');
});

// -----------------------------------------------------------------------------
// Test 13: Zentrale Config m() Funktion
// -----------------------------------------------------------------------------
$runner->run('Test 13: Zentrale Config m() Funktion', function(TestRunner $t) {
    // Teste Pfadauflösung
    $t->assert(str_ends_with(m('daten'), 'daten/'), 'm(daten) endet mit daten/');
    $t->assert(str_ends_with(m('struktur'), 'struktur/'), 'm(struktur) endet mit struktur/');
    $t->assert(str_ends_with(m('layout'), 'config/layout/'), 'm(layout) endet mit config/layout/');

    // Teste mit Dateiname
    $t->assert(str_ends_with(m('daten', 'o1.php'), 'daten/o1.php'), 'm(daten, o1.php) korrekt');

    // Teste pathExists
    $t->assert(pathExists('daten', 'o1.php'), 'pathExists findet o1.php');
    $t->assert(!pathExists('daten', 'nicht_vorhanden.php'), 'pathExists erkennt fehlende Datei');

    // Teste listFiles
    $objects = listFiles('daten', '*.php');
    $t->assert(count($objects) >= 10, 'listFiles findet mindestens 10 Objekte');
    $t->assert(in_array('o1.php', $objects), 'listFiles enthält o1.php');

    // Teste asset()
    $t->assert(asset('styles.css') === '/assets/styles.css', 'asset() erzeugt korrekten Pfad');

    // Teste projectInfo()
    $info = projectInfo();
    $t->assert($info['name'] === 'Tag-Press', 'projectInfo enthält Namen');
    $t->assert($info['version'] === '0.1', 'projectInfo enthält Version');
});

// -----------------------------------------------------------------------------
// Test 14: m() wirft Exception bei unbekanntem Typ
// -----------------------------------------------------------------------------
$runner->run('Test 14: m() wirft Exception bei unbekanntem Typ', function(TestRunner $t) {
    $t->expectException(
        fn() => m('unbekannt'),
        TagPressException::class,
        'Unbekannter Pfadtyp sollte Exception werfen'
    );
});

// -----------------------------------------------------------------------------
// Test 15: Cache-Statistiken funktionieren
// -----------------------------------------------------------------------------
$runner->run('Test 15: Cache-Statistiken funktionieren', function(TestRunner $t) {
    // Cache leeren für sauberen Test
    clearPathCache();

    // Erste Zugriffe (Misses)
    m('daten', 'o1.php');
    m('daten', 'o2.php');

    // Wiederholte Zugriffe (Hits)
    m('daten', 'o1.php');
    m('daten', 'o1.php');

    $stats = cacheStats();
    $t->assert($stats['hits'] >= 2, 'Mindestens 2 Cache-Hits');
    $t->assert($stats['misses'] >= 2, 'Mindestens 2 Cache-Misses');
    $t->assert($stats['hit_rate'] > 0, 'Hit-Rate ist positiv');
    $t->assert($stats['cache_size'] >= 2, 'Cache enthält mindestens 2 Einträge');
});

// -----------------------------------------------------------------------------
// Test 16: load() mit Typprüfung
// -----------------------------------------------------------------------------
$runner->run('Test 16: load() mit Typprüfung', function(TestRunner $t) {
    // Standard: Array erwartet (funktioniert)
    $data = load('daten', 'o1.php');
    $t->assert(is_array($data), 'load() gibt Array zurück');

    // Explizit Array erwartet
    $data2 = load('daten', 'o2.php', 'array');
    $t->assert(is_array($data2), 'load() mit explizitem array-Typ');

    // Ohne Typprüfung (null)
    $data3 = load('daten', 'o3.php', null);
    $t->assert($data3 !== null, 'load() ohne Typprüfung akzeptiert alles');
});

// -----------------------------------------------------------------------------
// Test 17: Lade-Statistiken werden erfasst
// -----------------------------------------------------------------------------
$runner->run('Test 17: Lade-Statistiken werden erfasst', function(TestRunner $t) {
    $stats = loadStats();

    $t->assert(is_array($stats), 'loadStats() gibt Array zurück');
    $t->assert(isset($stats['total']), 'Statistik enthält total');
    $t->assert($stats['total'] > 0, 'Mindestens eine Datei wurde geladen');
    $t->assert(isset($stats['files']), 'Statistik enthält files');
    $t->assert(count($stats['files']) > 0, 'Mindestens eine Datei in Liste');
});

// -----------------------------------------------------------------------------
// Test 18: Relative Pfade zwischen Objekten
// -----------------------------------------------------------------------------
$runner->run('Test 18: Relative Pfade zwischen Objekten', function(TestRunner $t) {
    $relPath = relativePath('daten', 'o1.php', 'assets', 'styles.css');
    $t->assert(str_contains($relPath, '..'), 'Relativer Pfad enthält ..');
    $t->assert(str_contains($relPath, 'assets'), 'Relativer Pfad enthält Ziel-Verzeichnis');

    // Pfad im gleichen Verzeichnis
    $sameDirPath = relativePath('daten', 'o1.php', 'daten', 'o2.php');
    $t->assert(!str_contains($sameDirPath, '..') || str_contains($sameDirPath, 'o2.php'),
               'Pfad im gleichen Verzeichnis');
});

// -----------------------------------------------------------------------------
// Test 19: Objektreferenzen prüfen
// -----------------------------------------------------------------------------
$runner->run('Test 19: Objektreferenzen prüfen', function(TestRunner $t) {
    // Gültige Referenz zwischen existierenden Objekten
    $t->assert(canReference('O1', 'O2'), 'O1 kann O2 referenzieren');
    $t->assert(canReference('O2', 'O1'), 'O2 kann O1 referenzieren');

    // Selbstreferenz nicht erlaubt
    $t->assert(!canReference('O1', 'O1'), 'Selbstreferenz nicht erlaubt');

    // Nicht existierende Objekte
    $t->assert(!canReference('O1', 'NICHT_DA'), 'Referenz zu fehlendem Objekt nicht erlaubt');
    $t->assert(!canReference('NICHT_DA', 'O1'), 'Referenz von fehlendem Objekt nicht erlaubt');
});

// -----------------------------------------------------------------------------
// Test 20: Debug-Panel generiert HTML
// -----------------------------------------------------------------------------
$runner->run('Test 20: Debug-Panel generiert HTML', function(TestRunner $t) {
    $panel = debugPanel();

    $t->assert(str_contains($panel, 'tag-press-debug-panel'), 'Panel hat CSS-Klasse');
    $t->assert(str_contains($panel, 'Tag-Press'), 'Panel zeigt Projektnamen');
    $t->assert(str_contains($panel, 'Cache-Statistiken'), 'Panel zeigt Cache-Info');
    $t->assert(str_contains($panel, 'Pfad-Typen'), 'Panel zeigt Pfad-Typen');
});

// -----------------------------------------------------------------------------
// Aufräumen und Zusammenfassung
// -----------------------------------------------------------------------------
cleanupTestData();

exit($runner->summary());

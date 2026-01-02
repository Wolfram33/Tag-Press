<?php
/**
 * Tag-Press – Onepage Demo
 *
 * Diese Datei ist ein eigenständiges Demo-Beispiel für eine Onepage-Website,
 * die die Tag-Press Logik verwendet.
 *
 * Dieses Demo ist vollständig in sich geschlossen und kann unabhängig
 * vom Hauptprojekt ausgeführt werden.
 *
 * Struktur:
 * - examples/onepage/
 *   ├── index.php          (diese Datei)
 *   ├── assets/styles.css  (Onepage-Styles)
 *   ├── struktur/geometrie.php (Semantische Geometrie)
 *   └── daten/*.php        (Datenobjekte)
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

declare(strict_types=1);

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Demo-Version Konstante
define('TAG_PRESS_VERSION', '0.1');

// Basis-Verzeichnis für dieses Demo
define('ONEPAGE_ROOT', __DIR__ . '/');

/**
 * Onepage-Klasse
 *
 * Spezialisierte Version des TagPress-Systems für Onepage-Websites.
 */
class OnepageRenderer
{
    private array $geometry;
    private array $objects = [];

    /**
     * Initialisiert den Onepage-Renderer
     */
    public function __construct()
    {
        $this->loadGeometry();
        $this->loadObjects();
    }

    /**
     * Lädt die Onepage-Geometrie
     */
    private function loadGeometry(): void
    {
        $geometryPath = ONEPAGE_ROOT . 'struktur/geometrie.php';
        
        if (!file_exists($geometryPath)) {
            throw new Exception("Onepage-Geometrie nicht gefunden: {$geometryPath}");
        }

        $this->geometry = require $geometryPath;
    }

    /**
     * Lädt alle Datenobjekte für die Onepage
     */
    private function loadObjects(): void
    {
        $dataPath = ONEPAGE_ROOT . 'daten/';
        
        if (!is_dir($dataPath)) {
            throw new Exception("Onepage-Datenverzeichnis nicht gefunden: {$dataPath}");
        }

        $files = glob($dataPath . '*.php');
        
        foreach ($files as $file) {
            $objectId = strtoupper(pathinfo($file, PATHINFO_FILENAME));
            $this->objects[$objectId] = require $file;
        }
    }

    /**
     * Rendert die komplette Onepage
     */
    public function render(): string
    {
        $html = $this->renderNavigation();
        
        $page = $this->geometry['pages']['ONEPAGE'] ?? null;
        
        if (!$page) {
            throw new Exception("ONEPAGE-Definition nicht gefunden in der Geometrie");
        }

        foreach ($page['zones'] as $zoneId => $zoneDef) {
            $html .= $this->renderZone($zoneId, $zoneDef);
        }

        return $html;
    }

    /**
     * Rendert die sticky Navigation
     */
    private function renderNavigation(): string
    {
        $navItems = [
            'home' => 'Start',
            'services' => 'Leistungen',
            'about' => 'Über Uns',
            'portfolio' => 'Projekte',
            'contact' => 'Kontakt'
        ];

        $html = '<nav class="onepage-nav">';
        $html .= '<div class="nav-container">';
        $html .= '<a href="#home" class="nav-logo">Tag-Press</a>';
        $html .= '<ul class="nav-links">';
        
        foreach ($navItems as $anchor => $label) {
            $html .= "<li><a href=\"#{$anchor}\">{$label}</a></li>";
        }
        
        $html .= '</ul>';
        $html .= '<button class="nav-toggle" aria-label="Menü öffnen">☰</button>';
        $html .= '</div>';
        $html .= '</nav>';

        return $html;
    }

    /**
     * Rendert eine einzelne Zone/Sektion
     */
    private function renderZone(string $zoneId, array $zoneDef): string
    {
        $anchor = $zoneDef['properties']['anchor'] ?? strtolower($zoneId);
        $zoneClass = $this->getZoneClass($zoneId, $zoneDef);
        
        $html = "<section id=\"{$anchor}\" class=\"onepage-section {$zoneClass}\">";
        $html .= '<div class="section-container">';

        // Rendere Objekte in der definierten Reihenfolge
        $objectIds = $zoneDef['order'] ?? [];
        
        // Prüfe ob es ein Grid-Layout ist
        $isGrid = ($zoneDef['properties']['layout'] ?? '') === 'grid';
        $columns = $zoneDef['properties']['columns'] ?? 3;
        
        if ($isGrid && count($objectIds) > 1) {
            // Erster Eintrag ist oft der Titel
            $firstObject = array_shift($objectIds);
            if ($firstObject && isset($this->objects[$firstObject])) {
                $html .= $this->renderObject($firstObject, $this->objects[$firstObject]);
            }
            
            // Rest in Grid
            $html .= "<div class=\"grid-{$columns}\">";
            foreach ($objectIds as $objectId) {
                if (isset($this->objects[$objectId])) {
                    $html .= $this->renderObject($objectId, $this->objects[$objectId]);
                }
            }
            $html .= '</div>';
        } else {
            foreach ($objectIds as $objectId) {
                if (isset($this->objects[$objectId])) {
                    $html .= $this->renderObject($objectId, $this->objects[$objectId]);
                }
            }
        }

        $html .= '</div>';
        $html .= '</section>';

        return $html;
    }

    /**
     * Ermittelt die CSS-Klasse für eine Zone
     */
    private function getZoneClass(string $zoneId, array $zoneDef): string
    {
        $classes = ['zone-' . strtolower($zoneId)];
        
        if ($zoneDef['properties']['full_width'] ?? false) {
            $classes[] = 'full-width';
        }
        
        if ($zoneDef['properties']['highlight'] ?? false) {
            $classes[] = 'highlight';
        }
        
        return implode(' ', $classes);
    }

    /**
     * Rendert ein einzelnes Objekt
     */
    private function renderObject(string $objectId, array $data): string
    {
        $type = $data['type'] ?? 'unknown';
        $objectClass = 'object object-' . $type;
        
        switch ($type) {
            case 'image':
                return $this->renderImage($data, $objectClass);
            
            case 'text':
                return $this->renderText($data, $objectClass);
            
            case 'action':
                return $this->renderAction($data, $objectClass);
            
            case 'card':
                return $this->renderCard($data, $objectClass);
            
            case 'project':
                return $this->renderProject($data, $objectClass);
            
            default:
                return "<!-- Unbekannter Objekttyp: {$type} -->";
        }
    }

    /**
     * Rendert ein Bild-Objekt
     */
    private function renderImage(array $data, string $class): string
    {
        $src = htmlspecialchars($data['src'] ?? '');
        $alt = htmlspecialchars($data['alt'] ?? '');
        $title = isset($data['title']) ? ' title="' . htmlspecialchars($data['title']) . '"' : '';
        
        return "<figure class=\"{$class}\"><img src=\"{$src}\" alt=\"{$alt}\"{$title} loading=\"lazy\"></figure>";
    }

    /**
     * Rendert ein Text-Objekt
     */
    private function renderText(array $data, string $class): string
    {
        $content = htmlspecialchars($data['content'] ?? '');
        $role = $data['role'] ?? 'paragraph';
        
        switch ($role) {
            case 'heading':
                return "<h2 class=\"{$class} text-heading\">{$content}</h2>";
            case 'subheading':
                return "<h3 class=\"{$class} text-subheading\">{$content}</h3>";
            case 'intro':
                return "<p class=\"{$class} text-intro\">{$content}</p>";
            default:
                return "<p class=\"{$class} text-paragraph\">{$content}</p>";
        }
    }

    /**
     * Rendert ein Action-Objekt (Button/Link)
     */
    private function renderAction(array $data, string $class): string
    {
        $label = htmlspecialchars($data['label'] ?? 'Klicken');
        $href = htmlspecialchars($data['href'] ?? '#');
        $actionType = $data['action_type'] ?? 'button';
        
        $btnClass = $actionType === 'button' ? 'btn btn-primary' : 'link';
        
        return "<a href=\"{$href}\" class=\"{$class} {$btnClass}\">{$label}</a>";
    }

    /**
     * Rendert ein Card-Objekt
     */
    private function renderCard(array $data, string $class): string
    {
        $title = htmlspecialchars($data['title'] ?? '');
        $description = htmlspecialchars($data['description'] ?? '');
        $icon = $data['icon'] ?? '';
        
        $html = "<div class=\"{$class} card\">";
        if ($icon) {
            $html .= "<span class=\"card-icon\">{$icon}</span>";
        }
        $html .= "<h4 class=\"card-title\">{$title}</h4>";
        $html .= "<p class=\"card-description\">{$description}</p>";
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Rendert ein Project-Objekt
     */
    private function renderProject(array $data, string $class): string
    {
        $title = htmlspecialchars($data['title'] ?? '');
        $description = htmlspecialchars($data['description'] ?? '');
        $image = htmlspecialchars($data['image'] ?? '');
        $link = $data['link'] ?? '#';
        
        $html = "<article class=\"{$class} project-card\">";
        $html .= "<div class=\"project-image\" style=\"background-image: url('{$image}')\"></div>";
        $html .= '<div class="project-content">';
        $html .= "<h4 class=\"project-title\">{$title}</h4>";
        $html .= "<p class=\"project-description\">{$description}</p>";
        if ($link && $link !== '#') {
            $html .= "<a href=\"{$link}\" class=\"project-link\">Mehr erfahren →</a>";
        }
        $html .= '</div>';
        $html .= '</article>';
        
        return $html;
    }
}

// ============================================
// Hauptausführung
// ============================================

try {
    $onepage = new OnepageRenderer();
    $content = $onepage->render();
    
    $version = TAG_PRESS_VERSION;

    echo <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Tag-Press Onepage v{$version}">
    <meta name="description" content="Eine moderne Onepage-Website erstellt mit Tag-Press">
    <title>Tag-Press Onepage Demo</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="onepage">
{$content}
    <footer class="onepage-footer">
        <div class="footer-container">
            <p>© 2026 Tag-Press Onepage | Powered by <strong>Tag-Press</strong> v{$version}</p>
            <p>Ein Projekt von <a href="https://robderoy.de">Rob de Roy</a></p>
        </div>
    </footer>
    <script>
        // Mobile Navigation Toggle
        document.querySelector('.nav-toggle')?.addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
        
        // Smooth Scroll für Navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    document.querySelector('.nav-links').classList.remove('active');
                }
            });
        });
        
        // Navigation Highlight beim Scrollen
        const sections = document.querySelectorAll('.onepage-section');
        const navLinks = document.querySelectorAll('.nav-links a');
        
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
            
            // Navigation Hintergrund beim Scrollen
            const nav = document.querySelector('.onepage-nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
HTML;

} catch (Exception $e) {
    echo "<!DOCTYPE html><html><head><title>Fehler</title></head><body>";
    echo "<h1>Tag-Press Onepage Fehler</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</body></html>";
}

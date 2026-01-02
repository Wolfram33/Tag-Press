# Tag-Press Onepage Demo

Ein vollständiges Beispiel für eine Onepage-Website, die die Tag-Press Logik verwendet.

## Struktur

```
examples/onepage/
├── index.php           # Haupteinstiegspunkt (Interpreter)
├── README.md           # Diese Datei
├── assets/
│   └── styles.css      # Onepage-spezifische Styles
├── struktur/
│   └── geometrie.php   # Semantische Geometrie der Onepage
└── daten/
    ├── hero_*.php      # Hero-Sektion Objekte
    ├── service_*.php   # Services-Sektion Objekte
    ├── about_*.php     # About-Sektion Objekte
    ├── project_*.php   # Portfolio-Sektion Objekte
    └── contact_*.php   # Kontakt-Sektion Objekte
```

## Verwendung

1. Starte einen lokalen PHP-Server:
   ```bash
   cd examples/onepage
   php -S localhost:8080
   ```

2. Öffne im Browser: `http://localhost:8080`

## Sektionen

Die Onepage besteht aus 5 Sektionen:

| Sektion | Beschreibung |
|---------|-------------|
| **HERO** | Hero-Bereich mit Titel, Untertitel und Call-to-Action |
| **SERVICES** | 3 Service-Karten im Grid-Layout |
| **ABOUT** | Über-uns-Sektion mit Text und Bild |
| **PORTFOLIO** | 4 Projekt-Karten |
| **CONTACT** | Kontaktbereich mit CTA |

## Tag-Press Prinzipien

Dieses Demo folgt den Tag-Press Grundprinzipien:

1. **Trennung der Concerns**
   - `struktur/geometrie.php` → WAS existiert, WO und WARUM
   - `daten/*.php` → Nur rohe Inhalte, keine Position
   - `assets/styles.css` → Visuelle Darstellung

2. **Deklarative Struktur**
   - Die Geometrie definiert vollständig, welche Objekte wo erscheinen
   - Keine impliziten Fallbacks

3. **Eigenständig**
   - Dieses Demo ist vollständig in sich geschlossen
   - Kann unabhängig vom Hauptprojekt ausgeführt werden

## Anpassung

### Inhalte ändern
Bearbeite die Dateien in `daten/`. Jede Datei enthält ein Objekt:

```php
<?php
return [
    'type' => 'text',
    'role' => 'heading',
    'content' => 'Dein Titel hier'
];
```

### Struktur ändern
Bearbeite `struktur/geometrie.php` um Sektionen hinzuzufügen/zu entfernen.

### Styling ändern
Bearbeite `assets/styles.css` für visuelle Anpassungen.

---

**Lizenz:** MIT | **Autor:** Rob de Roy | **Website:** [robderoy.de](https://robderoy.de)

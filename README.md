# Tag-Press

**Ein dateibasiertes, deklaratives Website-System ohne klassische Datenbank.**

Webseiten werden durch eine formale Beschreibungssprache definiert und zur Laufzeit interpretiert. Das System erzwingt strikte Trennung von semantischer Struktur, Inhalten und Darstellung.

---

**Entwickler:** Rob de Roy
**Website:** [https://robderoy.de](https://robderoy.de)
**Lizenz:** MIT License
**Version:** 0.1 (Minimal Viable Specification)

---

## Konzept

Tag-Press ist kein klassisches CMS. Es ist ein **geistiges Trainingsgerät** und Lern-Experimentierrahmen, das Webseiten nicht über Templates oder Datenbanken, sondern über eine formale Beschreibungssprache definiert. Eine Website wird nicht gebaut, sondern *beschrieben*. Rendering ist lediglich die Interpretation dieser Beschreibung.

**Ziel:** Eleganz durch Reduktion. Das System ist absichtlich einfach und streng, um klare Denkweisen zu fördern.

---

## Grundprinzipien

### 1. Trennung der Concerns

| Ebene | Beschreibung |
|-------|--------------|
| **Struktur** | Semantische Geometrie: WAS existiert, WO und WARUM |
| **Daten** | Nur rohe Inhalte und Metadaten, keine Position oder Layout-Info |
| **Darstellung** | Wie wird es visuell umgesetzt (CSS/Grid) |

### 2. Keine Datenbank
Alles basiert auf flachen Dateien (PHP-Arrays). Vollständige Transparenz, Versionierbarkeit und Determinismus.

### 3. Deterministisch und streng
Keine impliziten Fallbacks. Ungültige Definitionen führen zu hartem Abbruch mit klarer Fehlermeldung. Das System ist korrekt oder es existiert nicht.

### 4. Validierung zentral
Jede Seite muss vor Rendering vollständig validiert werden. Fehler sind keine Warnungen, sondern Abbruchbedingungen.

### 5. Minimalismus
Nur das Nötigste. Erweiterungen nur durch explizite, dokumentierte Regeln.

---

## Verzeichnisstruktur

```
tag-press/
├── index.php                  # Interpreter (lädt, validiert, rendert)
├── assets/
│   └── styles.css             # Globale Styles
├── config/
│   ├── layout/
│   │   └── grid_master.php    # Physisches Grid (CSS-Klassen, Breakpoints)
│   └── classes/
│       └── main_classes.php   # Parser, Validator, Renderer Klassen
├── struktur/
│   └── main_geometrie.php     # Semantische Geometrie (zentrale Wahrheit)
└── daten/                     # Inhaltsdateien (o1.php, o2.php, ...)
```

---

## Semantische Geometrie

Die Datei `struktur/main_geometrie.php` ist die **zentrale Wahrheit** des Systems. Sie definiert:

- Welche Seiten existieren (A = Startseite, B = Über uns, etc.)
- Welche Zonen jede Seite hat (Z1, Z2, Z3, ...)
- Welche Bedeutung jede Zone trägt
- Welche Objekte in welchen Zonen erlaubt sind (O1, O2, ...)
- Welche Objekttypen existieren und ihre Pflichtattribute

### Notation

```
A,Z1=O1,O2,O3
```

Bedeutet:
- **A** = Seite (Startseite)
- **Z1** = Zone (Primärfokusbereich)
- **O1,O2,O3** = Objekte in dieser Zone (in definierter Reihenfolge)

### Zonen-Konzept

Eine Zone ist ein **semantischer Raum** mit klarer Funktion. Ihre Bedeutung ist unabhängig von ihrer visuellen Position.

| Zone | Bedeutung |
|------|-----------|
| Z1 | Primärfokusbereich (Hero) |
| Z2 | Hauptinhaltsbereich |
| Z3 | Sekundärbereich |
| Z4 | Abschlussbereich |

### Objekttypen


Das System beschränkt sich bewusst auf wenige, klar definierte Typen. Die Attribute und Werte sind formal festgelegt:

| Typ      | Pflichtattribute         | Optionale Attribute | Werte/Details |
|----------|-------------------------|---------------------|---------------|
| `image`  | src (url), alt (string, min 5 Zeichen) | title (string), caption (string) | alt darf nicht Dateiname sein |
| `text`   | content (string), role (enum) | - | role: heading, subheading, intro, paragraph, note |
| `list`   | items (array, min 1)    | list_type (enum)    | list_type: ordered, unordered |
| `action` | label (string), href (url) | action_type (enum) | action_type: link, button |

**Wichtig:** Ein Bildobjekt ohne Alt-Text existiert in Tag-Press nicht!

---

## Datenebene

Dateien im `daten/`-Verzeichnis enthalten **ausschließlich Inhalte und Metadaten**:

```php
<?php
// daten/o1.php
return [
    'type' => 'image',
    'src' => '/assets/images/hero.jpg',
    'alt' => 'Beschreibung des Bildes',
    'title' => 'Optionaler Titel'
];
```

Datenobjekte wissen **nichts** über:
- Ihre Position auf der Seite
- Ihre Größe oder Breite
- Die Zone, in der sie erscheinen
- Ihr Layout oder Styling

---

## Grid-Master

Der Grid-Master (`config/layout/grid_master.php`) übersetzt semantische Zonen in CSS-Klassen:

```php
<?php
return [
    'zones' => [
        'Z1' => 'zone-hero full-width bg-gradient',
        'Z2' => 'zone-main container grid-container',
    ],
    'objects' => [
        'image' => 'img-responsive img-cover',
        'text' => 'text-block prose',
    ]
];
```

Der Grid-Master:
- Kennt **keine** Inhalte
- Trifft **keine** Bedeutungsentscheidungen
- Ist ein reiner **Übersetzer** zwischen Geometrie und Darstellung

---

## Validierung

Validierung ist ein **zentrales Prinzip**. Geprüft wird:

1. Existiert die angeforderte Seite?
2. Sind alle Zonen korrekt definiert?
3. Sind alle Objekte in erlaubten Zonen?
4. Haben alle Objekte ihre Pflichtattribute?

Bei Fehlern: **Harter Abbruch** mit präziser Fehlermeldung.

---

## Installation & Verwendung

1. Repository klonen
2. Webserver auf `index.php` zeigen lassen
3. Seite aufrufen: `/?page=A` (oder ohne Parameter für Startseite)

### Lokaler Test

```bash
cd tag-press
php -S localhost:8080
```

Dann im Browser: `http://localhost:8080`

---

## Didaktischer Fahrplan (für Azubis)

### Phase 1: Lesen und Verstehen
Jeder Azubi schreibt eine Dokumentation: "Was bedeutet Z1 semantisch?"

### Phase 2: Erweitern
Neue Zone oder Objekttyp definieren (muss argumentiert werden!)

### Phase 3: Validator bauen
Validierungslogik in `main_classes.php` implementieren

### Phase 4: Renderer erweitern
Einfache Renderer-Klasse für HTML-Ausgabe

### Phase 5: Echte Seite
Erste vollständige Seite bauen und testen

---

## Architektur-Übersicht

```
┌─────────────────────────────────────────────────────────────┐
│                        index.php                             │
│                       (Interpreter)                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    GeometryParser                            │
│              (Lädt main_geometrie.php)                       │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              ▼                               ▼
┌─────────────────────────┐     ┌─────────────────────────────┐
│       Validator         │     │        DataLoader           │
│  (Prüft Regeln)         │     │  (Lädt daten/*.php)         │
└─────────────────────────┘     └─────────────────────────────┘
              │                               │
              └───────────────┬───────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        Renderer                              │
│            (+ Grid-Master für CSS-Klassen)                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      HTML-Ausgabe                            │
└─────────────────────────────────────────────────────────────┘
```

---

## Warum Tag-Press?

Tag-Press ist kein weiteres CMS, sondern ein **strukturelles Gegenmodell** zu datenbankzentrierten Systemen. Es ist:

- **Deterministisch**: Kein Raten, kein "Best Effort"
- **Semantisch**: Bedeutung vor Darstellung
- **Streng**: Fehler werden nicht kaschiert
- **Lehrreich**: Zwingt zum Nachdenken über Abstraktion

Wer Tag-Press verstanden hat, versteht automatisch auch andere Systeme besser, weil er gelernt hat, zwischen Bedeutung, Struktur, Darstellung und Daten zu unterscheiden.

---

## Lizenz

MIT License - Freie Nutzung, Modifikation und Verteilung erlaubt.

---

*Tag-Press: Eleganz durch Einfachheit.*

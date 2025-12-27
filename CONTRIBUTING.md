# Beitragen zu Tag-Press

## Branching-Strategie für Azubis

Tag-Press verwendet eine phasenbasierte Branching-Strategie, die den didaktischen Lernpfad widerspiegelt. Jeder Branch entspricht einer Entwicklungsphase.

### Branch-Übersicht

| Branch | Phase | Beschreibung |
|--------|-------|--------------|
| `main` | - | Stabiler Hauptzweig mit vollständiger Referenzimplementierung |
| `phase-1-geometrie-understanding` | 1 | Nur Lesen und Verstehen der Semantik |
| `phase-2-validator-impl` | 2 | Implementierung des Validators |
| `phase-3-renderer-impl` | 3 | Aufbau des Renderers |
| `phase-4-example-pages` | 4 | Erste echte Seiten nach den Regeln |

---

## Phase 1: Geometrie verstehen

**Branch:** `phase-1-geometrie-understanding`

### Aufgabe
- Lies und verstehe `struktur/main_geometrie.php`
- Dokumentiere jede Zone: Was bedeutet sie semantisch?
- Erstelle eine Markdown-Datei `docs/geometrie-analyse.md`

### Deliverables
- [ ] Dokumentation aller Zonen (Z1-Z4)
- [ ] Erklärung der Tag-Notation (A,Z1=O1,O2)
- [ ] Beschreibung aller Objekttypen
- [ ] Erklärung der Attribut-Datentypen

### Erlaubte Änderungen
- NUR Dokumentationsdateien (`docs/*.md`)
- KEINE Code-Änderungen

---

## Phase 2: Validator implementieren

**Branch:** `phase-2-validator-impl`

### Aufgabe
- Erweitere den Validator in `config/classes/Validator.php`
- Implementiere fehlende Validierungsregeln
- Schreibe Tests für jeden Fehlerfall

### Deliverables
- [ ] Validierung aller Datentypen (string, url, enum, array, boolean)
- [ ] Validierung aller Constraints
- [ ] Aussagekräftige Fehlermeldungen
- [ ] Mindestens 5 Unit-Tests

### Erlaubte Änderungen
- `config/classes/Validator.php`
- `tests/ValidatorTest.php`
- `docs/validator.md`

---

## Phase 3: Renderer implementieren

**Branch:** `phase-3-renderer-impl`

### Aufgabe
- Verstehe den Renderer in `config/classes/main_classes.php`
- Implementiere einen neuen Objekttyp
- Füge den Typ zur Geometrie und zum Grid-Master hinzu

### Deliverables
- [ ] Neuer Objekttyp (z.B. `video`, `quote`, `card`)
- [ ] Semantische Begründung für den Typ
- [ ] Renderer-Methode
- [ ] Beispiel-Datenobjekt
- [ ] CSS-Styling

### Erlaubte Änderungen
- `config/classes/main_classes.php` (Renderer-Klasse)
- `struktur/main_geometrie.php` (Objekttypen)
- `config/layout/grid_master.php` (Mapping)
- `daten/o*.php` (neue Objekte)
- `assets/styles.css`

---

## Phase 4: Beispielseiten bauen

**Branch:** `phase-4-example-pages`

### Aufgabe
- Erstelle eine vollständige neue Seite (B, C oder D)
- Die Seite muss alle Validierungsregeln erfüllen
- Dokumentiere die Designentscheidungen

### Deliverables
- [ ] Neue Seitendefinition in Geometrie
- [ ] Alle benötigten Datenobjekte
- [ ] Funktionierende, validierte Seite
- [ ] Dokumentation: Warum diese Zonen?

### Erlaubte Änderungen
- `struktur/main_geometrie.php` (neue Seite)
- `daten/*.php` (neue Objekte)
- `examples/*.md` (Dokumentation)

---

## Allgemeine Regeln

### Code-Qualität
1. **Keine impliziten Annahmen** – Alles muss explizit sein
2. **Keine Fallbacks** – Fehler brechen hart ab
3. **Klare Benennung** – Variablen und Funktionen selbsterklärend
4. **Kommentare sparsam** – Code soll selbst sprechen

### Commit-Messages
```
<phase>: <kurze Beschreibung>

<ausführliche Erklärung>
```

Beispiel:
```
phase-2: Implementiere Array-Validierung

- Prüft min_items Constraint
- Validiert item_type für jedes Element
- Fehler bei leerem Array wenn min_items > 0
```

### Pull Requests
1. Branch muss von `main` abgezweigt sein
2. Alle Tests müssen bestehen
3. Code-Review durch mindestens einen anderen Azubi
4. Merge nur nach Freigabe

---

## Fragen?

Bei Fragen zur Architektur oder Konzepten:
1. Erst die Dokumentation lesen
2. Dann den Code studieren
3. Dann im Team diskutieren
4. Erst zuletzt den Ausbilder fragen

**Das Ziel ist nicht, schnell fertig zu werden, sondern zu verstehen.**

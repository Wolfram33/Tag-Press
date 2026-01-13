# Tag-Press Accessibility Testing Guide

## Browser-Tools für Accessibility-Tests

### 1. Chrome/Edge Lighthouse
1. Öffne DevTools (`F12`)
2. Tab "Lighthouse"
3. Wähle "Accessibility"
4. Klicke "Generate report"

**Ziel:** Score > 90

### 2. axe DevTools (Extension)
- Installation: [Chrome Web Store](https://chrome.google.com/webstore) → "axe DevTools"
- Nach Installation: DevTools → Tab "axe DevTools"
- Klicke "Scan ALL of my page"

**Vorteile:**
- Detaillierte Fehlerberichte
- Code-Vorschläge
- WCAG-Level Zuordnung

### 3. WAVE (Extension)
- Installation: [wave.webaim.org](https://wave.webaim.org/extension/)
- Nach Installation: Browser-Icon klicken
- Zeigt Fehler direkt auf der Seite

## Keyboard-Testing

### Tastenkombinationen
| Taste | Funktion |
|-------|----------|
| `Tab` | Vorwärts durch interaktive Elemente |
| `Shift + Tab` | Rückwärts durch interaktive Elemente |
| `Enter` | Link/Button aktivieren |
| `Space` | Button aktivieren, Checkbox togglen |
| `Arrow Keys` | Navigation in Listen, Menüs |

### Test-Checklist
- [ ] Skip-Link erscheint beim ersten `Tab`
- [ ] Skip-Link funktioniert (springt zu `#main-content`)
- [ ] Alle Links/Buttons erreichbar
- [ ] Focus-Indikator immer sichtbar
- [ ] Logische Tab-Reihenfolge
- [ ] Keine Keyboard-Traps (man kann wieder raus)
- [ ] Modale Dialoge: ESC schließt sie

## Screen Reader Testing

### Windows: NVDA (kostenlos)
1. Download: [nvaccess.org](https://www.nvaccess.org/)
2. Installation und Start
3. Grundbefehle:
   - `H` – Nächste Überschrift
   - `K` – Nächster Link
   - `B` – Nächster Button
   - `G` – Nächste Grafik
   - `Arrow Down/Up` – Zeile für Zeile lesen

### macOS: VoiceOver (eingebaut)
1. `Cmd + F5` zum Aktivieren
2. Grundbefehle:
   - `VO + A` – Seite vorlesen
   - `VO + Right Arrow` – Nächstes Element
   - `VO + Cmd + H` – Nächste Überschrift

### Test-Checklist
- [ ] Bilder werden beschrieben (Alt-Text wird gelesen)
- [ ] Links sind verständlich ohne Kontext
- [ ] Buttons haben aussagekräftige Labels
- [ ] Landmarks werden erkannt (main, nav, footer)
- [ ] Überschriften-Navigation funktioniert
- [ ] Listen werden als Listen erkannt
- [ ] ARIA-Labels werden gelesen

## Visuelle Tests

### Kontrast-Checker
- Tool: [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
- Minimum: 4.5:1 für normalen Text (WCAG AA)
- Minimum: 3:1 für großen Text (>18pt oder >14pt bold)

### Color Blindness Simulation
- Chrome Extension: "Colorblinding"
- Simuliert verschiedene Farbfehlsichtigkeiten
- Prüfe: Information nur durch Farbe? → Schlecht!

### Zoom-Test
1. Browser-Zoom auf 200% stellen (`Cmd/Ctrl + +`)
2. Prüfen:
   - [ ] Kein horizontales Scrollen
   - [ ] Text nicht abgeschnitten
   - [ ] Buttons noch klickbar
   - [ ] Layout nicht zerstört

## Automated Testing

### HTML-Validator
```bash
# Validiere HTML
https://validator.w3.org/
```

### Pa11y (CLI-Tool)
```bash
# Installation
npm install -g pa11y

# Test ausführen
pa11y http://localhost/tag-press

# Mit bestimmtem WCAG-Standard
pa11y --standard WCAG2AA http://localhost/tag-press
```

## Tag-Press Spezifische Tests

### 1. Alt-Text Vollständigkeit
```php
// Prüfe alle Objekte vom Typ 'image'
// Haben alle ein 'alt' Attribut?
```

### 2. Überschriften-Hierarchie
```php
// Geometrie prüfen:
// - Genau ein 'heading' (h1) pro Seite?
// - 'subheading' (h2) nur nach 'heading'?
```

### 3. ARIA-Labels
```php
// Komplexe Aktionen haben aria_label?
return [
    'type' => 'action',
    'label' => 'Download',
    'aria_label' => 'Whitepaper als PDF herunterladen (2.5 MB)' // ✓
];
```

## Browser-Matrix

Teste in verschiedenen Browsern:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (macOS/iOS)
- [ ] Mobile Browser (iOS Safari, Chrome Mobile)

## Mobile-spezifisch

### Touch-Target Größe
- Minimum: 44×44 CSS-Pixel (WCAG 2.5.5)
- Tag-Press: Automatisch für `.action-button`

### Orientation
- [ ] Portrait-Modus funktioniert
- [ ] Landscape-Modus funktioniert
- [ ] Content nicht abgeschnitten

### Zoom/Pinch
- [ ] Pinch-to-Zoom funktioniert
- [ ] Kein `user-scalable=no` im Meta-Tag

## Reporting Template

```markdown
## Accessibility Test Report

**Datum:** [Datum]
**Seite:** [URL]
**Tester:** [Name]

### Lighthouse Score
- Performance: __/100
- Accessibility: __/100
- Best Practices: __/100
- SEO: __/100

### Keyboard-Navigation
- [ ] Skip-Links funktionieren
- [ ] Focus sichtbar
- [ ] Tab-Reihenfolge logisch

### Screen Reader
- [ ] NVDA: __ Fehler
- [ ] VoiceOver: __ Fehler

### Gefundene Probleme
1. [Beschreibung]
2. [Beschreibung]

### Empfehlungen
1. [Empfehlung]
2. [Empfehlung]
```

## Continuous Testing

### Pre-Commit Hook
```bash
#!/bin/bash
# .git/hooks/pre-commit

# Validiere alle PHP-Dateien in daten/
for file in daten/*.php; do
    # Prüfe ob 'image' type 'alt' hat
    # Prüfe ob 'action' type 'label' hat
    # etc.
done
```

## Quick-Check Bookmarklet

```javascript
javascript:(function(){
    // Zeige alle Bilder ohne Alt-Text
    document.querySelectorAll('img:not([alt])').forEach(img => {
        img.style.border = '5px solid red';
    });
    
    // Zeige alle Links ohne Text
    document.querySelectorAll('a:empty').forEach(link => {
        link.style.outline = '5px solid red';
    });
    
    alert('Rote Rahmen = Accessibility-Probleme');
})();
```

---

**Testen, testen, testen!** Barrierefreiheit ist ein kontinuierlicher Prozess. 🧪♿

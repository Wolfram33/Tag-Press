# Tag-Press Barrierefreiheit (Accessibility)

Tag-Press wurde mit umfassender Unterstützung für Barrierefreiheit (WCAG 2.1 Level AA) entwickelt.

## 🎯 Implementierte Features

### 1. **Semantisches HTML & ARIA**
- ✅ Semantische HTML5-Elemente (`<main>`, `<section>`, `<footer>`)
- ✅ ARIA-Landmarks (`role="main"`, `role="contentinfo"`)
- ✅ ARIA-Labels für alle Zonen basierend auf ihrer semantischen Bedeutung
- ✅ Korrekte Verwendung von `<figure>` und `<figcaption>` für Bilder

### 2. **Keyboard-Navigation**
- ✅ **Skip-Links**: Ermöglichen das Überspringen zum Hauptinhalt und Footer
  - Sichtbar beim Focus via Tastatur
  - Positioniert am Seitenanfang
- ✅ **Focus-Styles**: Deutlich sichtbare Fokus-Indikatoren (3px Outline + Shadow)
- ✅ **Focus-Visible**: Unterscheidung zwischen Maus- und Tastatur-Navigation
- ✅ **Scroll-Margin**: Fokussierte Elemente werden nicht vom Header verdeckt

### 3. **Screen Reader Support**
- ✅ **Alt-Texte**: Alle Bilder haben alt-Attribute (leer für dekorative Bilder)
- ✅ **ARIA-Labels**: Beschreibende Labels für interaktive Elemente
- ✅ **Screen Reader Only**: `.sr-only` Klasse für visuell verborgene, aber lesbare Inhalte
- ✅ **Semantische Textstruktur**: Korrekte Verwendung von Überschriften-Hierarchien

### 4. **Visuelle Barrierefreiheit**
- ✅ **Kontraste**: WCAG AA konforme Farbkontraste
  - Text-Primär: #eee auf #0f0f23 (Ratio: ~13:1)
  - Text-Sekundär: #aaa auf #0f0f23 (Ratio: ~8:1)
  - Text-Muted verbessert: #888 statt #666 (besserer Kontrast)
- ✅ **Focus-Farbe**: Deutlich sichtbare blaue Fokus-Farbe (#4a9eff)
- ✅ **Link-Erkennbarkeit**: Unterstrichene Links mit ausreichendem Abstand

### 5. **Responsive & Adaptive Design**
- ✅ **Prefers-Reduced-Motion**: Animationen werden deaktiviert wenn der Nutzer dies wünscht
  ```css
  @media (prefers-reduced-motion: reduce) {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
  ```
- ✅ **Prefers-Contrast**: Höhere Kontraste im High-Contrast-Modus
- ✅ **Viewport**: Responsive Meta-Tag für mobile Geräte
- ✅ **Font-Skalierung**: Relative Schriftgrößen (rem/em) für Nutzer-Anpassungen

### 6. **Sichere Links**
- ✅ **External Links**: Automatisch `rel="noopener noreferrer"` für externe Links
- ✅ **Zugängliche Link-Texte**: Unterstützung für optionale `aria-label` Attribute

## 📋 Verwendung

### Alt-Texte für Bilder festlegen
```php
// daten/o1.php
return [
    'type' => 'image',
    'src' => 'images/hero.jpg',
    'alt' => 'Beschreibender Text für Screenreader', // WICHTIG!
    'caption' => 'Optionale Bildunterschrift',
    'title' => 'Optionaler Tooltip'
];
```

**Best Practices für Alt-Texte:**
- Beschreiben Sie den Inhalt, nicht das Aussehen
- Dekorative Bilder: Leeren Alt-Text verwenden (`'alt' => ''`)
- Informative Bilder: Kurze, präzise Beschreibung
- Komplexe Grafiken: Detaillierte Beschreibung in `caption`

### ARIA-Labels für Aktionen
```php
// daten/o5.php - Button mit zusätzlichem ARIA-Label
return [
    'type' => 'action',
    'label' => 'Mehr erfahren',
    'href' => '/about',
    'aria_label' => 'Mehr über unsere Mission erfahren', // Optional, aber empfohlen
    'action_type' => 'link'
];
```

### Semantische Textstruktur
```php
// daten/o2.php - Überschriften-Hierarchie beachten
return [
    'type' => 'text',
    'role' => 'heading',  // Wird zu <h1>
    'content' => 'Hauptüberschrift'
];

// daten/o3.php
return [
    'type' => 'text',
    'role' => 'subheading',  // Wird zu <h2>
    'content' => 'Unterüberschrift'
];
```

## 🧪 Testen der Barrierefreiheit

### Browser-Tools
1. **Lighthouse**: Chrome DevTools → Lighthouse → Accessibility Audit
2. **axe DevTools**: Browser-Extension für detaillierte Accessibility-Tests
3. **WAVE**: Web Accessibility Evaluation Tool

### Keyboard-Navigation testen
1. `Tab`: Durch interaktive Elemente navigieren
2. `Shift+Tab`: Rückwärts navigieren
3. `Enter/Space`: Links/Buttons aktivieren
4. Skip-Links sollten beim ersten `Tab` erscheinen

### Screen Reader testen
- **Windows**: NVDA (kostenlos) oder JAWS
- **macOS**: VoiceOver (eingebaut)
- **Linux**: Orca

### Checkliste
- [ ] Alle Bilder haben Alt-Texte oder sind dekorativ
- [ ] Skip-Links funktionieren
- [ ] Tastatur-Navigation ohne Maus möglich
- [ ] Focus-States sind sichtbar
- [ ] Kontraste erfüllen WCAG AA (4.5:1 für normalen Text)
- [ ] Überschriften-Hierarchie ist logisch
- [ ] Links haben beschreibende Texte (nicht "hier klicken")

## 📚 Ressourcen

- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [MDN Accessibility](https://developer.mozilla.org/en-US/docs/Web/Accessibility)
- [WebAIM](https://webaim.org/)
- [A11y Project](https://www.a11yproject.com/)

## 🔄 Zukünftige Verbesserungen

Mögliche Erweiterungen:
- [ ] Automatische Kontrast-Prüfung in der Validierung
- [ ] Warnung bei fehlenden Alt-Texten
- [ ] ARIA-Live-Regions für dynamische Inhalte
- [ ] Customizable Focus-Stile im Grid-Master
- [ ] Automatische Heading-Level-Validierung
- [ ] Dark/Light Mode Toggle mit prefers-color-scheme

## ⚠️ Wichtige Hinweise

1. **Alt-Texte sind Pflicht**: Jedes Bild sollte einen Alt-Text haben
2. **Überschriften-Hierarchie**: h1 → h2 → h3 (keine Ebenen überspringen)
3. **Interaktive Elemente**: Buttons für Aktionen, Links für Navigation
4. **Color ist nicht alles**: Information darf nicht nur durch Farbe vermittelt werden
5. **Testen Sie regelmäßig**: Mit echten Screen Readern und Tastatur

---

**Tag-Press** – Barrierefrei von Anfang an 🌐♿

# Tag-Press Barrierefreiheit – Schnellreferenz

## 🎯 Implementierte WCAG 2.1 Features

### ✅ Skip-Links
```html
<a href="#main-content" class="skip-link">Zum Hauptinhalt springen</a>
```
- Sichtbar bei Tastatur-Focus
- Erlaubt Überspringen der Navigation

### ✅ ARIA-Landmarks
```html
<main id="main-content" role="main" aria-label="Hauptinhalt">
<footer role="contentinfo" aria-label="Seiten-Footer">
<section aria-label="Semantische Bedeutung der Zone">
```

### ✅ Keyboard-Navigation
- **Focus-Styles**: 3px blaue Outline + Shadow
- **:focus-visible**: Unterscheidung Maus/Tastatur
- **Tab-Index**: Logische Reihenfolge
- **Scroll-Margin**: Fokussierte Elemente nicht verdeckt

### ✅ Touch-Targets (Mobile)
```css
.action-button {
    min-height: 44px; /* WCAG 2.5.5 */
    min-width: 44px;
}
```

### ✅ Responsive Accessibility
```css
/* Reduzierte Bewegung */
@media (prefers-reduced-motion: reduce) {
    animation-duration: 0.01ms !important;
}

/* High Contrast */
@media (prefers-contrast: high) {
    /* Erhöhte Kontraste */
}
```

### ✅ Farbkontraste (WCAG AA)
- Text-Primär: ~13:1 Ratio
- Text-Sekundär: ~8:1 Ratio
- Focus-Indikator: Deutlich sichtbar

### ✅ Screen Reader Support
```php
// Optionales ARIA-Label
'aria_label' => 'Beschreibender Text für Screen Reader'
```

### ✅ Semantisches HTML
- `<main>`, `<section>`, `<footer>`
- `<figure>` + `<figcaption>` für Bilder
- Korrekte Heading-Hierarchie (h1 → h2 → h3)

### ✅ Sichere Links
```php
// Externe Links automatisch mit:
rel="noopener noreferrer"
```

## 📝 Verwendung in Datenobjekten

### Bilder mit Alt-Text
```php
return [
    'type' => 'image',
    'src' => '/path/to/image.jpg',
    'alt' => 'Beschreibender Text', // PFLICHT!
    'caption' => 'Optional'
];
```

### Aktionen mit ARIA
```php
return [
    'type' => 'action',
    'label' => 'Klick mich',
    'href' => '/page',
    'aria_label' => 'Beschreibung für Screen Reader' // Optional
];
```

## 🧪 Quick-Test

1. **Tab-Test**: `Tab` drücken → Focus sichtbar?
2. **Skip-Links**: Erster `Tab` → Skip-Link erscheint?
3. **Screen Reader**: NVDA/VoiceOver → Alles beschrieben?
4. **Kontrast**: Lighthouse → Score > 90?

## 📚 Dokumentation

- [ACCESSIBILITY.md](ACCESSIBILITY.md) – Vollständige Dokumentation
- [examples/accessibility-examples.md](examples/accessibility-examples.md) – Beispiele

---

**Tag-Press** – Barrierefrei von Anfang an 🌐♿

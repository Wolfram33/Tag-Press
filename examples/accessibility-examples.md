# Barrierefreiheits-Beispiele für Tag-Press

Dieses Dokument zeigt Best Practices für barrierefreie Inhalte in Tag-Press.

## ✅ Gute Beispiele

### 1. Bild mit beschreibendem Alt-Text
```php
// daten/team_foto.php
return [
    'type' => 'image',
    'src' => '/assets/images/team.jpg',
    'alt' => 'Fünf Teammitglieder sitzen an einem Konferenztisch und arbeiten gemeinsam an einem Projekt',
    'caption' => 'Unser Team bei der wöchentlichen Planungssitzung'
];
```

**Warum gut:**
- Alt-Text beschreibt den Inhalt, nicht nur "Team Foto"
- Gibt Kontext: Wer, was, wo
- Caption ergänzt zusätzliche Information

### 2. Dekoratives Bild (kein Alt-Text benötigt)
```php
// daten/hintergrund_grafik.php
return [
    'type' => 'image',
    'src' => '/assets/images/decoration.svg',
    'alt' => '', // Leer für dekorative Bilder
];
```

**Warum gut:**
- Dekorative Bilder sollten leeren Alt-Text haben
- Screen Reader überspringen diese Bilder
- Kein unnötiger Noise für Nutzer mit Screen Reader

### 3. Button mit ARIA-Label
```php
// daten/kontakt_button.php
return [
    'type' => 'action',
    'label' => 'Kontakt',
    'href' => '/kontakt',
    'action_type' => 'link',
    'aria_label' => 'Kontaktformular öffnen um uns eine Nachricht zu senden'
];
```

**Warum gut:**
- Label ist kurz und sichtbar
- ARIA-Label gibt zusätzlichen Kontext für Screen Reader
- Beschreibt was passiert, wenn man klickt

### 4. Semantische Überschriften-Hierarchie
```php
// daten/hauptueberschrift.php
return [
    'type' => 'text',
    'role' => 'heading',  // → <h1>
    'content' => 'Willkommen bei Tag-Press'
];

// daten/unterueberschrift.php
return [
    'type' => 'text',
    'role' => 'subheading',  // → <h2>
    'content' => 'Was ist Tag-Press?'
];

// daten/absatz.php
return [
    'type' => 'text',
    'role' => 'paragraph',  // → <p>
    'content' => 'Tag-Press ist ein minimalistisches CMS...'
];
```

**Warum gut:**
- Logische Hierarchie: h1 → h2 → p
- Screen Reader können durch Überschriften navigieren
- Keine Ebenen übersprungen (nicht h1 → h3)

## ❌ Schlechte Beispiele (Vermeiden!)

### 1. Fehlender Alt-Text
```php
// ❌ SCHLECHT
return [
    'type' => 'image',
    'src' => '/assets/images/important.jpg'
    // Alt-Text fehlt komplett!
];
```

**Problem:**
- Screen Reader können Bild nicht beschreiben
- Wenn Bild nicht lädt, gibt es keine Information
- Verstößt gegen WCAG Richtlinien

**Lösung:**
```php
// ✅ GUT
return [
    'type' => 'image',
    'src' => '/assets/images/important.jpg',
    'alt' => 'Diagramm zeigt Anstieg der Nutzerzahlen um 300% in 2025'
];
```

### 2. Nicht-beschreibender Alt-Text
```php
// ❌ SCHLECHT
return [
    'type' => 'image',
    'src' => '/assets/images/chart.png',
    'alt' => 'Bild', // Zu generisch
];
```

**Problem:**
- "Bild" sagt nichts über den Inhalt aus
- Nutzer mit Screen Reader erhalten keine Information

**Lösung:**
```php
// ✅ GUT
return [
    'type' => 'image',
    'src' => '/assets/images/chart.png',
    'alt' => 'Balkendiagramm: Verkaufszahlen Q1-Q4 2025, zeigt stetiges Wachstum von 10.000 auf 45.000 Einheiten'
];
```

### 3. Alt-Text enthält "Bild von..." oder "Foto von..."
```php
// ❌ SCHLECHT
return [
    'type' => 'image',
    'src' => '/assets/images/ceo.jpg',
    'alt' => 'Foto von unserem CEO Max Mustermann', // Redundant
];
```

**Problem:**
- Screen Reader sagen bereits "Bild" oder "Grafik"
- "Foto von..." ist redundante Information

**Lösung:**
```php
// ✅ GUT
return [
    'type' => 'image',
    'src' => '/assets/images/ceo.jpg',
    'alt' => 'Max Mustermann, CEO, lächelt in die Kamera vor Bürogebäude'
];
```

### 4. Nicht-beschreibender Link-Text
```php
// ❌ SCHLECHT
return [
    'type' => 'action',
    'label' => 'Hier klicken', // Nicht aussagekräftig
    'href' => '/downloads/whitepaper.pdf',
    'action_type' => 'link'
];
```

**Problem:**
- "Hier klicken" gibt keinen Kontext
- Screen Reader-Nutzer hören nur "Link: Hier klicken"
- Liste aller Links auf der Seite ist nicht hilfreich

**Lösung:**
```php
// ✅ GUT
return [
    'type' => 'action',
    'label' => 'Whitepaper herunterladen',
    'href' => '/downloads/whitepaper.pdf',
    'action_type' => 'link',
    'aria_label' => 'Whitepaper zur Tag-Press Architektur als PDF herunterladen (2.5 MB)'
];
```

## 📊 Checkliste für jedes Datenobjekt

### Für Bilder (`type: 'image'`):
- [ ] `alt` Attribut ist vorhanden
- [ ] Alt-Text beschreibt den Inhalt (nicht das Aussehen)
- [ ] Alt-Text ist leer (`''`) wenn Bild dekorativ ist
- [ ] Alt-Text enthält keine Wörter wie "Bild von", "Foto von"
- [ ] Bei komplexen Grafiken: `caption` mit detaillierter Beschreibung

### Für Aktionen (`type: 'action'`):
- [ ] `label` ist beschreibend (nicht "Hier", "Klicken", etc.)
- [ ] Bei nicht-offensichtlichen Aktionen: `aria_label` hinzufügen
- [ ] Externe Links werden automatisch mit `rel="noopener noreferrer"` versehen

### Für Texte (`type: 'text'`):
- [ ] Richtige `role` gewählt (heading, subheading, paragraph)
- [ ] Überschriften-Hierarchie ist logisch (h1 → h2 → h3)
- [ ] Keine Überschriften-Ebenen übersprungen

## 🧪 Testen

### Schnell-Test mit Tastatur:
1. Öffne die Seite im Browser
2. Drücke `Tab` (keine Maus verwenden!)
3. Prüfe:
   - [ ] Sichtbarer Focus-Indikator?
   - [ ] Alle interaktiven Elemente erreichbar?
   - [ ] Logische Tab-Reihenfolge?
   - [ ] Skip-Links funktionieren?

### Screen Reader Test:
1. Aktiviere Screen Reader (NVDA, VoiceOver, etc.)
2. Prüfe:
   - [ ] Werden Bilder beschrieben?
   - [ ] Sind Links verständlich?
   - [ ] Ist die Seitenstruktur klar?
   - [ ] Navigation durch Überschriften möglich?

## 📚 Weitere Ressourcen

- [WebAIM Alt-Text Guidelines](https://webaim.org/techniques/alttext/)
- [W3C WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [WCAG 2.1 Quick Reference](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Denk dran:** Barrierefreiheit ist kein Feature, sondern eine Grundvoraussetzung! 🌐♿

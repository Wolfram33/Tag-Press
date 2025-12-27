# Ungültige Beispiele

Diese Datei zeigt absichtlich fehlerhafte Definitionen und erklärt, warum sie vom Validator abgelehnt werden.

**Lernziel:** Verstehen, welche Regeln das System erzwingt.

---

## 1. Fehlendes Pflichtattribut

### Fehlerhaft:
```php
// daten/invalid1.php
return [
    'type' => 'image',
    'src' => '/images/test.jpg'
    // FEHLER: 'alt' fehlt!
];
```

### Fehlermeldung:
```
Pflichtattribut 'alt' fehlt in Objekt 'INVALID1' (Typ: image)
```

### Warum?
Barrierefreiheit ist keine Option. Jedes Bild MUSS einen Alt-Text haben.

---

## 2. Alt-Text ist Dateiname

### Fehlerhaft:
```php
return [
    'type' => 'image',
    'src' => '/images/hero-banner.jpg',
    'alt' => 'hero-banner.jpg'  // FEHLER: Alt = Dateiname
];
```

### Fehlermeldung:
```
Alt-Text in 'INVALID2' darf nicht der Dateiname sein. Beschreibe das Bild sinnvoll!
```

### Warum?
Der Alt-Text soll das Bild beschreiben, nicht seinen technischen Namen wiederholen.

---

## 3. Ungültige Rolle

### Fehlerhaft:
```php
return [
    'type' => 'text',
    'content' => 'Hallo Welt',
    'role' => 'wichtig'  // FEHLER: 'wichtig' ist keine gültige Rolle
];
```

### Fehlermeldung:
```
Attribut 'role' in 'INVALID3' hat ungültigen Wert 'wichtig'.
Erlaubt: heading, subheading, intro, paragraph, note
```

### Warum?
Rollen sind strikt definiert. "wichtig" hat keine semantische Bedeutung im System.

---

## 4. Objekt in falscher Zone

### Fehlerhaft (in Geometrie):
```php
'page_assignments' => [
    'A' => [
        'Z1=O1,O2,O99'  // FEHLER: O99 ist nicht in Z1 erlaubt
    ]
]
```

### Fehlermeldung:
```
Objekt 'O99' ist nicht in Zone 'Z1' erlaubt.
Erlaubt: O1, O2, O3
```

### Warum?
Die Geometrie definiert exakt, welche Objekte wo erscheinen dürfen.

---

## 5. Leeres Pflichtfeld

### Fehlerhaft:
```php
return [
    'type' => 'text',
    'content' => '',  // FEHLER: Leerer String
    'role' => 'paragraph'
];
```

### Fehlermeldung:
```
Pflichtattribut 'content' fehlt in Objekt 'INVALID5' (Typ: text)
```

### Warum?
Leere Strings gelten als "fehlend". Ein Text ohne Inhalt hat keinen Sinn.

---

## 6. Leere Liste

### Fehlerhaft:
```php
return [
    'type' => 'list',
    'items' => []  // FEHLER: Keine Elemente
];
```

### Fehlermeldung:
```
Attribut 'items' in 'INVALID6' muss mindestens 1 Elemente haben
```

### Warum?
Eine Liste ohne Elemente ist semantisch sinnlos.

---

## 7. Falscher Datentyp

### Fehlerhaft:
```php
return [
    'type' => 'list',
    'items' => 'Erster Punkt, Zweiter Punkt'  // FEHLER: String statt Array
];
```

### Fehlermeldung:
```
Attribut 'items' in 'INVALID7' muss ein Array sein
```

### Warum?
Die Grammatik definiert exakte Datentypen. Strings und Arrays sind nicht austauschbar.

---

## 8. Nicht existierende Seite

### Fehlerhaft:
```
URL: /?page=X
```

### Fehlermeldung:
```
Seite 'X' ist nicht in der Geometrie definiert
```

### Warum?
Es gibt keine impliziten Fallbacks. Seiten müssen explizit definiert sein.

---

## 9. Ungültige Tag-Notation

### Fehlerhaft (in Geometrie):
```php
'page_assignments' => [
    'A' => [
        'Z1-O1,O2'  // FEHLER: '-' statt '='
    ]
]
```

### Fehlermeldung:
```
Ungültige Tag-Notation: Z1-O1,O2
```

### Warum?
Die Notation ist deterministisch: `Zone=Objekt1,Objekt2`

---

## 10. Objekt ohne Typ

### Fehlerhaft:
```php
return [
    'src' => '/images/test.jpg',
    'alt' => 'Testbild'
    // FEHLER: 'type' fehlt!
];
```

### Fehlermeldung:
```
Datenobjekt 'INVALID10' hat keinen Typ definiert
```

### Warum?
Jedes Objekt MUSS seinen Typ deklarieren. Das System rät nicht.

---

## Zusammenfassung

| Fehler | Regel |
|--------|-------|
| Fehlendes Attribut | Pflichtfelder sind nicht optional |
| Alt = Dateiname | Barrierefreiheit muss sinnvoll sein |
| Ungültige Rolle | Nur definierte Werte erlaubt |
| Falsche Zone | Geometrie bestimmt Zuordnung |
| Leerer String | Gilt als "fehlend" |
| Leere Liste | min_items = 1 |
| Falscher Typ | Grammatik ist strikt |
| Fehlende Seite | Kein Fallback |
| Notation falsch | Syntax ist deterministisch |
| Kein Typ | Selbstbeschreibung Pflicht |

**Merke:** Das System ist korrekt oder es existiert nicht.

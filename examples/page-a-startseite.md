# Seite A: Startseite

## Geometrie-Definition

```
Seite: A (Startseite)
Zonen: Z1, Z2, Z3, Z4
Notation: A,Z1=O1,O2,O3 | A,Z2=O4,O5,O6 | A,Z3=O7,O8 | A,Z4=O9,O10
```

## Zonen-Analyse

### Z1 – Primärfokusbereich (Hero)

**Semantische Bedeutung:**
Der erste, prominenteste Bereich der Seite. Er fängt die Aufmerksamkeit des Besuchers sofort und kommuniziert die Hauptbotschaft.

**Zugewiesene Objekte:**
| Objekt | Typ | Rolle |
|--------|-----|-------|
| O1 | image | Hero-Bild als visueller Anker |
| O2 | text | Hauptüberschrift (role: heading) |
| O3 | text | Einleitender Text (role: intro) |

**Warum diese Kombination?**
- Bild zieht Aufmerksamkeit
- Überschrift gibt Orientierung
- Intro erklärt den Zweck

---

### Z2 – Hauptinhaltsbereich

**Semantische Bedeutung:**
Zentrale Informationen und Features. Hier wird der Kerninhalt präsentiert.

**Zugewiesene Objekte:**
| Objekt | Typ | Rolle |
|--------|-----|-------|
| O4 | text | Feature: Struktur (role: paragraph) |
| O5 | text | Feature: Daten (role: paragraph) |
| O6 | text | Feature: Darstellung (role: paragraph) |

**Layout-Hinweis:**
Diese Zone verwendet ein 3-Spalten-Grid (`grid-cols-3`), daher genau 3 Objekte.

---

### Z3 – Sekundärbereich

**Semantische Bedeutung:**
Ergänzende Informationen, die den Hauptinhalt unterstützen.

**Zugewiesene Objekte:**
| Objekt | Typ | Rolle |
|--------|-----|-------|
| O7 | text | Zwischenüberschrift (role: subheading) |
| O8 | list | Prinzipien-Liste |

---

### Z4 – Abschlussbereich

**Semantische Bedeutung:**
Finale Handlungsaufforderung. Schließt die Seite ab.

**Zugewiesene Objekte:**
| Objekt | Typ | Rolle |
|--------|-----|-------|
| O9 | text | Abschlusstext (role: paragraph) |
| O10 | action | Call-to-Action Button |

---

## Vollständige Tag-Notation

```
page_assignments['A'] = [
    'Z1=O1,O2,O3',
    'Z2=O4,O5,O6',
    'Z3=O7,O8',
    'Z4=O9,O10'
]
```

## Validierungsregeln (erfüllt)

- [x] Alle Objekte haben Pflichtattribute
- [x] Alle Objekte sind in erlaubten Zonen
- [x] Alle Bilder haben Alt-Texte (min. 5 Zeichen)
- [x] Alle Texte haben gültige Rollen
- [x] Alle Actions haben href und label

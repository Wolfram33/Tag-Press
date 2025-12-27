<?php
/**
 * Tag-Press – Semantische Geometrie
 *
 * Diese Datei ist die zentrale Wahrheit des Tag-Press Systems.
 * Sie definiert vollständig und abschließend:
 * - Welche Seiten existieren
 * - Welche Zonen jede Seite hat
 * - Welche Bedeutung jede Zone trägt
 * - Welche Objekte in welchen Zonen erlaubt sind
 * - Welche Objekttypen existieren und ihre Pflichtattribute
 *
 * WICHTIG: Diese Datei enthält KEIN HTML und KEIN CSS.
 * Sie ist ein Regelwerk, kein Template.
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

return [
    /**
     * Seiten-Definitionen
     *
     * Jede Seite wird durch einen eindeutigen Bezeichner identifiziert.
     * 'A' = Startseite (Homepage)
     * Weitere Seiten: 'B' = Über uns, 'C' = Kontakt, etc.
     */
    'pages' => [
        'A' => [
            'name' => 'Startseite',
            'description' => 'Die Haupteinstiegsseite der Website',
            'zones' => [
                /**
                 * Z1 - Primärfokusbereich (Hero)
                 *
                 * Semantische Bedeutung: Der erste, prominenteste Bereich,
                 * der die Aufmerksamkeit des Besuchers sofort fängt.
                 * Typischerweise: großes Bild, Hauptüberschrift, Call-to-Action.
                 */
                'Z1' => [
                    'meaning' => 'Primärfokusbereich - Hero-Sektion mit Hauptbotschaft',
                    'allowed_objects' => ['O1', 'O2', 'O3'],
                    'order' => ['O1', 'O2', 'O3'],
                    'properties' => [
                        'height' => '500px',
                        'full_width' => true,
                        'position' => 'top'
                    ]
                ],

                /**
                 * Z2 - Hauptinhaltsbereich
                 *
                 * Semantische Bedeutung: Der zentrale Inhaltsbereich,
                 * in dem die Hauptinformationen präsentiert werden.
                 * Kann mehrere Spalten oder ein Grid enthalten.
                 */
                'Z2' => [
                    'meaning' => 'Hauptinhaltsbereich - Zentrale Informationen und Features',
                    'allowed_objects' => ['O4', 'O5', 'O6'],
                    'order' => ['O4', 'O5', 'O6'],
                    'properties' => [
                        'layout' => 'grid',
                        'columns' => 3
                    ]
                ],

                /**
                 * Z3 - Sekundärbereich
                 *
                 * Semantische Bedeutung: Ergänzende Informationen,
                 * die den Hauptinhalt unterstützen aber nicht dominieren.
                 */
                'Z3' => [
                    'meaning' => 'Sekundärbereich - Ergänzende Inhalte und Details',
                    'allowed_objects' => ['O7', 'O8'],
                    'order' => ['O7', 'O8'],
                    'properties' => [
                        'layout' => 'flow'
                    ]
                ],

                /**
                 * Z4 - Abschlussbereich
                 *
                 * Semantische Bedeutung: Der abschließende Bereich der Seite,
                 * typischerweise für Call-to-Actions oder Zusammenfassungen.
                 */
                'Z4' => [
                    'meaning' => 'Abschlussbereich - Finale Handlungsaufforderung',
                    'allowed_objects' => ['O9', 'O10'],
                    'order' => ['O9', 'O10'],
                    'properties' => [
                        'highlight' => true
                    ]
                ]
            ]
        ]
    ],

    /**
     * Objekttyp-Definitionen (Formale Grammatik)
     *
     * Jeder Objekttyp definiert exakt:
     * - 'type': Kategorie des Datentyps (scalar, compound, interactive)
     * - 'attributes': Formale Definition jedes Attributs mit Datentyp
     * - 'constraints': Zusätzliche Validierungsregeln
     *
     * Das System ist bewusst auf wenige, klar definierte Typen beschränkt.
     * Neue Typen müssen semantisch begründet werden.
     *
     * Attribut-Datentypen:
     * - string: Zeichenkette (nicht leer wenn required)
     * - url: Gültige URL/Pfad-Angabe
     * - enum: Wert aus definierter Liste
     * - array: Liste von Elementen
     * - boolean: true/false
     */
    'object_types' => [
        /**
         * image - Bildobjekt
         *
         * Typ: scalar (einfaches Medienobjekt)
         * Ein Bild ohne Alt-Text existiert in Tag-Press nicht!
         */
        'image' => [
            'type' => 'scalar',
            'description' => 'Ein Bildobjekt mit Quelle und Alternativtext',
            'attributes' => [
                'src' => [
                    'data_type' => 'url',
                    'required' => true,
                    'description' => 'Pfad oder URL zur Bilddatei'
                ],
                'alt' => [
                    'data_type' => 'string',
                    'required' => true,
                    'min_length' => 5,
                    'description' => 'Alternativtext für Barrierefreiheit (Pflicht!)'
                ],
                'title' => [
                    'data_type' => 'string',
                    'required' => false,
                    'description' => 'Optionaler Titel für Tooltip'
                ],
                'caption' => [
                    'data_type' => 'string',
                    'required' => false,
                    'description' => 'Optionale Bildunterschrift'
                ]
            ],
            'constraints' => [
                'alt_not_filename' => true  // Alt-Text darf nicht der Dateiname sein
            ]
        ],

        /**
         * text - Textobjekt
         *
         * Typ: scalar (einfaches Inhaltsobjekt)
         * Die Rolle bestimmt die semantische Bedeutung.
         */
        'text' => [
            'type' => 'scalar',
            'description' => 'Ein Textobjekt mit Inhalt und semantischer Rolle',
            'attributes' => [
                'content' => [
                    'data_type' => 'string',
                    'required' => true,
                    'min_length' => 1,
                    'description' => 'Der Textinhalt'
                ],
                'role' => [
                    'data_type' => 'enum',
                    'required' => true,
                    'allowed_values' => ['heading', 'subheading', 'intro', 'paragraph', 'note'],
                    'description' => 'Semantische Rolle des Textes'
                ]
            ],
            'constraints' => []
        ],

        /**
         * list - Listenobjekt
         *
         * Typ: compound (zusammengesetztes Objekt mit Kindelementen)
         */
        'list' => [
            'type' => 'compound',
            'description' => 'Eine geordnete oder ungeordnete Liste',
            'attributes' => [
                'items' => [
                    'data_type' => 'array',
                    'required' => true,
                    'min_items' => 1,
                    'item_type' => 'string',
                    'description' => 'Array von Listenelementen'
                ],
                'list_type' => [
                    'data_type' => 'enum',
                    'required' => false,
                    'allowed_values' => ['ordered', 'unordered'],
                    'default' => 'unordered',
                    'description' => 'Art der Liste'
                ]
            ],
            'constraints' => []
        ],

        /**
         * action - Aktionsobjekt (Button/Link)
         *
         * Typ: interactive (interaktives Element)
         */
        'action' => [
            'type' => 'interactive',
            'description' => 'Ein interaktives Element wie Button oder Link',
            'attributes' => [
                'label' => [
                    'data_type' => 'string',
                    'required' => true,
                    'min_length' => 1,
                    'max_length' => 100,
                    'description' => 'Beschriftung des Elements'
                ],
                'href' => [
                    'data_type' => 'url',
                    'required' => true,
                    'description' => 'Ziel-URL oder Anker'
                ],
                'action_type' => [
                    'data_type' => 'enum',
                    'required' => false,
                    'allowed_values' => ['link', 'button'],
                    'default' => 'link',
                    'description' => 'Darstellungsart'
                ]
            ],
            'constraints' => [
                'href_not_empty' => true
            ]
        ]
    ],

    /**
     * Seitenzuweisungen (Tag-Notation)
     *
     * Hier wird festgelegt, welche Objekte in welchen Zonen
     * einer Seite erscheinen. Die Notation ist deterministisch:
     *
     * A,Z1=O1,O2 bedeutet:
     * - Seite A
     * - Zone Z1
     * - Enthält Objekte O1 und O2 (in dieser Reihenfolge)
     */
    'page_assignments' => [
        'A' => [
            'Z1=O1,O2,O3',
            'Z2=O4,O5,O6',
            'Z3=O7,O8',
            'Z4=O9,O10'
        ]
    ]
];

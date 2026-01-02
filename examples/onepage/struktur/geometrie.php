<?php
/**
 * Tag-Press – Onepage Geometrie
 *
 * Diese Datei definiert die semantische Struktur einer Onepage-Website.
 * Eine Onepage hat alle Inhalte auf einer einzigen Seite mit
 * mehreren Sektionen, die durch Scroll-Navigation erreichbar sind.
 *
 * Aufbau:
 * - Hero: Einführung mit Hauptbotschaft
 * - Services: Dienstleistungen/Features
 * - About: Über uns Sektion
 * - Portfolio: Referenzen/Projekte
 * - Contact: Kontaktbereich
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

return [
    /**
     * Onepage-Definition
     *
     * Die Seite 'ONEPAGE' enthält alle Sektionen einer typischen
     * Onepage-Website. Die Zonen repräsentieren die verschiedenen
     * Sektionen der Seite.
     */
    'pages' => [
        'ONEPAGE' => [
            'name' => 'Onepage Website',
            'description' => 'Eine moderne Onepage-Website mit allen wichtigen Sektionen',
            'zones' => [
                /**
                 * HERO - Hauptsektion (Hero)
                 */
                'HERO' => [
                    'meaning' => 'Hero-Sektion - Erste Impression mit Hauptbotschaft',
                    'allowed_objects' => ['HERO_BG', 'HERO_TITLE', 'HERO_SUBTITLE', 'HERO_CTA'],
                    'order' => ['HERO_BG', 'HERO_TITLE', 'HERO_SUBTITLE', 'HERO_CTA'],
                    'properties' => [
                        'height' => '100vh',
                        'full_width' => true,
                        'position' => 'top',
                        'anchor' => 'home'
                    ]
                ],

                /**
                 * SERVICES - Dienstleistungen/Features
                 */
                'SERVICES' => [
                    'meaning' => 'Services-Sektion - Präsentation der Dienstleistungen',
                    'allowed_objects' => ['SERVICES_TITLE', 'SERVICE_1', 'SERVICE_2', 'SERVICE_3'],
                    'order' => ['SERVICES_TITLE', 'SERVICE_1', 'SERVICE_2', 'SERVICE_3'],
                    'properties' => [
                        'layout' => 'grid',
                        'columns' => 3,
                        'anchor' => 'services'
                    ]
                ],

                /**
                 * ABOUT - Über uns Sektion
                 */
                'ABOUT' => [
                    'meaning' => 'About-Sektion - Vorstellung und Geschichte',
                    'allowed_objects' => ['ABOUT_TITLE', 'ABOUT_TEXT', 'ABOUT_IMAGE'],
                    'order' => ['ABOUT_TITLE', 'ABOUT_TEXT', 'ABOUT_IMAGE'],
                    'properties' => [
                        'layout' => 'two-column',
                        'anchor' => 'about'
                    ]
                ],

                /**
                 * PORTFOLIO - Referenzen/Projekte
                 */
                'PORTFOLIO' => [
                    'meaning' => 'Portfolio-Sektion - Zeigt Projekte und Referenzen',
                    'allowed_objects' => ['PORTFOLIO_TITLE', 'PROJECT_1', 'PROJECT_2', 'PROJECT_3', 'PROJECT_4'],
                    'order' => ['PORTFOLIO_TITLE', 'PROJECT_1', 'PROJECT_2', 'PROJECT_3', 'PROJECT_4'],
                    'properties' => [
                        'layout' => 'grid',
                        'columns' => 2,
                        'anchor' => 'portfolio'
                    ]
                ],

                /**
                 * CONTACT - Kontaktbereich
                 */
                'CONTACT' => [
                    'meaning' => 'Kontakt-Sektion - Kontaktmöglichkeiten',
                    'allowed_objects' => ['CONTACT_TITLE', 'CONTACT_TEXT', 'CONTACT_CTA'],
                    'order' => ['CONTACT_TITLE', 'CONTACT_TEXT', 'CONTACT_CTA'],
                    'properties' => [
                        'highlight' => true,
                        'anchor' => 'contact'
                    ]
                ]
            ]
        ]
    ],

    /**
     * Objekttyp-Definitionen
     * (Erbt von main_geometrie.php, erweitert um Onepage-spezifische Typen)
     */
    'object_types' => [
        'image' => [
            'type' => 'scalar',
            'description' => 'Ein Bildobjekt',
            'attributes' => [
                'src' => ['data_type' => 'url', 'required' => true],
                'alt' => ['data_type' => 'string', 'required' => true, 'min_length' => 5]
            ]
        ],
        'text' => [
            'type' => 'scalar',
            'description' => 'Ein Textobjekt',
            'attributes' => [
                'content' => ['data_type' => 'string', 'required' => true],
                'role' => ['data_type' => 'enum', 'required' => true, 'allowed_values' => ['heading', 'subheading', 'intro', 'paragraph', 'note']]
            ]
        ],
        'action' => [
            'type' => 'interactive',
            'description' => 'Button oder Link',
            'attributes' => [
                'label' => ['data_type' => 'string', 'required' => true],
                'href' => ['data_type' => 'url', 'required' => true],
                'action_type' => ['data_type' => 'enum', 'required' => false, 'allowed_values' => ['link', 'button'], 'default' => 'button']
            ]
        ],
        'card' => [
            'type' => 'compound',
            'description' => 'Eine Feature- oder Service-Karte',
            'attributes' => [
                'title' => ['data_type' => 'string', 'required' => true],
                'description' => ['data_type' => 'string', 'required' => true],
                'icon' => ['data_type' => 'string', 'required' => false]
            ]
        ],
        'project' => [
            'type' => 'compound',
            'description' => 'Ein Portfolio-Projekt',
            'attributes' => [
                'title' => ['data_type' => 'string', 'required' => true],
                'description' => ['data_type' => 'string', 'required' => true],
                'image' => ['data_type' => 'url', 'required' => true],
                'link' => ['data_type' => 'url', 'required' => false]
            ]
        ]
    ],

    /**
     * Seitenzuweisungen für Onepage
     */
    'page_assignments' => [
        'ONEPAGE' => [
            'HERO=HERO_BG,HERO_TITLE,HERO_SUBTITLE,HERO_CTA',
            'SERVICES=SERVICES_TITLE,SERVICE_1,SERVICE_2,SERVICE_3',
            'ABOUT=ABOUT_TITLE,ABOUT_TEXT,ABOUT_IMAGE',
            'PORTFOLIO=PORTFOLIO_TITLE,PROJECT_1,PROJECT_2,PROJECT_3,PROJECT_4',
            'CONTACT=CONTACT_TITLE,CONTACT_TEXT,CONTACT_CTA'
        ]
    ]
];

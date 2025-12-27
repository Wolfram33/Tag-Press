<?php
/**
 * Datenobjekt O8 - Prinzipien-Liste
 *
 * Dieses Objekt enthält ausschließlich Inhalte und Metadaten.
 * Es weiß NICHTS über seine Position, Größe oder Darstellung.
 *
 * @package Tag-Press
 */

return [
    'type' => 'list',
    'list_type' => 'unordered',
    'items' => [
        'Deterministisch: Keine impliziten Fallbacks, keine automatische Korrektur',
        'Streng validiert: Ungültige Definitionen führen zu hartem Abbruch',
        'Minimal: Nur das Nötigste, Eleganz durch Reduktion',
        'Barrierefrei: Alt-Texte und semantische Rollen sind Pflicht',
        'Versionierbar: Alles liegt in flachen Dateien, vollständig transparent'
    ]
];

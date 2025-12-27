<?php
/**
 * Tag-Press – Validator
 *
 * Der Validator ist die zentrale Prüfinstanz des Tag-Press Systems.
 * Er validiert Geometrie und Daten gegen die formale Grammatik.
 *
 * WICHTIG:
 * - Validierung kommt IMMER vor Rendering
 * - Fehler sind Abbruchbedingungen, keine Warnungen
 * - Das System ist korrekt oder es existiert nicht
 *
 * Der Validator prüft:
 * 1. Seitenexistenz und Zonendefinitionen
 * 2. Objektzuweisungen zu erlaubten Zonen
 * 3. Attribut-Datentypen gegen formale Grammatik
 * 4. Pflichtfelder und Constraints
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

declare(strict_types=1);

require_once __DIR__ . '/TagPressException.php';

class Validator
{
    private GeometryParser $geometry;
    private DataLoader $dataLoader;
    private array $errors = [];
    private array $warnings = [];

    public function __construct(GeometryParser $geometry, DataLoader $dataLoader)
    {
        $this->geometry = $geometry;
        $this->dataLoader = $dataLoader;
    }

    /**
     * Validiert eine komplette Seite
     *
     * @throws TagPressException bei Validierungsfehlern
     */
    public function validatePage(string $pageId): bool
    {
        $this->errors = [];
        $this->warnings = [];

        // Prüfe ob Seite existiert
        $page = $this->geometry->getPage($pageId);
        if ($page === null) {
            throw new TagPressException(
                "Seite '{$pageId}' ist nicht in der Geometrie definiert",
                "pages"
            );
        }

        // Prüfe ob Seitenzuweisung existiert
        $assignments = $this->geometry->getPageAssignment($pageId);
        if ($assignments === null) {
            throw new TagPressException(
                "Seite '{$pageId}' hat keine Objektzuweisungen",
                "page_assignments"
            );
        }

        // Validiere jede Zone und ihre Objekte
        foreach ($assignments as $notation) {
            $parsed = $this->geometry->parseTagNotation($notation);
            $this->validateZone($pageId, $parsed['zone'], $parsed['objects']);
        }

        if (!empty($this->errors)) {
            throw new TagPressException(
                "Validierung fehlgeschlagen:\n" . implode("\n", $this->errors),
                "Seite: {$pageId}"
            );
        }

        return true;
    }

    /**
     * Validiert eine Zone und ihre Objekte
     */
    private function validateZone(string $pageId, string $zoneId, array $objectIds): void
    {
        $page = $this->geometry->getPage($pageId);

        // Prüfe ob Zone in der Seite definiert ist
        if (!isset($page['zones'][$zoneId])) {
            $this->errors[] = "Zone '{$zoneId}' ist nicht für Seite '{$pageId}' definiert";
            return;
        }

        $zoneDef = $page['zones'][$zoneId];

        // Prüfe ob alle Objekte erlaubt sind
        foreach ($objectIds as $objectId) {
            if (!in_array($objectId, $zoneDef['allowed_objects'])) {
                $this->errors[] = "Objekt '{$objectId}' ist nicht in Zone '{$zoneId}' erlaubt. Erlaubt: " . implode(', ', $zoneDef['allowed_objects']);
            }

            // Validiere das Datenobjekt selbst
            $this->validateObject($objectId);
        }
    }

    /**
     * Validiert ein einzelnes Datenobjekt gegen seinen Typ
     */
    private function validateObject(string $objectId): void
    {
        try {
            $data = $this->dataLoader->load($objectId);
        } catch (TagPressException $e) {
            $this->errors[] = $e->getMessage();
            return;
        }

        $type = $data['type'];
        $typeDef = $this->geometry->getObjectType($type);

        if ($typeDef === null) {
            $this->errors[] = "Objekttyp '{$type}' ist nicht definiert (Objekt: {$objectId})";
            return;
        }

        // Validiere gegen formale Grammatik
        $this->validateAgainstGrammar($objectId, $data, $typeDef);
    }

    /**
     * Validiert Daten gegen die formale Grammatik der Typdefinition
     */
    private function validateAgainstGrammar(string $objectId, array $data, array $typeDef): void
    {
        // Prüfe ob 'attributes' im neuen Format existiert
        if (isset($typeDef['attributes'])) {
            $this->validateNewFormat($objectId, $data, $typeDef);
        } else {
            // Fallback für altes Format (required/optional Arrays)
            $this->validateLegacyFormat($objectId, $data, $typeDef);
        }

        // Validiere Constraints wenn vorhanden
        if (isset($typeDef['constraints'])) {
            $this->validateConstraints($objectId, $data, $typeDef['constraints']);
        }
    }

    /**
     * Validiert gegen das neue Attribut-Format mit Datentypen
     */
    private function validateNewFormat(string $objectId, array $data, array $typeDef): void
    {
        foreach ($typeDef['attributes'] as $attrName => $attrDef) {
            $value = $data[$attrName] ?? null;
            $isRequired = $attrDef['required'] ?? false;

            // Prüfe Pflichtfeld
            if ($isRequired && ($value === null || $value === '')) {
                $this->errors[] = "Pflichtattribut '{$attrName}' fehlt in Objekt '{$objectId}' (Typ: {$data['type']})";
                continue;
            }

            // Wenn Wert vorhanden, validiere Datentyp
            if ($value !== null && $value !== '') {
                $this->validateDataType($objectId, $attrName, $value, $attrDef);
            }
        }
    }

    /**
     * Validiert einen Wert gegen seinen definierten Datentyp
     */
    private function validateDataType(string $objectId, string $attrName, mixed $value, array $attrDef): void
    {
        $dataType = $attrDef['data_type'] ?? 'string';

        switch ($dataType) {
            case 'string':
                if (!is_string($value)) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss ein String sein";
                    return;
                }
                // Prüfe min_length
                if (isset($attrDef['min_length']) && strlen($value) < $attrDef['min_length']) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss mindestens {$attrDef['min_length']} Zeichen haben";
                }
                // Prüfe max_length
                if (isset($attrDef['max_length']) && strlen($value) > $attrDef['max_length']) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' darf maximal {$attrDef['max_length']} Zeichen haben";
                }
                break;

            case 'url':
                if (!is_string($value)) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss eine URL/Pfad sein";
                    return;
                }
                // Einfache URL/Pfad-Validierung (erlaubt relative Pfade)
                if (empty($value) || (!str_starts_with($value, '/') && !str_starts_with($value, 'http') && !str_starts_with($value, '#'))) {
                    $this->warnings[] = "Attribut '{$attrName}' in '{$objectId}' hat möglicherweise ungültigen Pfad: {$value}";
                }
                break;

            case 'enum':
                $allowedValues = $attrDef['allowed_values'] ?? [];
                if (!in_array($value, $allowedValues, true)) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' hat ungültigen Wert '{$value}'. Erlaubt: " . implode(', ', $allowedValues);
                }
                break;

            case 'array':
                if (!is_array($value)) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss ein Array sein";
                    return;
                }
                // Prüfe min_items
                if (isset($attrDef['min_items']) && count($value) < $attrDef['min_items']) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss mindestens {$attrDef['min_items']} Elemente haben";
                }
                // Prüfe item_type
                if (isset($attrDef['item_type'])) {
                    foreach ($value as $idx => $item) {
                        if ($attrDef['item_type'] === 'string' && !is_string($item)) {
                            $this->errors[] = "Element {$idx} in '{$attrName}' von '{$objectId}' muss ein String sein";
                        }
                    }
                }
                break;

            case 'boolean':
                if (!is_bool($value)) {
                    $this->errors[] = "Attribut '{$attrName}' in '{$objectId}' muss ein Boolean sein";
                }
                break;
        }
    }

    /**
     * Validiert spezielle Constraints
     */
    private function validateConstraints(string $objectId, array $data, array $constraints): void
    {
        // Alt-Text darf nicht der Dateiname sein
        if (isset($constraints['alt_not_filename']) && $constraints['alt_not_filename']) {
            if (isset($data['alt']) && isset($data['src'])) {
                $filename = basename($data['src']);
                $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                if (strtolower($data['alt']) === strtolower($filename) ||
                    strtolower($data['alt']) === strtolower($filenameWithoutExt)) {
                    $this->errors[] = "Alt-Text in '{$objectId}' darf nicht der Dateiname sein. Beschreibe das Bild sinnvoll!";
                }
            }
        }

        // href darf nicht leer sein
        if (isset($constraints['href_not_empty']) && $constraints['href_not_empty']) {
            if (isset($data['href']) && trim($data['href']) === '') {
                $this->errors[] = "Href in '{$objectId}' darf nicht leer sein";
            }
        }
    }

    /**
     * Fallback für Legacy-Format (required/optional Arrays)
     */
    private function validateLegacyFormat(string $objectId, array $data, array $typeDef): void
    {
        $required = $typeDef['required'] ?? [];
        foreach ($required as $attr) {
            if (!isset($data[$attr]) || $data[$attr] === '') {
                $this->errors[] = "Pflichtattribut '{$attr}' fehlt in Objekt '{$objectId}' (Typ: {$data['type']})";
            }
        }

        // Prüfe valid_roles wenn vorhanden (für text)
        if (isset($typeDef['valid_roles']) && isset($data['role'])) {
            if (!in_array($data['role'], $typeDef['valid_roles'])) {
                $this->errors[] = "Ungültige Rolle '{$data['role']}' in '{$objectId}'. Erlaubt: " . implode(', ', $typeDef['valid_roles']);
            }
        }
    }

    /**
     * Gibt gesammelte Fehler zurück
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Gibt gesammelte Warnungen zurück
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Prüft ob die Validierung Fehler enthält
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Prüft ob die Validierung Warnungen enthält
     */
    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    /**
     * Gibt einen formatierten Validierungsbericht zurück
     */
    public function getReport(): string
    {
        $report = "=== Tag-Press Validierungsbericht ===\n\n";

        if (empty($this->errors) && empty($this->warnings)) {
            $report .= "Status: GÜLTIG\n";
            $report .= "Keine Fehler oder Warnungen gefunden.\n";
        } else {
            if (!empty($this->errors)) {
                $report .= "FEHLER (" . count($this->errors) . "):\n";
                foreach ($this->errors as $idx => $error) {
                    $report .= "  " . ($idx + 1) . ". {$error}\n";
                }
                $report .= "\n";
            }

            if (!empty($this->warnings)) {
                $report .= "WARNUNGEN (" . count($this->warnings) . "):\n";
                foreach ($this->warnings as $idx => $warning) {
                    $report .= "  " . ($idx + 1) . ". {$warning}\n";
                }
            }
        }

        return $report;
    }
}

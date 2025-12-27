<?php
/**
 * Tag-Press – Exception
 *
 * Spezifische Exception für Tag-Press Fehler.
 * Fehler sind in Tag-Press keine Warnungen, sondern Abbruchbedingungen.
 * Das System ist korrekt oder es existiert nicht.
 *
 * @author Rob de Roy
 * @version 0.1
 * @license MIT
 */

declare(strict_types=1);

class TagPressException extends Exception
{
    private string $context;

    public function __construct(string $message, string $context = '')
    {
        $this->context = $context;
        parent::__construct($message);
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getFormattedMessage(): string
    {
        $output = "[TAG-PRESS FEHLER]\n";
        $output .= "Meldung: {$this->getMessage()}\n";
        if ($this->context) {
            $output .= "Kontext: {$this->context}\n";
        }
        $output .= "Datei: {$this->getFile()}:{$this->getLine()}\n";
        return $output;
    }
}

<?php

declare(strict_types=1);

namespace xenialdan\libnbs;

class CustomInstrument
{
    /** @var int The instruments index */
    public $index;
    /** @var string The name of the instrument. */
    public $name;
    /** @var string The sound file of the instrument (just the filename, not the path). */
    public $soundFileName;
    /** @var string Sound name */
    public $sound = "";
    /** @var int The pitch of the sound file. Just like the note blocks, this ranges from 0-87. Default is 45. */
    public $pitch;
    /** @var bool Whether the piano should automatically press keys with this instrument when the marker passes them (0 or 1). */
    public $pressKey;

    /**
     * Creates a CustomInstrument
     * @param int $index
     * @param string $name
     * @param string $soundFile
     * @param int $pitch
     * @param bool $pressKey
     */
    public function __construct(int $index, string $name, string $soundFile, int $pitch = 45, bool $pressKey = false)
    {
        $this->index = $index;
        $this->name = $name;
        $this->soundFileName = str_replace([".ogg", ".fsb"], "", $soundFile);
        if (strtolower($this->soundFileName) === "pling") {
            $this->sound = "note.pling";
        }
        $this->pitch = $pitch;
        $this->pressKey = $pressKey;
    }

    /**
     * Gets index of CustomInstrument
     * @return int index
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Gets name of CustomInstrument
     * @return string $name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets file name of the sound
     * @return string file name
     */
    public function getSoundFileName(): string
    {
        return $this->soundFileName;
    }

    /**
     * Gets the resource pack sound_definitions entry for this CustomInstrument
     * @return string $resourceEntry
     */
    public function getSound(): string
    {
        return $this->sound;
    }
}
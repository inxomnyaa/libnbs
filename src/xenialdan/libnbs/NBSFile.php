<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace xenialdan\libnbs;

use pocketmine\Server;
use pocketmine\utils\Binary;

class NBSFile
{
    const INSTRUMENT_PIANO = 0;//0 = Piano (air)
    const INSTRUMENT_DOUBLE_BASS = 1;//1 = Double Bass (wood)
    const INSTRUMENT_BASS_DRUM = 2;//2 = Bass Drum (stone)
    const INSTRUMENT_SNARE = 3;//3 = Snare Drum (sand)
    const INSTRUMENT_CLICK = 4;//4 = Click (glass)
    const INSTRUMENT_GUITAR = 5;//5 = Guitar (wool)
    const INSTRUMENT_FLUTE = 6;//6 = Flute (Clay)
    const INSTRUMENT_BELL = 7;//7 = Bell (Block of Gold)
    const INSTRUMENT_CHIME = 8;//8 = Chime (Packed Ice)
    const INSTRUMENT_XYLOPHONE = 9;//9 = Xylophone (Bone Block)
    /**
     * OpenNoteBlockStudio
     * @see https://hielkeminecraft.github.io/OpenNoteBlockStudio/nbs
     */
    const INSTRUMENT_IRONXYLOPHONE = 10;//10 = Iron Xylophone (Iron Block)
    const INSTRUMENT_COWBELL = 11;//11 = Cow Bell (Soul Sand)
    const INSTRUMENT_DIDGERIDOO = 12;//12 = Didgeridoo (Pumpkin)
    const INSTRUMENT_BIT = 13;//13 = Bit (Block of Emerald)
    const INSTRUMENT_BANJO = 14;//14 = Banjo (Hay)
    const INSTRUMENT_PLING = 16;//15 = Pling (Glowstone)

    public const MAPPING = [
        NBSFile::INSTRUMENT_PIANO => "note.harp",
        NBSFile::INSTRUMENT_DOUBLE_BASS => "note.bass",
        NBSFile::INSTRUMENT_BASS_DRUM => "note.basedrum",//TODO confirm. And where did bassattack go?
        NBSFile::INSTRUMENT_SNARE => "note.snare",
        NBSFile::INSTRUMENT_CLICK => "note.hat",
        NBSFile::INSTRUMENT_GUITAR => "note.guitar",
        NBSFile::INSTRUMENT_FLUTE => "note.flute",
        NBSFile::INSTRUMENT_BELL => "note.bell",
        NBSFile::INSTRUMENT_CHIME => "note.icechime",
        NBSFile::INSTRUMENT_XYLOPHONE => "note.xylobone",
        NBSFile::INSTRUMENT_IRONXYLOPHONE => "note.iron_xylophone",
        NBSFile::INSTRUMENT_COWBELL => "note.cow_bell",
        NBSFile::INSTRUMENT_DIDGERIDOO => "note.didgeridoo",
        NBSFile::INSTRUMENT_BIT => "note.bit",
        NBSFile::INSTRUMENT_BANJO => "note.banjo",
        NBSFile::INSTRUMENT_PLING => "note.pling",
    ];

    public $buffer;
    public $offset;
    private $data;

    public $length = 0;
    public $layers = 0;
    public $name = "";
    public $author = "";
    public $originalAuthor = "";
    public $songDescription = "";
    public $tempo = 0; // $tempo / 100 = tps
    public $autoSaving = 0;
    public $autoSavingDuration = 60;
    public $timeSignature = 4;
    public $minutesSpent = 0;
    public $leftClicks = 0;
    public $rightClicks = 0;
    public $blocksAdded = 0;
    public $blocksRemoved = 0;
    public $importedFileName = "";

    /** @var Note[] */
    public $notes = [];
    /** @var Layer[] */
    public $layerInfo = [];
    /** @var CustomInstrument[] */
    public $customInstruments = [];
    /**
     * If the file is created via OpenNoteBlockStudio https://github.com/HielkeMinecraft/OpenNoteBlockStudio/
     * @var bool
     */
    private $isOpenNBS = false;
    private $openNBSVersion = 0;
    private $vanillaInstrumentsCount = 0;

    public function __construct(string $path)
    {
        $fopen = fopen($path, "r");
        $this->buffer = fread($fopen, filesize($path));
        fclose($fopen);
        ### HEADER ###
        $this->length = $this->getShort();
        if ($this->length === 0) {
            $this->isOpenNBS = true;
        }
        if ($this->isOpenNBS) {
            $this->openNBSVersion = $this->getByte();
            $this->vanillaInstrumentsCount = $this->getByte();
            $this->length = $this->getShort();
        }
        $this->layers = $this->getShort();
        $this->name = $this->getString();
        $this->author = $this->getString();
        $this->originalAuthor = $this->getString();
        $this->songDescription = $this->getString();
        $this->tempo = $this->getShort();
        $this->autoSaving = $this->getByte();
        $this->autoSavingDuration = $this->getByte();
        $this->timeSignature = $this->getByte();
        $this->minutesSpent = $this->getInt();
        $this->leftClicks = $this->getInt();
        $this->rightClicks = $this->getInt();
        $this->blocksAdded = $this->getInt();
        $this->blocksRemoved = $this->getInt();
        $this->importedFileName = $this->getString();
        ### DATA ###
        /** @var Note[] $noteblocks */
        $notes = [];
        /** @var int[] $instrumentcount */
        $instrumentcount = [];
        /** @var int[] $layercount */
        $layercount = [];

        $tick = -1;
        $jumps = 0;
        while (true) {
            $jumps = $this->getShort();
            if ($jumps === 0) break;
            $tick += $jumps;
            $layer = -1;
            while (true) {
                $jumps = $this->getShort();
                if ($jumps === 0) break;
                $layer += $jumps;
                $instrument = $this->getByte();
                $key = $this->getByte();
                $notes[] = new Note($tick, $layer, $instrument, $key);
                if (isset($instrumentcount[$instrument])) {
                    $instrumentcount[$instrument]++;
                } else {
                    $instrumentcount[$instrument] = 1;
                }
                if ($layer < $this->layers) {
                    if (isset($layercount[$layer])) {
                        $layercount[$layer]++;
                    } else {
                        $layercount[$layer] = 1;
                    }
                };
            }
        }

        $this->notes = $notes;

        Server::getInstance()->getLogger()->debug("Found " . count($notes) . " notes");

        ### LAYER INFO ###
        for ($i = 0; $i < $this->layers; $i++) {
            $stereo = 100;
            $name = $this->getString();
            $volume = $this->getByte();
            if ($this->isOpenNBS) $stereo = $this->getByte();
            $layer = new Layer($i + 1, $name, $volume, $layercount[$i] ?? 0, $stereo);
            $this->layerInfo[] = $layer;

            //Stereoinfo string
            $stereoPercentage = abs($stereo - 100) / 100;
            $stereoString = "Center";
            if ($stereo > 100) {
                $stereoString = "Right";
            }
            if ($stereo < 100) {
                $stereoString = "Left";
            }
            Server::getInstance()->getLogger()->debug("Layer " . $layer->id . ", Name: " . $layer->name . ", Volume: " . $layer->volume . "%, Note blocks: " . $layer->notes . ", Stereo: " . $stereoString . " ($stereoPercentage%)");
        }

        if ($this->get(1) > 0) {
            for ($i = 0; $i < $this->getByte(); $i++) {
                $name = $this->getString();
                $soundFile = $this->getString();
                $pitch = $this->getByte();
                $pressKey = $this->getByte() === 1;
                $layer = new CustomInstrument($name, $soundFile, $pitch, $pressKey);
                $this->customInstruments[] = $layer;
                Server::getInstance()->getLogger()->debug("Custom instrument " . $i . ": Name: $name, Sound file: $soundFile, Pitch: $pitch, Press Key: " . ($pressKey ? "Yes" : "No"));
            }
        }
    }

    /**
     * @return Note[]
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param int $tick
     * @return Note[]
     */
    public function getNotesAtTick(int $tick)
    {
        $notes = [];
        foreach ($this->notes as $note) {
            if ($note->tick === $tick) $notes[] = $note;
        }
        return $notes;
    }

    /**
     * @return Layer[]
     */
    public function getLayerInfo(): array
    {
        return $this->layerInfo;
    }

    private function get($len)
    {
        if ($len < 0) {
            $this->offset = strlen($this->buffer) - 1;
            return "";
        } else if ($len === true) {
            return substr($this->buffer, $this->offset);
        }

        return $len === 1 ? $this->buffer{$this->offset++} : substr($this->buffer, ($this->offset += $len) - $len, $len);
    }

    private function getString(bool $network = false)
    {
        return $this->get(unpack("I", $this->get(4))[1]);
    }

    private function getShort(): int
    {
        return Binary::readLShort($this->get(2));
    }

    private function getByte(): int
    {
        return Binary::readByte($this->get(1));
    }

    private function getInt(bool $network = false): int
    {
        if ($network === true) {
            return Binary::readVarInt($this->buffer, $this->offset);
        }
        return Binary::readLInt($this->get(4));
    }
}
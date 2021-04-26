<?php
namespace iva3682\InsertHighLightTags;

/**
 * Tag short summary.
 *
 * Tag description.
 *
 * @version 1.0
 * @author Иван
 */
class Tag
{
    private $name = '';
    private $open = '';
    private $close = '';
    private $lengthOpen = 0;
    private $lengthClose = 0;

    public function __construct(string $name) {
        if(!strlen($name)) {
            throw new \Exception('Tag name cannot be empty.');
        }

        $this->name = $name;
        $this->open = '<' . $this->name . '>';
        $this->close = '</' . $this->name . '>';
        $this->lengthOpen = strlen($this->open);
        $this->lengthClose = strlen($this->close);
    }

    public function getLengthOpen(): int { return $this->lengthOpen; }
    public function getLengthClose(): int { return $this->lengthClose; }
    public function getOpen(): string { return $this->open; }
    public function getClose(): string { return $this->close; }
}
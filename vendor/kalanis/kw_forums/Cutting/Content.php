<?php

namespace kalanis\kw_forums\Cutting;


/**
 * Class CuttingContent
 * @package kalanis\kw_forums\Cutting
 * Cutting messages
 */
class Content
{
    // it's necessary to have both arrays sorted this way - it's for lookup by positions
    /** @var string[] */
    protected static array $OPENING_TAGS = ['<b ',  '<b>',  '<i ',  '<i>',  '<u ',  '<u>',  '<center>',  '<span>',  '<span ',  '<font color', '<font face', '<font size', '<font ', '<font', '<table ', '<table', '<tr>', '<td>', '<a ', '<a>'];
    /** @var string[] */
    protected static array $CLOSING_TAGS = ['</b>', '</b>', '</i>', '</i>', '</u>', '</u>', '</center>', '</span>', '</span>', '</font>', '</font>', '</font>', '</font>', '</font>', '</table>', '</table>', '</tr>', '</td>', '</a>', '</a>'];

    protected int $wordLengthNeedle = 20;
    protected int $maxLength = 0;
    protected string $content = '';
    /**
     * Contains positions of tags in
     * @var int[]
     */
    protected array $tagPositionsStack = [];

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        $this->tagPositionsStack = [];
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function process(): self
    {
        if (mb_strlen($this->content) <= $this->maxLength) {
            return $this;
        }

        // lookup for available end, the values has been found empirically
        $content = mb_substr($this->content, 0, $this->maxLength + $this->wordLengthNeedle);
        $len = mb_strrpos($content, ' ');
        $len = false !== $len ? intval($len) : null;
        $content = mb_substr($content, 0, $len) . ' ';

        $this->content = $this->closeQuotationMarks($this->contentCut($content)) . $this->fillCutTags() . ' ...';
        return $this;
    }

    /**
     * @param string $content content to cut
     * @return string updated string
     */
    protected function contentCut(string $content): string
    {
        $this->tagPositionsStack = [];
        for ($i = 0; $i <= mb_strlen($content); $i++) {
            // start tag sequence
            if ('<' == mb_substr($content, $i, 1)) {
//print_r(['found open', $i]);
                if ('/' == mb_substr($content, $i + 1, 1)) {
                    // closing tag
                    $tagPosition = $this->tagPosition(mb_strtolower(mb_substr($content, $i, 7)), static::$CLOSING_TAGS);
                    if (!is_null($tagPosition)) {
                        // is correct one in array of closing tags?
                        $lastPosition = array_pop($this->tagPositionsStack);
                        if (!is_null($lastPosition)) {
                            if (static::$CLOSING_TAGS[$tagPosition] == static::$CLOSING_TAGS[$lastPosition]) { // because there are multiple openings
                                $i = $i + mb_strlen(static::$CLOSING_TAGS[$tagPosition]) - 2; // move to next available position in string
                            } else {
                                $this->tagPositionsStack[] = $lastPosition;
                            }
                        }
                    }
//print_r(['is closing', $tagPosition, is_null($tagPosition) ? 'x' : static::$CLOSING_TAGS[$tagPosition]]);
                } else {
                    // opening tag
                    // cut by longest available tag in array
                    $tagPosition = $this->tagPosition(mb_substr($content, $i, 11), static::$OPENING_TAGS);
                    if (!is_null($tagPosition)) { // is as pair in array and I know that
                        $this->tagPositionsStack[] = $tagPosition;
                        $i = $i + mb_strlen(static::$OPENING_TAGS[$tagPosition]) - 1; // move to next available position in string
                    }
//print_r(['is opening', $tagPosition, is_null($tagPosition) ? 'x' : static::$OPENING_TAGS[$tagPosition]]);
                }
            }
        }

        return $content;
    }

    /**
     * Returns tag position
     * @param string $lookupTag
     * @param string[] $definedTags
     * @return int|null
     * int as position in defined tags, null as not found
     */
    protected function tagPosition(string $lookupTag, array $definedTags): ?int
    {
        foreach ($definedTags as $position => $definedTag) {
            $cutTag = mb_substr($lookupTag, 0, mb_strlen($definedTag));
            if ($cutTag == $definedTag) {
                return $position;
            }
        }
        return null;
    }

    protected function closeQuotationMarks(string $content): string
    {
        // check for closing tags with spaces and quotation marks inside
        $tagStarts = mb_strrpos($content, '<');
        $tagEnds = mb_strrpos($content, '>');
        if ((false !== $tagStarts) && ((false === $tagEnds) || (false !== $tagEnds) && ($tagStarts > $tagEnds))) { // unclosed tag
            $foundOdd = mb_substr_count(mb_substr($content, $tagStarts), "'");
            if ($foundOdd % 2) {
                $content .= "'";
            }
            $foundOdd = mb_substr_count(mb_substr($content, $tagStarts), '"');
            if ($foundOdd % 2) {
                $content .= '"';
            }
            $content .= '>';
        }
        return $content;
    }

    protected function fillCutTags(): string
    {
//var_dump(['rev', $this->tagPositionsStack]);
        $content = '';
        foreach (array_reverse($this->tagPositionsStack) as $positions) {
            $content .= static::$CLOSING_TAGS[$positions];
        }

        return $content;
    }
}

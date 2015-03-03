<?php
namespace RAFIE\Parser;

use Michelf\Markdown;
use RAFIE\Contracts\ParserContract;

/**
 * Class MarkdownParser
 * @package RAFIE\Parser
 */
class MarkdownParser implements ParserContract
{
  /**
   * @var Markdown
   */
  protected $parser;

  public function __construct(Markdown $parser)
  {
    $this->parser = $parser;
  }

  /**
   * @param string $content
   * @return string HTML output
   */
  public function parse($content)
  {
    return $this->parser->defaultTransform($content);
  }

}
<?php
namespace RAFIE\Contracts;

/**
 * Interface ParserContract
 */
interface ParserContract
{
  /**
   * @param string $content
   * @return string HTML output
   */
  public function parse($content);
}

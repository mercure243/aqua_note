<?php
namespace AppBundle\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Doctrine\Common\Cache\Cache;

class MarkdownTransformer
{
    private $markdownParser;
    private $cache;

    public function __construct(MarkdownParserInterface $markdownParser, Cache $cacheDriver)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cacheDriver;
    }

    public function parse($str)
    {
      $cache = $this->cache;
      $key = md5($str);
      if ($cache->contains($key)) {
         $str = $cache->fetch($key);
        }
        sleep(1);
      $str = $this->markdownParser->transformMarkdown($str);
      $cache->save($key,$str);

      return $str;
    }
}

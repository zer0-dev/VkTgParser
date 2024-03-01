<?php
class PostFile{
  public string $name;
  public string $type;
  public string $content;
  
  public function __construct(string $url, string $name) {
    $this->name    = $name;
    $this->type    = Util::getContentType($url);
    $this->content = file_get_contents($url);
  }
}
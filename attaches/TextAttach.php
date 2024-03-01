<?php
class TextAttach implements IAttach{
  const TYPE = 'text';
  
  private string $text;
  
  public function __construct(string $text) {
    $this->text = $text;
  }
  
  public function getType(): string {
    return self::TYPE;
  }
  
  public function getAttach(): string {
    return Util::vkToMarkdown($this->text);
  }
  
  public function getParameters(): array {
    return [];
  }
  
  public function getThumb(): array {
    return [];
  }
}
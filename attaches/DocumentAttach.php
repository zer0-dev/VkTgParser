<?php
class DocumentAttach implements IAttach{
  const TYPE = 'document';
  const ANIM_TYPE = 'animation';
  
  private string $type;
  private string $attach;
  private array $parameters;
  private array $thumb;
  
  public function __construct(object $doc) {
    $this->thumb      = [];
    $this->parameters = [];
    $this->attach     = $doc->url;
    if($doc->type == 3) {
      $this->type       = self::ANIM_TYPE;
      $this->parameters = ['width' => $doc->preview->video->width, 'height' => $doc->preview->video->height];
    }
    else $this->type = self::TYPE;
    if(!empty($doc->preview->photo)) {
      $this->thumb               = Util::getThumb($doc->preview->photo->sizes);
      $this->parameters['thumb'] = 'attach://'.$this->thumb['name'];
    }
    if($doc->size > Util::MAX_FILE_SIZE) {
      $this->type   = TextAttach::TYPE;
      $this->attach = Util::vkToMarkdown('Файл: '.$this->attach);
    }
  }
  
  public function getType(): string {
    return $this->type;
  }
  
  public function getAttach(): string {
    return $this->attach;
  }
  
  public function getParameters(): array {
    return $this->parameters;
  }
  
  public function getThumb(): array {
    return $this->thumb;
  }
}
<?php
class PhotoAttach implements IAttach{
  const TYPE = 'photo';
  
  private string $type;
  private string $attach;
  
  public function __construct(object $photo) {
    $s            = $photo->sizes;
    $this->attach = $s[count($s)-1]->url;
    $info         = Util::getFileSize($this->attach);
    $this->type   = ($info['size'] > Util::MAX_PHOTO_SIZE || $info['code'] != 200) ? TextAttach::TYPE : self::TYPE;
  }
  
  public function getType(): string {
    return $this->type;
  }
  
  public function getAttach(): string {
    return $this->attach;
  }
  
  public function getParameters(): array {
    return [];
  }
  
  public function getThumb(): array {
    return [];
  }
}
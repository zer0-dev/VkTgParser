<?php
class AudioAttach implements IAttach{
  const TYPE = 'audio';
  
  private string $type;
  private string $attach;
  private array $parameters;
  
  public function __construct(object $audio) {
    $this->parameters = [];
    $get              = VkCallback::api('audio.getById',['audios' => $audio->owner_id.'_'.$audio->id]);
    if(empty($get->error)) {
      $obj  = $get->response[0];
      $info = Util::getFileSize($obj->url);
      if($info['size'] > Util::MAX_FILE_SIZE || $info['code'] != 200) {
        $this->type   = TextAttach::TYPE;
        $this->attach = Util::vkToMarkdown('Аудиозапись: '.$obj->artist.' - '.$obj->title);
      } else{
        $this->type       = self::TYPE;
        $this->attach     = $obj->url;
        $this->parameters = ['performer' => $obj->artist, 'title' => $obj->title, 'duration' => $obj->duration];
      }
    } else{
      $this->type   = TextAttach::TYPE;
      $this->attach = Util::vkToMarkdown('Ошибка получения аудиозаписи: '.$get->error->error_msg);
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
    return [];
  }
}
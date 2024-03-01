<?php
class VideoAttach implements IAttach{
  const TYPE = 'video';
  
  private string $type;
  private string $attach;
  private array $thumb;
  private array $parameters;
  
  public function __construct(object $video) {
    $this->parameters = [];
    $this->thumb      = [];
    $get              = VkCallback::api('video.get',['videos' => $video->owner_id.'_'.$video->id.'_'.$video->access_key]);
    if(empty($get->error)) {
      $item        = $get->response->items[0];
      $this->thumb = Util::getThumb($item->image);
      $this->getBestVideo($item);
      $this->parameters = ['duration' => $item->duration, 'thumb' => 'attach://'.$this->thumb['name'], 'width' => $item->width, 'height' => $item->height];
    } else{
      $this->type   = TextAttach::TYPE;
      $this->attach = Util::vkToMarkdown('https://vk.com/video'.$video->owner_id.'_'.$video->id);
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
  
  private function getBestVideo(object $obj) {
    $files = array_reverse(get_object_vars($obj->files));
    $i = 0;
    if(count($files) > 0) {
      foreach($files as $k => $url) {
        if($k == 'external') {
          $this->type = TextAttach::TYPE;
          $this->attach = Util::vkToMarkdown($files['external']);
          return;
        } elseif(substr($k, 0, 4) == 'mp4_'){
          $info = Util::getFileSize($url);
          if($info['code'] != 200) {
            if($i == count($files)-1) break;
            continue;
          }
          if($info['size'] > Util::MAX_FILE_SIZE) continue;
          $this->type = self::TYPE;
          $this->attach = $url;
          return;
        }
        $i++;
      }
    }
    $this->type = TextAttach::TYPE;
    $this->attach = Util::vkToMarkdown('Видео: '.Util::shortLink($obj->player));
  }
}
<?php
class TeleBot{
  
  private string $token;
  private string $chat_id;
  
  public function __construct(string $token, string $chat_id) {
    $this->token   = $token;
    $this->chat_id = $chat_id;
  }
  
  public function sendMessage(string $text, string $parse_mode = Util::MARKDOWN) {
    $this->api('sendMessage',['chat_id' => $this->chat_id, 'text' => $text, 'parse_mode' => $parse_mode]);
  }
  
  public function sendMedia(Attach $file, string $caption = '', string $parse_mode = Util::MARKDOWN) {
    $thumb = [];
    if(!empty($file->thumb)) $thumb = [$file->thumb['name'] => $file->thumb['thumb']];
    $this->apiPostFile('send'.ucfirst($file->type), array_merge(['chat_id' => $this->chat_id, $file->type => $file->getPostFile(), 'caption' => $caption, 'parse_mode' => $parse_mode], $file->parameters, $thumb));
  }
  
  public function sendOther(string $type, array $parameters, string $caption = '', string $parse_mode = Util::MARKDOWN) {
    if(!empty($caption)) $this->sendMessage($caption, $parse_mode);
    $this->api('send'.ucfirst($type), array_merge(['chat_id' => $this->chat_id], $parameters));
  }
  
  public function sendAnimations(array $files, string $caption = '') {
    foreach($files as $f){
      $this->sendMedia($f, $caption);
      $caption = '';
    }
  }
  
  public function sendMediaGroup(array $files, string $caption = '', string $parse_mode = Util::MARKDOWN, bool $ignoreSeparate = false) {
    if(count($files) == 1) {
      $this->sendMedia($files[0], $caption, $parse_mode);
      return;
    }
    $post_files = [];
    $media      = [];
    $separate   = [];
    foreach($files as $f) {
      if(($f->type == 'audio' || $f->type == 'document' || $f->type == 'animation' || $f->type == 'location' || $f->type == 'poll') && !$ignoreSeparate) {
        $separate[$f->type][] = $f;
      } else{
        $post_files[$f->name] = $f->getPostFile();
        $media[]              = $f->getMediaInfo();
        if(!empty($f->thumb)) $post_files[$f->thumb['name']] = $f->thumb['thumb'];
      }
    }
    $media[0]['caption']    = $caption;
    $media[0]['parse_mode'] = $parse_mode;
    if(count($post_files) > 0) $this->apiPostFile('sendMediaGroup', array_merge(['chat_id' => $this->chat_id, 'media' => json_encode($media)], $post_files));
    if(count($separate) > 0) $this->sendMediaGroupSeparately($separate, (count($post_files) == 0) ? $caption : '');
  }
  
  public function sendMediaGroupSeparately(array $files, string $caption = '', string $parse_mode = Util::MARKDOWN) {
    foreach($files as $k => $v) {
      if(array_search($k, array_keys($files)) > 0) $caption = '';
      if($k == 'animation') $this->sendAnimations($v, $caption);
      else if($k == 'poll' || $k == 'location') $this->sendOther($k, $v[0]->parameters, $caption, $parse_mode);
      else $this->sendMediaGroup($v, $caption, $parse_mode, true);
    }
  }
  
  public function send(string $text, array $a) {
    for($i=0;$i<count($a);$i++) {
      if($a[$i]->type  == 'text') {
        $text .= "\n\n".$a[$i]->attach;
        unset($a[$i]);
      }
    }
    array_values($a);
    if(count($a) == 0) $this->sendMessage($text);
    else $this->sendMediaGroup($a, $text);
  }
  
  public function api(string $method, array $params): object {
    return json_decode(Util::get('https://api.telegram.org/bot'.$this->token.'/'.$method.'?'.http_build_query($params)));
  }
  
  public function apiPostFile(string $method, array $params): object {
    $body     = '';
    $boundary = '-------------'.uniqid();
    foreach($params as $k => $v) {
      $body .= '--'.$boundary."\r\n".'Content-Disposition: form-data; name="'.$k.'"';
      if($v instanceof PostFile) {
        $body .= '; filename="'.$v->name.'"'."\r\n";
        $body .= 'Content-Type: '.$v->type."\r\n\r\n";
        $body .= $v->content."\r\n";
      } else{
        $body .= "\r\n\r\n";
        $body .= $v."\r\n";
      }
    }
    $body .= '--'.$boundary.'--';
    return json_decode(Util::post('https://api.telegram.org/bot'.$this->token.'/'.$method, $body, ['Content-Type: multipart/form-data; boundary='.$boundary, 'Content-Length: '.strlen($body)]));
  }
}
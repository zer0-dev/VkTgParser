<?php
class VkCallback{
  
  private string $secret;
  private array $events;
  private string $ccode;
  
  public ?stdClass $data;
  
  public function __construct(string $ccode, string $secret = '') {
    $this->secret = $secret;
    $this->ccode  = $ccode;
    $this->events = [];
    $this->addEvent('confirmation',function() {
      echo $this->ccode;
    });
    $this->data = json_decode(file_get_contents('php://input'));
  }
  
  public function addEvent(string $event, callable $callback) {
    $this->events[$event] = $callback;
  }
  
  public function getAttachments(array $obj = null): array {
    $obj = $this->getObject();
    if(is_null($obj)) return [];
    $a = $obj->attachments;
    $r = [];
    if(count($a) > 0) {
      foreach($a as $f) {
        $r[] = new Attach($f);
      }
    }
    if(!empty($obj->geo)) $r[] = new Attach($obj->geo);
    return $r;
  }
  
  public function init() {
    if($this->data->secret == $this->secret) {
      $f = $this->events[$this->data->type];
      if(!empty($f)) $f();
    }
  }
  
  public function getObject(): ?object {
	if(empty($this->data)) return null;
	$o = $this->data->object;
	if(count($o->copy_history) > 0){
		$c = $o->copy_history[0];
		$t = "";
		if(!empty($o->text)) $t = $o->text."\n\n";
		$c->text = $t.$c->text;
		return $c;
	}
    return $this->data->object;
  }
  
  public static function api(string $method, array $params, string $token = Config::VK_TOKEN): object {
    return json_decode(Util::get('https://api.vk.com/method/'.$method.'?'.http_build_query($params).'&access_token='.$token.'&v='.Config::API_V, ['User-Agent: KateMobileAndroid/70-486 (Android 10; SDK 29; arm64-v8a; samsung SM-A715F; ru)']));
  }
}
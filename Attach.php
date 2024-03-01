<?php
require 'attaches/IAttach.php';
require 'attaches/PhotoAttach.php';
require 'attaches/VideoAttach.php';
require 'attaches/AudioAttach.php';
require 'attaches/DocumentAttach.php';
require 'attaches/LocationAttach.php';
require 'attaches/PollAttach.php';
require 'attaches/TextAttach.php';

class Attach{
  public string $type;
  public string $attach;
  public string $name;
  public array $parameters;
  public array $thumb;
  
  public function __construct(object $attach) {
    $this->name   = uniqid();
    $attachObject = new TextAttach('');
    switch($attach->type){
      case Util::PHOTO_TYPE:
        $attachObject = new PhotoAttach($attach->photo);
        break;
      case Util::VIDEO_TYPE:
        $attachObject = new VideoAttach($attach->video);
        break;
      case Util::AUDIO_TYPE:
        $attachObject = new AudioAttach($attach->audio);
        break;
      case Util::DOC_TYPE:
        $this->name = $attach->doc->title;
        $attachObject = new DocumentAttach($attach->doc);
        break;
      case Util::LINK_TYPE:
        $attachObject = new TextAttach('Ссылка: '.$attach->link->url);
        break;
      case Util::POINT_TYPE:
        $attachObject = new LocationAttach($attach);
        break;
      case Util::POLL_TYPE;
        $attachObject = new PollAttach($attach->poll);
        break;
      case Util::EVENT_TYPE:
        $attachObject = new TextAttach('Событие: https://vk.com/club'.$attach->event->id);
        break;
      case Util::NARRATIVE_TYPE:
        $attachObject = new TextAttach('Сюжет: https://vk.com/narrative'.$attach->narrative->owner_id.'_'.$attach->narrative->id);
        break;
      case Util::PAGE_TYPE:
        $attachObject = new TextAttach('Страница: https://vk.com/page-'.$attach->page->group_id.'_'.$attach->page->id);
        break;
      case Util::MARKET_TYPE:
        $attachObject = new TextAttach('Товар: https://vk.com/product'.$attach->market->owner_id.'_'.$attach->market->id);
        break;
      case Util::PODCAST_TYPE:
        $attachObject = new TextAttach('Подкаст "'.$attach->podcast->title.'": https://vk.com/podcast'.$attach->podcast->owner_id.'_'.$attach->podcast->id);
        break;
	  case Util::TEXTLIVE_TYPE:
        $attachObject = new TextAttach('Репортаж "'.$attach->textlive->title.'": '.$attach->textlive->attach_url);
        break;
    }
    $this->type       = $attachObject->getType();
    $this->attach     = $attachObject->getAttach();
    $this->parameters = $attachObject->getParameters();
    $this->thumb      = $attachObject->getThumb();
  }
  
  public function getPostFile(): PostFile {
    if($this->type != TextAttach::TYPE && !empty($this->attach)) return new PostFile($this->attach, $this->name);
    return '';
  }
  
  public function getMediaInfo(): array {
    return array_merge(['type' => $this->type, 'media' => 'attach://'.$this->name], $this->parameters);
  }
}
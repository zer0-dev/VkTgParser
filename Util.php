<?php
class Util{
  const PHOTO_TYPE     = 'photo';
  const VIDEO_TYPE     = 'video';
  const AUDIO_TYPE     = 'audio';
  const DOC_TYPE       = 'doc';
  const LINK_TYPE      = 'link';
  const POINT_TYPE     = 'point';
  const POLL_TYPE      = 'poll';
  const EVENT_TYPE     = 'event';
  const NARRATIVE_TYPE = 'narrative';
  const PAGE_TYPE      = 'page';
  const MARKET_TYPE    = 'market';
  const PODCAST_TYPE   = 'podcast';
  const TEXTLIVE_TYPE  = 'textlive';
  
  const MARKDOWN = 'MarkdownV2';
  const HTML = 'HTML';
  
  const MAX_PHOTO_SIZE = 10485760;
  const MAX_FILE_SIZE  = 52428800;
  
  const MAX_THUMB_FILESIZE = 204800;
  const MAX_THUMB_SIZE = 320;
  
  public static function vkToMarkdown(string $text): string {
	$text = preg_replace_callback('/#(.*)@([^\s]+)\b/Ui', function($m) {
	  return '#'.$m[1];
	}, $text);
    $text = preg_replace_callback('/[\_\*\[\]\(\)\~\`\>\#\+\-\=\|\{\}\.\!]/', function($m) {
      return '\\'.$m[0];
    }, $text);
    $text = preg_replace_callback('/\\\\\[https:\/\/(.*)\\\\\|(.*)\\\\\]/Ui', function($m) {
      return '['.$m[2].']('.str_replace([')','\\'],['\)','\\'],'https://'.$m[1]).')';
    }, $text);
	$text = preg_replace_callback('/\\\\\[http:\/\/(.*)\\\\\|(.*)\\\\\]/Ui', function($m) {
      return '['.$m[2].']('.str_replace([')','\\'],['\)','\\'],'http://'.$m[1]).')';
    }, $text);
	$text = preg_replace_callback('/\\\\\[vk\\\.com\\/(.*)\\\\\|(.*)\\\\\]/Ui', function($m) {
      return '['.$m[2].']('.str_replace([')','\\'],['\)','\\'],'https://vk.com/'.$m[1]).')';
    }, $text);
    $text = preg_replace_callback('/\\\\\[(.*)\\\\\|(.*)\\\\\]/Ui', function($m) {
      return '['.$m[2].']('.str_replace([')','\\'],['\)','\\'],'https://vk.com/'.$m[1]).')';
    }, $text);
    return $text;
  }
  
  public static function getFileSize(string $url): array {
    $ch  = curl_init($url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_NOBODY => true, CURLOPT_FOLLOWLOCATION => true];
    curl_setopt_array($ch, $opt);
    curl_exec($ch);
    return ['size' => curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD), 'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)];
  }
  
  public static function getContentType(string $url): string {
    $ch  = curl_init($url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_NOBODY => true, CURLOPT_FOLLOWLOCATION => true];
    curl_setopt_array($ch, $opt);
    curl_exec($ch);
    return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  }
  
  public static function getThumb(array $images): array {
    $name   = uniqid();
    $images = array_reverse($images);
    foreach($images as $i) {
      $url  = (empty($i->url)) ? $i->src : $i->url;
      $info = self::getFileSize($url);
      if($i->width > self::MAX_THUMB_SIZE || $i->height > self::MAX_THUMB_SIZE || $info['size'] > self::MAX_THUMB_FILESIZE || $info['code'] != 200) continue;
      return ['name' => $name, 'thumb' => new PostFile($url, $name)];
    }
    return ['name' => '', 'thumb' => null];
  }
  
  public static function shortLink(string $url): string {
	$req = VkCallback::api('utils.getShortLink', ['url' => $url, 'private' => 1]);
	if(!empty($req->response)){
	  return $req->response->short_url;
	} else{
	  return $url;
	}
  }
  
  public static function get(string $url, array $headers = []) {
    $ch  = curl_init($url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => $headers];
    curl_setopt_array($ch, $opt);
    return curl_exec($ch);
  }
  
  public static function post(string $url, string $data, array $headers = []) {
    $ch  = curl_init($url);
    $opt = [CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => $headers, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $data];
    curl_setopt_array($ch, $opt);
    return curl_exec($ch);
  }
}
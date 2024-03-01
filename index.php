<?php
require 'Config.php';
require 'PostFile.php';
require 'Attach.php';
require 'VkCallback.php';
require 'TeleBot.php';
require 'Util.php';

$vk = new VkCallback(Config::CONFIRM_CODE, Config::SECRET);
$t = new TeleBot(Config::BOT_TOKEN, Config::CHAT_ID);
$vk->addEvent('wall_post_new', function() {
  global $vk, $t;
  echo 'ok';
  fastcgi_finish_request();
  $d = $vk->getObject();
  $post = VkCallback::api('wall.getById',['posts' => $d->owner_id.'_'.$d->id]);
  if(empty($post->error)){
    $p = $post->response[0];
    if($p->post_type == 'suggest' || ($p->donut->is_donut && $p->donut->can_publish_free_copy)) return;
    $t->send(Util::vkToMarkdown($d->text), $vk->getAttachments());
  }
});
$vk->init();

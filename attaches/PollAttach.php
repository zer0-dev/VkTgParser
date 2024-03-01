<?php
class PollAttach implements IAttach{
  const TYPE = 'poll';
  
  private string $type;
  private string $attach;
  private array $parameters;
  
  public function __construct(object $poll) {
    $this->parameters = [];
    $this->attach = '';
    if(count($poll->answers) < 2 || count($poll->answers) > 10) {
      $this->type   = TextAttach::TYPE;
      $this->attach = Util::vkToMarkdown('Опрос: https://vk.com/poll'.$poll->owner_id.'_'.$poll->id);
      return;
    }
    $options = [];
    for($i=0;$i<count($poll->answers);$i++) {
      $options[] = $poll->answers[$i]->text;
    }
    $this->type       = self::TYPE;
    $this->parameters = ['question' => $poll->question, 'options' => json_encode($options), 'is_anonymous' => true, 'allows_multiple_answers' => $poll->multiple];
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
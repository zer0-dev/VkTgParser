<?php
class LocationAttach implements IAttach{
  const TYPE = 'location';
  
  private array $parameters;
  
  public function __construct(object $geo) {
    $c                = explode(' ', $geo->coordinates);
    $this->parameters = ['latitude' => $c[0], 'longitude' => $c[1]];
  }
  
  public function getType(): string {
    return self::TYPE;
  }
  
  public function getAttach(): string {
    return '';
  }
  
  public function getParameters(): array {
    return $this->parameters;
  }
  
  public function getThumb(): array {
    return [];
  }
}
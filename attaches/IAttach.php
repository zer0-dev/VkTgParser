<?php
interface IAttach{
  public function getType(): string;
  public function getAttach(): string;
  public function getParameters(): array;
  public function getThumb(): array;
}
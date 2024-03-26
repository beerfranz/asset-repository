<?php

class Frequency extends RogerEntity {

  protected \DateTimeImmutable $startsAt;

  protected ?\DateTimeImmutable $endsAt;

  // mmmh... finally, this is a crontab config
  protected $repeatYear;
  protected $repeatMonth;
  protected $repeatDay;
  protected $repeatHour;
  protected $repeatDayOfTheWeek;

  public function __construct(array $data = []) {

    if (!isset($data['startsAt']))
      $data['startsAt'] = new \DateTimeImmutable();

    $this->hydrator($data);
  }
  
}

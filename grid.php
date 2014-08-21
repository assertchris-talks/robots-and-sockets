<?php

use PHPMake\Firmata\Device;
use Ratchet\ConnectionInterface as Connection;
use Ratchet\MessageComponentInterface as MessageComponent;

class Grid implements MessageComponent
{
  protected $device;

  protected $connections;

  public function __construct()
  {
    $this->connections = new SplObjectStorage();

    $this->device = $device = new Device(
      "/dev/tty.usbserial-A7030YFN"
    );

    $device->setPinMode(2, PHPMake\Firmata::OUTPUT);
    $device->setPinMode(3, PHPMake\Firmata::OUTPUT);
    $device->setPinMode(4, PHPMake\Firmata::OUTPUT);
    $device->setPinMode(5, PHPMake\Firmata::OUTPUT);
    $device->setPinMode(6, PHPMake\Firmata::OUTPUT);
    $device->setPinMode(7, PHPMake\Firmata::OUTPUT);

    $device->setPinMode(9, PHPMake\Firmata::PWM);
  }

  public function onOpen(Connection $c)
  {
    $this->connections->attach($c);
  }

  public function onMessage(Connection $c, $message)
  {
    if (strlen($message) === 3) {
      $parts = explode(":", $message);

      $this->set((integer) $parts[0], (integer) $parts[1], 255);

      foreach ($this->connections as $connection) {
        if ($c != $connection) {
          $connection->send($message);
        }
      }
    }
  }

  public function onClose(Connection $c)
  {
    $this->connections->detach($c);
  }

  public function onError(Connection $c, Exception $e)
  {
    $c->close();
  }

  protected function set($column, $row, $value)
  {
    list($two, $three, $four) = [0, 0, 1];

    if ($column == 2) {
      list($two, $three, $four) = [1, 1, 0];
    }

    if ($column == 3) {
      list($two, $three, $four) = [0, 1, 0];
    }

    if ($column == 4) {
      list($two, $three, $four) = [1, 0, 0];
    }

    if ($column == 5) {
      list($two, $three, $four) = [0, 0, 0];
    }

    list($five, $six, $seven) = [0, 0, 1];

    if ($row == 2) {
      list($five, $six, $seven) = [1, 1, 0];
    }

    if ($row == 3) {
      list($five, $six, $seven) = [0, 1, 0];
    }

    if ($row == 4) {
      list($five, $six, $seven) = [1, 0, 0];
    }

    if ($row == 5) {
      list($five, $six, $seven) = [0, 0, 0];
    }

    $device = $this->device;

    $device->digitalWrite(2, $two);
    $device->digitalWrite(3, $three);
    $device->digitalWrite(4, $four);
    $device->digitalWrite(5, $five);
    $device->digitalWrite(6, $six);
    $device->digitalWrite(7, $seven);

    $device->analogWrite(9, $value);
  }
}

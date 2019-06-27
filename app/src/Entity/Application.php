<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
{
    public const APPLICATION_STATUS_NEW = 0;
    public const APPLICATION_STATUS_PROCESSED = 1;
    public const APPLICATION_STATUS_CANCELED = 2;
    public const APPLICATION_STATUS_CLOSED = 3;

    private static $statuses = [
      self::APPLICATION_STATUS_NEW => 'Новая',
      self::APPLICATION_STATUS_PROCESSED => 'В работе',
      self::APPLICATION_STATUS_CANCELED => 'Отменена',
      self::APPLICATION_STATUS_CLOSED => 'Закрыта',
    ];

    public static function getStatuses(): array {
      return self::$statuses;
    }

    public function getHumanStatus(): string {
      return self::$statuses[$this->status];
    }

    public function getPriority(): string {
      $now = time();
      return $now - $this->getCreated()  >= 3600 && $this->getStatus() === self::APPLICATION_STATUS_NEW ? 'urgent' : 'normal';
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}

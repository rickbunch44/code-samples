<?php

namespace AppBundle\Entity\Social;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Network
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SocialRepository")
 * @ORM\Table(name="ping_social_networks")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="media_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "snapchat" = "AppBundle\Entity\Social\Snapchat",
 *     "instagram" = "AppBundle\Entity\Social\Instagram",
 *     "facebook" = "AppBundle\Entity\Social\Facebook",
 *     "linkedin" = "AppBundle\Entity\Social\LinkedIn",
 * })
 */
abstract class Network
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string")
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string")
     */
    private $user;

    /**
     * @var string $handle
     *
     * @ORM\Column(name="handle", type="string")
     */
    private $handle;

    /**
     * @var string $socialId
     *
     * @ORM\Column(name="social_id", type="string")
     */
    private $socialId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = null;

    /**
     * @param string $userId
     * @param string $media
     * @throws \Exception
     */
    public function __construct($userId, $media)
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->user = $userId;
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function getSocialId()
    {
        return $this->socialId;
    }

    /**
     * @param string $socialId
     */
    public function setSocialId($socialId)
    {
        $this->socialId = $socialId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
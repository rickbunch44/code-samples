<?php

namespace AppBundle\Document;

use AppBundle\Document\Settings\AccountSettingsDocument;
use AppBundle\Document\Settings\GeneralSettingsDocument;
use AppBundle\Document\Settings\NotificationSettingsDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use MongoDB\BSON\Persistable;

/**
 * @MongoDB\Document()
 */
class userSettingsDocument implements Persistable
{
    /**
     * @Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $userId;

    /**
     * @var GeneralSettingsDocument
     * @MongoDB\Field(type="object")
     */
    private $general;

    /**
     * @var AccountSettingsDocument
     * @MongoDB\Field(type="object")
     */
    private $account;

    /**
     * @var NotificationSettingsDocument
     * @MongoDB\Field(type="object")
     */
    private $notifications;

    public function __construct($uuid)
    {
        $this->userId = $uuid;
        $this->general = new GeneralSettingsDocument();
        $this->account = new AccountSettingsDocument();
        $this->notifications = new NotificationSettingsDocument();
    }

    public function bsonSerialize()
    {
        return [
            "userId" => $this->userId,
            "general" => $this->general,
            "account" => $this->account,
            "notifications" => $this->notifications
        ];
    }

    public function bsonUnserialize(array $data)
    {
        $this->userId = $data['userId'];
        $this->general = $data['general'];
        $this->account = $data['account'];
        $this->notifications = $data['notifications'];
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return GeneralSettingsDocument
     */
    public function getGeneral()
    {
        return $this->general;
    }

    /**
     * @param GeneralSettingsDocument $general
     */
    public function setGeneral($general)
    {
        $this->general = $general;
    }

    /**
     * @return AccountSettingsDocument
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param AccountSettingsDocument $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return NotificationSettingsDocument
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param NotificationSettingsDocument $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }
}
<?php


namespace AdamPaterson\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class ResourceGuruResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var
     */
    protected $response;

    /**
     * ResourceGuruResourceOwner constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->response['id'] ?: null;
    }

    /**
     * Return all of the owner details available as an array
     *
     * @return mixed
     */
    public function toArray()
    {
        return $this->response;
    }

    /**
     * Get resource owner first name
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->response['first_name'] ?: null;
    }

    /**
     * Get resource owner last name
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->response['last_name'] ?: null;
    }

    /**
     * Get resource owner email address
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->response['email'] ?: null;
    }

    /**
     * Get resource owner image path
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->response['image'] ?: null;
    }

    /**
     * Get resource owners configured timezone
     *
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->response['timezone'] ?: null;
    }

    /**
     * Get resource owners last login date
     *
     * @return string|null
     */
    public function getLastLoginAt()
    {
        return $this->response['last_login_at'] ?: null;
    }

    /**
     * Get resource owners last logout date
     *
     * @return string|null
     */
    public function getLastLogoutAt()
    {
        return $this->response['last_logout_at'] ?: null;
    }

    /**
     * Get resource owners last activity date
     *
     * @return string|null
     */
    public function getLastActivityAt()
    {
        return $this->response['last_activity_at'] ?: null;
    }

    /**
     * Get resource owners activiation state
     *
     * @return string|null
     */
    public function getActivationState()
    {
        return $this->response['activation_state'] ?: null;
    }

    /**
     * Get date resource owner was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->response['created_at'] ?: null;
    }

    /**
     * Get date resource owners as updated
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->response['updated_at'] ?: null;
    }
}

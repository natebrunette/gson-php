<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock;

use Tebru\Gson\Annotation as Gson;
use Tebru\Gson\Annotation\Exclude;

/**
 * Class UserMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
class UserMock
{
    /**
     * @var int
     *
     * @Gson\Type("int")
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     *
     * @Exclude()
     */
    private $password;

    /**
     * @var string
     */
    private $name;

    /**
     * @var AddressMock
     */
    private $address;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var bool
     *
     * @Exclude(deserialize=false)
     */
    private $enabled = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return AddressMock
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param AddressMock $address
     */
    public function setAddress(AddressMock $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}

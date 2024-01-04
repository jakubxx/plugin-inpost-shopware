<?php

namespace WebLivesInPost\Models\Receiver;

class Address
{
    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $building_number;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $post_code;

    /**
     * @var string
     */
    private $country_code;

    /**
     * @var string
     */
    private $line1;

    /**
     * @var string
     */
    private $line2;

    /**
     * Address constructor.
     * @param string $street
     * @param string $building_number
     * @param string $city
     * @param string $post_code
     * @param string $country_code
     * @param string $line1
     * @param string $line2
     */
    public function __construct(
        string $street,
        string $building_number,
        string $city,
        string $post_code,
        string $country_code,
        string $line1 = '',
        string $line2 = ''
    ) {
        // required
        $this->street = $street;
        $this->building_number = $building_number;
        $this->city = $city;
        $this->post_code = $post_code;
        $this->country_code = $country_code;

        // optional
        $this->line1 = $line1;
        $this->line2 = $line2;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getBuildingNumber(): string
    {
        return $this->building_number;
    }

    /**
     * @param string $building_number
     */
    public function setBuildingNumber(string $building_number): void
    {
        $this->building_number = $building_number;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->post_code;
    }

    /**
     * @param string $post_code
     */
    public function setPostCode(string $post_code): void
    {
        $this->post_code = $post_code;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    /**
     * @param string $country_code
     */
    public function setCountryCode(string $country_code): void
    {
        $this->country_code = $country_code;
    }

    /**
     * @return string
     */
    public function getLine1(): string
    {
        return $this->line1;
    }

    /**
     * @param string $line1
     */
    public function setLine1(string $line1): void
    {
        $this->line1 = $line1;
    }

    /**
     * @return string
     */
    public function getLine2(): string
    {
        return $this->line2;
    }

    /**
     * @param string $line2
     */
    public function setLine2(string $line2): void
    {
        $this->line2 = $line2;
    }
}

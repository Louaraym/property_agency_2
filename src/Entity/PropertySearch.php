<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class PropertySearch
{
    /**
     * @var int|null
     *      * @Assert\Range(
     *      min = 100000,
     * )
     */
    private $maxPrice;

    /**
     * @var int|null
     * @Assert\Range(
     *      min = 10,
     *      max = 2000,
     * )
     */
    private $minSurface;

    /**
     * @var ArrayCollection
     */
    private $options;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var integer|null
     */
    private $distance;

    /**
     * @var float|null
     */
    private $lng;

    /**
     * @var float|null
     */
    private $lat;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * @param int|null $maxPrice
     * @return PropertySearch
     */
    public function setMaxPrice(int $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinSurface(): ?int
    {
        return $this->minSurface;
    }

    /**
     * @param int|null $minSurface
     * @return PropertySearch
     */
    public function setMinSurface(int $minSurface): self
    {
        $this->minSurface = $minSurface;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOptions(): ?ArrayCollection
    {
        return $this->options;
    }

    /**
     * @param ArrayCollection $options
     * @return PropertySearch
     */
    public function setOptions(ArrayCollection $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param int|null $distance
     * @return PropertySearch
     */
    public function setDistance(?int $distance): PropertySearch
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * @param float|null $lat
     * @return PropertySearch
     */
    public function setLat(?float $lat): PropertySearch
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLng(): ?float
    {
        return $this->lng;
    }

    /**
     * @param float|null $lng
     * @return PropertySearch
     */
    public function setLng(?float $lng): PropertySearch
    {
        $this->lng = $lng;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     * @return PropertySearch
     */
    public function setAddress(?string $address): PropertySearch
    {
        $this->address = $address;
        return $this;
    }

}
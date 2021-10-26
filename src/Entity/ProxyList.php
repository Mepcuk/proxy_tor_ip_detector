<?php

namespace App\Entity;

use App\Repository\ProxyListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProxyListRepository::class)
 */
class ProxyList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="integer")
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $countryCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proxyAnonymity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $googleCheck;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $httpsCheck;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $protocol;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getProxyAnonymity(): ?string
    {
        return $this->proxyAnonymity;
    }

    public function setProxyAnonymity(?string $proxyAnonymity): self
    {
        $this->proxyAnonymity = $proxyAnonymity;

        return $this;
    }

    public function getGoogleCheck(): ?bool
    {
        return $this->googleCheck;
    }

    public function setGoogleCheck(?bool $googleCheck): self
    {
        $this->googleCheck = $googleCheck;

        return $this;
    }

    public function getHttpsCheck(): ?bool
    {
        return $this->httpsCheck;
    }

    public function setHttpsCheck(?bool $httpsCheck): self
    {
        $this->httpsCheck = $httpsCheck;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }
}

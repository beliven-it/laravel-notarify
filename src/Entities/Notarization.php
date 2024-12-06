<?php

namespace Beliven\Notarify\Entities;

use JsonSerializable;
use Illuminate\Support\Carbon;

/**
 * Class Notarization
 *
 * Represents a notarization entity with an ID, hash, timestamp, and explorer URLs.
 */
class Notarization implements JsonSerializable
{
    /**
     * The unique identifier for the notarization.
     *
     * @var string
     */
    private string $id;

    /**
     * @var string $hash The hash value associated with the notarization.
     */
    private string $hash;

    /**
     * @var Carbon|null $timestamp The timestamp of the notarization, which can be null.
     */
    private ?Carbon $timestamp;

    /**
     * @var array|null $explorerUrls An array of URLs for explorers, or null if not set.
     */
    private ?array $explorerUrls;

    /**
     * Notarization constructor.
     *
     * @param string $id The unique identifier for the notarization.
     * @param string $hash The hash value associated with the notarization.
     */
    public function __construct(string $id, string $hash)
    {
        $this->id = $id;
        $this->hash = $hash;

        $this->timestamp = null;
        $this->explorerUrls = [];
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed The data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'timestamp' => $this->getTimestamp(),
            'hash' => $this->getHash(),
            'explorerUrls' => $this->getExplorerUrls(),
        ];
    }

    /**
     * Get the ID of the notarization.
     *
     * @return string The ID of the notarization.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the timestamp of the notarization.
     *
     * @return \Carbon\Carbon|null The timestamp of the notarization, or null if not set.
     */
    public function getTimestamp(): ?Carbon
    {
        return $this->timestamp;
    }

    /**
     * Get the hash of the notarization.
     *
     * @return string|null The hash of the notarization, or null if not available.
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * Get the explorer URLs.
     *
     * This method returns an array of URLs that can be used to explore
     * the notarization details.
     *
     * @return array An array of explorer URLs.
     */
    public function getExplorerUrls(): array
    {
        return $this->explorerUrls;
    }

    /**
     * Set the ID for the notarization.
     *
     * @param string $id The ID to set.
     * @return self Returns the instance of the Notarization for method chaining.
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the timestamp for the notarization.
     *
     * @param Carbon $timestamp The timestamp to set.
     * @return self
     */
    public function setTimestamp(Carbon $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Set the hash value.
     *
     * @param string $hash The hash value to set.
     * @return self
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Set the explorer URLs.
     *
     * @param array $explorerUrls An array of explorer URLs.
     * @return self Returns the current instance for method chaining.
     */
    public function setExplorerUrls(array $explorerUrls): self
    {
        $this->explorerUrls = $explorerUrls;
        return $this;
    }

    /**
     * Adds an explorer URL to the notarization entity's $explorerUrl array.
     *
     * @param string $explorerUrl The URL of the explorer to be added.
     * @return self Returns the instance of the Notarization entity.
     */
    public function addExplorerUrl(string $explorerUrl): self
    {
        $this->explorerUrls[] = $explorerUrl;
        return $this;
    }
}

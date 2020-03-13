<?php
namespace BetterEmbed\NeosEmbed\Domain\Dto;

class BetterEmbedRecord {

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $itemType;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $thumbnailUrl;

    /**
     * @var string
     */
    private $thumbnailContentType;

    /**
     * @var string
     */
    private $thumbnailContent;

    /**
     * @var string
     */
    private $embedHtml;

    /**
     * @var string
     */
    private $authorName;

    /**
     * @var string
     */
    private $authorUrl;

    /**
     * @var string
     */
    private $authorImage;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * BetterEmbedRecord constructor.
     * @param $object
     * @throws \Exception
     */
    public function __construct($object)
    {
        $this->url = $object->url;
        $this->itemType = $object->itemType;
        $this->title = $object->title ?? '';
        $this->body = $object->body ?? '';
        $this->thumbnailUrl = $object->thumbnailUrl ?? '';
        $this->thumbnailContentType = $object->thumbnailContentType ?? '';
        $this->thumbnailContent = $object->thumbnailContent ?? '';
        $this->embedHtml = $object->embedHtml ?? '';
        $this->authorName = $object->authorName ?? '';
        $this->authorUrl = $object->authorUrl ?? '';
        $this->authorImage = $object->authorImage ?? '';
        $this->publishedAt = isset($object->publishedAt) ? new \DateTime($object->publishedAt) : null;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getItemType(): string
    {
        return $this->itemType;
    }

    /**
     * @param string $itemType
     */
    public function setItemType(string $itemType): void
    {
        $this->itemType = $itemType;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string $thumbnailUrl
     */
    public function setThumbnailUrl(string $thumbnailUrl): void
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @return string
     */
    public function getThumbnailContentType(): string
    {
        return $this->thumbnailContentType;
    }

    /**
     * @param string $thumbnailContentType
     */
    public function setThumbnailContentType(string $thumbnailContentType): void
    {
        $this->thumbnailContentType = $thumbnailContentType;
    }

    /**
     * @return string
     */
    public function getThumbnailContent(): string
    {
        return $this->thumbnailContent;
    }

    /**
     * @param string $thumbnailContent
     */
    public function setThumbnailContent(string $thumbnailContent): void
    {
        $this->thumbnailContent = $thumbnailContent;
    }

    /**
     * @return string
     */
    public function getEmbedHtml(): string
    {
        return $this->embedHtml;
    }

    /**
     * @param string $embedHtml
     */
    public function setEmbedHtml(string $embedHtml): void
    {
        $this->embedHtml = $embedHtml;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return string
     */
    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    /**
     * @param string $authorUrl
     */
    public function setAuthorUrl(string $authorUrl): void
    {
        $this->authorUrl = $authorUrl;
    }

    /**
     * @return string
     */
    public function getAuthorImage(): string
    {
        return $this->authorImage;
    }

    /**
     * @param string $authorImage
     */
    public function setAuthorImage(string $authorImage): void
    {
        $this->authorImage = $authorImage;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
}


<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posts", indexes={
 *     @ORM\Index(name="blog", columns={"blog"}),
 *     @ORM\Index(name="author", columns={"author"}),
 *     @ORM\Index(name="posted_by", columns={"posted_by"})
 * })
 * @ORM\Entity(repositoryClass="PostRepository")
 */
class Post {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=13, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Domain\Generator\UniqidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=16777215, nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2, nullable=false, options={"default"="en"})
     */
    private $language = 'en';

    /**
     * @var string
     *
     * @ORM\Column(name="shortlink", type="string", length=30, nullable=false)
     */
    private $shortlink;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="header_img", type="string", length=13, nullable=true)
     */
    private $headerImage;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author", referencedColumnName="email")
     * })
     */
    private $author;

    /**
     * @var Blog
     *
     * @ORM\ManyToOne(targetEntity="Blog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="blog", referencedColumnName="name")
     * })
     */
    private $blog;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posted_by", referencedColumnName="email")
     * })
     */
    private $postedBy;

    /**
     * Post constructor.
     * @param string $content
     * @param string $language
     * @param string $shortlink
     * @param string $title
     * @param string $headerImage
     * @param User $author
     * @param Blog $blog
     * @param User $postedBy
     */
    private function __construct(string $content, string $language, string $shortlink, string $title, string $headerImage, User $author, Blog $blog, User $postedBy) {
        $this->content = $content;
        $this->language = $language;
        $this->shortlink = $shortlink;
        $this->title = $title;
        $this->headerImage = $headerImage;
        $this->author = $author;
        $this->blog = $blog;
        $this->postedBy = $postedBy;
    }

    public static function constructPost(string $content, string $language, string $title, User $author, Blog $blog, user $postedBy, string $headerImage = null): Post {
        $post = new Post(
            $content,
            $language,
            "",
            $title,
            $headerImage,
            $author,
            $blog,
            $postedBy
        );

        $post->generateShortlink();
        return $post;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getLanguage(): string {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getShortlink(): string {
        return $this->shortlink;
    }

    /**
     * @param string $shortlink
     */
    public function setShortlink(string $shortlink): void {
        $this->shortlink = $shortlink;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getHeaderImage(): string {
        return $this->headerImage;
    }

    /**
     * @param string $headerImage
     */
    public function setHeaderImage(string $headerImage): void {
        $this->headerImage = $headerImage;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }

    /**
     * @return User
     */
    public function getAuthor(): User {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void {
        $this->author = $author;
    }

    /**
     * @return Blog
     */
    public function getBlog(): Blog {
        return $this->blog;
    }

    /**
     * @param Blog $blog
     */
    public function setBlog(Blog $blog): void {
        $this->blog = $blog;
    }

    /**
     * @return User
     */
    public function getPostedBy(): User {
        return $this->postedBy;
    }

    /**
     * @param User $postedBy
     */
    public function setPostedBy(User $postedBy): void {
        $this->postedBy = $postedBy;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    public function getStringTimestamp(): string {
        return $this->timestamp->format(\DateTime::ATOM);
    }



    /**
     * Generate a new shortlink for this Post.
     */
    public function generateShortlink(): void {
        $shortlink = "";
        $titleWordArray = explode(" ", $this->title);

        // get first 5 words and hyphenate
        if(count($titleWordArray) < 5) {
            foreach($titleWordArray as $word) {
                $shortlinkWord = preg_replace("/[^A-Za-z0-9 ]/", '', $word);
                $shortlink .= strtolower($shortlinkWord) . "-";
            }
        } else {
            for($i = 0; $i < 5; $i++) {
                $shortlinkWord = preg_replace("/[^A-Za-z0-9 ]/", '', $titleWordArray[$i]);
                $shortlink .= strtolower($shortlinkWord) . "-";
            }
        }
        // remove illegal characters
        $shortlink = htmlspecialchars(substr($shortlink, 0, -1));

        // trim to 30
        if (strlen($shortlink) > 30) {
            $shortlink = substr($shortlink, 0, 30);
        }

        $this->shortlink = $shortlink;
    }

}

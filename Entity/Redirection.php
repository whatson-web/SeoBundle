<?php

namespace WH\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WH\LibBundle\Entity\LogDate;

/**
 * Redirection
 *
 * @ORM\Table(name="redirection")
 * @ORM\Entity(repositoryClass="WH\SeoBundle\Repository\RedirectionRepository")
 */
class Redirection
{

    use LogDate;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="urlToRedirect", type="string", length=255)
     */
    private $urlToRedirect;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectionUrl", type="string", length=255, nullable=true)
     */
    private $redirectionUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectionType", type="integer")
     */
    private $redirectionType;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urlToRedirect
     *
     * @param string $urlToRedirect
     *
     * @return Redirection
     */
    public function setUrlToRedirect($urlToRedirect)
    {
        $this->urlToRedirect = $urlToRedirect;

        return $this;
    }

    /**
     * Get urlToRedirect
     *
     * @return string
     */
    public function getUrlToRedirect()
    {
        return $this->urlToRedirect;
    }

    /**
     * Set redirectionUrl
     *
     * @param string $redirectionUrl
     *
     * @return Redirection
     */
    public function setRedirectionUrl($redirectionUrl)
    {
        $this->redirectionUrl = $redirectionUrl;

        return $this;
    }

    /**
     * Get redirectionUrl
     *
     * @return string
     */
    public function getRedirectionUrl()
    {
        return $this->redirectionUrl;
    }

    /**
     * Set redirectionType
     *
     * @param integer $redirectionType
     *
     * @return Redirection
     */
    public function setRedirectionType($redirectionType)
    {
        $this->redirectionType = $redirectionType;

        return $this;
    }

    /**
     * Get redirectionType
     *
     * @return integer
     */
    public function getRedirectionType()
    {
        return $this->redirectionType;
    }
}

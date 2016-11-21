<?php

namespace WH\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Metas
 *
 * @ORM\Table(name="metas")
 * @ORM\Entity(repositoryClass="WH\SeoBundle\Repository\MetasRepository")
 */
class Metas
{

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
	 * @ORM\Column(name="title", type="string", length=255, nullable=true)
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="robots", type="string", length=255, nullable=true)
	 */
	private $robots;

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Metas
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Metas
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set robots
	 *
	 * @param string $robots
	 *
	 * @return Metas
	 */
	public function setRobots($robots)
	{
		$this->robots = $robots;

		return $this;
	}

	/**
	 * Get robots
	 *
	 * @return string
	 */
	public function getRobots()
	{
		return $this->robots;
	}
}


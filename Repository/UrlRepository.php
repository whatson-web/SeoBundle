<?php

namespace WH\SeoBundle\Repository;

use WH\LibBundle\Repository\BaseRepository;

/**
 * Class UrlRepository
 *
 * @package WH\SeoBundle\Repository
 */
class UrlRepository extends BaseRepository
{

    /**
     * @return string
     */
    public function getEntityNameQueryBuilder()
    {
        return 'url';
    }
}

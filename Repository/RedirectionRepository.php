<?php

namespace WH\SeoBundle\Repository;

use WH\LibBundle\Repository\BaseRepository;

/**
 * Class RedirectionRepository
 *
 * @package WH\SeoBundle\Repository
 */
class RedirectionRepository extends BaseRepository
{

    /**
     * @return string
     */
    public function getEntityNameQueryBuilder()
    {
        return 'redirection';
    }
}

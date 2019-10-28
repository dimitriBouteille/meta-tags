<?php

namespace Dbout\MetaTags;

/**
 * Class MetaTagsGroup
 *
 * @package Dbout\MetaTags
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2019 Dimitri BOUTEILLE
 */
class MetaTagsGroup
{

    /**
     * @var string[]
     */
    private $meta = [];

    /**
     * Function addMeta
     *
     * @param string $meta
     * @return MetaTagsGroup
     */
    public function addMeta(string $meta): self {

        $this->meta[] = $meta;
        return $this;
    }

    /**
     * Function get
     *
     * @return array
     */
    public function get(): array {

        return $this->meta;
    }

}
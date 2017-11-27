<?php

namespace REBELinBLUE\Deployer\Traits;

use Venturecraft\Revisionable\RevisionableTrait;

trait Revisionable
{
    use RevisionableTrait;

    /**
     * Revision creations enabled.
     *
     * @var boolean
     */
    protected $revisionCreationsEnabled = true;
}

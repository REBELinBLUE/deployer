<?php

namespace REBELinBLUE\Deployer;

use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\Revision as RevisionModel;
use REBELinBLUE\Deployer\View\Presenters\RevisionPresenter;

/**
 * Revision model.
 */
class Revision extends RevisionModel implements HasPresenter
{
    /**
     * Belongs to relationship.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return RevisionPresenter::class;
    }
}

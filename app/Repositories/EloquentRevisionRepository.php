<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\RevisionRepositoryInterface;
use REBELinBLUE\Deployer\Revision;

class EloquentRevisionRepository extends EloquentRepository implements RevisionRepositoryInterface
{
    /**
     * EloquentRevisionRepository constructor.
     *
     * @param Revision $model
     */
    public function __construct(Revision $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->model->select('revisionable_type')->distinct()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances($type)
    {
        return $this->model->select('revisionable_type', 'revisionable_id')
                           ->where('revisionable_type', $type)
                           ->distinct()
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogEntries($paginate = 50, $filterByType = null, $filterByInstance = null)
    {
        $revisions = $this->model->orderBy('id', 'DESC');

        if (!empty($filterByType)) {
            $revisions->where('revisionable_type', $filterByType);

            if (!empty($filterByInstance)) {
                $revisions->where('revisionable_id', $filterByInstance);
            }
        }

        return $revisions->paginate($paginate);
    }
}

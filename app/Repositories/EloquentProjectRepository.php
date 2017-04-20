<?php

namespace REBELinBLUE\Deployer\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use REBELinBLUE\Deployer\Jobs\QueueUpdateGitMirror;
use REBELinBLUE\Deployer\Jobs\SetupProject;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;

/**
 * The project repository.
 */
class EloquentProjectRepository extends EloquentRepository implements ProjectRepositoryInterface
{
    use DispatchesJobs;

    /**
     * EloquentProjectRepository constructor.
     *
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->model
                    ->orderBy('name')
                    ->get();
    }

    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields)
    {
        $template = false;
        if (array_key_exists('template_id', $fields)) {
            $template = $fields['template_id'];

            unset($fields['template_id']);
        }

        if (array_key_exists('private_key', $fields) && empty($fields['private_key'])) {
            unset($fields['private_key']);
        }

        $project = $this->model->create($fields);

        if ($template) {
            $this->dispatch(new SetupProject(
                $project,
                $template
            ));
        }

        return $project;
    }

    /**
     * @param array $fields
     * @param int   $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, $model_id)
    {
        $project = $this->getById($model_id);

        if (array_key_exists('private_key', $fields)) {
            if (empty($fields['private_key'])) {
                unset($fields['private_key']);
            } else {
                $project->public_key = '';
            }
        }

        $project->update($fields);

        return $project;
    }

    /**
     * @param string $hash
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByHash($hash)
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return array
     */
    public function refreshBranches($model_id)
    {
        $project = $this->getById($model_id);

        $this->dispatch(new QueueUpdateGitMirror($project));
    }

    /**
     * Gets the projects last mirrored before the provided date.
     *
     * @param Carbon   $last_mirrored_since
     * @param int      $count
     * @param callable $callback
     *
     * @return Collection
     */
    public function getLastMirroredBefore(Carbon $last_mirrored_since, $count, callable $callback)
    {
        return $this->model->where('is_mirroring', false)
                           ->where('last_mirrored', '<', $last_mirrored_since)
                           ->orWhereNull('last_mirrored')
                           ->chunk($count, $callback);
    }
}

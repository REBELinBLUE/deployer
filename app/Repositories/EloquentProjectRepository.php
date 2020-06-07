<?php

namespace REBELinBLUE\Deployer\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Str;
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
    public function getAll(bool $with_users = false)
    {
        $projects = $this->model
            ->orderBy('name');

        if ($with_users === true) {
            $projects = $projects->with('users');
        }

        return $projects->get();
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

        // Finally we update the project members
        $this->setProjectMembers([
            'managers' => isset($fields['managers']) ? explode(',', $fields['managers']) : null,
            'users'    => isset($fields['users']) ? explode(',', $fields['users']) : null,
        ], $project);

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
    public function updateById(array $fields, int $model_id)
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

        // Finally we update the project members
        $this->setProjectMembers([
            'managers' => isset($fields['managers']) ? explode(',', $fields['managers']) : null,
            'users'    => isset($fields['users']) ? explode(',', $fields['users']) : null,
        ], $project);

        return $project;
    }

    /**
     * @param array   $members
     * @param Project $project
     */
    public function setProjectMembers(array $members, Project $project)
    {
        $sync = [];

        // Attaching the members to the projects
        if (is_array($members) && count($members) > 0) {
            foreach ($members as $role => $users) {
                if (is_array($users) && count($users) > 0) {
                    foreach ($users as $u) {
                        $u = trim($u);

                        // If user ID is invalid, skipping...
                        if (empty($u) || (bool) is_int($u)) {
                            continue;
                        }

                        // Adding relation to the sync array
                        $sync[$u] = ['role' => Str::singular($role)];
                    }
                }
            }

            // Finally we sync
            $project->users()->sync($sync);
        }
    }

    /**
     * @param string $hash
     *
     * @return Project
     */
    public function getByHash(string $hash): Project
    {
        return $this->model->where('hash', $hash)->firstOrFail();
    }

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return array
     */
    public function refreshBranches(int $model_id)
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
    public function getLastMirroredBefore(Carbon $last_mirrored_since, int $count, callable $callback)
    {
        return $this->model->where('is_mirroring', false)
                           ->where('last_mirrored', '<', $last_mirrored_since)
                           ->orWhereNull('last_mirrored')
                           ->chunk($count, $callback);
    }
}

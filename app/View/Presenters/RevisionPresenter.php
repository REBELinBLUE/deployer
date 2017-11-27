<?php

namespace REBELinBLUE\Deployer\View\Presenters;

/**
 * The view presenter for a revision class.
 */
class RevisionPresenter extends Presenter
{
    /**
     * Get the creator.
     *
     * @return string
     */
    public function presentCreator()
    {
        if ($this->wrappedObject->user) {
            return $this->wrappedObject->user->name;
        }

        return $this->translator->trans('revisions.system');
    }

    /**
     * Get the identifiable name for the model instance.
     *
     * @return string
     */
    public function presentIdentifiableName()
    {
        if (!$this->wrappedObject->revisionable) {
            // FIXME: Change to include trashed
            // https://github.com/VentureCraft/revisionable/issues/244
            //return $this->translator->trans('revisions.removed');
            return $this->wrappedObject->revisionable_id;
        }

        return $this->wrappedObject->revisionable->name . ' ('. $this->wrappedObject->revisionable->id . ')';
    }

    /**
     * Get the the label for the model.
     *
     * @return string
     */
    public function presentItemType()
    {
        $labels = [
            'group', 'project', 'template', 'server', 'user',
            'channel', 'checkurl', 'configfile', 'command',
            'heartbeat', 'sharedfile', 'variable',
        ];

        $className = class_basename($this->wrappedObject->revisionable_type);

        $label = strtolower($className);
//        if (in_array($label, $labels)) {
        if ($this->translator->has('revisions.' . $label)) {
            return $this->translator->trans('revisions.' . $label);
        }

        return $className;
    }

    /**
     * Gets the label for the event.
     *
     * @return string
     */
    public function presentEvent()
    {
        if (!$this->wrappedObject->revisionable || $this->wrappedObject->key === 'deleted_at') {
            return $this->translator->trans('revisions.removed');
        }

        if ($this->wrappedObject->key === 'created_at') {
            return $this->translator->trans('revisions.created');
        }

        if (empty($this->wrappedObject->old_value)) {
            return $this->translator->trans('revisions.set', [
                'field' => $this->wrappedObject->key,
                'new'   => $this->wrappedObject->new_value,
            ]);
        }

        return $this->translator->trans('revisions.changed', [
            'field' => $this->wrappedObject->key,
            'new'   => $this->wrappedObject->new_value,
            'old'   => $this->wrappedObject->old_value,
        ]);
    }
}

<?php namespace App\Http\Controllers;

use App\Heartbeat;
use App\Http\Requests\StoreHeartbeatRequest;

/**
 * Controller for managing notifications
 */
class HeartbeatController extends ResourceController
{
    /**
     * Handles the callback URL for the heartbeat
     *
     * @param string $hash The webhook hash
     * @return Response
     */
    public function ping($hash)
    {
        $heartbeat = Heartbeat::where('hash', $hash)
                              ->firstOrFail();

        $heartbeat->pinged();

        return 'OK';
    }

    /**
     * Store a newly created heartbeat in storage.
     *
     * @param StoreHeartbeatRequest $request
     * @return Response
     */
    public function store(StoreHeartbeatRequest $request)
    {
        return Heartbeat::create($request->only(
            'name',
            'interval',
            'project_id'
        ));
    }

    /**
     * Update the specified heartbeat in storage.
     *
     * @param Heartbeat $heartbeat
     * @param StoreHeartbeatRequest $request
     * @return Response
     */
    public function update(Heartbeat $heartbeat, StoreHeartbeatRequest $request)
    {
        $heartbeat->update($request->only(
            'name',
            'interval'
        ));

        return $heartbeat;
    }

    /**
     * Remove the specified heartbeat from storage.
     *
     * @param Heartbeat $heartbeat
     * @return Response
     */
    public function destroy(Heartbeat $heartbeat)
    {
        $heartbeat->delete();

        return [
            'success' => true
        ];
    }
}

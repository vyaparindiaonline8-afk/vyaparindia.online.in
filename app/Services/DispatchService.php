<?php

namespace App\Services;

use App\Models\Dispatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DispatchService
{
    /**
     * Create a new dispatch record.
     *
     * @param array $data
     * @return Dispatch
     */
    public function createDispatch(array $data): Dispatch
    {
        // Logic to create a dispatch
        return Dispatch::create($data);
    }

    /**
     * Update the status of a dispatch.
     *
     * @param Dispatch $dispatch
     * @param string $status
     * @return bool
     */
    public function updateDispatchStatus(Dispatch $dispatch, string $status): bool
    {
        $dispatch->status = $status;
        return $dispatch->save();
    }

    /**
     * Get a specific dispatch by its ID.
     *
     * @param int $id
     * @return Dispatch|null
     */
    public function getDispatchById(int $id): ?Dispatch
    {
        return Dispatch::find($id);
    }

    /**
     * Get all dispatches related to a user.
     *
     * @param User $user
     * @return Collection<int, Dispatch>
     */
    public function getDispatchesByUser(User $user): Collection
    {
        return $user->dispatches()->get();
    }
}

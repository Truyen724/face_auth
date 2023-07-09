<?php

namespace App\Repositories\User;

use App\Enums\UserTypes;
use App\Filters\UserFilter;
use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements  IUserRepository
{
    public function __construct()
    {
        $this->model = new User();
    }

    // public function get()
    // {
    //     $this->model = $this->model
    //         ->filter(new UserFilter(request()))
    //         ->where('id', '<>', SUPER_ADMIN_ID)
    //         ->where('type', UserTypes::ADMIN)
    //         ->with(['roles']);

    //     return parent::get();
    // }

    /**
     * Get single user by email
     */
    public function getByEmail($email)
    {
        $query = $this->model->where('email', $email)->frist();
        return $query;
    }

    public function getById($id)
    {
        $query = $this->model->where('id', $id)->get()->first();
        return $query;
    }

    public function checkExistenceByEmail($email)
    {
        return $this->model->where('email', $email)->first() != null;
    }

    public function checkExistenceByPhone($phone)
    {
        return $this->model->where('phone', $phone)->first() != null;
    }

    public function getUserProfileByUsername($username)
    {
        return $this->model->where('email', $username)->orWhere('phone', $username)->first();
    }

}

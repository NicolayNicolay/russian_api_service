<?php

namespace Modules\Users\Filters;

class UserFilter extends \Modules\System\Filters\QueryFilter
{
    /**
     * @param string $email
     */
    public function email(string $email)
    {
        $this->builder->where("email", "like", "%{$email}%");
    }
}

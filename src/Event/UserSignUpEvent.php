<?php


namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserSignUpEvent
 * @package App\Event
 */
class UserSignUpEvent extends Event
{
    public const EVENT_SIGN_UP = 'user.signup';

    /**
     * @var User
     */
    private $user;

    /**
     * UserRegisterEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
<?php


namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class MicroPostVoter
 * @package App\Security
 */
class MicroPostVoter extends Voter
{
    /**
     *
     */
    const EDIT_ACTION = 'edit';
    /**
     *
     */
    const REMOVE_ACTION = 'remove';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * MicroPostVoter constructor.
     * @param AccessDecisionManagerInterface $accessDecisionManager
     */
    public function __construct(
        AccessDecisionManagerInterface $accessDecisionManager
    )
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool|void
     */
    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT_ACTION, self::REMOVE_ACTION])){
            return false;
        }
        if(!$subject instanceof MicroPost){
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool|void
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->accessDecisionManager->decide($token, [User::ROLE_ADMIN])){
            return true;
        }

        $authenticatedUser = $token->getUser();
        if(!$authenticatedUser instanceof User){
            return false;
        }

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return ($microPost->getUser()->getId() === $authenticatedUser->getId());
    }
}
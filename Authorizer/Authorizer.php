<?php

namespace Ornicar\MessageBundle\Authorizer;

use Ornicar\MessageBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ornicar\MessageBundle\Model\ParticipantInterface;

/**
 * Provides the authenticated participant,
 * and manages permissions to manipulate threads and messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Authorizer implements AuthorizerInterface
{
    /**
     * The security context
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Tells if the current participant is allowed
     * to see this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    public function canSeeThread(ThreadInterface $thread)
    {
        return $this->isAuthenticated() && $thread->isParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Tells if the current participant is allowed
     * to delete this thread
     *
     * @param ThreadInterface $thread
     * @return boolean
     */
    public function canDeleteThread(ThreadInterface $thread)
    {
        return $this->canSeeThread($thread);
    }

    /**
     * Gets the current authenticated user
     *
     * @return ParticipantInterface
     */
    public function getAuthenticatedParticipant()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        $participant = $this->securityContext->getToken()->getUser();

        if (!$participant instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $participant;
    }

    /**
     * Tells if there is an authenticated user
     *
     * @return boolean
     */
    protected function isAuthenticated()
    {
        return $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @Assert\NotBlank()
     * @SecurityAssert\UserPassword()
     */
    protected $oldPassword;

    /**
     * @Assert\NotBlank()
     */
    protected $newPassword;
    
    function getOldPassword()
    {
        return $this->oldPassword;
    }

    function getNewPassword()
    {
        return $this->newPassword;
    }

    function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

}

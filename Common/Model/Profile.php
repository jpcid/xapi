<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Common\Model;

/**
 * A profile.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class Profile implements ProfileInterface
{
    /**
     * @var string The profile id
     */
    protected $profileId;

    /**
     * {@inheritDoc}
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileId()
    {
        return $this->profileId;
    }
}

<?php

namespace Oro\Bundle\DashboardBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Oro\Bundle\DashboardBundle\Entity\Dashboard;
use Oro\Bundle\UserBundle\Entity\User;

class DashboardRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Dashboard|null
     */
    public function findUserActiveDashboard(User $user)
    {
        $activeDashboard = $this->getEntityManager()->getRepository('OroDashboardBundle:ActiveDashboard')
            ->findOneBy(array('user' => $user));

        return $activeDashboard ? $activeDashboard->getDashboard() : null;
    }

    /**
     * @return Dashboard|null
     */
    public function findDefaultDashboard()
    {
        return $this->findOneBy(array('isDefault' => true));
    }
}
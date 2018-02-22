<?php

namespace AppBundle\Repository\Harvest;

use AppBundle\Entity\Harvest\TimeEntry;

/**
 * TimeEntryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TimeEntryRepository extends BaseRepository
{
	public function findAllByProject() {
		$qb = $this->getEntityManager()->createQueryBuilder();

		$query = $qb->select('te.hours', 'te.billableRate', '(te.hours * te.billableRate) AS total')
			->from('AppBundle:Harvest\TimeEntry', 'te')
			->getQuery();

		return $query->getResult();
	}
}

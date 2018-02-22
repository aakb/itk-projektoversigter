<?php

namespace AppBundle\Repository\Harvest;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends BaseRepository {

	public function findAllOwnedByValues() {
		$qb = $this->getEntityManager()->createQueryBuilder();

		$query = $qb->select( 'count(p.ownedBy) as count, p.ownedBy' )
		            ->from( 'AppBundle:Harvest\Project', 'p' )
		            ->groupBy( 'p.ownedBy' )
		            ->getQuery();

		return $query->getResult();
	}

	public function findBySearchData( array $data = null ) {
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select( 'p', 'c' )
		   ->from( 'AppBundle:Harvest\Project', 'p' )
		   ->leftJoin( 'p.client', 'c' )
		   ->orderBy( 'p.name', 'ASC' );

		if ( $data ) {
			$parameters = [];
			foreach ( $data as $key => $value ) {
				if ( $value ) {
					switch ( $key ) {
						case 'ownedBy':
							$qb->andWhere( 'p.' . $key . ' IN (:' . $key . ')' );
							$parameters[ $key ] = $value;
							break;
						case 'name':
							$qb->andWhere( 'p.' . $key . ' LIKE :' . $key );
							$parameters[ $key ] = '%' . $value . '%';
							break;
						case 'client':
							$qb->andWhere( 'c.name LIKE :' . $key );
							$parameters[ $key ] = '%' . $value . '%';
							break;
						case 'isActive':
							$qb->andWhere( 'p.' . $key . ' = :' . $key );
							$parameters[ $key ] = true;
							break;
						case 'type':
							$expression = $qb->expr()->orX();
							$conditions = [];
							switch ( $value ) {
								case 'non_billable':
									$conditions[] = 'p.isFixedFee = false AND p.isBillable = false';
									break;
								case 'fixed':
									$conditions[] = 'p.isFixedFee = true AND p.isBillable = true';
									break;
								case 'time':
									$conditions[] = 'p.isFixedFee = false AND p.isBillable = true';
									break;
							}

							if ( ! empty( $conditions ) ) {
								$expression->addMultiple( $conditions );
								$qb->andWhere( $expression );
							}
					}
				}
			}
			$qb->setParameters( $parameters );
		}

		$query = $qb->getQuery();

		return $query->getResult();
	}
}

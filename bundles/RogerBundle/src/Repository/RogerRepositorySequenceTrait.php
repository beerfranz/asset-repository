<?php

namespace Beerfranz\RogerBundle\Repository;

trait RogerRepositorySequenceTrait {

	public function increment(int $seqId = 1, int $increment = 1)
	{
		$conn = $this->getEntityManager()->getConnection();
        $sql = '
        WITH updated AS (
		  UPDATE assessment_sequence SET sequence_number = sequence_number + :increment 
		  WHERE id = :id RETURNING sequence_number
		)
		SELECT * FROM updated;
		';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('id', $seqId);
        $stmt->bindValue('increment', $increment);
        $result = $stmt->executeQuery();
        return $result->fetch()['sequence_number'];
	}

}

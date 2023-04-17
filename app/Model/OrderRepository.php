<?php
namespace App\Model;

use Nette;
use stdClass;
use Nette\Database\Explorer;

class OrderRepository
{
	public function __construct(
		private Nette\Database\Connection $database,
	) {
        $this->database = $database;
	}

	public function findPublishedOrders(): Nette\Database\Table\Selection
	{
		return
            $this->database->table('orders')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}
}
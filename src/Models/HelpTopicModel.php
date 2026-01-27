<?php
namespace App\Models;

use MongoDB\Client;
use App\Configs\Database;
use MongoDB\BSON\ObjectId;

class HelpTopicModel
{
	private $collection;

	public function __construct()
	{
		$db = Database::getDatabase();
		$this->collection = $db->help_topics;
	}

	/**
	 * Get all help topics
	 */
	public function getAll($activeOnly = true)
	{
		$query = $activeOnly ? ['status' => 'active'] : [];
		return $this->collection->find($query)->toArray();
	}

	/**
	 * Get help topics by department
	 */
	public function getByDepartment($departmentId)
	{
		try {
			return $this->collection->find([
				'department_id' => new ObjectId($departmentId),
				'status' => 'active'
			])->toArray();
		} catch (\Exception $e) {
			return [];
		}
	}

	/**
	 * Get help topic by ID
	 */
	public function getById($id)
	{
		try {
			return $this->collection->findOne(['_id' => new ObjectId($id)]);
		} catch (\Exception $e) {
			return null;
		}
	}
}
?>
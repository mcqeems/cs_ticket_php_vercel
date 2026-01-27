<?php
namespace App\Models;

use MongoDB\Client;
use App\Configs\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class DepartmentModel
{
	private $collection;
	private $db;

	public function __construct()
	{
		$this->db = Database::getDatabase();
		$this->collection = $this->db->departments;
	}

	/**
	 * Get all departments
	 */
	public function getAll($activeOnly = true)
	{
		$query = $activeOnly ? ['status' => 'active'] : [];
		return $this->collection->find($query)->toArray();
	}

	/**
	 * Get department by ID
	 */
	public function getById($id)
	{
		try {
			return $this->collection->findOne(['_id' => new ObjectId($id)]);
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Find department by name
	 */
	public function findByName($name)
	{
		return $this->collection->findOne(['name' => $name]);
	}

	/**
	 * Create new department
	 */
	public function create($data)
	{
		$departmentData = [
			'name' => $data['name'],
			'description' => $data['description'] ?? '',
			'status' => $data['status'] ?? 'active',
			'created_at' => new UTCDateTime(),
			'updated_at' => new UTCDateTime()
		];

		$result = $this->collection->insertOne($departmentData);
		return $result->getInsertedId();
	}

	/**
	 * Update department
	 */
	public function update($id, $data)
	{
		$updateData = [
			'name' => $data['name'],
			'description' => $data['description'] ?? '',
			'status' => $data['status'] ?? 'active',
			'updated_at' => new UTCDateTime()
		];

		$result = $this->collection->updateOne(
			['_id' => new ObjectId($id)],
			['$set' => $updateData]
		);

		return $result->getModifiedCount();
	}

	/**
	 * Delete department
	 */
	public function delete($id)
	{
		$result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
		return $result->getDeletedCount();
	}

	/**
	 * Get department statistics
	 */
	public function getStatistics($id)
	{
		$stats = [
			'total_tickets' => $this->db->tickets->countDocuments(['department_id' => new ObjectId($id)]),
			'open_tickets' => $this->db->tickets->countDocuments([
				'department_id' => new ObjectId($id),
				'status' => 'open'
			]),
			'agents_count' => $this->db->users->countDocuments([
				'role' => 'support_agent',
				'department' => $this->getById($id)['name'] ?? ''
			])
		];

		return $stats;
	}

	/**
	 * Get agents by department
	 */
	public function getAgentsByDepartment($departmentId)
	{
		try {
			$agents = $this->db->users->find([
				'role' => 'support_agent',
				'department_id' => new ObjectId($departmentId),
				'status' => 'active'
			])->toArray();

			return $agents;
		} catch (\Exception $e) {
			return [];
		}
	}

	/**
	 * Get all agents (for admin)
	 */
	public function getAllAgents()
	{
		return $this->db->users->find([
			'role' => ['$in' => ['support_agent', 'admin']],
			'status' => 'active'
		])->toArray();
	}
}
?>
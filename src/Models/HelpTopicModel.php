<?php
namespace App\Models;

use MongoDB\Client;
use App\Configs\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

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

	/**
	 * Find help topic by topic name
	 */
	public function findByTopicName($topicName)
	{
		return $this->collection->findOne(['topic_name' => $topicName]);
	}

	/**
	 * Create new help topic
	 */
	public function create($data)
	{
		$topicData = [
			'topic_name' => $data['topic_name'],
			'department_id' => new ObjectId($data['department_id']),
			'status' => $data['status'] ?? 'active',
			'created_at' => new UTCDateTime()
		];

		$result = $this->collection->insertOne($topicData);
		return $result->getInsertedId();
	}

	/**
	 * Update help topic
	 */
	public function update($id, $data)
	{
		$updateData = [
			'topic_name' => $data['topic_name'],
			'department_id' => new ObjectId($data['department_id']),
			'status' => $data['status'] ?? 'active'
		];

		$result = $this->collection->updateOne(
			['_id' => new ObjectId($id)],
			['$set' => $updateData]
		);

		return $result->getModifiedCount();
	}

	/**
	 * Delete help topic
	 */
	public function delete($id)
	{
		$result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
		return $result->getDeletedCount();
	}

	/**
	 * Get help topic statistics
	 */
	public function getStatistics($id)
	{
		$stats = [
			'total_tickets' => 0
		];

		// Get tickets collection
		$db = Database::getDatabase();
		$ticketsCollection = $db->tickets;

		// Count tickets with this help topic
		$stats['total_tickets'] = $ticketsCollection->countDocuments([
			'help_topic_id' => new ObjectId($id)
		]);

		return $stats;
	}
}
?>
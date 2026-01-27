<?php
namespace App\Models;

use MongoDB\Client;
use App\Configs\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class TicketModel
{
	private $collection;
	private $db;

	public function __construct()
	{
		$this->db = Database::getDatabase();
		$this->collection = $this->db->tickets;
	}

	/**
	 * Generate unique ticket number
	 */
	private function generateTicketNumber()
	{
		$year = date('Y');
		$lastTicket = $this->collection->findOne(
			[],
			['sort' => ['created_at' => -1]]
		);

		if ($lastTicket && isset($lastTicket['ticket_number'])) {
			// Extract number from last ticket (e.g., TKT-2026-00001)
			$parts = explode('-', $lastTicket['ticket_number']);
			$lastNumber = isset($parts[2]) ? intval($parts[2]) : 0;
			$newNumber = $lastNumber + 1;
		} else {
			$newNumber = 1;
		}

		return 'TKT-' . $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
	}

	/**
	 * Create new ticket
	 */
	public function create($data)
	{
		$ticketData = [
			'ticket_number' => $this->generateTicketNumber(),
			'user_id' => new ObjectId($data['user_id']),
			'department_id' => new ObjectId($data['department_id']),
			'help_topic_id' => new ObjectId($data['help_topic_id']),
			'subject' => $data['subject'],
			'message' => $data['message'],
			'status' => 'open',
			'priority' => $data['priority'] ?? 'normal',
			'assigned_to' => null,
			'created_at' => new UTCDateTime(),
			'updated_at' => null,
			'closed_at' => null
		];

		$result = $this->collection->insertOne($ticketData);

		if ($result->getInsertedCount() > 0) {
			// Log ticket creation in history
			$this->logHistory($result->getInsertedId(), $data['user_id'], 'created', 'Ticket created');

			return [
				'success' => true,
				'ticket_id' => $result->getInsertedId(),
				'ticket_number' => $ticketData['ticket_number']
			];
		}

		return ['success' => false, 'message' => 'Failed to create ticket'];
	}

	/**
	 * Get all tickets with filters
	 */
	public function getTickets($filters = [])
	{
		$query = [];

		if (isset($filters['user_id'])) {
			$query['user_id'] = new ObjectId($filters['user_id']);
		}

		if (isset($filters['status']) && !empty($filters['status'])) {
			$query['status'] = $filters['status'];
		}

		if (isset($filters['assigned_to'])) {
			$query['assigned_to'] = new ObjectId($filters['assigned_to']);
		}

		if (isset($filters['department_id'])) {
			$query['department_id'] = new ObjectId($filters['department_id']);
		}

		// Build aggregation pipeline
		$pipeline = [];

		// Only add $match stage if we have filters
		if (!empty($query)) {
			$pipeline[] = ['$match' => $query];
		}

		// Add lookup stages
		$pipeline = array_merge($pipeline, [
			[
				'$lookup' => [
					'from' => 'users',
					'localField' => 'user_id',
					'foreignField' => '_id',
					'as' => 'user'
				]
			],
			[
				'$lookup' => [
					'from' => 'departments',
					'localField' => 'department_id',
					'foreignField' => '_id',
					'as' => 'department'
				]
			],
			[
				'$lookup' => [
					'from' => 'help_topics',
					'localField' => 'help_topic_id',
					'foreignField' => '_id',
					'as' => 'help_topic'
				]
			],
			[
				'$lookup' => [
					'from' => 'users',
					'localField' => 'assigned_to',
					'foreignField' => '_id',
					'as' => 'agent'
				]
			],
			['$sort' => ['created_at' => -1]],
			[
				'$project' => [
					'ticket_number' => 1,
					'subject' => 1,
					'message' => 1,
					'status' => 1,
					'priority' => 1,
					'created_at' => 1,
					'updated_at' => 1,
					'user' => ['$arrayElemAt' => ['$user', 0]],
					'department' => ['$arrayElemAt' => ['$department', 0]],
					'help_topic' => ['$arrayElemAt' => ['$help_topic', 0]],
					'agent' => ['$arrayElemAt' => ['$agent', 0]]
				]
			]
		]);

		$tickets = $this->collection->aggregate($pipeline)->toArray();

		return $tickets;

		return $tickets;
	}

	/**
	 * Get ticket by ID
	 */
	public function getTicketById($ticketId)
	{
		try {
			$ticket = $this->collection->aggregate([
				['$match' => ['_id' => new ObjectId($ticketId)]],
				[
					'$lookup' => [
						'from' => 'users',
						'localField' => 'user_id',
						'foreignField' => '_id',
						'as' => 'user'
					]
				],
				[
					'$lookup' => [
						'from' => 'departments',
						'localField' => 'department_id',
						'foreignField' => '_id',
						'as' => 'department'
					]
				],
				[
					'$lookup' => [
						'from' => 'help_topics',
						'localField' => 'help_topic_id',
						'foreignField' => '_id',
						'as' => 'help_topic'
					]
				],
				[
					'$lookup' => [
						'from' => 'users',
						'localField' => 'assigned_to',
						'foreignField' => '_id',
						'as' => 'agent'
					]
				],
				[
					'$project' => [
						'ticket_number' => 1,
						'subject' => 1,
						'message' => 1,
						'status' => 1,
						'priority' => 1,
						'created_at' => 1,
						'updated_at' => 1,
						'closed_at' => 1,
						'user' => ['$arrayElemAt' => ['$user', 0]],
						'department' => ['$arrayElemAt' => ['$department', 0]],
						'help_topic' => ['$arrayElemAt' => ['$help_topic', 0]],
						'agent' => ['$arrayElemAt' => ['$agent', 0]]
					]
				]
			])->toArray();

			return $ticket[0] ?? null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Update ticket status
	 */
	public function updateStatus($ticketId, $status, $agentId = null)
	{
		$updateData = [
			'status' => $status,
			'updated_at' => new UTCDateTime()
		];

		if ($status === 'close') {
			$updateData['closed_at'] = new UTCDateTime();
		}

		$result = $this->collection->updateOne(
			['_id' => new ObjectId($ticketId)],
			['$set' => $updateData]
		);

		if ($result->getModifiedCount() > 0) {
			$this->logHistory($ticketId, $agentId, 'status_changed', "Status changed to: $status");
			return true;
		}

		return false;
	}

	/**
	 * Assign ticket to agent
	 */
	public function assignToAgent($ticketId, $agentId, $assignedBy)
	{
		$result = $this->collection->updateOne(
			['_id' => new ObjectId($ticketId)],
			[
				'$set' => [
					'assigned_to' => new ObjectId($agentId),
					'status' => 'process',
					'updated_at' => new UTCDateTime()
				]
			]
		);

		if ($result->getModifiedCount() > 0) {
			$this->logHistory($ticketId, $assignedBy, 'assigned', "Ticket assigned to agent");
			return true;
		}

		return false;
	}

	/**
	 * Add reply to ticket
	 */
	public function addReply($ticketId, $userId, $message, $isInternal = false)
	{
		$reply = [
			'ticket_id' => new ObjectId($ticketId),
			'user_id' => new ObjectId($userId),
			'message' => $message,
			'is_internal_note' => $isInternal,
			'created_at' => new UTCDateTime()
		];

		$result = $this->db->ticket_replies->insertOne($reply);

		if ($result->getInsertedCount() > 0) {
			// Update ticket status to process if it's open
			$ticket = $this->collection->findOne(['_id' => new ObjectId($ticketId)]);
			if ($ticket && $ticket['status'] === 'open') {
				$this->collection->updateOne(
					['_id' => new ObjectId($ticketId)],
					['$set' => ['status' => 'process', 'updated_at' => new UTCDateTime()]]
				);
			}

			$this->logHistory($ticketId, $userId, 'replied', 'Added a reply');
			return true;
		}

		return false;
	}

	/**
	 * Get ticket replies
	 */
	public function getReplies($ticketId)
	{
		$replies = $this->db->ticket_replies->aggregate([
			['$match' => ['ticket_id' => new ObjectId($ticketId)]],
			[
				'$lookup' => [
					'from' => 'users',
					'localField' => 'user_id',
					'foreignField' => '_id',
					'as' => 'user'
				]
			],
			['$sort' => ['created_at' => 1]],
			[
				'$project' => [
					'message' => 1,
					'is_internal_note' => 1,
					'created_at' => 1,
					'user' => ['$arrayElemAt' => ['$user', 0]]
				]
			]
		])->toArray();

		return $replies;
	}

	/**
	 * Get ticket history
	 */
	public function getHistory($ticketId)
	{
		$history = $this->db->ticket_history->aggregate([
			['$match' => ['ticket_id' => new ObjectId($ticketId)]],
			[
				'$lookup' => [
					'from' => 'users',
					'localField' => 'user_id',
					'foreignField' => '_id',
					'as' => 'user'
				]
			],
			['$sort' => ['timestamp' => 1]],
			[
				'$project' => [
					'action' => 1,
					'message' => 1,
					'timestamp' => 1,
					'user' => ['$arrayElemAt' => ['$user', 0]]
				]
			]
		])->toArray();

		return $history;
	}

	/**
	 * Log ticket history
	 */
	private function logHistory($ticketId, $userId, $action, $message)
	{
		if (!$userId)
			return;

		$history = [
			'ticket_id' => is_string($ticketId) ? new ObjectId($ticketId) : $ticketId,
			'user_id' => is_string($userId) ? new ObjectId($userId) : $userId,
			'action' => $action,
			'message' => $message,
			'timestamp' => new UTCDateTime()
		];

		$this->db->ticket_history->insertOne($history);
	}

	/**
	 * Get ticket statistics
	 */
	public function getStatistics($filters = [])
	{
		$match = [];

		if (isset($filters['user_id'])) {
			$match['user_id'] = new ObjectId($filters['user_id']);
		}

		if (isset($filters['assigned_to'])) {
			$match['assigned_to'] = new ObjectId($filters['assigned_to']);
		}

		$stats = [
			'total' => $this->collection->countDocuments($match),
			'open' => $this->collection->countDocuments(array_merge($match, ['status' => 'open'])),
			'process' => $this->collection->countDocuments(array_merge($match, ['status' => 'process'])),
			'closed' => $this->collection->countDocuments(array_merge($match, ['status' => 'close']))
		];

		return $stats;
	}

	/**
	 * Get agent-specific statistics
	 */
	public function getAgentStatistics($agentId)
	{
		$today = new UTCDateTime(strtotime('today') * 1000);

		$stats = [
			'assigned' => $this->collection->countDocuments(['assigned_to' => new ObjectId($agentId)]),
			'in_progress' => $this->collection->countDocuments([
				'assigned_to' => new ObjectId($agentId),
				'status' => 'process'
			]),
			'closed_today' => $this->collection->countDocuments([
				'assigned_to' => new ObjectId($agentId),
				'status' => 'close',
				'updated_at' => ['$gte' => $today]
			])
		];

		return $stats;
	}

	/**
	 * Get client-specific statistics
	 */
	public function getClientStatistics($userId)
	{
		$stats = [
			'total' => $this->collection->countDocuments(['user_id' => new ObjectId($userId)]),
			'open' => $this->collection->countDocuments([
				'user_id' => new ObjectId($userId),
				'status' => 'open'
			]),
			'process' => $this->collection->countDocuments([
				'user_id' => new ObjectId($userId),
				'status' => 'process'
			]),
			'closed' => $this->collection->countDocuments([
				'user_id' => new ObjectId($userId),
				'status' => 'close'
			])
		];

		return $stats;
	}
}
?>
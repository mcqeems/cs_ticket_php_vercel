<?php
namespace App\Models;

use App\Configs\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class KnowledgeBaseModel
{
	private $collection;
	private $db;

	public function __construct()
	{
		$this->db = Database::getDatabase();
		$this->collection = $this->db->knowledge_base;
	}

	/**
	 * Get all knowledge base articles
	 */
	public function getAll($publishedOnly = true, $filters = [])
	{
		$pipeline = [];
		$matchConditions = [];

		if ($publishedOnly) {
			$matchConditions['status'] = 'published';
		}

		// Add category filter
		if (!empty($filters['category'])) {
			$matchConditions['category'] = $filters['category'];
		}

		// Add tag filter
		if (!empty($filters['tag'])) {
			$matchConditions['tags'] = $filters['tag'];
		}

		if (!empty($matchConditions)) {
			$pipeline[] = ['$match' => $matchConditions];
		}

		$pipeline[] = [
			'$lookup' => [
				'from' => 'users',
				'localField' => 'created_by',
				'foreignField' => '_id',
				'as' => 'author'
			]
		];

		$pipeline[] = ['$unwind' => ['path' => '$author', 'preserveNullAndEmptyArrays' => true]];

		$pipeline[] = ['$sort' => ['created_at' => -1]];

		return $this->collection->aggregate($pipeline)->toArray();
	}

	/**
	 * Get article by ID
	 */
	public function getById($id)
	{
		try {
			$pipeline = [
				['$match' => ['_id' => new ObjectId($id)]],
				[
					'$lookup' => [
						'from' => 'users',
						'localField' => 'created_by',
						'foreignField' => '_id',
						'as' => 'author'
					]
				],
				['$unwind' => ['path' => '$author', 'preserveNullAndEmptyArrays' => true]]
			];

			$result = $this->collection->aggregate($pipeline)->toArray();
			return $result[0] ?? null;
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Get articles by author
	 */
	public function getByAuthor($authorId)
	{
		try {
			$pipeline = [
				['$match' => ['created_by' => new ObjectId($authorId)]],
				[
					'$lookup' => [
						'from' => 'users',
						'localField' => 'created_by',
						'foreignField' => '_id',
						'as' => 'author'
					]
				],
				['$unwind' => ['path' => '$author', 'preserveNullAndEmptyArrays' => true]],
				['$sort' => ['created_at' => -1]]
			];

			return $this->collection->aggregate($pipeline)->toArray();
		} catch (\Exception $e) {
			return [];
		}
	}

	/**
	 * Search knowledge base
	 */
	public function search($query)
	{
		$pipeline = [
			[
				'$match' => [
					'$or' => [
						['title' => ['$regex' => $query, '$options' => 'i']],
						['content' => ['$regex' => $query, '$options' => 'i']],
						['tags' => ['$regex' => $query, '$options' => 'i']]
					],
					'status' => 'published'
				]
			],
			[
				'$lookup' => [
					'from' => 'users',
					'localField' => 'created_by',
					'foreignField' => '_id',
					'as' => 'author'
				]
			],
			['$unwind' => ['path' => '$author', 'preserveNullAndEmptyArrays' => true]],
			['$sort' => ['created_at' => -1]]
		];

		return $this->collection->aggregate($pipeline)->toArray();
	}

	/**
	 * Create new article
	 */
	public function create($data)
	{
		$articleData = [
			'title' => $data['title'],
			'content' => $data['content'],
			'category' => $data['category'] ?? 'general',
			'tags' => isset($data['tags']) ? array_filter(array_map('trim', explode(',', $data['tags']))) : [],
			'status' => $data['status'] ?? 'draft',
			'created_by' => new ObjectId($data['author_id']),
			'views' => 0,
			'created_at' => new UTCDateTime()
		];

		$result = $this->collection->insertOne($articleData);
		return $result->getInsertedId();
	}

	/**
	 * Update article
	 */
	public function update($id, $data)
	{
		$updateData = [
			'title' => $data['title'],
			'content' => $data['content'],
			'category' => $data['category'] ?? 'general',
			'tags' => isset($data['tags']) ? array_filter(array_map('trim', explode(',', $data['tags']))) : [],
			'status' => $data['status'] ?? 'draft'
		];

		$result = $this->collection->updateOne(
			['_id' => new ObjectId($id)],
			['$set' => $updateData]
		);

		return $result->getModifiedCount();
	}

	/**
	 * Delete article
	 */
	public function delete($id)
	{
		$result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
		return $result->getDeletedCount();
	}

	/**
	 * Increment view count
	 */
	public function incrementViews($id)
	{
		$this->collection->updateOne(
			['_id' => new ObjectId($id)],
			['$inc' => ['views' => 1]]
		);
	}

	/**
	 * Get statistics
	 */
	public function getStatistics()
	{
		$totalArticles = $this->collection->countDocuments(['status' => 'published']);
		$totalDrafts = $this->collection->countDocuments(['status' => 'draft']);
		$totalViews = $this->collection->aggregate([
			['$group' => ['_id' => null, 'total' => ['$sum' => '$views']]]
		])->toArray();

		return [
			'total_articles' => $totalArticles,
			'total_drafts' => $totalDrafts,
			'total_views' => $totalViews[0]['total'] ?? 0
		];
	}
}
?>
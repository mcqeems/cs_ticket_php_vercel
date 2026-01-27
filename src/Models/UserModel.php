<?php
namespace App\Models;

use App\Configs\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class UserModel
{
	private $collection;

	public function __construct()
	{
		$db = Database::getDatabase();
		$this->collection = $db->users;
	}

	public function getData()
	{
		return $this->collection->find()->toArray();
	}

	/**
	 * Get users by role
	 */
	public function getUsersByRole($role)
	{
		return $this->collection->find(['role' => $role])->toArray();
	}

	/**
	 * Find user by ID
	 */
	public function findById($id)
	{
		try {
			return $this->collection->findOne(['_id' => new ObjectId($id)]);
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Find user by email
	 */
	public function findByEmail($email)
	{
		return $this->collection->findOne(['email' => $email]);
	}

	/**
	 * Create new user
	 */
	public function create($data)
	{
		$userData = [
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => password_hash($data['password'], PASSWORD_BCRYPT),
			'role' => $data['role'],
			'created_at' => new UTCDateTime(),
			'updated_at' => new UTCDateTime()
		];

		// Add optional fields
		if (isset($data['department']) && !empty($data['department'])) {
			$userData['department'] = $data['department'];
		}

		$result = $this->collection->insertOne($userData);
		return $result->getInsertedId();
	}

	/**
	 * Update user
	 */
	public function update($id, $data)
	{
		$updateData = [
			'name' => $data['name'],
			'email' => $data['email'],
			'role' => $data['role'],
			'updated_at' => new UTCDateTime()
		];

		// Update password only if provided
		if (isset($data['password']) && !empty($data['password'])) {
			$updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		}

		// Update department
		if (isset($data['department'])) {
			if (!empty($data['department'])) {
				$updateData['department'] = $data['department'];
			} else {
				$updateData['department'] = null;
			}
		}

		$result = $this->collection->updateOne(
			['_id' => new ObjectId($id)],
			['$set' => $updateData]
		);

		return $result->getModifiedCount();
	}

	/**
	 * Delete user
	 */
	public function delete($id)
	{
		$result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
		return $result->getDeletedCount();
	}
}

?>
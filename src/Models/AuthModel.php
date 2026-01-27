<?php
namespace App\Models;

use MongoDB\Client;
use App\Configs\Database;

class AuthModel
{
	private $collection;

	public function __construct()
	{
		$db = Database::getDatabase();
		$this->collection = $db->users;
	}

	/**
	 * Authenticate user by email and password
	 */
	public function login($email, $password)
	{
		$user = $this->collection->findOne(['email' => $email]);

		if ($user && password_verify($password, $user['password'])) {
			// Update last login
			$this->collection->updateOne(
				['_id' => $user['_id']],
				['$set' => ['last_login' => new \MongoDB\BSON\UTCDateTime()]]
			);

			return $user;
		}

		return false;
	}

	/**
	 * Register new user
	 */
	public function register($data)
	{
		// Check if email already exists
		$existingUser = $this->collection->findOne(['email' => $data['email']]);

		if ($existingUser) {
			return ['success' => false, 'message' => 'Email already registered'];
		}

		// Hash password
		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		$data['created_at'] = new \MongoDB\BSON\UTCDateTime();
		$data['status'] = 'active';

		// Set default role to client if not specified
		if (!isset($data['role'])) {
			$data['role'] = 'client';
		}

		$result = $this->collection->insertOne($data);

		if ($result->getInsertedCount() > 0) {
			return ['success' => true, 'user_id' => $result->getInsertedId()];
		}

		return ['success' => false, 'message' => 'Registration failed'];
	}

	/**
	 * Get user by ID
	 */
	public function getUserById($userId)
	{
		try {
			return $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($userId)]);
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Update user password
	 */
	public function updatePassword($userId, $newPassword)
	{
		$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

		$result = $this->collection->updateOne(
			['_id' => new \MongoDB\BSON\ObjectId($userId)],
			[
				'$set' => [
					'password' => $hashedPassword,
					'updated_at' => new \MongoDB\BSON\UTCDateTime()
				]
			]
		);

		return $result->getModifiedCount() > 0;
	}

	/**
	 * Update user profile
	 */
	public function updateProfile($userId, $data)
	{
		$data['updated_at'] = new \MongoDB\BSON\UTCDateTime();

		$result = $this->collection->updateOne(
			['_id' => new \MongoDB\BSON\ObjectId($userId)],
			['$set' => $data]
		);

		return $result->getModifiedCount() > 0;
	}
}
?>
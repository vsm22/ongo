<?php

namespace ongo\api\controller;

use ongo\shared\entity\SerializableEntity;
use ongo\shared\entity\UserEntity;
use ongo\shared\exception\NoAccessException;
use ongo\shared\exception\SessionNotFoundException;
use ongo\shared\model\OrderModel;
use Doctrine\DBAL\Connection;
use ongo\shared\model\PhotoModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class OrderController {
	private $dbConn;

	public function __construct(Connection $dbConn) {
		$this->dbConn = $dbConn;
	}

	public function create(UserEntity $user, Array $order)
	{
		$model = new OrderModel($this->dbConn);

		$order = $model->createForUser($user->getId(), serialize($order));

        return new JsonResponse($order->serialize($this->dbConn));
        
	}

	public function byUserId($id)
	{
		$model = new OrderModel($this->dbConn);
		$orders = $model->byUserId($id);

		$result = SerializableEntity::serializeArray($orders, $this->dbConn);
		array_walk($result,function(&$order) {
			$order['payment_status'] = $order['status'] == 'paid' ? 'payment_approved' : 'payment_failed';
		});
		return new JsonResponse($result);
	}

	public function byIdforUserId($id, $user_id)
	{
		$order = $this->getUserOrder($id, $user_id);

		return new JsonResponse($order->serialize($this->dbConn));
	}

	public function downloadByUser($id, $photo_id, $user_id)
	{
		$order = $this->getUserOrder($id, $user_id);
		if ($order->getStatus() != 'paid') {
			throw new NoAccessException('Order not paid');
		}
		$data=$order->serialize($this->dbConn);
		$items = $data['payload']['items'];
		if (!$items || !isset($items[$photo_id])) {
			throw new NoAccessException('Photo did not ordered');
		}
		$photoModel = new PhotoModel($this->dbConn);
		$photo = $photoModel->findById($photo_id);

		return new JsonResponse(['src' => $photo->getSrc()]);

	}

	/**
	 * @param $id
	 * @param $user_id
	 * @return \ongo\shared\entity\OrderEntity
	 * @throws NoAccessException
	 * @throws \ongo\shared\exception\OrderNotFoundException
	 */
	private function getUserOrder($id, $user_id)
	{
		$model = new OrderModel($this->dbConn);
		$order = $model->findById($id);

		if ($order->getUserId() !== $user_id) {
			throw new NoAccessException();
		}
		return $order;
	}
}


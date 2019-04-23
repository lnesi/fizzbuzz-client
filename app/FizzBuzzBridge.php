<?php

namespace App;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\RequestOptions;

class FizzBuzzBridge {

	/**
	 * @var API_ROOT root end point for fizzbuzz api
	 **/
	private $API_ROOT = "http://localhost:3000";

	/**
	 *  Perist cookie into file system
	 **/
	private $cookieFile = 'cookie_jar.txt';

	public function __construct() {
		$cookieJar = new FileCookieJar($this->cookieFile, TRUE);
		$this->client = new Client(['cookies' => $cookieJar]);

	}

	/**
	 * Get list fizz buzz by page and size
	 * @param $page int page to load from 1 to max pages
	 * @param $size int size of page results default 100
	 *
	 * @return array mixed json decoded from api payload
	 **/
	public function getList($page = 1, $size = 100) {
		$result = $this->client->get($this->API_ROOT . '/api/v1/fizzbuzz?page=' . $page . "&size=" . $size);
		$data = json_decode($result->getBody(), true);
		return $data;
	}

	/**
	 * Persist favorites at cookie this will also sync list on api
	 * @param $id int id of item on the list to add to favorites
	 *
	 * @return object Api response please visit API docs for more details
	 **/
	public function postFav($id) {
		$result = $this->client->post($this->API_ROOT . '/api/v1/favorites', [
			RequestOptions::JSON => ['id' => $id],
		]);
		$data = json_decode($result->getBody(), true);
		return $data;
	}

	/**
	 * Get list of favorites for display
	 * @return array mixed of current added to fav
	 **/
	public function getFavorites() {
		$result = $this->client->get($this->API_ROOT . '/api/v1/favorites');
		$data = json_decode($result->getBody(), true);
		return $data;
	}

}

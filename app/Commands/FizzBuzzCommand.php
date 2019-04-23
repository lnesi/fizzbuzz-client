<?php

namespace App\Commands;

use App\FizzBuzzBridge;
use GuzzleHttp\Client;
use LaravelZero\Framework\Commands\Command;

class FizzBuzzCommand extends Command {
	/**
	 * The signature of the command.
	 *
	 * @var string
	 */
	protected $signature = 'start';

	/**
	 * CLI Session pagination cursor
	 *
	 * @var array(mixed)
	 */
	private $page = ['current' => 1, 'size' => 100];

	/**
	 * CLI Session list on display
	 *
	 * @var array(mixed)
	 */
	private $list = [];

	/**
	 * CLI UI operations
	 *
	 * @var array(string)
	 */
	private $menu = ['Help', 'Show results', 'Change page size', 'Go to page', 'Show favorites', 'Add or remove to favorites', 'Close (exit)'];

	/**
	 * HTTP Client to share for single session on cookie
	 */
	private $client;
	/**
	 * The description of the command.
	 *
	 * @var string
	 */
	protected $description = 'Strat FizzBuzz Client app';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->info('FizzBuzz');
		$this->line('Welcome to FizzBuzz Client');
		//Initialize cookie JAR
		$this->client = new FizzBuzzBridge();
		$this->load();
	}

	/**
	 * Print Table list  on CLI
	 **/
	private function printPage() {
		$headers = ['ID', 'Value', 'Favorite'];
		$this->table($headers, $this->list);
		$this->pagination();
		$this->printMenu();
	}

	/**
	 * Print Help for app getrusage
	 */
	private function printHelp() {
		$this->info("FizzBuzz Client Help");
		$this->line("Select the oprations you desire to execute by introducing the action number in brakets on the menu.");
		$this->info("Navigation");
		$this->line("Yo can navigate the pagination by using Go to page [3] command and introducing page number, if page number is invalid the app will default back to page 1");
		$this->info("Favorites");
		$this->line("You can add or remove to favories by using option [5] and indicating the ID of the item you desire to save or remove.");
		$this->line("If the item is not already added into the favorites will be added however if the item is alredy on the favorite list will be removed.");
		$this->info("Pagination");
		$this->line("You can specified the size of list to display by using option [2]. If the size is negative or out of bounds (100,000,000,000) will default to 100 which is the default value.");
		$this->printMenu();
	}

	/**
	 * Load list from api
	 */
	private function load() {
		$jsonData = $this->client->getList($this->page['current'], $this->page['size']);
		$this->page = $jsonData['page'];
		$this->list = array_map([$this, 'transformListResponse'], $jsonData['data']);
		$this->printPage();
	}

	/**
	 * Transform list from api structure to require array with favorite display for CLI table
	 */
	private function transformListResponse($item) {
		return array(
			$item['id'],
			$item['value'],
			$item['fav'] == true ? 'Yes' : 'No');
	}

	/**
	 * Print pagination status on Client
	 */
	private function pagination() {
		$this->line("Page: <fg=green>" . $this->page['current'] . " of " . $this->page['total'] . "</>");
		$this->line("Page size: <fg=green>" . $this->page['size'] . "</>");
	}

	/**
	 * Print Menu on screen and listen for action
	 */
	private function printMenu() {
		$this->logo();
		$this->line("Please select the number of options below:");
		$action = $this->choice('Main Menu', $this->menu);
		$this->processAction($action);
	}

	/**
	 * Actions map handler
	 */
	private function processAction($action) {
		switch ($action) {
		case "Help":
			$this->printHelp();
			break;
		case "Show results":
			$this->load();
			break;
		case "Change page size":
			$this->changePageSize();
			break;
		case "Go to page":
			$this->goToPage();
			break;
		case "Show favorites":
			$this->showFavorites();
			break;
		case "Add or remove to favorites":
			$this->processFavorites();
			break;
		case "Close (exit)":
			$this->line("Bye Bye! :) Thank you!");
			break;
		}
	}

	/**
	 *  Change Page Size action handler
	 */
	private function changePageSize() {
		$size = $this->ask('Please indicate how manu items per page (number)');
		if (is_numeric($size)) {
			$this->page['size'] = $size;
			$this->load();
		} else {
			$this->error("Value is required or  invalid type");
			$this->changePageSize();
		}
	}

	/**
	 * Navigate to page on list action handler
	 */
	private function goToPage() {
		$current = $this->ask('Please indicate which page you will like to load (number)');
		if (is_numeric($current)) {
			$this->page['current'] = $current;
			$this->load();
		} else {
			$this->error("Value is required or  invalid type");
			$this->changePageSize();
		}
	}

	/**
	 * Process Favorites Action handler
	 */
	private function processFavorites() {
		$this->line('<fg=red>Attention:</> If the item you are trying to add is already added will be removed from favorites');
		$id = $this->ask('Please id(index) whould you like to add or remove (number)');
		if (is_numeric($id)) {
			$this->postFavorites($id);
		} else {
			$this->error("Value is required or invalid type");
			$this->changePageSize();
		}
	}

	/**
	 * POST favorites to API to persist
	 */
	private function postFavorites($id) {
		$data = $this->client->postFav($id);
		$this->line($data['msg']);
		$this->printMenu();
	}

	/**
	 * Load and print table Favorites
	 */
	private function showFavorites() {
		$data = $this->client->getFavorites();
		$this->info("Favorites");
		if (count($data['data']) > 0) {
			$headers = ['ID', 'Value'];
			$this->table($headers, $data['data']);
		} else {
			$this->line("No items added yet");
		}

		$this->printMenu();
	}

	/**
	 * Print fizz buzz logo
	 */
	private function logo() {
		$this->line(" __ _          _                   ");
		$this->line("/ _(_)        | |                  ");
		$this->line("| |_ _ _______| |__  _   _ ________");
		$this->line("|  _| |_  /_  / '_ \| | | |_  /_  /");
		$this->line("| | | |/ / / /| |_) | |_| |/ / / / ");
		$this->line("|_| |_/___/___|_.__/ \__,_/___/___|");

	}
}

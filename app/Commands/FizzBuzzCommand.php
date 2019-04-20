<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class FizzBuzzCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';
    //Perist pagination and list for reprint
    private $page=['current'=>1,'size'=>100];
    private $list=[];
    //Global Menu
    private $menu=['Help','Show results','Change page size','Go to page','Add or remove to favorites'];


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
    public function handle()
    {
        $this->info('FizzBuzz');
        $this->line('Welcome to FizzBuzz Client');
        $this->load();

    }

    private function process($page=1,$size=100){
      $list=$this->loadList($page,$size);
      $this->page=$list['page'];

      $this->printPage();

    }
    private function printPage(){
      $headers = ['ID', 'Value','Favorite'];
      $this->table($headers, $this->list);
      $this->pagination();
      $this->printMenu();
    }

    private function printHelp(){
        $this->info("FizzBuzz Client Help");
        $this->printMenu();
    }

    private function load(){
      $client = new Client(); //GuzzleHttp\Client
      $result=$client->get('http://localhost:3000/api/v1/fizzbuzz?page='.$this->page['current']."&size=".$this->page['size']);
      $jsonData=json_decode($result->getBody(),true);
      $this->page=$jsonData['page'];
      $this->list=array_map([$this,'transformListResponse'],$jsonData['data']);
      $this->printPage();
    }

    private function transformListResponse($item){
      return array(
        $item['id'],
        $item['value'],
        $item['fav']===true?'Yes':'No');
    }

    private function pagination(){
      $this->line("Page: <fg=green>".$this->page['current']." of ".$this->page['total']."</>");
      $this->line("Page size: <fg=green>".$this->page['size']."</>");
    }



    private function resetMenu(){
      $this->menu=[];
      $this->buildDefaultMenu();
    }

    private function printMenu(){
      $this->line("Please select the number of options below:");
      $action=$this->choice('Main Menu',$this->menu);
      $this->processAction($action);
    }

    private function processAction($action){
      switch($action){
        case "Help":
          $this->printHelp();
          break;
        case "Show results":
          $this->printPage();
          break;
        case "Change page size":
            $this->changePageSize();
            break;
        case "Go to page":
            $this->goToPage();
            break;
      }
    }

    private function changePageSize(){
      $size = $this->ask('Please indicate how manu items per page (number)');
      if(is_numeric($size)){
        $this->page['size']=$size;
        $this->load();
      }else{
        $this->error("Value is required or invalid");
        $this->changePageSize();
      }
    }

    private function goToPage(){
      $current = $this->ask('Please indicate which page you will like to load (number)');
      if(is_numeric($current)){
        $this->page['current']=$current;
        $this->load();
      }else{
        $this->error("Value is required or invalid");
        $this->changePageSize();
      }
    }
}

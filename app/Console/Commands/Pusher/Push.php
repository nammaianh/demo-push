<?php namespace App\Console\Commands\Pusher;

use App\Services\PushService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Push extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'pusher:push';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run a notification-push.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(PushService $pushService)
	{
		$message = null;
		$tokens = ['31099971008333D4'];

		while (! $message) {
			$message = $this->ask('What is the message?');
		}

		$pushes = $pushService->pushToGcm($message, $tokens);

		$this->info('Pushed');

		/** @var \Sly\NotificationPusher\Model\Push $push */
		echo "{$pushes->count()} messages was pushed successfully" . PHP_EOL;
		foreach ($pushes as $push) {
			$this->info($push->getStatus());
		}
	}

	protected function _newLine()
	{
		echo PHP_EOL;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}

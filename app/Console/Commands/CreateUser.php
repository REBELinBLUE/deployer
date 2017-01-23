<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use RuntimeException;

/**
 * A command to create a user.
 **/
class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:create-user
                            {name : The name for the user}
                            {email : The email address for the user}
                            {password? : The password for the user, one will be generated if not supplied}
                            {--no-email : Do not send a welcome email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * The user repository.
     *
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * Create a new command instance..
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \RuntimeException
     * @fires UserWasCreated
     */
    public function handle()
    {
        $arguments  = $this->argument();
        $send_email = (!$this->option('no-email'));

        $password_generated = false;
        if (!$arguments['password']) {
            $arguments['password'] = str_random(15);
            $password_generated    = true;
        }

        $validator = Validator::make($arguments, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if (!$validator->passes()) {
            throw new RuntimeException($validator->errors()->first());
        }

        $user = $this->repository->create($arguments);

        $message = 'The user has been created';

        if ($send_email) {
            $message = 'The user has been created and their account details have been emailed to ' . $user->email;

            event(new UserWasCreated($user, $arguments['password']));
        } elseif ($password_generated) {
            $message .= ', however you elected to not email the account details to them. ';
            $message .= 'Their password is ' . $arguments['password'];
        }

        $this->info($message);
    }
}

<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
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
     */
    private $repository;

    /**
     * Create a new command instance..
     *
     * @param  UserRepositoryInterface $repository
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @todo validate
     */
    public function handle()
    {
        $arguments = $this->argument();
        $options   = $this->option();

        $password_generated = false;
        if (!$arguments['password']) {
            $arguments['password'] = str_random(15);
            $password_generated    = true;
        }

        // TODO: See if we can get these from StoreUserRequest somehow?
        $validator = Validator::make($arguments, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if (!$validator->passes()) {
            throw new RuntimeException($validator->errors()->first());
        }

        // FIXME: This is horrible!
        $emailed = true;
        if (isset($options['no-email']) && $options['no-email']) {
            $arguments['do_not_email'] = true;
            $emailed                   = false;
        }

        $user = $this->repository->create($arguments);

        $message = 'The user has been created and their account details have been emailed to ' . $user->email;

        if (!$emailed) {
            $message = 'The user has been created';

            if ($password_generated) {
                $message .= ', however you elected to not email the account details to them. Their password is ' . $arguments['password'];
            }
        }

        $this->info($message);
    }
}

<?php

namespace Janyk\LaravelSendinblueSync\Commands;

use Illuminate\Console\Command;
use Janyk\LaravelSendinblueSync\Exceptions\SyncException;
use Janyk\LaravelSendinblueSync\Traits\IsSendinblueContact;

class PushContactCommand extends Command
{
    public $signature = 'sendinblue:push {--id= : The ID of the contact model}';

    public $description = 'Push a model or all models as contacts to Sendinblue';

    public function handle(): int
    {
        $model = config('moneybird-sync.model');

        if (! class_exists($model) || ! in_array(IsSendinblueContact::class, class_uses_recursive($model))) {
            $this->error("The model {$model} either does not exist or does not have the IsMoneybirdContact trait applied.");

            return self::FAILURE;
        }

        if (! $this->option('id') && ! $this->confirm("Are you sure you want to push all models ({$model}) to Moneybird?", false)) {
            return self::FAILURE;
        }

        $contacts = $this->option('id') ?
            collect($model::find($this->option('id'))) :
            $model::all();

        $contacts->each(function ($contact): void {
            $this->line("Pushing customer {$contact->id} to Moneybird");

            try {
                $contact->createOrUpdateMoneybirdContact();
            } catch (SyncException $exception) {
                $this->error("Pushing {$contact->id} failed: {$exception->getMessage()}");
            }
        });

        return self::SUCCESS;
    }
}

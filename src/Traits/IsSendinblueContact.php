<?php

namespace Janyk\LaravelSendinblueSync\Traits;

use Janyk\LaravelSendinblueSync\Exceptions\SyncException;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\ApiException;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\UpdateContact;

trait IsSendinblueContact
{
    public static function bootIsSendinblueContact(): void
    {
        // Register the event listeners
        static::created(function ($model) {
            $model->createOrUpdateSendinblueContact();
        });

        static::updated(function ($model) {
            $model->createOrUpdateSendinblueContact();
        });
    }

    public function createOrUpdateSendinblueContact(): bool
    {
        $id = $this->getSendinblueId();

        return $id ? $this->updateContact($id) : $this->createContact();
    }

    private function getContactsApi(): ContactsApi
    {
        return app(ContactsApi::class);
    }

    private function getEmail(): ?string
    {
        return $this->{config('sendinblue-sync.email_field')};
    }

    private function getSendinblueId(): ?string
    {
        return $this->{config('sendinblue-sync.sendinblue_id_field')};
    }

    private function setSendinblueId(string $id): bool
    {
        $this->{config('sendinblue-sync.sendinblue_id_field')} = $id;

        return $this->saveQuietly();
    }

    private function mapAttributes(): array {
        $attributes = [];
        $mapping = config('sendinblue-sync.attributes');

        foreach ($mapping as $attributeKey => $modelKey) {
            if (empty($modelKey)) {
                continue;
            }

            $attributes[$attributeKey] = $this->{$modelKey};
        }

        return $attributes;
    }


    private abstract function saveQuietly(array $options = []);

    private function createContact(): bool
    {
        $contact = new CreateContact();
        $contact->setEmail($this->getEmail());
        $contact['attributes'] = $this->mapAttributes();

        $response = $this->getContactsApi()->createContact($contact);

        if(! $response->valid()) {
            return false;
        }

        $this->setSendinblueId($response->getId());

        return true;
    }

    private function updateContact(string $id): bool
    {
        $contact = new UpdateContact();
        $contact['attributes'] = $this->mapAttributes();

        try {
            $this->getContactsApi()->updateContact($id, $contact);
        } catch (ApiException $exception) {
            throw new SyncException($exception->getMessage());
        }

        return true;
    }
}

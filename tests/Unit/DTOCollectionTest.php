<?php

namespace Tests\Unit;

use App\Dto\Model\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DTOCollectionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @return User[]
     */
    private function createMultipleUsers(): array
    {
        $storedUserList = [];

        $index = 0;
        while ($index <= 2) {
            $userDTO = UserDTO::make()->setName($this->faker->name)
                ->setEmail($this->faker->email)
                ->setPassword($this->faker->password);

            $storedUserList[] = User::query()->create($userDTO->toArrayForModel());
            $index++;
        }

        return $storedUserList;
    }

    /**
     * @param Collection|User[] $userCollection
     * @param UserDTO[] $userDTOList
     * @return void
     */
    private function compareDataBetweenCollectionAndDTOList(Collection|array $userCollection, array $userDTOList): void
    {
        foreach ($userCollection as $index => $user) {
            $this->assertEquals($user->getAttribute('name'), $userDTOList[$index]->getName());
        }
    }
}

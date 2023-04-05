<?php

namespace Tests\Unit;

use App\Dto\Model\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DataTransferObjectTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @covers \App\Dto\Model\UserDTO::makeWith
     */
    public function testCanChangeModelToDataTransferObject(): void
    {
        // Given
        $user = User::query()->create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password
        ]);

        // When
        $userDTO = UserDTO::makeWith($user);

        // Then
        $this->assertNotEmpty($user->getAttribute('id'));
        $this->assertIsInt($user->getAttribute('id'));
        $this->assertEquals($user->getAttribute('name'), $userDTO->getName());
    }

    /**
     * @covers \App\Dto\Model\UserDTO::toArrayForModel
     */
    public function testCanUseDataTransferObjectWhenCreateModel(): void
    {
        // Given
        $userDTO = UserDTO::make()->setName($this->faker->name)
            ->setEmail($this->faker->email)
            ->setPassword($this->faker->password);

        // When
        $storedUser = User::query()->create($userDTO->toArrayForModel());

        // Then
        $this->assertNotEmpty($storedUser->getAttribute('id'));
        $this->assertIsInt($storedUser->getAttribute('id'));
        $this->assertEquals($userDTO->getName(), $storedUser->getAttribute('name'));
    }

    /**
     * @covers \App\Dto\Model\UserDTO::toArray
     * @covers \App\Dto\Model\UserDTO::make
     */
    public function testCanGetArrayUserDataFromDTO(): void
    {
        // Given
        $userDTO = UserDTO::make()->setName($this->faker->name)
            ->setEmail($this->faker->email)
            ->setPassword($this->faker->password);

        $storedUser = User::query()->create($userDTO->toArrayForModel());

        // When
        $userDataArrayForm = UserDTO::makeWith($storedUser)->toArray();

        // Then
        $this->assertNotNull($userDataArrayForm['id']);
        $this->assertEquals($userDTO->getName(), $userDataArrayForm['name']);
        $this->assertEquals($userDTO->getEmail(), $userDataArrayForm['email']);
    }

    /**
     * @covers \App\Dto\Model\UserDTO::toJson
     */
    public function testCanGetJSONUserDataFromDTO(): void
    {
        // Given
        $userDTO = UserDTO::make()->setName($this->faker->name)
            ->setEmail($this->faker->email)
            ->setPassword($this->faker->password);

        $storedUser = User::query()->create($userDTO->toArrayForModel());

        // When
        $userDataJsonForm = UserDTO::makeWith($storedUser)->toJson();

        // Then
        $userDataStdClass = json_decode($userDataJsonForm);
        $this->assertNotNull($userDataStdClass->id);
        $this->assertEquals($userDTO->getName(), $userDataStdClass->name);
        $this->assertEquals($userDTO->getEmail(), $userDataStdClass->email);
    }

    /**
     * @covers \App\Dto\Model\UserDTO::setDataFrom
     */
    public function testCanSetUserDataAfterCreateDTO(): void
    {
        // Given
        $userDTO = UserDTO::make()->setName($this->faker->name)
            ->setEmail($this->faker->email)
            ->setPassword($this->faker->password);

        $storedUser = User::query()->create($userDTO->toArrayForModel());

        // When
        $storedUserDTO = UserDTO::make();
        $storedUserDTO->setDataFrom($storedUser);

        // Then
        $this->assertNotNull($storedUserDTO->getId());
        $this->assertEquals($userDTO->getName(), $storedUserDTO->getName());
        $this->assertEquals($userDTO->getEmail(), $storedUserDTO->getEmail());
    }

    /**
     * @covers \App\Dto\Model\UserDTO::getOriginalModel
     */
    public function testCanGetOriginalUserModelFromDTO(): void
    {
        // Given
        $userDTO = UserDTO::make()->setName($this->faker->name)
            ->setEmail($this->faker->email)
            ->setPassword($this->faker->password);

        $storedUser = User::query()->create($userDTO->toArrayForModel());
        $storedUserDTO = UserDTO::makeWith($storedUser);

        // When
        $user = $storedUserDTO->getOriginalModel();

        // Then
        $this->assertNotNull($storedUserDTO->getId());
        $this->assertTrue($user instanceof User);
        $this->assertEquals($userDTO->getName(), $user->getAttribute('name'));
        $this->assertEquals($userDTO->getEmail(), $user->getAttribute('email'));
    }

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

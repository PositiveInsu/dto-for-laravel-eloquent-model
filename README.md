<!-- @format -->
# Data Transfer Object for Laravel Eloquent Model/Collection

![Licence](https://img.shields.io/github/license/PositiveInsu/dto-for-laravel-eloquent-model)
![GitHub top language](https://img.shields.io/github/languages/top/PositiveInsu/dto-for-laravel-eloquent-model)
![Contributors](https://img.shields.io/github/contributors/PositiveInsu/dto-for-laravel-eloquent-model)
![Forks](https://img.shields.io/github/forks/PositiveInsu/dto-for-laravel-eloquent-model)
![Stars](https://img.shields.io/github/stars/PositiveInsu/dto-for-laravel-eloquent-model)
![Issues](https://img.shields.io/github/issues/PositiveInsu/dto-for-laravel-eloquent-model)

## Overview

Data Transfer Object (DTO) is a simple object to hold the data from Laravel Eloquent Model
or Collection.

Laravel eloquent Model object handles data with an array; it is an easy and flexible way to handle the data.
On the contrary, when we use the Model object, we have to reference the target Model object's fillable fields 
or databases to understand what is inside. 
Luckily, If you use the Debug tool, it gives more detailed information to you. 
But anyway, it is hard to know what data Model handles when you write the code.

I developed this code for reducing this uncomfortable situation.
Data Transfer Object allow PHP Laravel developer to use Model more clearly and way of OOP.

## Usage

I planned to deploy the library by the Composer, but for now, you just copy and paste the code to your project and use it.

> Check the /app/Dto/AbstractDataTransferObject.php

In order to use the DTO, firstly you have to make the Data Transfer Object with target Model.
In my example, I use the User Model which the Laravel give us default Model.

Firstly, you have to check the User Migration file. 

```php
// in the User Migration file 
Schema::create('users', function (Blueprint $table) {
    $table->id();                                           // id
    $table->string('name');                                 // name
    $table->string('email')->unique();                      // email
    $table->timestamp('email_verified_at')->nullable();     // email_verified_at
    $table->string('password');                             // password
    $table->rememberToken();                                // remember_token
    $table->timestamps();                                   // create_at, update_at
});
```
You can make the DTO by database column names.
DTO class has to extend AbstractDataTransferObject.

> Convention: Please make sure DTO's variables name must equal to Database column name.

```php
class UserDTO extends AbstractDataTransferObject
{
    private int|null $id;
    private string|null $name;
    private string|null $email;
    private string|null $email_verified_at;
    private string|null $password;
    private string|null $remember_token;
    private string|null $created_at;
    private string|null $updated_at;
    
    ...getter and setter
}
```
That's it! Now you can handle the data easier and clear way by UserDTO.
If you use this with IDE, it reduces the coding time with auto-completion function.

```php
$user = User::query()->create([
    'name' => $this->faker->name,
    'email' => $this->faker->email,
    'password' => $this->faker->password
]);

$userDTO = UserDTO::makeWith($user);

// can handle the data with DTO easier and clear way.
$name = $userDTO->getName();
$id = $userDTO->getId();
```
